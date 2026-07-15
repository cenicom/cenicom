<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Core\Contracts\ServiceInterface;
use App\Models\Currency;

interface CurrencyServiceInterface extends ServiceInterface
{
    /**
     * Obtiene la moneda predeterminada.
     */
    public function default(): ?Currency;
}
