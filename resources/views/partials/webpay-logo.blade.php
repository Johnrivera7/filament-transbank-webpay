@php
    use JohnRivera7\FilamentTransbankWebpay\Support\BrandAssets;
    $src = BrandAssets::webpayPlusDataUri();
@endphp
@if ($src !== '')
    <img
        src="{{ $src }}"
        alt="Webpay Plus"
        class="{{ $class ?? 'h-10 w-auto' }}"
    />
@endif
