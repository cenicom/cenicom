<?php

declare(strict_types=1);

namespace App\Core\Actions;
use Illuminate\Support\Facades\DB;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Clase base para todos los casos de uso del sistema.
 *
 * Las acciones representan una única operación del negocio.
 *
 * @package App\Core\Actions
 * @since 1.0.0
 */
abstract class BaseAction
{
    //
    protected function transaction(callable $callback): mixed
    {
        return \Illuminate\Support\Facades\DB::transaction($callback);
    }
}


