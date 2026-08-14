@php
    use JohnRivera7\FilamentTransbankWebpay\Support\BrandAssets;
    $src = BrandAssets::webpayDataUri();
@endphp
@if ($src !== '')
    <img
        src="{{ $src }}"
        alt="Webpay"
        class="{{ $class ?? 'h-8 w-auto opacity-90' }}"
    />
@endif
