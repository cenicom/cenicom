<?php

declare(strict_types=1);

namespace App\Http\Controllers\Core;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Base para todos los CRUD del sistema.
 *
 * @package App\Http\Controllers\Core
 * @since 1.0.0
 */
abstract class BaseCrudController extends BaseController
{
    /**
     * Vista principal.
     */
    protected string $indexView;

    /**
     * Vista crear.
     */
    protected string $createView;

    /**
     * Vista editar.
     */
    protected string $editView;

    /**
     * Ruta base.
     */
    protected string $route;
}
