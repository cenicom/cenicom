<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Core\Contracts\TestFormServiceInterface;

use App\Http\Requests\TestForm\StoreTestFormRequest;

use App\Http\Requests\TestForm\UpdateTestFormRequest;

use App\Models\TestForm;

use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Controlador del módulo TestForm.
 *
 * Gestiona las operaciones CRUD del módulo.
 *
 * @package App\Http\Controllers
 */
final class TestFormController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly TestFormServiceInterface $service,
    ) {
    }

    /**
    * Muestra el listado del recurso.
    */
    public function index(): View
    {
        return view('test_forms.index', [
            'test_forms' => $this->service->paginate(
                perPage: self::PER_PAGE,
            ),
        ]);
    }

    /**
        * Muestra el formulario de creación.
        */
    public function create(): View
    {
        return view('test_forms.create');
    }

    /**
        * Almacena un nuevo recurso.
        */
    public function store(
        StoreTestFormRequest $request,
    ): RedirectResponse {

        $this->service->create(
            $request->validated()
        );

        return redirect()
            ->route('test_forms.index')
            ->with('success', 'test_form creado correctamente.');
    }

    /**
        * Muestra un recurso específico.
        */
    public function show(
        TestForm $testForm
    ): View {

        return view('test_forms.show', [
            'testForm' => $testForm,
        ]);
    }

    /**
        * Edita un recurso específico.
        */
    public function edit(
        TestForm $testForm
    ): View {

        return view('test_forms.edit', [
            'testForm' => $testForm ,
        ]);
    }

    /**
    * Actualiza un recurso específico.
    */
    public function update(
        UpdateTestFormRequest $request,
        TestForm $testForm
    ): RedirectResponse {

        $this->service->update(
            $testForm,
            $request->validated()
        );

        return redirect()
            ->route('test_forms.index')
            ->with('success', 'test_form actualizado correctamente.');
    }

    /**
    * Elimina el recurso específico.
    */
    public function destroy(
        TestForm $testForm
    ): RedirectResponse {

        $this->service->destroy($testForm);

        return redirect()
            ->route('test_forms.index')
            ->with('success', 'test_form eliminado correctamente.');
    }
}
