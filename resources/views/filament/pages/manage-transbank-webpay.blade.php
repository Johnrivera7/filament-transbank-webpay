<x-filament-panels::page>
    <div class="ftw-page space-y-6">
        <div class="ftw-brand flex flex-wrap items-center gap-5">
            @include('filament-transbank-webpay::partials.webpay-logo', ['class' => 'h-10 w-auto'])
            <p class="text-sm" style="color:#75787B">
                {{ __('filament-transbank-webpay::plugin.brand_hint') }}
            </p>
        </div>

        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-6">
                <x-filament::button type="submit" color="primary">
                    {{ __('filament-transbank-webpay::plugin.save') }}
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
