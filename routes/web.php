<?php

use Stripe\Stripe;
use App\Models\User;
use App\Models\Notification;
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

// Password Reset Routes
Route::get('/forgot-password', [HomeController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [HomeController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [HomeController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [HomeController::class, 'resetPassword'])->name('password.update');

// Email verification routes
Route::get('/email/verify', [HomeController::class, 'showVerificationNotice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [HomeController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');
Route::post('/email/verification-notification', [HomeController::class, 'resendVerificationEmail'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
Route::post('/email/verification-notification-guest', [HomeController::class, 'resendVerificationEmailGuest'])->middleware(['throttle:6,1'])->name('verification.send.guest');
Route::get('/gigs-show/{slug}', [HomeController::class, 'gigShow'])->name('gigs.show');
Route::get('/services-search', [HomeController::class, 'search'])->name('services.search');
Route::get('/gig-content/{id}', [HomeController::class, 'gigContact'])->name('gig.contact');

Route::get('/chat', ChatPage::class);

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Follower Routes
    Route::post('/follow/{user}', [App\Http\Controllers\FollowerController::class, 'follow'])->name('follow');
    Route::delete('/unfollow/{user}', [App\Http\Controllers\FollowerController::class, 'unfollow'])->name('unfollow');
    Route::get('/following', [App\Http\Controllers\FollowerController::class, 'following'])->name('following');
    Route::get('/followers', [App\Http\Controllers\FollowerController::class, 'followers'])->name('followers');
    Route::get('/api/follow/count/{user}', [App\Http\Controllers\FollowerController::class, 'getCount'])->name('follow.count');
    Route::get('/api/follow/check/{user}', [App\Http\Controllers\FollowerController::class, 'checkFollowing'])->name('follow.check');
    
    // Notification Routes
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
    Route::patch('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/api/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/api/notifications/latest', [App\Http\Controllers\NotificationController::class, 'getLatest'])->name('notifications.latest');
    
    // Chat Routes
    Route::get('/messages', [App\Http\Controllers\ChatController::class, 'index'])->name('messages');
    Route::post('/chat/room/{receiver}', [App\Http\Controllers\ChatController::class, 'getOrCreateRoom'])->name('chat.room.create');
    Route::get('/chat/room/{chatRoom}/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/room/{chatRoom}/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/upload-attachment', [App\Http\Controllers\ChatController::class, 'uploadAttachment'])->name('chat.upload');
    Route::get('/api/chat/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
    
    // Professional Profile Routes
    Route::get('/profile/edit', [App\Http\Controllers\ProfessionalProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [App\Http\Controllers\ProfessionalProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/preview', [App\Http\Controllers\ProfessionalProfileController::class, 'preview'])->name('professional.preview');
    
    // Profile Media Routes
    Route::post('/profile/media/upload', [App\Http\Controllers\ProfessionalProfileController::class, 'uploadMedia'])->name('profile.media.upload');
    Route::delete('/profile/media/{id}', [App\Http\Controllers\ProfessionalProfileController::class, 'deleteMedia'])->name('profile.media.delete');
    Route::post('/profile/media/reorder', [App\Http\Controllers\ProfessionalProfileController::class, 'reorderMedia'])->name('profile.media.reorder');
    
    // Gig Review Routes (Authenticated)
    Route::post('/gigs/{gig}/reviews', [App\Http\Controllers\GigReviewController::class, 'store'])->name('gigs.reviews.store');
    Route::put('/reviews/{review}', [App\Http\Controllers\GigReviewController::class, 'update'])->name('gigs.reviews.update');
    Route::delete('/reviews/{review}', [App\Http\Controllers\GigReviewController::class, 'destroy'])->name('gigs.reviews.destroy');
    Route::post('/reviews/{review}/helpful', [App\Http\Controllers\GigReviewController::class, 'markHelpful'])->name('gigs.reviews.helpful');
    
    // Gig Save Routes (Authenticated)
    Route::post('/gigs/{gig}/save', [App\Http\Controllers\GigSaveController::class, 'toggle'])->name('gigs.save.toggle');
    Route::get('/api/gigs/{gig}/save/check', [App\Http\Controllers\GigSaveController::class, 'check'])->name('gigs.save.check');
    Route::get('/api/saved-gigs', [App\Http\Controllers\GigSaveController::class, 'savedGigs'])->name('gigs.saved');
});

// Public Professional Profile
Route::get('/@{username}', [App\Http\Controllers\ProfessionalProfileController::class, 'show'])->name('professional.profile');

// Public Gig Review Routes
Route::get('/api/gigs/{gig}/reviews', [App\Http\Controllers\GigReviewController::class, 'index'])->name('gigs.reviews.index');

// Public Gig Share Routes
Route::post('/api/gigs/{gig}/share', [App\Http\Controllers\GigShareController::class, 'track'])->name('gigs.share.track');
Route::get('/api/gigs/{gig}/share/count', [App\Http\Controllers\GigShareController::class, 'count'])->name('gigs.share.count');
Route::get('/api/gigs/{gig}/share/urls', [App\Http\Controllers\GigShareController::class, 'urls'])->name('gigs.share.urls');
Route::get('/api/gigs/{gig}/save/count', [App\Http\Controllers\GigSaveController::class, 'count'])->name('gigs.save.count');

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

// Temporary route to create test notifications (for development only)
Route::get('/create-test-notifications', function () {
    $user = User::find(1); // Test User
    
    if (!$user) {
        return 'User not found';
    }
    
    // Clear existing notifications for this user
    Notification::where('user_id', $user->id)->delete();
    
    // Create test notifications
    Notification::create([
        'user_id' => $user->id,
        'type' => 'follow',
        'data' => ['message' => 'John Doe started following you'],
        'read_at' => null,
    ]);
    
    Notification::create([
        'user_id' => $user->id,
        'type' => 'message',
        'data' => ['message' => 'You have a new message from Sarah Smith'],
        'read_at' => null,
    ]);
    
    Notification::create([
        'user_id' => $user->id,
        'type' => 'review',
        'data' => ['message' => 'New 5-star review on "Professional Personal Training"'],
        'read_at' => null,
    ]);
    
    Notification::create([
        'user_id' => $user->id,
        'type' => 'order',
        'data' => ['message' => 'New order received for €140.00'],
        'read_at' => null,
    ]);
    
    return 'Created 4 test notifications for ' . $user->name . '. <br><a href="/admin">Go to Admin Panel</a>';
})->name('create.test.notifications');
