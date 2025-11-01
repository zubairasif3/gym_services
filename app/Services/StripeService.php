<?php

namespace App\Services;

use Stripe\Stripe;
use App\Models\Gig;
use App\Models\User;
use Stripe\Customer;
use Stripe\SetupIntent;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Auth;

class StripeService
{
    public function createSetupIntent()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $userId = Auth::id();
        $user = User::find($userId);

        // If user doesn't already have a Stripe customer ID, create one
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
            ]);

            $user->update(['stripe_customer_id' => $customer->id]);
        }

        $setupIntent = SetupIntent::create([
            'customer' => $user->stripe_customer_id,
        ]);

        return response()->json(['clientSecret' => $setupIntent->client_secret]);
    }
    public function chargeSeller(Gig $gig)
    {
        $seller = $gig->user;

        Stripe::setApiKey(config('services.stripe.secret'));

        $amount = $gig->promotion->rate_per_impression * 100; // in cents

        $paymentIntent = PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'usd',
            'customer' => $seller->stripe_customer_id,
            'payment_method' => $seller->default_payment_method,
            'off_session' => true,
            'confirm' => true,
            'description' => "Promotion charge for gig ID: {$gig->id}",
        ]);

        // You can now record the transaction in your DB...

        return response()->json(['message' => 'Seller charged successfully!']);
    }
}
