<?php

declare(strict_types=1);

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Clase base para todos los controladores.
 *
 * @package App\Http\Controllers\Core
 * @since 1.0.0
 */
abstract class BaseController extends Controller
{
    /**
     * Renderiza una vista.
     */
    protected function view(
        string $view,
        array $data = []
    ): View {
        return view($view, $data);
    }

    /**
     * Redirección estándar.
     */
    protected function redirectTo(
        string $route
    ): RedirectResponse {
        return redirect()->route($route);
    }

    /**
     * Mensaje flash de éxito.
     */
    protected function success(
        string $message
    ): RedirectResponse {

        return back()->with('success', $message);

    }

    /**
     * Mensaje flash de error.
     */
    protected function error(
        string $message
    ): RedirectResponse {

        return back()->with('error', $message);

    }
}
