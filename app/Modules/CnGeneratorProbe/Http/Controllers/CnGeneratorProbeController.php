<?php

declare(strict_types=1);

namespace App\Modules\CnGeneratorProbe\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CnGeneratorProbe\Domain\Contracts\CnGeneratorProbeServiceInterface;
use App\Modules\CnGeneratorProbe\Http\Requests\StoreCnGeneratorProbeRequest;
use App\Modules\CnGeneratorProbe\Http\Requests\UpdateCnGeneratorProbeRequest;
use App\Modules\CnGeneratorProbe\Models\CnGeneratorProbe;
use App\Modules\CnGeneratorProbe\Actions\CnGeneratorProbeAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Controlador del módulo CnGeneratorProbe.
 *
 * Gestiona las operaciones CRUD del módulo.
 *
 * @package App\Modules\CnGeneratorProbe\Http\Controllers
 */
final class CnGeneratorProbeController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly CnGeneratorProbeServiceInterface $service,
        private readonly CnGeneratorProbeAction $action,
    ) {
    }

    /**
     * Muestra el listado del recurso.
     */
    public function index(): View
    {
        return view('cn_generator_probes::index', [
            'cnGeneratorProbes' => $this->service->paginate(
                perPage: self::PER_PAGE,
            ),
        ]);
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create(): View
    {
        return view('cn_generator_probes::create');
    }

    /**
     * Almacena un nuevo recurso.
     */
    public function store(
        StoreCnGeneratorProbeRequest $request,
    ): RedirectResponse {

        $this->action->create(
            $request->validated()
        );

        return redirect()
            ->route('cn_generator_probes.index')
            ->with('success', 'Cn Generator Probe creado correctamente.');
    }

    /**
     * Muestra un recurso específico.
     */
    public function show(
        CnGeneratorProbe $cnGeneratorProbe
    ): View {

        return view('cn_generator_probes::show', [
            'cnGeneratorProbe' => $cnGeneratorProbe,
        ]);
    }

    /**
     * Edita un recurso específico.
     */
    public function edit(
        CnGeneratorProbe $cnGeneratorProbe
    ): View {

        return view('cn_generator_probes::edit', [
            'cnGeneratorProbe' => $cnGeneratorProbe,
        ]);
    }

    /**
     * Actualiza un recurso específico.
     */
    public function update(
        UpdateCnGeneratorProbeRequest $request,
        CnGeneratorProbe $cnGeneratorProbe
    ): RedirectResponse {

        $this->action->update(
            $cnGeneratorProbe->getKey(),
            $request->validated()
        );

        return redirect()
            ->route('cn_generator_probes.index')
            ->with('success', 'Cn Generator Probe actualizado correctamente.');
    }

    /**
     * Elimina el recurso específico.
     */
    public function destroy(
        CnGeneratorProbe $cnGeneratorProbe
    ): RedirectResponse {

        $this->action->delete(
            $cnGeneratorProbe->getKey());

        return redirect()
            ->route('cn_generator_probes.index')
            ->with('success', 'Cn Generator Probe eliminado correctamente.');
    }
}
