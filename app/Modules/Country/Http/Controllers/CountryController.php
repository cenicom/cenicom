<?php

declare(strict_types=1);

namespace App\Modules\Country\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Country\Domain\Contracts\CountryServiceInterface;
use App\Modules\Country\Http\Requests\StoreCountryRequest;
use App\Modules\Country\Http\Requests\UpdateCountryRequest;
use App\Modules\Country\Models\Country;
use App\Modules\Country\Actions\CountryAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Controlador del módulo Country.
 *
 * Gestiona las operaciones CRUD del módulo.
 *
 * @package App\Modules\Country\Http\Controllers
 */
final class CountryController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly CountryServiceInterface $service,
        private readonly CountryAction $action,
    ) {
    }

    /**
     * Muestra el listado del recurso.
     */
    public function index(): View
    {
        return view('countries::index', [
            'countries' => $this->service->paginate(
                perPage: self::PER_PAGE,
            ),
        ]);
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create(): View
    {
        return view('countries::create');
    }

    /**
     * Almacena un nuevo recurso.
     */
    public function store(
        StoreCountryRequest $request,
    ): RedirectResponse {

        $this->action->create(
            $request->validated()
        );

        return redirect()
            ->route('countries.index')
            ->with('success', 'Country creado correctamente.');
    }

    /**
     * Muestra un recurso específico.
     */
    public function show(
        Country $country
    ): View {

        return view('countries::show', [
            'country' => $country,
        ]);
    }

    /**
     * Edita un recurso específico.
     */
    public function edit(
        Country $country
    ): View {

        return view('countries::edit', [
            'country' => $country,
        ]);
    }

    /**
     * Actualiza un recurso específico.
     */
    public function update(
        UpdateCountryRequest $request,
        Country $country
    ): RedirectResponse {

        $this->action->update(
            $country->getKey(),
            $request->validated()
        );

        return redirect()
            ->route('countries.index')
            ->with('success', 'Country actualizado correctamente.');
    }

    /**
     * Elimina el recurso específico.
     */
    public function destroy(
        Country $country
    ): RedirectResponse {

        $this->action->delete(
            $country->getKey());

        return redirect()
            ->route('countries.index')
            ->with('success', 'Country eliminado correctamente.');
    }
}
