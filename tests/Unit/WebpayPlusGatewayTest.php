<?php

namespace JohnRivera7\FilamentTransbankWebpay\Tests\Unit;

use JohnRivera7\FilamentTransbankWebpay\Services\WebpayPlusGateway;
use JohnRivera7\FilamentTransbankWebpay\Support\TransbankCredentials;
use PHPUnit\Framework\TestCase;

class WebpayPlusGatewayTest extends TestCase
{
    public function test_builds_credentials_from_array(): void
    {
        $c = TransbankCredentials::fromArray([
            'commerce_code' => '597055555532',
            'api_key' => 'secret',
            'environment' => 'production',
            'enabled' => true,
        ]);

        $this->assertTrue($c->isProduction());
        $this->assertTrue($c->isComplete());
        $this->assertSame('597055555532', $c->toArray()['commerce_code']);
    }

    public function test_detects_abort_returns_without_token_ws(): void
    {
        $this->assertTrue(WebpayPlusGateway::isAbortReturn([
            'TBK_TOKEN' => 'abc',
            'TBK_ORDEN_COMPRA' => 'ORD1',
        ]));

        $this->assertSame('aborted', WebpayPlusGateway::abortReason([
            'TBK_TOKEN' => 'abc',
        ]));

        $this->assertSame('timeout', WebpayPlusGateway::abortReason([
            'TBK_ORDEN_COMPRA' => 'ORD1',
        ]));
    }

    public function test_prefers_token_ws_over_tbk_abort_markers(): void
    {
        $this->assertFalse(WebpayPlusGateway::isAbortReturn([
            'token_ws' => 'tok',
            'TBK_TOKEN' => 'abc',
        ]));
    }
}
