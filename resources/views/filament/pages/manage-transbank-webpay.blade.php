<x-filament-panels::page>
    <div class="ftw-page space-y-6">
        <div class="ftw-brand flex flex-wrap items-center gap-4">
            @include('filament-transbank-webpay::partials.webpay-logo', ['class' => 'h-10 w-auto'])
            @include('filament-transbank-webpay::partials.transbank-mark', ['class' => 'h-8 w-auto opacity-80'])
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('filament-transbank-webpay::plugin.brand_hint') }}
            </p>
        </div>

        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-6">
                <x-filament::button type="submit">
                    {{ __('filament-transbank-webpay::plugin.save') }}
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
