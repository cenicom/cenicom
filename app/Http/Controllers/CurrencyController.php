<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Core\Contracts\CurrencyServiceInterface;

use App\Http\Requests\Currency\StoreCurrencyRequest;

use App\Http\Requests\Currency\UpdateCurrencyRequest;

use App\Models\Currency;

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
 * @package App\Http\Controllers
 */
final class CurrencyController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly CurrencyServiceInterface $service,
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

        $this->service->create(
            $request->validated()
        );

        return redirect()
            ->route('currencies.index')
            ->with('success', 'currency creado correctamente.');
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
            'currency' => $currency ,
        ]);
    }

    /**
    * Actualiza un recurso específico.
    */
    public function update(
        UpdateCurrencyRequest $request,
        Currency $currency
    ): RedirectResponse {

        $this->service->update(
            $currency,
            $request->validated()
        );

        return redirect()
            ->route('currencies.index')
            ->with('success', 'currency actualizado correctamente.');
    }

    /**
    * Elimina el recurso específico.
    */
    public function destroy(
        Currency $currency
    ): RedirectResponse {

        $this->service->destroy($currency);

        return redirect()
            ->route('currencies.index')
            ->with('success', 'currency eliminado correctamente.');
    }
}
