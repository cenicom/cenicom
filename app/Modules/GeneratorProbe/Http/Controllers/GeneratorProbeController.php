<?php

declare(strict_types=1);

namespace App\Modules\GeneratorProbe\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\GeneratorProbe\Domain\Contracts\GeneratorProbeServiceInterface;
use App\Modules\GeneratorProbe\Http\Requests\StoreGeneratorProbeRequest;
use App\Modules\GeneratorProbe\Http\Requests\UpdateGeneratorProbeRequest;
use App\Modules\GeneratorProbe\Models\GeneratorProbe;
use App\Modules\GeneratorProbe\Actions\GeneratorProbeAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Controlador del módulo GeneratorProbe.
 *
 * Gestiona las operaciones CRUD del módulo.
 *
 * @package App\Modules\GeneratorProbe\Http\Controllers
 */
final class GeneratorProbeController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly GeneratorProbeServiceInterface $service,
        private readonly GeneratorProbeAction $action,
    ) {
    }

    /**
     * Muestra el listado del recurso.
     */
    public function index(): View
    {
        return view('generator_probes::index', [
            'generatorProbes' => $this->service->paginate(
                perPage: self::PER_PAGE,
            ),
        ]);
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create(): View
    {
        return view('generator_probes::create');
    }

    /**
     * Almacena un nuevo recurso.
     */
    public function store(
        StoreGeneratorProbeRequest $request,
    ): RedirectResponse {

        $this->action->create(
            $request->validated()
        );

        return redirect()
            ->route('generator_probes.index')
            ->with('success', 'Generator Probe creado correctamente.');
    }

    /**
     * Muestra un recurso específico.
     */
    public function show(
        GeneratorProbe $generatorProbe
    ): View {

        return view('generator_probes::show', [
            'generatorProbe' => $generatorProbe,
        ]);
    }

    /**
     * Edita un recurso específico.
     */
    public function edit(
        GeneratorProbe $generatorProbe
    ): View {

        return view('generator_probes::edit', [
            'generatorProbe' => $generatorProbe,
        ]);
    }

    /**
     * Actualiza un recurso específico.
     */
    public function update(
        UpdateGeneratorProbeRequest $request,
        GeneratorProbe $generatorProbe
    ): RedirectResponse {

        $this->action->update(
            $generatorProbe->getKey(),
            $request->validated()
        );

        return redirect()
            ->route('generator_probes.index')
            ->with('success', 'Generator Probe actualizado correctamente.');
    }

    /**
     * Elimina el recurso específico.
     */
    public function destroy(
        GeneratorProbe $generatorProbe
    ): RedirectResponse {

        $this->action->delete(
            $generatorProbe->getKey());

        return redirect()
            ->route('generator_probes.index')
            ->with('success', 'Generator Probe eliminado correctamente.');
    }
}
