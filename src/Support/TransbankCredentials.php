<?php

namespace JohnRivera7\FilamentTransbankWebpay\Support;

final class TransbankCredentials
{
    public function __construct(
        public string $commerceCode,
        public string $apiKey,
        public string $environment = 'integration',
        public bool $enabled = true,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            commerceCode: (string) config('filament-transbank-webpay.commerce_code', ''),
            apiKey: (string) config('filament-transbank-webpay.api_key', ''),
            environment: strtolower((string) config('filament-transbank-webpay.environment', 'integration')),
            enabled: (bool) config('filament-transbank-webpay.enabled', true),
        );
    }

    /**
     * @param  array{commerce_code?: string, api_key?: string, environment?: string, enabled?: bool}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            commerceCode: (string) ($data['commerce_code'] ?? ''),
            apiKey: (string) ($data['api_key'] ?? ''),
            environment: strtolower((string) ($data['environment'] ?? 'integration')),
            enabled: (bool) ($data['enabled'] ?? true),
        );
    }

    /**
     * @return array{commerce_code: string, api_key: string, environment: string, enabled: bool}
     */
    public function toArray(): array
    {
        return [
            'commerce_code' => $this->commerceCode,
            'api_key' => $this->apiKey,
            'environment' => $this->isProduction() ? 'production' : 'integration',
            'enabled' => $this->enabled,
        ];
    }

    public function isProduction(): bool
    {
        return $this->environment === 'production';
    }

    public function isComplete(): bool
    {
        return $this->commerceCode !== '' && $this->apiKey !== '';
    }
}
