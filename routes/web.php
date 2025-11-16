<?php

use Stripe\Stripe;
use App\Models\User;
use Stripe\Customer;
use Stripe\Exception\CardException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Filament\Facades\Filament;
use App\Filament\Pages\ChatPage;


Route::get('/', [HomeController::class, 'index'])->name('web.index');
Route::get('/contact', [HomeController::class, 'contact'])->name('web.contact');
Route::get('/services/{category?}/{subcategory?}', [HomeController::class, 'services'])->name('web.services');
Route::get('/about', [HomeController::class, 'about'])->name('web.about');
Route::get('/term-of-services', [HomeController::class, 'term_of_services'])->name('web.term_of_services');
Route::get('/privacy-policy', [HomeController::class, 'privacy_policy'])->name('web.privacy_policy');
Route::get('/login', [HomeController::class, 'login'])->name('web.login');
Route::post('/login', [HomeController::class, 'loginProcess'])->name('web.login.post');
Route::post('/logout', [HomeController::class, 'logout'])->name('web.logout');
Route::get('/register', [HomeController::class, 'register'])->name('web.register');
Route::post('/register', [HomeController::class, 'registerProcess'])->name('web.register.process');

// Email verification routes
Route::get('/email/verify', [HomeController::class, 'showVerificationNotice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [HomeController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');
Route::post('/email/verification-notification', [HomeController::class, 'resendVerificationEmail'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
Route::get('/gigs-show/{slug}', [HomeController::class, 'gigShow'])->name('gigs.show');
Route::get('/services-search', [HomeController::class, 'search'])->name('services.search');
Route::get('/gig-content/{id}', [HomeController::class, 'gigContact'])->name('gig.contact');

Route::get('/chat', ChatPage::class);

// API route for fetching subcategories
Route::get('/api/subcategories/{categoryId}', function ($categoryId) {
    $subcategories = \App\Models\Subcategory::where('category_id', $categoryId)
        ->where('is_active', true)
        ->select('id', 'name')
        ->orderBy('name')
        ->get();
    
    return response()->json($subcategories);
});

// API route to check Stripe configuration
Route::get('/api/check-stripe-config', function () {
    if (config('app.env') !== 'local') {
        return response()->json(['isLocal' => false]);
    }
    
    $stripeKey = config('services.stripe.key');
    $stripeSecret = config('services.stripe.secret');
    
    $isTestKey = strpos($stripeKey, 'pk_test_') === 0;
    $isTestSecret = strpos($stripeSecret, 'sk_test_') === 0;
    
    return response()->json([
        'isLocal' => true,
        'isTestKey' => $isTestKey,
        'isTestSecret' => $isTestSecret,
        'hasKeys' => !empty($stripeKey) && !empty($stripeSecret),
        'keyPreview' => $stripeKey ? substr($stripeKey, 0, 8) . '...' : 'Not set',
    ]);
});

// API route for creating setup intent during registration (before user exists)
Route::post('/api/create-setup-intent-for-registration', function (Request $request) {
    try {
        // Validate Stripe keys in local environment
        if (config('app.env') === 'local') {
            $stripeSecret = config('services.stripe.secret');
            $isTestSecret = strpos($stripeSecret, 'sk_test_') === 0;
            
            if (!$isTestSecret && !empty($stripeSecret)) {
                return response()->json([
                    'error' => true,
                    'message' => '⚠️ Live mode keys detected in local environment! Please update your .env file:\nSTRIPE_KEY=pk_test_...\nSTRIPE_SECRET=sk_test_...\n\nGet your test keys from: https://dashboard.stripe.com/test/apikeys'
                ], 400);
            }
        }
        
        Stripe::setApiKey(config('services.stripe.secret'));
        
        // Create a temporary setup intent without customer (we'll attach it later)
        $setupIntent = \Stripe\SetupIntent::create([
            'payment_method_types' => ['card'],
        ]);
        
        return response()->json(['clientSecret' => $setupIntent->client_secret]);
    } catch (CardException $e) {
        // Card was declined - provide helpful error message
        $message = $e->getMessage();
        
        // In local environment, provide more helpful test mode guidance
        if (config('app.env') === 'local' && strpos($message, 'live mode') !== false) {
            $message = 'Test card used with live mode keys. Please ensure you are using Stripe TEST keys in your .env file when in local environment. Test card: 4242 4242 4242 4242';
        }
        
        return response()->json([
            'error' => true,
            'message' => $message
        ], 400);
    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => config('app.env') === 'local' 
                ? 'Stripe error. Make sure you are using TEST API keys in your .env file: STRIPE_KEY=pk_test_... and STRIPE_SECRET=sk_test_...'
                : $e->getMessage()
        ], 400);
    }
});

Route::post('/store-payment-method', function (Request $request) {
    $user = User::find(Auth::id());

    Stripe::setApiKey(config('services.stripe.secret'));

    Customer::update($user->stripe_customer_id, [
        'invoice_settings' => [
            'default_payment_method' => $request->payment_method,
        ],
    ]);

    $user->update([
        'default_payment_method' => $request->payment_method,
    ]);

    return response()->json(['message' => 'Payment method saved.']);
});


// use Stripe\PaymentIntent;

// public function chargeSeller(Gig $gig)
// {
//     $seller = $gig->user;

//     Stripe::setApiKey(config('services.stripe.secret'));

//     $amount = $gig->promotion->rate_per_impression * 100; // in cents

//     $paymentIntent = PaymentIntent::create([
//         'amount' => $amount,
//         'currency' => 'usd',
//         'customer' => $seller->stripe_customer_id,
//         'payment_method' => $seller->default_payment_method,
//         'off_session' => true,
//         'confirm' => true,
//         'description' => "Promotion charge for gig ID: {$gig->id}",
//     ]);

//     // You can now record the transaction in your DB...

//     return response()->json(['message' => 'Seller charged successfully!']);
// }
