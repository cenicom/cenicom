<?php

declare(strict_types=1);

namespace App\View\Components\Cn\Crud;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use InvalidArgumentException;

/**
 * -----------------------------------------------------------------------------
 * CENICOM ERP
 * CN UI Framework
 * -----------------------------------------------------------------------------
 *
 * ID          : CN-CRUD-005
 * Componente  : x-cn.crud.actions
 * Categoría   : CRUD
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Presenta acciones CRUD previamente preparadas por la capa de aplicación.
 *
 * Este componente:
 *
 * - NO resuelve autorización.
 * - NO consulta permisos.
 * - NO recibe IdentityInterface.
 * - NO decide qué acciones están permitidas.
 * - Solo acepta modelos CrudActionView.
 *
 * @param array<int, CrudActionView> $actions
 */
final class Actions extends Component
{
    /**
     * @param array<int, CrudActionView> $actions
     */
    public function __construct(
        public array $actions = [],
    ) {
        foreach ($this->actions as $action) {
            if (!$action instanceof CrudActionView) {
                throw new InvalidArgumentException(
                    sprintf(
                        'CRUD action must be an instance of %s, %s given.',
                        CrudActionView::class,
                        get_debug_type($action),
                    ),
                );
            }
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.crud.actions');
    }
}
