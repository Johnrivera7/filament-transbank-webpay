# Transbank Webpay

<img src="https://raw.githubusercontent.com/Johnrivera7/filament-transbank-webpay/main/art/banner.jpg" alt="Transbank Webpay Filament plugin" class="filament-hidden" />

A Filament v5 plugin that integrates **Transbank Webpay Plus** (Chile) into your Laravel application: credentials UI, logos, POST `token_ws` redirects, and create / commit / status / refund helpers on the [official Transbank SDK](https://github.com/TransbankDevelopers/transbank-sdk-php).

## Requirements

- PHP 8.2+
- Laravel 11+ / 12+
- Filament 5.x

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
./vendor/bin/phpunit
```

## Security

See [SECURITY.md](SECURITY.md).

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

MIT
