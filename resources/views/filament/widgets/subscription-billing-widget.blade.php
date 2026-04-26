<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6 relative" x-data="{ showCancelModal: false }">
            @if(request()->get('card_updated') == '1')
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
                    {{ __('admin.widgets.card_updated') }}
                </div>
            @endif

            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">{{ __('admin.widgets.subscription_billing') }}</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-xl border p-4 bg-white dark:bg-gray-900">
                    <p class="text-sm text-gray-500">{{ __('admin.widgets.subscription_status') }}</p>
                    <p class="text-xl font-semibold mt-1">{{ $summary['status'] ?? __('admin.widgets.inactive') }}</p>
                </div>
                <div class="rounded-xl border p-4 bg-white dark:bg-gray-900">
                    <p class="text-sm text-gray-500">{{ __('admin.widgets.active_promotions') }}</p>
                    <p class="text-xl font-semibold mt-1">{{ $summary['active_promotions'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl border p-4 bg-white dark:bg-gray-900">
                    <p class="text-sm text-gray-500">{{ __('admin.widgets.payment_method') }}</p>
                    <p class="text-xl font-semibold mt-1">{{ $summary['payment_method'] ?? __('admin.widgets.not_configured') }}</p>
                </div>
            </div>

            <div class="rounded-xl border bg-white dark:bg-gray-900 p-6">
                <h4 class="text-base font-semibold">{{ __('admin.widgets.renewal_expiration') }}</h4>
                <div class="mt-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 space-y-3">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <strong>{{ __('admin.widgets.renewal') }}</strong> {{ $summary['renewal_note'] ?? '-' }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <strong>{{ __('admin.widgets.expiration') }}</strong> {{ $summary['expiration_note'] ?? '-' }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <strong>{{ __('admin.widgets.last_billing_activity') }}</strong> {{ $summary['last_activity_at'] ?? 'N/A' }}
                    </p>
                </div>
            </div>

            <div class="rounded-xl border bg-white dark:bg-gray-900 p-6">
                <h4 class="text-sm font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">{{ __('admin.widgets.payment_method') }}</h4>
                <div class="mt-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4">
                    @if(!empty($summary['saved_card_last4']) && !empty($summary['saved_card_exp']))
                        @php
                            $cardBrandUpper = strtoupper($summary['saved_card_brand'] ?? 'CARD');
                            $badgeStyle = match (strtolower($summary['saved_card_brand'] ?? '')) {
                                'mastercard' => 'background:#eb001b;color:#ffffff;',
                                'visa' => 'background:#1a1f71;color:#ffffff;',
                                'amex', 'american express' => 'background:#2e77bb;color:#ffffff;',
                                default => 'background:#374151;color:#ffffff;',
                            };
                        @endphp
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-md text-xs font-bold px-2 py-1"
                                  style="{{ $badgeStyle }}">
                                {{ $cardBrandUpper }}
                            </span>
                            <div>
                                <p class="text-base font-medium text-gray-800 dark:text-gray-100">
                                    {{ ucfirst(strtolower($summary['saved_card_brand'] ?? 'Card')) }} •••• {{ $summary['saved_card_last4'] }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ __('admin.widgets.expires', ['date' => $summary['saved_card_exp']]) }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ $summary['saved_card_display'] ?? __('admin.widgets.not_configured') }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ url('/admin/add-card') }}"
                    class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50"
                    style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);">
                    {{ __('admin.widgets.modify_payment_method') }}
                </a>

                <button type="button"
                    @click="showCancelModal = true"
                    class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm"
                    style="background:#dc2626;color:#ffffff;border:1px solid #b91c1c;">
                    {{ __('admin.widgets.cancel_subscription') }}
                </button>
            </div>

            <div x-show="showCancelModal"
                 x-transition.opacity
                 class="fixed inset-0 z-[100] flex items-center justify-center bg-black/55 backdrop-blur-[2px] px-4"
                 style="display: none; background: #0000004a; z-index: 100000; margin: 0px;">
                <div class="w-full max-w-md rounded-xl border border-gray-300 bg-white p-6 shadow-[0_20px_50px_rgba(0,0,0,0.35)] dark:border-gray-600 dark:bg-gray-900">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('admin.widgets.cancel_subscription') }}</h4>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        {{ __('admin.widgets.cancel_subscription_text') }}
                    </p>

                    <div class="mt-5 flex justify-end gap-3">
                        <button type="button"
                                @click="showCancelModal = false"
                                class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                            {{ __('admin.widgets.keep_subscription') }}
                        </button>
                        <button type="button"
                                @click="$wire.cancelSubscription(); showCancelModal = false"
                                class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm"
                                style="background:#dc2626;color:#ffffff;border:1px solid #b91c1c;">
                            {{ __('admin.widgets.yes_cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

