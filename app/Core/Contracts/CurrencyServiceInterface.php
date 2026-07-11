<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use App\Core\Contracts\ServiceInterface;
use App\Models\Currency;

interface CurrencyServiceInterface extends ServiceInterface
{
    /**
     * Obtiene la moneda predeterminada.
     */
    public function default(): ?Currency;

    /**
     * Crea una moneda.
     */
    public function create(array $attributes): Currency;

    /**
     * Actualiza una moneda.
     */
    public function update(Currency $currency, array $attributes): bool;

    /**
     * Elimina una moneda.
     */
    public function delete(Currency $currency): bool;
}
