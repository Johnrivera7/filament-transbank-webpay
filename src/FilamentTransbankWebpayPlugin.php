<?php

namespace JohnRivera7\FilamentTransbankWebpay;

use Filament\Contracts\Plugin;
use Filament\Panel;
use JohnRivera7\FilamentTransbankWebpay\Filament\Pages\ManageTransbankWebpay;
use JohnRivera7\FilamentTransbankWebpay\Services\WebpayPlusGateway;
use JohnRivera7\FilamentTransbankWebpay\Support\TransbankCredentials;

class FilamentTransbankWebpayPlugin implements Plugin
{
    protected bool $hasSettingsPage = true;

    /** @var (callable(): TransbankCredentials)|null */
    protected $credentialsResolver = null;

    /** @var (callable(TransbankCredentials): mixed)|null */
    protected $credentialsPersister = null;

    protected ?string $navigationGroup = null;

    protected ?string $navigationLabel = null;

    protected ?string $navigationIcon = null;

    protected ?int $navigationSort = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'filament-transbank-webpay';
    }

    public function register(Panel $panel): void
    {
        if ($this->hasSettingsPage()) {
            $panel->pages([
                ManageTransbankWebpay::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function settingsPage(bool $condition = true): static
    {
        $this->hasSettingsPage = $condition;

        return $this;
    }

    public function hasSettingsPage(): bool
    {
        return $this->hasSettingsPage && (bool) config('filament-transbank-webpay.register_settings_page', true);
    }

    /**
     * @param  callable(): TransbankCredentials  $callback
     */
    public function credentialsUsing(callable $callback): static
    {
        $this->credentialsResolver = $callback;

        return $this;
    }

    /**
     * @param  callable(TransbankCredentials): mixed  $callback
     */
    public function persistCredentialsUsing(callable $callback): static
    {
        $this->credentialsPersister = $callback;

        return $this;
    }

    public function resolveCredentials(): TransbankCredentials
    {
        if ($this->credentialsResolver !== null) {
            return ($this->credentialsResolver)();
        }

        return TransbankCredentials::fromConfig();
    }

    public function persistCredentials(TransbankCredentials $credentials): void
    {
        if ($this->credentialsPersister !== null) {
            ($this->credentialsPersister)($credentials);

            return;
        }

        // Single-tenant fallback: no DB layer — host app should provide persistCredentialsUsing().
    }

    public function gateway(?TransbankCredentials $credentials = null): WebpayPlusGateway
    {
        return WebpayPlusGateway::make($credentials ?? $this->resolveCredentials());
    }

    public function navigationGroup(?string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup ?? config('filament-transbank-webpay.navigation.group');
    }

    public function navigationLabel(?string $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function getNavigationLabel(): string
    {
        return $this->navigationLabel
            ?? (string) config('filament-transbank-webpay.navigation.label', 'Transbank Webpay');
    }

    public function navigationIcon(?string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function getNavigationIcon(): string
    {
        return $this->navigationIcon
            ?? (string) config('filament-transbank-webpay.navigation.icon', 'heroicon-o-credit-card');
    }

    public function navigationSort(?int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function getNavigationSort(): int
    {
        return $this->navigationSort
            ?? (int) config('filament-transbank-webpay.navigation.sort', 40);
    }
}
