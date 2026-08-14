<?php

namespace JohnRivera7\FilamentTransbankWebpay;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTransbankWebpayServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-transbank-webpay';

    public static string $viewNamespace = 'filament-transbank-webpay';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations();
    }

    public function packageRegistered(): void
    {
        $this->publishes([
            __DIR__.'/../resources/images' => public_path('vendor/filament-transbank-webpay/images'),
        ], 'filament-transbank-webpay-assets');
    }

    public function packageBooted(): void
    {
        FilamentAsset::register(
            assets: [
                Css::make('filament-transbank-webpay', __DIR__.'/../resources/dist/filament-transbank-webpay.css')
                    ->loadedOnRequest(),
            ],
            package: 'johnrivera7/filament-transbank-webpay',
        );
    }
}
