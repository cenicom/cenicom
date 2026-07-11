<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Contracts\CurrencyRepositoryInterface;
use App\Core\Contracts\CurrencyServiceInterface;
use App\Core\Services\BaseService;
use App\DTO\Currency\CurrencyCreateData;
use App\Models\Currency;
use Illuminate\Validation\ValidationException;

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
    public function default(): ?Currency
    {
        return $this->repository->default();
    }

    /**
     * Crea una nueva moneda.
     */
    public function create(CurrencyCreateData $dto): Currency
    {
        return $this->transaction(function () use ($dto): Currency {

            $attributes = $dto->toArray();

            if (!empty($attributes['is_default'])) {
                $this->clearDefaultCurrency();
            }

            /** @var Currency */
            return parent::create($attributes);
        });
    }

    /**
     * Actualiza una moneda.
     */
    public function update(
        Currency $currency,
        array $attributes
    ): bool {
        return $this->transaction(function () use ($currency, $attributes): bool {

            if (!empty($attributes['is_default'])) {
                $this->clearDefaultCurrency($currency->id);
            }

            return parent::update($currency, $attributes);
        });
    }

    /**
     * Elimina una moneda.
     *
     * @throws ValidationException
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

    /**
     * Quita la condición de moneda predeterminada
     * de la moneda actual.
     */
    private function clearDefaultCurrency(?int $exceptId = null): void
    {
        $default = $this->repository->default();

        if ($default !== null && $default->id !== $exceptId) {
            $this->repository->update($default, [
                'is_default' => false,
            ]);
        }
    }
}
