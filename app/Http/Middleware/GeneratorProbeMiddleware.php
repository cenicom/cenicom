<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Middleware del módulo GeneratorProbeMiddleware.
 *
 * Archivo generado automáticamente por el CN Generator.
 * No modificar manualmente.
 */
final class GeneratorProbeMiddleware
{
/**
 * Handle an incoming request.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
 */
 public function handle(Request $request, Closure $next): Response
{
            // El módulo no define permisos.
        
        return $next($request);
}
}
