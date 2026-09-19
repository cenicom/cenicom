<?php

declare(strict_types=1);

namespace App\Modules\Currency\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Middleware del módulo CurrencyMiddleware.
 *
 * Archivo generado automáticamente por el CN Generator.
 * No modificar manualmente.
 */
final class CurrencyMiddleware
{
/**
 * Handle an incoming request.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
 */
 public function handle(Request $request, Closure $next): Response
{
            if (! $request->user()?->can('currencies.view')) {
            abort(403);
        }
        
        return $next($request);
}
}
