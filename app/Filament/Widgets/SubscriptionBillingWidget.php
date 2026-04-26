<?php

namespace App\Filament\Widgets;

use App\Models\Promotion;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class SubscriptionBillingWidget extends Widget
{
    protected static string $view = 'filament.widgets.subscription-billing-widget';

    protected int|string|array $columnSpan = 'full';

    public array $summary = [];

    public static function canView(): bool
    {
        return Filament::auth()->check() && (int) Filament::auth()->user()->user_type === 3;
    }

    public function mount(): void
    {
        $this->loadSummary();
    }

    public function loadSummary(): void
    {
        $user = Filament::auth()->user();
        $savedCardDisplay = 'Not configured';
        $savedCardBrand = null;
        $savedCardLast4 = null;
        $savedCardExp = null;

        $activePromotions = Promotion::query()
            ->whereHas('service', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('is_active', true)
            ->count();

        $latestActivePromotion = Promotion::query()
            ->whereHas('service', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('is_active', true)
            ->latest('updated_at')
            ->first();

        if ($user->default_payment_method) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $paymentMethod = PaymentMethod::retrieve($user->default_payment_method);
                $card = $paymentMethod->card;

                if ($card) {
                    $brand = ucfirst((string) $card->brand);
                    $last4 = (string) $card->last4;
                    $expMonth = str_pad((string) $card->exp_month, 2, '0', STR_PAD_LEFT);
                    $expYear = (string) $card->exp_year;
                    $savedCardDisplay = "{$brand} ending in {$last4} (exp {$expMonth}/{$expYear})";
                    $savedCardBrand = strtoupper($brand);
                    $savedCardLast4 = $last4;
                    $savedCardExp = "{$expMonth}/{$expYear}";
                } else {
                    $savedCardDisplay = 'Payment method configured';
                }
            } catch (\Throwable $e) {
                $savedCardDisplay = 'Payment method configured';
            }
        }

        $this->summary = [
            'active_promotions' => $activePromotions,
            'status' => $activePromotions > 0 ? 'Active' : 'Inactive',
            'renewal_note' => 'Per-impression billing model (no fixed monthly renewal).',
            'expiration_note' => $activePromotions > 0
                ? 'Subscription remains active while promotions are enabled.'
                : 'No active promotion, so billing is currently inactive.',
            'last_activity_at' => $latestActivePromotion?->updated_at?->format('d M Y, H:i'),
            'payment_method' => $user->default_payment_method
                ? ('**** ' . substr($user->default_payment_method, -4))
                : 'Not configured',
            'saved_card_display' => $savedCardDisplay,
            'saved_card_brand' => $savedCardBrand,
            'saved_card_last4' => $savedCardLast4,
            'saved_card_exp' => $savedCardExp,
        ];
    }

    public function cancelSubscription(): void
    {
        $user = Filament::auth()->user();

        $affected = Promotion::query()
            ->whereHas('service', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $this->loadSummary();

        Notification::make()
            ->title($affected > 0 ? 'Subscription cancelled' : 'No active subscription')
            ->body($affected > 0
                ? 'All active promotions were disabled successfully.'
                : 'There were no active promotions to disable.')
            ->success()
            ->send();
    }
}

