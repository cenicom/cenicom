<?php

declare(strict_types=1);

namespace App\Core\Http\Controllers;

use App\Core\Contracts\ServiceInterface;
use App\Http\Controllers\Controller;

/**
 * -----------------------------------------------------------------------------
 * CENICOM ERP
 * -----------------------------------------------------------------------------
 *
 * Módulo      : Currency
 * Componente  : BaseCrudController
 * Versión     : 1.0.0
 *
 * Responsabilidad:
 * Validar y normalizar los datos necesarios para actualizar una moneda.
 */
abstract class BaseCrudController extends Controller
{
    public function __construct(
        /**
         * Servicio de dominio utilizado por el controlador.
         */
        protected ServiceInterface $service,
    ) {
    }
}
