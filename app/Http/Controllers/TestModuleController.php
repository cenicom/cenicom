<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Core\Contracts\TestModuleServiceInterface;

use App\Http\Requests\TestModule\StoreTestModuleRequest;
use App\Http\Requests\TestModule\UpdateTestModuleRequest;
use App\Models\TestModule;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Controlador del módulo TestModule.
 *
 * Gestiona las operaciones CRUD del módulo.
 *
 * @package App\Http\Controllers
 */
final class TestModuleController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly TestModuleServiceInterface $service,
    ) {
    }

    /**
        * Muestra el listado del recurso.
        */
    public function index(): View
    {
        return view('test_modules.index', [
            'test_modules' => $this->service->paginate(
                perPage: self::PER_PAGE,
            ),
        ]);
    }

    /**
        * Muestra el formulario de creación.
        */
    public function create(): View
    {
        return view('test_modules.create');
    }

    /**
        * Almacena un nuevo recurso.
        */
    public function store(
        StoreTestModuleRequest $request,
    ): RedirectResponse {

        $this->service->create(
            $request->validated()
        );

        return redirect()
            ->route('test_modules.index')
            ->with('success', 'test_module creado correctamente.');
    }

    /**
        * Muestra un recurso específico.
        */
    public function show(
        TestModule $testModule
    ): View {

        return view('test_modules.show', [
            'testModule' => $testModule,
        ]);
    }

    /**
        * Edita un recurso específico.
        */
    public function edit(
        TestModule $testModule
    ): View {

        return view('test_modules.edit', [
            'testModule' => $testModule ,
        ]);
    }

    /**
    * Actualiza un recurso específico.
    */
    public function update(
        UpdateTestModuleRequest $request,
        TestModule $testModule
    ): RedirectResponse {

        $this->service->update(
            $testModule,
            $request->validated()
        );

        return redirect()
            ->route('test_modules.index')
            ->with('success', 'test_module actualizado correctamente.');
    }

    /**
    * Elimina el recurso específico.
    */
    public function destroy(
        TestModule $testModule
    ): RedirectResponse {

        $this->service->destroy($testModule);

        return redirect()
            ->route('test_modules.index')
            ->with('success', 'test_module eliminado correctamente.');
    }
}
