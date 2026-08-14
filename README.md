# Transbank Webpay

<img src="https://raw.githubusercontent.com/Johnrivera7/filament-transbank-webpay/main/art/banner.jpg" alt="Transbank Webpay Filament plugin" class="filament-hidden" />

<p align="center">
    <a href="https://filamentphp.com/docs/4.x/panels/installation">
        <img alt="FILAMENT 4.x" src="https://img.shields.io/badge/FILAMENT-4.x-EBB304?style=for-the-badge">
    </a>
    <a href="https://filamentphp.com/docs/5.x/panels/installation">
        <img alt="FILAMENT 5.x" src="https://img.shields.io/badge/FILAMENT-5.x-EBB304?style=for-the-badge">
    </a>
</p>

<p align="center">
    <a href="https://packagist.org/packages/johnrivera7/filament-transbank-webpay">
        <img alt="Latest Version on Packagist" src="https://img.shields.io/packagist/v/johnrivera7/filament-transbank-webpay.svg?style=flat-square&label=packagist">
    </a>
    <a href="https://packagist.org/packages/johnrivera7/filament-transbank-webpay">
        <img alt="Total Downloads" src="https://img.shields.io/packagist/dt/johnrivera7/filament-transbank-webpay.svg?style=flat-square">
    </a>
    <a href="https://github.com/Johnrivera7/filament-transbank-webpay/blob/main/LICENSE">
        <img alt="License" src="https://img.shields.io/packagist/l/johnrivera7/filament-transbank-webpay.svg?style=flat-square">
    </a>
    <a href="https://github.com/Johnrivera7/filament-transbank-webpay">
        <img alt="GitHub Stars" src="https://img.shields.io/github/stars/Johnrivera7/filament-transbank-webpay?style=flat-square">
    </a>
    <a href="https://github.com/Johnrivera7/filament-transbank-webpay/issues">
        <img alt="GitHub Issues" src="https://img.shields.io/github/issues/Johnrivera7/filament-transbank-webpay?style=flat-square">
    </a>
    <a href="https://github.com/Johnrivera7/filament-transbank-webpay/releases">
        <img alt="Latest release" src="https://img.shields.io/github/v/release/Johnrivera7/filament-transbank-webpay?style=flat-square&label=release">
    </a>
    <a href="https://github.com/Johnrivera7/filament-transbank-webpay/tree/main/tests">
        <img alt="PHPUnit tests" src="https://img.shields.io/badge/tests-PHPUnit-6D2077?style=flat-square" class="filament-hidden">
    </a>
</p>

<p align="center">
    <img alt="PHP 8.2+" src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white">
    <img alt="Laravel 11+" src="https://img.shields.io/badge/Laravel-11%2B%20%2F%2012%2B-FF2D20?style=flat-square&logo=laravel&logoColor=white">
    <img alt="Livewire 3/4" src="https://img.shields.io/badge/Livewire-3.x%20%2F%204.x-FB70A9?style=flat-square">
    <img alt="Filament 4/5" src="https://img.shields.io/badge/Filament-4.x%20%2F%205.x-FDAE4B?style=flat-square">
    <img alt="Built with Transbank SDK" src="https://img.shields.io/badge/Built%20with-Transbank%20SDK-6D2077?style=flat-square">
    <img alt="MIT" src="https://img.shields.io/badge/License-MIT-green?style=flat-square">
</p>

A Filament plugin that integrates **Transbank Webpay Plus** (Chile) into your Laravel application: credentials UI, logos, POST `token_ws` redirects, and create / commit / status / refund helpers on the [official Transbank SDK](https://github.com/TransbankDevelopers/transbank-sdk-php).

> **Open source (MIT).** Badges like *Buy license* / *Private Composer package* / *Proprietary documentation* apply to paid Filament plugins only — this package is free on [Packagist](https://packagist.org/packages/johnrivera7/filament-transbank-webpay).

## Requirements

| Stack | Versions |
| --- | --- |
| PHP | 8.2+ |
| Laravel | 11.28+ / 12+ |
| Filament | **4.x** or **5.x** |
| Livewire | 3.x (with Filament 4) / 4.x (with Filament 5) |

## Installation

```bash
composer require johnrivera7/filament-transbank-webpay
```

Optional publishes:

```bash
php artisan vendor:publish --tag=filament-transbank-webpay-config
php artisan vendor:publish --tag=filament-transbank-webpay-assets
```

Register the plugin in your `PanelProvider`:

```php
use JohnRivera7\FilamentTransbankWebpay\FilamentTransbankWebpayPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(
            FilamentTransbankWebpayPlugin::make()
                ->navigationGroup('Pagos')
                ->navigationSort(40)
        );
}
```

### Multi-tenant (recommended)

```php
use JohnRivera7\FilamentTransbankWebpay\Support\TransbankCredentials;

->plugin(
    FilamentTransbankWebpayPlugin::make()
        ->credentialsUsing(function (): TransbankCredentials {
            $cfg = /* read from your DB */;

            return TransbankCredentials::fromArray($cfg);
        })
        ->persistCredentialsUsing(function (TransbankCredentials $credentials): void {
            /* persist $credentials->toArray() */
        })
)
```

### Single-tenant (`.env`)

```env
TRANSBANK_ENABLED=true
TRANSBANK_COMMERCE_CODE=597055555532
TRANSBANK_API_KEY=...
TRANSBANK_ENVIRONMENT=integration
```

Without `persistCredentialsUsing()`, the settings page does not write credentials to disk—wire persistence via the callbacks above (or manage `.env` yourself).

## Gateway usage

```php
use JohnRivera7\FilamentTransbankWebpay\FilamentTransbankWebpayPlugin;
use JohnRivera7\FilamentTransbankWebpay\Services\WebpayPlusGateway;

$gateway = FilamentTransbankWebpayPlugin::get()->gateway();

$payment = $gateway->create(
    buyOrder: 'ORD123',
    sessionId: 'sess-1',
    amountClp: 15990,
    returnUrl: route('payments.transbank.return'),
);

return response()->view('filament-transbank-webpay::payment-redirect', [
    'url' => $payment['redirect_url'],
    'fields' => $payment['redirect_fields'], // ['token_ws' => ...]
]);
```

On the return URL:

```php
if (WebpayPlusGateway::isAbortReturn($request->all())) {
    $reason = WebpayPlusGateway::abortReason($request->all()); // aborted|timeout
}

// If token_ws is present (even alongside TBK_*), call commit:
$result = $gateway->commit($request->all());
```

## Reusable form schema

```php
use JohnRivera7\FilamentTransbankWebpay\Forms\Components\TransbankCredentialsSchema;

$schema->components([
    ...TransbankCredentialsSchema::make('payments.transbank'),
]);
```

## Integration notes

- Webpay Plus requires a **POST** redirect with `token_ws` (not a GET query string).
- Integration sandbox: commerce `597055555532` + Transbank’s public integration API key.
- Production: real commerce code + API Key Secret after Transbank validation.
- Abort/timeout returns send `TBK_*` **without** `token_ws`. If both arrive, **`token_ws` wins**—run `commit`.

## Testing

```bash
composer install
composer test
```

## Security

See [SECURITY.md](SECURITY.md).

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

MIT
