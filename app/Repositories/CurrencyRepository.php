<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Models\Currency;
use App\Contracts\CurrencyRepositoryInterface;

class CurrencyRepository extends BaseRepository
    implements CurrencyRepositoryInterface
{
    public function __construct(Currency $model)
    {
        parent::__construct($model);
    }

    /**
     * Obtiene la moneda predeterminada.
     */
    public function findDefault(): ?Currency
    {
         return $this->query()
            ->default()
            ->first();
    }

    /**
     * Busca por código ISO.
     */
    public function findByCode(string $code): ?Currency
    {
       return $this->query()
            ->byCode($code)
            ->first();
    }
}
