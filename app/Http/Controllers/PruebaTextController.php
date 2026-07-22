<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Core\Services\Contracts\PruebaTextServiceInterface;

use App\Http\Requests\PruebaText\StorePruebaTextRequest;

use App\Http\Requests\PruebaText\UpdatePruebaTextRequest;

use App\Models\PruebaText;

use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Controlador del módulo PruebaText.
 *
 * Gestiona las operaciones CRUD del módulo.
 *
 * @package App\Http\Controllers
 */
final class PruebaTextController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly PruebaTextServiceInterface $service,
    ) {
    }

    /**
    * Muestra el listado del recurso.
    */
    public function index(): View
    {
        return view('prueba_texts.index', [
            'pruebaTexts' => $this->service->paginate(
                perPage: self::PER_PAGE,
            ),
        ]);
    }

    /**
        * Muestra el formulario de creación.
        */
    public function create(): View
    {
        return view('prueba_texts.create');
    }

    /**
        * Almacena un nuevo recurso.
        */
    public function store(
        StorePruebaTextRequest $request,
    ): RedirectResponse {

        $this->service->create(
            $request->validated()
        );

        return redirect()
            ->route('prueba_texts.index')
            ->with('success', 'prueba_text creado correctamente.');
    }

    /**
        * Muestra un recurso específico.
        */
    public function show(
        PruebaText $pruebaText
    ): View {

        return view('prueba_texts.show', [
            'pruebaText' => $pruebaText,
        ]);
    }

    /**
        * Edita un recurso específico.
        */
    public function edit(
        PruebaText $pruebaText
    ): View {

        return view('prueba_texts.edit', [
            'pruebaText' => $pruebaText ,
        ]);
    }

    /**
    * Actualiza un recurso específico.
    */
    public function update(
        UpdatePruebaTextRequest $request,
        PruebaText $pruebaText
    ): RedirectResponse {

        $this->service->update(
            $pruebaText,
            $request->validated()
        );

        return redirect()
            ->route('prueba_texts.index')
            ->with('success', 'prueba_text actualizado correctamente.');
    }

    /**
    * Elimina el recurso específico.
    */
    public function destroy(
        PruebaText $pruebaText
    ): RedirectResponse {

        $this->service->destroy($pruebaText);

        return redirect()
            ->route('prueba_texts.index')
            ->with('success', 'prueba_text eliminado correctamente.');
    }
}
