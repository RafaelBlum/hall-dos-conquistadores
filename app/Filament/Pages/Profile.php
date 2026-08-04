<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use App\Enums\PanelTypeEnum;
use App\Models\Campaign;
use App\Models\User;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Meu Perfil';

    protected string $view = 'filament.pages.profile';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            auth()->user()->load('channel.campaign')->attributesToArray()
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ═══════════════════════════════════════════
                // SEÇÃO 1 — Dados Pessoais
                // ═══════════════════════════════════════════
                Section::make('Dados Pessoais')
                    ->description('Informações da sua conta')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(12)->schema([

                            // Avatar
                            Group::make()->schema([
                                FileUpload::make('avatar')
                                    ->label('Foto de perfil')
                                    ->default('default.jpg')
                                    ->disk('public')
                                    ->directory('thumbnails')
                                    ->removeUploadedFileButtonPosition('right')
                                    ->openable()
                                    ->avatar()
                                    ->columnSpanFull(),
                            ])->columnSpan(2),

                            // Campos
                            Group::make()->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->label('Nome completo')
                                        ->required()
                                        ->maxLength(150)
                                        ->minLength(2)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state) {
                                            if (strlen($state) < 2 || strlen($state) > 150) {
                                                $this->validateCaracteres(2, 150);
                                            }
                                        }),

                                    TextInput::make('email')
                                        ->label('E-mail')
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state) {
                                            if (!filter_var($state, FILTER_VALIDATE_EMAIL) && !empty($state)) {
                                                Notification::make()
                                                    ->title('E-mail inválido')
                                                    ->body('O e-mail inserido não é válido.')
                                                    ->danger()
                                                    ->send();
                                            }
                                            if (!$this->validateEmailDatabase($state)) {
                                                Notification::make()
                                                    ->title('E-mail em uso')
                                                    ->body('O e-mail inserido já está em uso.')
                                                    ->danger()
                                                    ->send();
                                            }
                                        }),

                                    Select::make('panel')
                                        ->label('Tipo de usuário')
                                        ->options(PanelTypeEnum::class)
                                        ->native(false)
                                        ->rules([
                                            fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                                if (empty($value)) {
                                                    $fail('Precisa definir o seu tipo de usuário.');
                                                }
                                            },
                                        ])
                                        ->required(),

                                    TextInput::make('password')
                                        ->label('Nova senha')
                                        ->password()
                                        ->revealable()
                                        ->dehydrated(fn(?string $state): bool => filled($state))
                                        ->required(fn(string $operation): bool => $operation === 'create')
                                        ->minLength(8)
                                        ->maxLength(32)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state) {
                                            if (!empty($state) && !preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $state)) {
                                                Notification::make()
                                                    ->title('Senha inválida')
                                                    ->body('Mínimo 8 caracteres, uma maiúscula, um número e um caractere especial.')
                                                    ->danger()
                                                    ->send();
                                            }
                                        }),
                                ]),
                            ])->columnSpan(10),

                        ]),
                    ]),

                // ═══════════════════════════════════════════
                // SEÇÃO 2 — Canal
                // ═══════════════════════════════════════════
                Section::make('Meu Canal')
                    ->description('Informações do seu canal do YouTube')
                    ->icon('heroicon-o-tv')
                    ->relationship('channel')
                    ->schema([
                        Grid::make(12)->schema([

                            // Logo do canal
                            Group::make()->schema([
                                FileUpload::make('brand')
                                    ->label('Logo do canal')
                                    ->disk('public')
                                    ->helperText('Logo do seu canal')
                                    ->directory('channel_brand')
                                    ->removeUploadedFileButtonPosition('right')
                                    ->openable()
                                    ->avatar()
                                    ->columnSpanFull(),
                            ])->columnSpan(2),

                            // Campos do canal
                            Group::make()->schema([
                                Grid::make(2)->schema([

                                    TextInput::make('title')
                                        ->label('Nome do canal')
                                        ->hintIcon('heroicon-m-check-badge', tooltip: 'Seu canal do Youtube.')
                                        ->hintColor(Color::Green)
                                        ->minLength(2)
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state) {
                                            if (strlen($state) < 2 || strlen($state) > 255) {
                                                $this->validateCaracteres(2, 255);
                                            }
                                        })
                                        ->required(),

                                    TextInput::make('name')
                                        ->label('Seu nome')
                                        ->minLength(2)
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state) {
                                            if (strlen($state) < 2 || strlen($state) > 255) {
                                                $this->validateCaracteres(2, 255);
                                            }
                                        })
                                        ->required(),

                                    TextInput::make('link')
                                        ->label('Link do YouTube')
                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Nome do canal na URL sem "@"')
                                        ->hintColor(Color::Yellow)
                                        ->prefix('https://www.youtube.com/@')
                                        ->suffixIcon('heroicon-m-globe-alt')
                                        ->minLength(2)
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state) {
                                            if (strlen($state) < 2 || strlen($state) > 255) {
                                                $this->validateCaracteres(2, 255);
                                            }
                                        })
                                        ->required(),

                                    ColorPicker::make('color')
                                        ->label('Cor do canal'),

                                    Textarea::make('description')
                                        ->label('Descrição do canal')
                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Descreva brevemente seu canal.')
                                        ->hintColor(Color::Yellow)
                                        ->columnSpanFull(),

                                ]),
                            ])->columnSpan(10),

                        ]),
                    ]),

                // ═══════════════════════════════════════════
                // SEÇÃO 3 — Campanha
                // ═══════════════════════════════════════════
                Section::make('Minha Campanha')
                    ->description('Configure sua campanha do LivePix')
                    ->icon('heroicon-o-megaphone')
                    ->relationship('channel')
                    ->schema([
                        Grid::make(1)
                            ->relationship('campaign')
                            ->schema([
                                Grid::make(12)->schema([

                                    // Imagem da campanha
                                    Group::make()->schema([
                                        FileUpload::make('image')
                                            ->label('Imagem da campanha')
                                            ->disk('public')
                                            ->helperText('Imagem informativa da campanha')
                                            ->directory('campaing_folder')
                                            ->image()
                                            ->imagePreviewHeight('250')
                                            ->loadingIndicatorPosition('left')
                                            ->panelAspectRatio('1:1')
                                            ->panelLayout('integrated')
                                            ->removeUploadedFileButtonPosition('right')
                                            ->uploadButtonPosition('left')
                                            ->uploadProgressIndicatorPosition('left')
                                            ->openable()
                                            ->uploadingMessage('Enviando imagem...')
                                            ->columnSpanFull(),
                                    ])->columnSpan(3),

                                    // Campos da campanha
                                    Group::make()->schema([
                                        Grid::make(2)->schema([

                                            TextInput::make('title')
                                                ->label('Título da campanha')
                                                ->minLength(2)
                                                ->maxLength(255)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function ($state) {
                                                    if (strlen($state) < 2 || strlen($state) > 255) {
                                                        $this->validateCaracteres(2, 255);
                                                    }
                                                })
                                                ->required(),

                                            Toggle::make('camping')
                                                ->label(function (Get $get) {
                                                    return $get('camping') ? 'Campanha ativada' : 'Campanha desativada';
                                                })
                                                ->live()
                                                ->columnSpan(1),

                                            TextInput::make('linkGoal')
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
                                                ->columnSpanFull(),

                                            TextInput::make('qrCode')
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
                                                ->required()
                                                ->columnSpanFull(),

                                            Textarea::make('content')
                                                ->label('Descrição da campanha')
                                                ->required()
                                                ->columnSpanFull(),

                                        ]),
                                    ])->columnSpan(5),

                                    // QR Code preview
                                    Group::make()->schema([
                                        Placeholder::make('qrCode_preview')
                                            ->label('Preview QR Code')
                                            ->content(function (Get $get) {
                                                $qrCode = $get('qrCode');

                                                if (empty($qrCode)) {
                                                    return 'Insira o link do QR Code para visualizar.';
                                                }

                                                $campaign = Campaign::find($get('id'));

                                                if (!$campaign) {
                                                    return 'Campanha não encontrada.';
                                                }

                                                return new HtmlString(
                                                    view('filament.campaing.iframe', ['state' => $campaign])->render()
                                                );
                                            }),
                                    ])->columnSpan(4)
                                        ->hidden(fn(Get $get) => empty($get('qrCode'))),

                                ]),
                            ]),
                    ]),

            ])
            ->statePath('data')
            ->model(auth()->user());
    }

    protected function validateCaracteres(int $min, int $max): void
    {
        Notification::make()
            ->title('Erro de validação')
            ->body("O campo deve ter entre {$min} e {$max} caracteres.")
            ->danger()
            ->send();
    }

    protected function validateEmailDatabase(string $email): bool
    {
        return !User::where('email', $email)
            ->where('id', '!=', auth()->id())
            ->exists();
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('salvar')
                ->label('Salvar modificações')
                ->color('primary')
                ->submit('update'),
        ];
    }

    public function update(): void
    {
        $user = auth()->user()->load('channel.campaign');

        $oldImageAvatar = $user->avatar;
        $oldImageChannel = $user->channel->brand;
        $oldImageCamping = $user->channel->campaign->image;

        $state = $this->form->getState();

        // Atualiza usuário
        $user->update($state);

        // Atualiza channel
        $user->channel->update($state['channel'] ?? []);
        $user->channel->slug = Str::slug(($state['channel']['title'] ?? '') . '-' . $user->id);
        $user->channel->save();

        // Atualiza campaign
        $user->channel->campaign->update($state['channel']['campaign'] ?? []);

        // Remove imagens antigas se foram trocadas
        if ($user->avatar !== $oldImageAvatar && $oldImageAvatar !== 'default.jpg') {
            Storage::disk('public')->delete($oldImageAvatar);
        }

        if ($user->channel->brand !== $oldImageChannel && $oldImageChannel !== 'default-brand.png') {
            Storage::disk('public')->delete($oldImageChannel);
        }

        if ($user->channel->campaign->image !== $oldImageCamping && !empty($oldImageCamping)) {
            Storage::disk('public')->delete($oldImageCamping);
        }

        Notification::make()
            ->title('Perfil atualizado com sucesso!')
            ->body(auth()->user()->name)
            ->success()
            ->send();
    }
}