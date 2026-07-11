<?php

declare(strict_types=1);

namespace App\View\Components\Cn\Crud;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * -----------------------------------------------------------------------------
 * CENICOM ERP
 * CN UI Framework
 * -----------------------------------------------------------------------------
 *
 * ID          : CN-CRUD-005
 * Componente  : x-cn.actions
 * Categoría   : CRUD
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Organiza y distribuye las acciones asociadas a un registro o recurso.
 */
class Actions extends Component
{
    public function __construct(
        public ?string $id = null,
        public string $align = 'end',
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.actions');
    }
}
