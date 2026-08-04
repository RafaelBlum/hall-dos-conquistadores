<?php

declare(strict_types=1);

namespace App\Enums;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PanelTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    case SUPER_ADMIN = "super-admin";
    case CHANNEL = "channel";
    case SUBSCRIBER = "subscriber";



    public function getLabel(): ?string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrador',
            self::CHANNEL => 'Canal',
            self::SUBSCRIBER => 'Inscrito',
        };
    }


    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SUPER_ADMIN => 'success',
            self::CHANNEL => 'success',
            self::SUBSCRIBER => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'heroicon-o-check-badge',
            self::CHANNEL => 'heroicon-o-shield-check',
            self::SUBSCRIBER => 'heroicon-o-users',
        };
    }
}
