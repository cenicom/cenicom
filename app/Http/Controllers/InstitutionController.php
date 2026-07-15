<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use {{ contractNamespace }}\InstitutionServiceInterface;
use App\Http\Requests\Institution\StoreInstitutionRequest;
use App\Http\Requests\Institution\UpdateInstitutionRequest;
use {{ modelNamespace }}\{{ model }};

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Controlador del módulo {{ model }}.
 *
 * Gestiona las operaciones CRUD del módulo.
 *
 * @package App\Http\Controllers
 */
final class {{ controller }} extends Controller
{
    public function __construct(
        private readonly InstitutionServiceInterface $service,
    ) {
    }

    public function index(): View
    {
        return view('{{ view }}.index', [
            '{{ pluralVariable }}' => $this->service->all(),
        ]);
    }

    public function create(): View
    {
        return view('{{ view }}.create');
    }

    public function store(
        StoreInstitutionRequest $request,
    ): RedirectResponse {

        $this->service->create(
            $request->validated()
        );

        return redirect()
            ->route('{{ route }}.index')
            ->with('success', '{{ singular }} creado correctamente.');
    }

    public function show(
        {{ model }} ${{ variable }}
    ): View {

        return view('{{ view }}.show', compact('{{ variable }}'));
    }

    public function edit(
        {{ model }} ${{ variable }}
    ): View {

        return view('{{ view }}.edit', compact('{{ variable }}'));
    }

    public function update(
        UpdateInstitutionRequest $request,
        {{ model }} ${{ variable }}
    ): RedirectResponse {

        $this->service->update(
            ${{ variable }},
            $request->validated()
        );

        return redirect()
            ->route('{{ route }}.index')
            ->with('success', '{{ singular }} actualizado correctamente.');
    }

    public function destroy(
        {{ model }} ${{ variable }}
    ): RedirectResponse {

        $this->service->delete(${{ variable }});

        return redirect()
            ->route('{{ route }}.index')
            ->with('success', '{{ singular }} eliminado correctamente.');
    }
}
