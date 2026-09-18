<?php

declare(strict_types=1);

namespace App\Modules\City\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\City\Domain\Contracts\CityServiceInterface;
use App\Modules\City\Http\Requests\StoreCityRequest;
use App\Modules\City\Http\Requests\UpdateCityRequest;
use App\Modules\City\Models\City;
use App\Modules\City\Actions\CityAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Controlador del módulo City.
 *
 * Gestiona las operaciones CRUD del módulo.
 *
 * @package App\Modules\City\Http\Controllers
 */
final class CityController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly CityServiceInterface $service,
        private readonly CityAction $action,
    ) {
    }

    /**
     * Muestra el listado del recurso.
     */
    public function index(): View
    {
        return view('cities.index', [
            'cities' => $this->service->paginate(
                perPage: self::PER_PAGE,
            ),
        ]);
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create(): View
    {
        return view('cities.create');
    }

    /**
     * Almacena un nuevo recurso.
     */
    public function store(
        StoreCityRequest $request,
    ): RedirectResponse {

        $this->action->create(
            $request->validated()
        );

        return redirect()
            ->route('cities.index')
            ->with('success', 'City creado correctamente.');
    }

    /**
     * Muestra un recurso específico.
     */
    public function show(
        City $city
    ): View {

        return view('cities.show', [
            'city' => $city,
        ]);
    }

    /**
     * Edita un recurso específico.
     */
    public function edit(
        City $city
    ): View {

        return view('cities.edit', [
            'city' => $city,
        ]);
    }

    /**
     * Actualiza un recurso específico.
     */
    public function update(
        UpdateCityRequest $request,
        City $city
    ): RedirectResponse {

        $this->action->update(
            $city->getKey(),
            $request->validated()
        );

        return redirect()
            ->route('cities.index')
            ->with('success', 'City actualizado correctamente.');
    }

    /**
     * Elimina el recurso específico.
     */
    public function destroy(
        City $city
    ): RedirectResponse {

        $this->action->delete(
            $city->getKey());

        return redirect()
            ->route('cities.index')
            ->with('success', 'City eliminado correctamente.');
    }
}
