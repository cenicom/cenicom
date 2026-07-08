<?php

declare(strict_types=1);

namespace App\Modules\Currency\DTO;

use App\Core\DTO\BaseDTO;

final class CurrencyResponseData extends BaseDTO
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $code,
        public readonly string $name,
        public readonly string $symbol,
        public readonly bool $status,
    ) {
    }
}
