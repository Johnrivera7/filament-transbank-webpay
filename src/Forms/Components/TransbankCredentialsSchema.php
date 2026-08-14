<?php

namespace JohnRivera7\FilamentTransbankWebpay\Forms\Components;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

final class TransbankCredentialsSchema
{
    /**
     * Reusable Filament schema for Transbank Webpay credentials.
     * Embed in any page/resource: ...TransbankCredentialsSchema::make()
     *
     * @return array<int, Section>
     */
    public static function make(string $statePathPrefix = ''): array
    {
        $p = $statePathPrefix !== '' ? rtrim($statePathPrefix, '.').'.' : '';

        return [
            Section::make('Transbank Webpay Plus')
                ->description('Credenciales del SDK oficial. Integración = pruebas; Producción = dinero real.')
                ->icon(Heroicon::OutlinedCreditCard)
                ->schema([
                    Toggle::make($p.'enabled')
                        ->label('Habilitado')
                        ->default(true)
                        ->columnSpanFull(),
                    TextInput::make($p.'commerce_code')
                        ->label('Código de comercio')
                        ->required()
                        ->maxLength(20)
                        ->helperText('Producción: el código real (ej. 5970…). Integración: 597055555532.'),
                    TextInput::make($p.'api_key')
                        ->label('API Key Secret')
                        ->password()
                        ->revealable()
                        ->required()
                        ->maxLength(255)
                        ->helperText('En integración usa la API Key pública de Transbank. En producción, la secret que entregan tras validar.'),
                    Select::make($p.'environment')
                        ->label('Ambiente')
                        ->options([
                            'integration' => 'Integración (pruebas)',
                            'production' => 'Producción',
                        ])
                        ->required()
                        ->native(false)
                        ->default('integration')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }
}
