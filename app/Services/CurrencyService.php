<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CurrencyRepositoryInterface;
use App\Contracts\CurrencyServiceInterface;
use App\Core\Services\BaseService;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\DTO\Currency\CurrencyCreateData;

class CurrencyService extends BaseService implements CurrencyServiceInterface
{
    public function __construct(
        protected CurrencyRepositoryInterface $repository
    ) {
        parent::__construct($repository);
    }

    /**
     * Obtiene la moneda predeterminada.
     */
    public function findDefault(): ?Currency
    {
        return $this->repository->default();
    }

    /**
     * Crea una moneda.
     */
    public function create(CurrencyCreateData $dto): Currency
    {
        return $this->transaction(function () use ($dto) {

            $attributes = $dto->toArray();

            if (!empty($attributes['is_default'])) {
                $this->removeDefaultCurrency();
            }

            /** @var Currency */
            return parent::create($attributes);
        });
    }

    /**
     * Actualiza una moneda.
     */
    public function update(Currency $currency, array $attributes): bool
    {
        return DB::transaction(function () use ($currency, $attributes) {

            if (!empty($attributes['is_default'])) {
                $this->removeDefaultCurrency($currency->id);
            }

            return parent::update($currency, $attributes);
        });
    }

    /**
     * Elimina una moneda.
     */
    public function delete(Currency $currency): bool
    {
        if ($currency->isDefault()) {
            throw ValidationException::withMessages([
                'currency' => 'No es posible eliminar la moneda predeterminada.',
            ]);
        }

        return parent::delete($currency);
    }

    private function removeDefaultCurrency(?int $exceptId = null): void
    {
        $default = $this->repository->default();

        if ($default && $default->id !== $exceptId) {
            $this->repository->update($default, [
                'is_default' => false,
            ]);
        }
    }


}
