<?php

declare(strict_types=1);

namespace App\DTO\Currency;

use App\Core\DTO\BaseDTO;

final class CurrencyCreateData extends BaseDTO
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $symbol,
        public readonly int $decimalPlaces = 2,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new self(
            code: $data['code'],
            name: $data['name'],
            symbol: $data['symbol'],
            decimalPlaces: $data['decimal_places'] ?? 2,
        );
    }
}
