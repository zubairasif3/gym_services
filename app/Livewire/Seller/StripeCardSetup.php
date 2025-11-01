<?php

namespace App\Livewire\Seller;

use Stripe\Stripe;
use App\Models\User;
use Stripe\Customer;
use Livewire\Component;
use Stripe\SetupIntent;
use Illuminate\Support\Facades\Auth;

class StripeCardSetup extends Component
{
    public $clientSecret;

    public function mount()
    {
        $user = User::find(Auth::id());

        Stripe::setApiKey(config('services.stripe.secret'));

        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        $intent = SetupIntent::create([
            'customer' => $user->stripe_customer_id,
        ]);

        $this->clientSecret = $intent->client_secret;
    }

    public function render()
    {
        return view('livewire.seller.stripe-card-setup');
    }
}

