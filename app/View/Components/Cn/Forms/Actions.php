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
 * ID          : CN-FORMS-107
 * Componente  : x-cn.actions
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Contenedor para las acciones de un formulario.
 *
 * @package App\View\Components\Cn\Forms
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
        return view('components.cn.forms.actions');
    }
}
