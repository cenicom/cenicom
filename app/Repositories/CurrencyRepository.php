<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Models\Currency;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio de monedas.
 *
 * @package App\Repositories
 * @since 1.0.0
 */
class CurrencyRepository extends BaseRepository
{
    public function __construct(Currency $model)
    {
        $this->model = $model;
    }

    /**
     * Busca una moneda por código ISO.
     */
    public function findByCode(string $code): ?Currency
    {
        return $this->query()
            ->where('code', strtoupper($code))
            ->first();
    }
}
