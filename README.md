# Filament Transbank Webpay

Plugin de [Filament](https://filamentphp.com) **v5** para **Transbank Webpay Plus** (Chile): UI de credenciales, logos, redirect POST con `token_ws`, y helpers `create` / `commit` / `status` / `refund` sobre el [SDK oficial](https://github.com/TransbankDevelopers/transbank-sdk-php).

Repositorio: https://github.com/Johnrivera7/filament-transbank-webpay

## Requisitos

- PHP 8.2+
- Laravel 11 / 12
- Filament 5
- Cuenta / comercio Transbank Webpay Plus

## Instalación

```bash
composer require johnrivera7/filament-transbank-webpay
```

Publica config (opcional) y assets de imagen (opcional; las vistas ya incluyen SVG inline):

```bash
php artisan vendor:publish --tag=filament-transbank-webpay-config
php artisan vendor:publish --tag=filament-transbank-webpay-assets
```

Registra el plugin en tu `PanelProvider`:

```php
use JohnRivera7\FilamentTransbankWebpay\FilamentTransbankWebpayPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(
            FilamentTransbankWebpayPlugin::make()
                ->navigationGroup('Pagos')
                ->navigationSort(40)
        );
}
```

### Multi-tenant (recomendado)

Resuelve y persiste credenciales por tenant (ej. `Empresa` / `IntegrationSetting`):

```php
->plugin(
    FilamentTransbankWebpayPlugin::make()
        ->credentialsUsing(function (): \JohnRivera7\FilamentTransbankWebpay\Support\TransbankCredentials {
            $cfg = /* leer de tu DB */;
            return \JohnRivera7\FilamentTransbankWebpay\Support\TransbankCredentials::fromArray($cfg);
        })
        ->persistCredentialsUsing(function (\JohnRivera7\FilamentTransbankWebpay\Support\TransbankCredentials $c): void {
            /* guardar $c->toArray() en tu DB */
        })
)
```

### Single-tenant (.env)

```env
TRANSBANK_ENABLED=true
TRANSBANK_COMMERCE_CODE=597055555532
TRANSBANK_API_KEY=...
TRANSBANK_ENVIRONMENT=integration
```

Sin `persistCredentialsUsing`, la página de settings **no** escribe en disco: úsala solo con callbacks o guarda vía tu propio flujo.

## Uso del gateway

```php
use JohnRivera7\FilamentTransbankWebpay\FilamentTransbankWebpayPlugin;
use JohnRivera7\FilamentTransbankWebpay\Services\WebpayPlusGateway;

$gateway = FilamentTransbankWebpayPlugin::get()->gateway();
// o: WebpayPlusGateway::make(TransbankCredentials::fromConfig());

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

En el return URL:

```php
if (WebpayPlusGateway::isAbortReturn($request->all())) {
    // TBK_* sin token_ws → abort | timeout
    $reason = WebpayPlusGateway::abortReason($request->all()); // aborted|timeout
}

// Si hay token_ws (aunque también lleguen TBK_*), haz commit:
$result = $gateway->commit($request->all());
```

## Schema reutilizable

Puedes embeber el formulario de credenciales en otra página Filament:

```php
use JohnRivera7\FilamentTransbankWebpay\Forms\Components\TransbankCredentialsSchema;

$schema->components([
    ...TransbankCredentialsSchema::make('payments.transbank'),
]);
```

## Notas de integración

- Webpay Plus exige **POST** con `token_ws` (no redirect GET).
- Integración de prueba: commerce `597055555532` + API Key pública de Transbank.
- Producción: commerce real + API Key Secret tras validación comercial.
- En abort/timeout Webpay envía `TBK_*` **sin** `token_ws`. Si llegan juntos, **gana `token_ws`** y debes hacer `commit`.

## Licencia

MIT
