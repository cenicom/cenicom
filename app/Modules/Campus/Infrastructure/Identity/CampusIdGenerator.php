<?php

declare(strict_types=1);

namespace App\Modules\Campus\Infrastructure\Identity;

use App\Modules\Campus\Domain\Contracts\CampusIdGeneratorInterface;
use Illuminate\Support\Str;

final class CampusIdGenerator implements CampusIdGeneratorInterface
{
    public function generate(): string
    {
        return (string) Str::ulid();
    }
}
