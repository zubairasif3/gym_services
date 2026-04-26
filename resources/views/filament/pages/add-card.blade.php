<x-filament-panels::page>
    <div class="space-y-6">
        <div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <a href="{{ url('/admin') }}" class="hover:text-primary-600">{{ __('admin.pages.dashboard') }}</a>
                <span class="mx-2">›</span>
                <span>{{ __('admin.pages.card') }}</span>
            </div>
            <h2 class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('admin.pages.add_update_card') }}</h2>
        </div>

        <livewire:seller.stripe-card-setup />
    </div>
</x-filament-panels::page>
