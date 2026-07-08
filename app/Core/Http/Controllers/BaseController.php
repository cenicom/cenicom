<?php

declare(strict_types=1);

namespace App\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

abstract class BaseController extends Controller
{
    /**
     * Redirección con mensaje de éxito.
     */
    protected function success(
        string $route,
        string $message
    ): RedirectResponse {
        return redirect()
            ->route($route)
            ->with('success', $message);
    }

    /**
     * Redirección con mensaje de error.
     */
    protected function error(
        string $route,
        string $message
    ): RedirectResponse {
        return redirect()
            ->route($route)
            ->with('error', $message);
    }

    /**
     * Redirección con mensaje informativo.
     */
    protected function info(
        string $route,
        string $message
    ): RedirectResponse {
        return redirect()
            ->route($route)
            ->with('info', $message);
    }

    /**
     * Redirección con mensaje de advertencia.
     */
    protected function warning(
        string $route,
        string $message
    ): RedirectResponse {
        return redirect()
            ->route($route)
            ->with('warning', $message);
    }
}
