<?php

namespace App\Http\Controllers;

use App\Models\Gig;
use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ChatRoom;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;

class HomeController extends Controller
{
    public function index(){

        $categories = Category::with([
            'gigs' => function ($query) {
                $query->where('gigs.is_active', true); // 👈 specify table
            }
        ])
        ->where('categories.is_active', true) // 👈 specify table
        ->get();

        // Get subcategories with gig count for the carousel
        $subcategories = Subcategory::withCount([
            'gigs' => function ($query) {
                $query->where('is_active', true);
            }
        ])
        ->where('is_active', true)
        ->having('gigs_count', '>', 0) // Ensure at least 1 active gig
        ->take(10) // Limit to 10 subcategories for the carousel
        ->get();

        // Static fallback images array
        $staticImages = [
            'web/images/listings/category-1.jpg',
            'web/images/listings/category-2.jpg',
            'web/images/listings/category-3.jpg',
            'web/images/listings/category-4.jpg',
            'web/images/listings/category-5.jpg'
        ];

        return view('web.index', compact('categories', 'subcategories', 'staticImages'));
    }
    public function search(Request $request)
    {
        $query = $request->input('search');
        $category_id = $request->input('category_id');

        $services = Gig::where('title', 'like', "%{$query}%")
            ->orWhereHas('subcategory', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->where(function($query) use ($category_id) {
                if (filter_var($category_id, FILTER_VALIDATE_INT) !== false) {
                    $query->whereHas('subcategory', function ($q) use ($category_id) {
                        $q->where('category_id', $category_id);
                    });
                }
            })
            ->with(['user.profile', 'subcategory', 'images'])
            ->get();
        return view('web.search', compact('services', 'query'));
    }

    public function gigShow($slug)
    {
        $gig = Gig::with([
                'user.profile',
                'user.followers',
                'user.following',
                'packages',
                'images',
                'reviews' => function($query) {
                    $query->with('user.profile')
                        ->recent()
                        ->limit(5);
                },
            ])
            ->withCount(['saves', 'shares', 'reviews'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Check if current user saved this gig
        $isSaved = auth()->check() ? $gig->isSavedByUser(auth()->id()) : false;

        // Get related gigs with their stats
        $relatedGigs = Gig::with(['user.profile', 'images'])
            ->withCount(['reviews', 'saves'])
            ->where('subcategory_id', $gig->subcategory_id)
            ->where('id', '!=', $gig->id)
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        // Increment impressions count
        $gig->increment('impressions');

        // Calculate review statistics
        $reviewStats = [
            'average' => $gig->reviews()->avg('rating') ?? 0,
            'total' => $gig->reviews_count,
            'breakdown' => $this->getReviewBreakdown($gig),
        ];

        return view('web.gigi-show', compact('gig', 'relatedGigs', 'isSaved', 'reviewStats'));
    }

    /**
     * Get review rating breakdown
     */
    private function getReviewBreakdown($gig)
    {
        $breakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $breakdown[$i] = $gig->reviews()->where('rating', $i)->count();
        }
        return $breakdown;
    }

    public function contact(){
        return view('web.contact');
    }

    public function about(){
        return view('web.about');
    }
    public function term_of_services(){
        return view('web.term_of_services');
    }
    public function privacy_policy(){
        return view('web.privacy_policy');
    }

    public function login(){
        return view('web.login');
    }

    public function register(){
        return view('web.register');
    }

    public function services(Request $request, $categoryId = null, $subcategoryId = null)
    {
        $country = $request->get('country'); // Get the country filter from the request
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $categories = Category::with([
            'subcategories',
            'gigs' => function ($query) use ($minPrice, $maxPrice) {
                $query->where('gigs.is_active', true); // Specify table here
                
                // Apply price range filter
                if ($minPrice !== null && $minPrice !== '') {
                    $query->where('starting_price', '>=', $minPrice);
                }
                if ($maxPrice !== null && $maxPrice !== '') {
                    $query->where('starting_price', '<=', $maxPrice);
                }
            },
            'gigs.user.profile',
            'gigs.images',
            'gigs.subcategory'
        ])
        ->when($country, function ($query) use ($country) {
            // If a country is provided, filter by gigs' users' profile country
            return $query->whereHas('gigs.user.profile', function ($query) use ($country) {
                $query->where('country', $country); // Apply the country filter
            });
        })
        ->where('categories.is_active', true) // Optional: Specify if you're doing joins or to avoid ambiguity
        ->get();

        // Collect all countries
        $countries = $categories->flatMap(function ($category) {
            return $category->gigs->map(function ($gig) {
                return $gig->user->profile->country;
            });
        })->filter();

        // Get unique countries
        $uniqueCountries = $countries->unique();

        // If you want to return the result as an array, you can call ->toArray()
        $finalCountries = $uniqueCountries->toArray();
        
        // Get price range for the filter
        $priceRange = Gig::where('is_active', true)->selectRaw('MIN(starting_price) as min_price, MAX(starting_price) as max_price')->first();
        
        return view('web.services', compact('categories', 'categoryId', 'subcategoryId', 'finalCountries', 'priceRange', 'minPrice', 'maxPrice'));
    }

    public function registerProcess(Request $request)
    {
        try {
            // Get user type from hidden field (2 = Customer, 3 = Professional/Seller)
            $userType = $request->input('user_type', 2);

            // Base validation rules for all users
            $rules = [
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'country' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'privacy_consent' => 'required|accepted',
            ];

            // Add conditional validation based on user type
            if ($userType == 2) {
                // Customer specific fields
                $rules['date_of_birth'] = 'required|date|before:today';
                $rules['cap'] = 'required|string|max:10';
            } elseif ($userType == 3) {
                // Professional/Seller specific fields
                $rules['business_name'] = 'nullable|string|max:255';
                $rules['address'] = 'nullable|string|max:500';
                $rules['cap'] = 'required|string|max:10';
                $rules['category_id'] = 'required|exists:categories,id';
                $rules['subcategory_1'] = 'required|exists:subcategories,id';
                $rules['subcategory_2'] = 'nullable|exists:subcategories,id';
                $rules['subcategory_3'] = 'nullable|exists:subcategories,id';
            }

            // Validate the input
            $validated = $request->validate($rules);

            // Use database transaction to ensure data consistency
            DB::beginTransaction();

            try {
                // Prepare user data
                $userData = [
                    'name' => $validated['name'],
                    'surname' => $validated['surname'],
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'user_type' => $userType,
                ];

                // Add business_name for professionals
                if ($userType == 3 && isset($validated['business_name'])) {
                    $userData['business_name'] = $validated['business_name'];
                }

                // Create user
                $user = User::create($userData);

                // Prepare profile data
                $profileData = [
                    'user_id' => $user->id,
                    'country' => $validated['country'],
                    'city' => $validated['city'],
                ];

                // Add conditional profile fields
                if ($userType == 2) {
                    // Customer
                    $profileData['date_of_birth'] = $validated['date_of_birth'];
                    $profileData['cap'] = $validated['cap'];
                    $profileData['is_provider'] = false;
                } elseif ($userType == 3) {
                    // Professional/Seller
                    $profileData['cap'] = $validated['cap'];
                    $profileData['is_provider'] = true;
                    // Address is optional for professionals
                    if (isset($validated['address']) && !empty($validated['address'])) {
                        $profileData['address'] = $validated['address'];
                    }
                }

                // Create user profile
                $userProfile = UserProfile::create($profileData);

                // For professionals, attach subcategories and handle payment method
                if ($userType == 3) {
                    $subcategories = [
                        ['subcategory_id' => $validated['subcategory_1'], 'priority' => 1],
                    ];

                    if (!empty($validated['subcategory_2'])) {
                        $subcategories[] = ['subcategory_id' => $validated['subcategory_2'], 'priority' => 2];
                    }

                    if (!empty($validated['subcategory_3'])) {
                        $subcategories[] = ['subcategory_id' => $validated['subcategory_3'], 'priority' => 3];
                    }

                    foreach ($subcategories as $subcategory) {
                        \App\Models\UserSubcategory::create([
                            'user_id' => $user->id,
                            'subcategory_id' => $subcategory['subcategory_id'],
                            'priority' => $subcategory['priority'],
                        ]);
                    }

                    // Handle Stripe payment method for professionals
                    if ($request->has('payment_method_id') && !empty($request->input('payment_method_id'))) {
                        try {
                            Stripe::setApiKey(config('services.stripe.secret'));
                            
                            // Check if we're in local/test environment and using test keys
                            $stripeSecret = config('services.stripe.secret');
                            $isTestMode = strpos($stripeSecret, 'sk_test_') === 0;
                            
                            // Create Stripe customer
                            $customer = Customer::create([
                                'email' => $user->email,
                                'name' => $user->name . ' ' . $user->surname,
                            ]);
                            
                            // Attach payment method to customer
                            PaymentMethod::retrieve($request->input('payment_method_id'))
                                ->attach(['customer' => $customer->id]);
                            
                            // Set as default payment method
                            Customer::update($customer->id, [
                                'invoice_settings' => [
                                    'default_payment_method' => $request->input('payment_method_id'),
                                ],
                            ]);
                            
                            // Update user with Stripe customer ID and payment method
                            $user->update([
                                'stripe_customer_id' => $customer->id,
                                'default_payment_method' => $request->input('payment_method_id'),
                            ]);
                        } catch (\Stripe\Exception\CardException $e) {
                            // Card-specific errors
                            Log::error('Stripe card error during registration: ' . $e->getMessage());
                            // In local, provide more context
                            if (config('app.env') === 'local') {
                                Log::info('Make sure you are using TEST Stripe keys: pk_test_... and sk_test_...');
                            }
                            // Continue with registration even if Stripe fails
                        } catch (\Exception $e) {
                            // Log Stripe error but don't fail registration
                            Log::error('Stripe error during registration: ' . $e->getMessage());
                            // Continue with registration even if Stripe fails
                        }
                    }
                }

                // Commit transaction
                DB::commit();

                // Send email verification notification
                $user->sendEmailVerificationNotification();

                // Redirect to email verification notice page
                return redirect()->route('verification.notice')
                    ->with('registered', true)
                    ->with('email', $user->email);

            } catch (\Exception $e) {
                // Rollback transaction on error
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'An error occurred during registration. Please try again later.'])
                ->withInput();
        }
    }
    public function loginProcess(Request $request)
    {
        // Validate input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Try to find the user
        $user = User::where('email', $request->email)->first();

        // User found?
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            Auth::login($user);
            return redirect()->route('verification.notice')
                ->withErrors(['email' => 'You must verify your email address before you can access. Check your email for the verification link.']);
        }

        // All good - log in user
        Auth::login($user);

        // Check if user_type is 2
        if ($user->user_type != 2) {
            return redirect('/admin');
        }

        // Redirect to dashboard or home
        return redirect()->route('web.index')->with('success', 'Login successful!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('web.login')->with('success', 'Logged out successfully.');
    }

    /**
     * Show the email verification notice page.
     */
    public function showVerificationNotice()
    {
        // Allow both authenticated and unauthenticated users to see this page
        // If authenticated but not verified, show resend option
        // If just registered, show success message
        return view('web.verify-email');
    }

    /**
     * Mark the user's email as verified.
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Verify the hash matches
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('web.login')->with('success', 'Email already verified!');
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        // Auto-login after verification
        Auth::login($user);

        // Redirect based on user type
        if ($user->user_type == 3) {
            return redirect('/admin')->with('success', 'Email verified successfully! You can now access the dashboard.');
        }

        return redirect()->route('web.index')->with('success', 'Email verified successfully! You can now access your account.');
    }

    /**
     * Resend the email verification notification.
     */
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('web.index');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent!');
    }

    /**
     * Resend the email verification notification for non-authenticated users.
     */
    public function resendVerificationEmailGuest(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found with this email address.']);
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('status', 'Email already verified!');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent!')->with('email', $user->email);
    }


    public function gigContact(Request $request, $id)
    {
        // Step 1: Retrieve the gig by ID
        $gig = Gig::findOrFail($id); // Assuming Gig model exists and has an `id` column

        // Step 2: Get the receiver (user associated with this gig)
        $receiver = $gig->user; // Assuming `user()` relationship exists on the Gig model

        // Step 3: Check if the gig is active
        if ($gig->is_active != 1) {
            return response()->json(['message' => 'This gig is not active'], 400);
        }

        // Step 4: Check if the authenticated user is not the receiver
        if (Auth::id() === $receiver->id) {
            return response()->json(['message' => 'You cannot chat with yourself'], 400);
        }

        // Step 5: Check if a chat room already exists between the sender (authenticated user) and receiver
        $chatRoom = ChatRoom::updateOrCreate(
            [
                'sender_id' => Auth::id(),  // Authenticated user's ID
                'receiver_id' => $receiver->id, // Receiver's ID (user associated with the gig)
            ],
            [
                // Additional fields to be updated/created
                'is_active' => 1,  // Set the chat room as active
            ]
        );

        $url = "/chat?chat_room_id=". $chatRoom->id;
        return redirect($url);
    }
}
