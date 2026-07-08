<?php

declare(strict_types=1);

namespace App\Core\Enums;

enum Status: bool
{
    case ACTIVE = true;

    case INACTIVE = false;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Activo',
            self::INACTIVE => 'Inactivo',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'check-circle',
            self::INACTIVE => 'times-circle',
        };
    }
}
