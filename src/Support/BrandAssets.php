<?php

namespace JohnRivera7\FilamentTransbankWebpay\Support;

final class BrandAssets
{
    public static function webpayPlusDataUri(): string
    {
        return self::dataUri('webpay-plus.png', 'image/png');
    }

    public static function webpayPlusOnDarkDataUri(): string
    {
        return self::dataUri('webpay-plus-on-dark.png', 'image/png');
    }

    public static function webpayDataUri(): string
    {
        $png = self::path('webpay.png');

        if (is_file($png)) {
            return self::dataUri('webpay.png', 'image/png');
        }

        return self::webpayPlusDataUri();
    }

    protected static function dataUri(string $filename, string $mime): string
    {
        $path = self::path($filename);

        if (! is_file($path)) {
            return '';
        }

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));
    }

    protected static function path(string $filename): string
    {
        return dirname(__DIR__, 2).'/resources/images/'.$filename;
    }
}
