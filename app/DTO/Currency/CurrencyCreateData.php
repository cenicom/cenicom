<?php

declare(strict_types=1);

namespace App\DTO\Currency;

use App\Models\Currency;

final readonly class CurrencyCreateData
{
    public function __construct(
        public string $code,
        public string $name,
        public string $symbol,
        public int $decimalPlaces = Currency::DEFAULT_DECIMAL_PLACES,
        public string $decimalSeparator = Currency::DECIMAL_SEPARATOR,
        public string $thousandsSeparator = Currency::THOUSANDS_SEPARATOR,
        public string $symbolPosition = Currency::SYMBOL_BEFORE,
        public bool $isDefault = false,
        public bool $status = true,
    ) {
    }

    /**
     * Crea un DTO a partir de un arreglo.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: strtoupper($data['code']),
            name: trim($data['name']),
            symbol: trim($data['symbol']),
            decimalPlaces: (int) ($data['decimal_places'] ?? Currency::DEFAULT_DECIMAL_PLACES),
            decimalSeparator: $data['decimal_separator'] ?? Currency::DECIMAL_SEPARATOR,
            thousandsSeparator: $data['thousands_separator'] ?? Currency::THOUSANDS_SEPARATOR,
            symbolPosition: $data['symbol_position'] ?? Currency::SYMBOL_BEFORE,
            isDefault: (bool) ($data['is_default'] ?? false),
            status: (bool) ($data['status'] ?? true),
        );
    }

    /**
     * Convierte el DTO en un arreglo.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'decimal_places' => $this->decimalPlaces,
            'decimal_separator' => $this->decimalSeparator,
            'thousands_separator' => $this->thousandsSeparator,
            'symbol_position' => $this->symbolPosition,
            'is_default' => $this->isDefault,
            'status' => $this->status,
        ];
    }


}
