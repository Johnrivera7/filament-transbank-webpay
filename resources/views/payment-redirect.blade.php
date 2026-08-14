{{--
  Auto-submit POST redirect to Webpay (required: token_ws via POST).
  Usage:
    return response()->view('filament-transbank-webpay::payment-redirect', [
        'url' => $payment['redirect_url'],
        'fields' => $payment['redirect_fields'],
    ]);
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('filament-transbank-webpay::plugin.redirecting') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; display: grid; place-items: center; min-height: 100vh; margin: 0; background: #0b1f33; color: #fff; }
        .box { text-align: center; padding: 2rem; }
        p { opacity: .85; }
    </style>
</head>
<body>
    <div class="box">
        @include('filament-transbank-webpay::partials.webpay-logo', ['class' => 'h-10 w-auto mx-auto mb-4'])
        <p>{{ __('filament-transbank-webpay::plugin.redirecting') }}</p>
    </div>
    <form id="webpay-redirect" method="post" action="{{ $url }}">
        @foreach ($fields as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach
    </form>
    <script>document.getElementById('webpay-redirect').submit();</script>
</body>
</html>
