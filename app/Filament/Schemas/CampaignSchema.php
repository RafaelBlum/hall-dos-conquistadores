<?php

namespace App\Filament\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;

class CampaignSchema
{
    public static function fields(): array
    {
        return [

            // ═══════════════════════════════════════════
            // GRID PRINCIPAL: 3 (imagem) + 9 (campos)
            // Segue a mesma proporção do UserForm
            // ═══════════════════════════════════════════
            Grid::make(12)->schema([

                // ──────────────────────────────────────
                // COLUNA ESQUERDA — Imagem da campanha
                // ──────────────────────────────────────
                Group::make()->schema([

                    FileUpload::make('image')
                        ->label('Imagem da campanha')
                        ->disk('public')
                        ->helperText('Imagem informativa da campanha. Proporção recomendada: 16:9.')
                        ->directory('campaing_folder')
                        ->image()
                        ->imagePreviewHeight('220')
                        ->loadingIndicatorPosition('left')
                        ->panelLayout('integrated')
                        ->removeUploadedFileButtonPosition('right')
                        ->uploadButtonPosition('left')
                        ->uploadProgressIndicatorPosition('left')
                        ->openable()
                        ->uploadingMessage('Enviando imagem...')
                        ->columnSpanFull(),

                ])->columnSpan(3),

                // ──────────────────────────────────────
                // COLUNA DIREITA — Campos da campanha
                // ──────────────────────────────────────
                Group::make()->schema([

                    // Título + Toggle na mesma linha
                    Grid::make(3)->schema([

                        TextInput::make('title')
                            ->label('Título da campanha')
                            ->minLength(2)
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->required()
                            ->columnSpan(2),

                        Toggle::make('camping')
                            ->label(fn(Get $get) => $get('camping') ? 'Ativada' : 'Desativada')
                            ->live()
                            ->columnSpan(1),

                    ]),

                    // Links lado a lado
                    Grid::make(2)->schema([

                        TextInput::make('goal_link')
                            ->label('Link campanha status')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Link da campanha do LivePix')
                            ->hintColor(Color::Yellow)
                            ->prefixIcon('heroicon-m-currency-dollar')
                            ->suffixIcon('heroicon-m-chart-bar')
                            ->url()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state) {
                                if (!empty($state) && !filter_var($state, FILTER_VALIDATE_URL)) {
                                    Notification::make()
                                        ->title('URL inválida')
                                        ->body('O link inserido não é uma URL válida.')
                                        ->danger()
                                        ->send();
                                }
                            })
                            ->required()
                            ->columnSpan(1),

                        /* TextInput::make('qrCode')
                            ->label('Link QR Code LivePix')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Link do QR Code do LivePix')
                            ->hintColor(Color::Yellow)
                            ->prefixIcon('heroicon-m-qr-code')
                            ->suffixIcon('heroicon-m-viewfinder-circle')
                            ->url()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state) {
                                if (!empty($state) && !filter_var($state, FILTER_VALIDATE_URL)) {
                                    Notification::make()
                                        ->title('URL inválida')
                                        ->body('O link inserido não é uma URL válida.')
                                        ->danger()
                                        ->send();
                                }
                            })
                            ->required(fn(Get $get) => self::isCampaignStarted($get))
                            ->columnSpan(1), */

                    ]),

                    // Descrição em linha única abaixo
                    /* Textarea::make('content')
                        ->label('Descrição da campanha')
                        ->placeholder('Descreva o objetivo da campanha, como os recursos serão usados, etc.')
                        ->rows(6)
                        ->maxLength(1000)
                        ->live(onBlur: true)
                        ->helperText(function ($get) {
                            $length = strlen((string) $get('content'));
                            return "Limite: {$length}/1000 caracteres.";
                        })
                        ->required()
                        ->columnSpanFull(), */

                ])->columnSpan(9),

            ]),
        ];
    }

    public static function isCampaignStarted(Get $get): bool
    {
        return filled($get('title'))
            || filled($get('content'))
            || filled($get('qr_code'))
            || filled($get('goal_link'))
            || filled($get('pix_page_link'));
    }

    public static function sanitize(array $campaignData): ?array
    {
        $hasData = filled($campaignData['title'] ?? null)
            || filled($campaignData['content'] ?? null)
            || filled($campaignData['qr_code'] ?? null)
            || filled($campaignData['goal_link'] ?? null)
            || filled($campaignData['pix_page_link'] ?? null);

        return $hasData ? $campaignData : null;
    }
}