<?php

declare(strict_types=1);

namespace App\DTO\Currency;

use App\Models\Currency;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * DTO para la actualización de una moneda.
 *
 * Transporta los datos validados desde la capa HTTP
 * hacia la capa de aplicación.
 *
 * @package App\DTO\Currency
 * @since 1.0.0
 */
final readonly class CurrencyUpdateData
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
     * Crea el DTO desde un arreglo.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: strtoupper(trim($data['code'])),
            name: trim($data['name']),
            symbol: trim($data['symbol']),
            decimalPlaces: (int) ($data['decimal_places'] ?? Currency::DEFAULT_DECIMAL_PLACES),
            decimalSeparator: trim($data['decimal_separator'] ?? Currency::DECIMAL_SEPARATOR),
            thousandsSeparator: trim($data['thousands_separator'] ?? Currency::THOUSANDS_SEPARATOR),
            symbolPosition: strtolower(trim($data['symbol_position'] ?? Currency::SYMBOL_BEFORE)),
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
