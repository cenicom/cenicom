<?php

declare(strict_types=1);

namespace App\Modules\Currency\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Currency\Domain\Contracts\CurrencyServiceInterface;
use App\Modules\Currency\Http\Requests\StoreCurrencyRequest;
use App\Modules\Currency\Http\Requests\UpdateCurrencyRequest;
use App\Modules\Currency\Models\Currency;
use App\Modules\Currency\Actions\CurrencyAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Controlador del módulo Currency.
 *
 * Gestiona las operaciones CRUD del módulo.
 *
 * @package App\Modules\Currency\Http\Controllers
 */
final class CurrencyController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly CurrencyServiceInterface $service,
        private readonly CurrencyAction $action,
    ) {
    }

    /**
     * Muestra el listado del recurso.
     */
    public function index(): View
    {
        return view('currencies.index', [
            'currencies' => $this->service->paginate(
                perPage: self::PER_PAGE,
            ),
        ]);
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create(): View
    {
        return view('currencies.create');
    }

    /**
     * Almacena un nuevo recurso.
     */
    public function store(
        StoreCurrencyRequest $request,
    ): RedirectResponse {

        $this->action->create(
            $request->validated()
        );

        return redirect()
            ->route('currencies.index')
            ->with('success', 'Currency creado correctamente.');
    }

    /**
     * Muestra un recurso específico.
     */
    public function show(
        Currency $currency
    ): View {

        return view('currencies.show', [
            'currency' => $currency,
        ]);
    }

    /**
     * Edita un recurso específico.
     */
    public function edit(
        Currency $currency
    ): View {

        return view('currencies.edit', [
            'currency' => $currency,
        ]);
    }

    /**
     * Actualiza un recurso específico.
     */
    public function update(
        UpdateCurrencyRequest $request,
        Currency $currency
    ): RedirectResponse {

        $this->action->update(
            $currency->getKey(),
            $request->validated()
        );

        return redirect()
            ->route('currencies.index')
            ->with('success', 'Currency actualizado correctamente.');
    }

    /**
     * Elimina el recurso específico.
     */
    public function destroy(
        Currency $currency
    ): RedirectResponse {

        $this->action->delete(
            $currency->getKey());

        return redirect()
            ->route('currencies.index')
            ->with('success', 'Currency eliminado correctamente.');
    }
}
