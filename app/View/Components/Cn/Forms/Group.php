<?php

declare(strict_types=1);

namespace App\View\Components\Cn\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * -----------------------------------------------------------------------------
 * CENICOM ERP
 * CN UI Framework
 * -----------------------------------------------------------------------------
 *
 * ID          : CN-FORMS-106
 * Componente  : x-cn.group
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Agrupa varios campos de formulario.
 *
 * @package App\View\Components\Cn\Forms
 */
class Group extends Component
{
    public function __construct(
        public ?string $id = null,
        public int $columns = 1,
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.forms.group');
    }
}
