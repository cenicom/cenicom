<?php

declare(strict_types=1);

namespace App\Modules\Institution\Infrastructure\Identity;


use App\Modules\Institution\Domain\Contracts\InstitutionIdGeneratorInterface;
use Illuminate\Support\Str;

final class InstitutionIdGenerator implements InstitutionIdGeneratorInterface
{
    public function generate(): string
    {
        return (string) Str::ulid();
    }
}
