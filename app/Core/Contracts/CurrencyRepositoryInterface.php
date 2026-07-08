<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Core\Contracts\RepositoryInterface;
use App\Models\Currency;

interface CurrencyRepositoryInterface extends RepositoryInterface
{
    /**
     * Obtiene la moneda predeterminada.
     */
    public function default(): ?Currency;

    /**
     * Busca una moneda por código ISO.
     */
    public function findByCode(string $code): ?Currency;
}
