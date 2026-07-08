<?php

declare(strict_types=1);

namespace App\Core\Responses;

use Illuminate\Http\RedirectResponse;

class CnResponse
{
    public static function created(
        string $route,
        string $message
    ): RedirectResponse {
        return redirect()
            ->route($route)
            ->with('success', $message);
    }

    public static function updated(
        string $route,
        string $message
    ): RedirectResponse {
        return redirect()
            ->route($route)
            ->with('success', $message);
    }

    public static function deleted(
        string $route,
        string $message
    ): RedirectResponse {
        return redirect()
            ->route($route)
            ->with('success', $message);
    }

    public static function error(
        string $message
    ): RedirectResponse {
        return back()->with('error', $message);
    }
}
