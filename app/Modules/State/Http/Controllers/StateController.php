<?php

declare(strict_types=1);

namespace App\Modules\State\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\State\Domain\Contracts\StateServiceInterface;
use App\Modules\State\Http\Requests\StoreStateRequest;
use App\Modules\State\Http\Requests\UpdateStateRequest;
use App\Modules\State\Models\State;
use App\Modules\State\Actions\StateAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Controlador del módulo State.
 *
 * Gestiona las operaciones CRUD del módulo.
 *
 * @package App\Modules\State\Http\Controllers
 */
final class StateController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly StateServiceInterface $service,
        private readonly StateAction $action,
    ) {
    }

    /**
     * Muestra el listado del recurso.
     */
    public function index(): View
    {
        return view('states.index', [
            'states' => $this->service->paginate(
                perPage: self::PER_PAGE,
            ),
        ]);
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create(): View
    {
        return view('states.create');
    }

    /**
     * Almacena un nuevo recurso.
     */
    public function store(
        StoreStateRequest $request,
    ): RedirectResponse {

        $this->action->create(
            $request->validated()
        );

        return redirect()
            ->route('states.index')
            ->with('success', 'State creado correctamente.');
    }

    /**
     * Muestra un recurso específico.
     */
    public function show(
        State $state
    ): View {

        return view('states.show', [
            'state' => $state,
        ]);
    }

    /**
     * Edita un recurso específico.
     */
    public function edit(
        State $state
    ): View {

        return view('states.edit', [
            'state' => $state,
        ]);
    }

    /**
     * Actualiza un recurso específico.
     */
    public function update(
        UpdateStateRequest $request,
        State $state
    ): RedirectResponse {

        $this->action->update(
            $state->getKey(),
            $request->validated()
        );

        return redirect()
            ->route('states.index')
            ->with('success', 'State actualizado correctamente.');
    }

    /**
     * Elimina el recurso específico.
     */
    public function destroy(
        State $state
    ): RedirectResponse {

        $this->action->delete(
            $state->getKey());

        return redirect()
            ->route('states.index')
            ->with('success', 'State eliminado correctamente.');
    }
}
