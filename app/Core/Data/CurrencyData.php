<?php

declare(strict_types=1);

namespace App\Data;

use App\Core\Data\BaseData;

final class CurrencyDTO extends BaseData
{
    public function __construct(
        public readonly string $code,
        public readonly string $symbol,
        public readonly string $name,
        public readonly int $decimal_places,
        public readonly string $decimal_separator,
        public readonly string $thousands_separator,
        public readonly string $symbol_position,
        public readonly bool $is_default,
        public readonly bool $status,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(
            code: strtoupper($data['code']),
            symbol: $data['symbol'],
            name: $data['name'],
            decimal_places: $data['decimal_places'],
            decimal_separator: $data['decimal_separator'],
            thousands_separator: $data['thousands_separator'],
            symbol_position: $data['symbol_position'],
            is_default: (bool) $data['is_default'],
            status: (bool) $data['status'],
        );
    }
}
