<?php

declare(strict_types=1);

namespace App\Modules\City\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Middleware del módulo CityMiddleware.
 *
 * Archivo generado automáticamente por el CN Generator.
 * No modificar manualmente.
 */
final class CityMiddleware
{
/**
 * Handle an incoming request.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
 */
 public function handle(Request $request, Closure $next): Response
{
            if (! $request->user()?->can('cities.view')) {
            abort(403);
        }
        
        return $next($request);
}
}
