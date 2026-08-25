<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

use App\Core\Crud\CrudAction;

/**
 * -----------------------------------------------------------------------------
 * CENICOM ERP
 * -----------------------------------------------------------------------------
 *
 * Contrato del Presenter de acciones CRUD.
 *
 * Responsabilidad:
 * Transforma acciones CRUD ya autorizadas en representaciones neutrales
 * de presentación.
 *
 * Este contrato:
 * - NO resuelve autorización.
 * - NO renderiza.
 * - NO conoce Blade.
 * - NO depende de App\View.
 * - NO depende de componentes GUI.
 */
interface CrudActionPresenterInterface
{
    /**
     * @param array<int, CrudAction> $actions
     *
     * @return array<int, CrudActionPresentationInterface>
     */
    public function present(array $actions): array;
}
