<?php

namespace JohnRivera7\FilamentTransbankWebpay\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use JohnRivera7\FilamentTransbankWebpay\FilamentTransbankWebpayPlugin;
use JohnRivera7\FilamentTransbankWebpay\Support\TransbankCredentials;
use UnitEnum;

class ManageTransbankWebpay extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected string $view = 'filament-transbank-webpay::filament.pages.manage-transbank-webpay';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return FilamentTransbankWebpayPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentTransbankWebpayPlugin::get()->getNavigationLabel();
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return FilamentTransbankWebpayPlugin::get()->getNavigationIcon();
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentTransbankWebpayPlugin::get()->getNavigationSort();
    }

    public function getTitle(): string
    {
        return __('filament-transbank-webpay::plugin.page_title');
    }

    public function mount(): void
    {
        $credentials = FilamentTransbankWebpayPlugin::get()->resolveCredentials();

        $this->form->fill($credentials->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament-transbank-webpay::plugin.section_credentials'))
                    ->description(__('filament-transbank-webpay::plugin.section_credentials_help'))
                    ->icon(Heroicon::OutlinedCreditCard)
                    ->schema([
                        Toggle::make('enabled')
                            ->label(__('filament-transbank-webpay::plugin.enabled'))
                            ->columnSpanFull(),
                        TextInput::make('commerce_code')
                            ->label(__('filament-transbank-webpay::plugin.commerce_code'))
                            ->required()
                            ->maxLength(20)
                            ->helperText(__('filament-transbank-webpay::plugin.commerce_code_help')),
                        TextInput::make('api_key')
                            ->label(__('filament-transbank-webpay::plugin.api_key'))
                            ->password()
                            ->revealable()
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('filament-transbank-webpay::plugin.api_key_help')),
                        Select::make('environment')
                            ->label(__('filament-transbank-webpay::plugin.environment'))
                            ->options([
                                'integration' => __('filament-transbank-webpay::plugin.env_integration'),
                                'production' => __('filament-transbank-webpay::plugin.env_production'),
                            ])
                            ->required()
                            ->native(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-transbank-webpay::plugin.save'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $credentials = TransbankCredentials::fromArray($state);

        FilamentTransbankWebpayPlugin::get()->persistCredentials($credentials);

        Notification::make()
            ->title(__('filament-transbank-webpay::plugin.saved'))
            ->success()
            ->send();
    }
}
