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
        :root {
            --ftw-purple: #6D2077;
            --ftw-magenta: #D00070;
            --ftw-cyan: #009CDD;
        }
        body {
            font-family: system-ui, sans-serif;
            display: grid;
            place-items: center;
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(700px 400px at 80% 10%, rgba(208, 0, 112, 0.18), transparent 55%),
                linear-gradient(160deg, #6D2077 0%, #4a1654 100%);
            color: #fff;
        }
        .box { text-align: center; padding: 2rem; }
        .box img {
            height: 44px;
            width: auto;
            margin: 0 auto 1.25rem;
            display: block;
        }
        p { opacity: .9; }
        .bar {
            width: 160px;
            height: 3px;
            margin: 1.25rem auto 0;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--ftw-magenta), var(--ftw-cyan));
        }
    </style>
</head>
<body>
    <div class="box">
        @php
            $darkLogo = \JohnRivera7\FilamentTransbankWebpay\Support\BrandAssets::webpayPlusOnDarkDataUri();
        @endphp
        @if ($darkLogo !== '')
            <img src="{{ $darkLogo }}" alt="Webpay Plus" class="h-11 w-auto">
        @else
            @include('filament-transbank-webpay::partials.webpay-logo', ['class' => 'h-11 w-auto'])
        @endif
        <p>{{ __('filament-transbank-webpay::plugin.redirecting') }}</p>
        <div class="bar"></div>
    </div>
    <form id="webpay-redirect" method="post" action="{{ $url }}">
        @foreach ($fields as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach
    </form>
    <script>document.getElementById('webpay-redirect').submit();</script>
</body>
</html>
