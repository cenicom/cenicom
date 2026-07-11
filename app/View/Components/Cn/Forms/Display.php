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
 * ID          : CN-FORMS-101
 * Componente  : x-cn.display
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Mostrar información en modo lectura utilizando
 * la misma identidad visual del CN UI Framework.
 *
 * @package App\View\Components\Cn\Forms
 */

class Display extends Component
{
    /**
     * Valor a mostrar.
     */
    public function __construct(
        public mixed $value = null,
    ) {
    }

    /**
     * Renderiza el componente.
     */
    public function render(): View|Closure|string
    {
        return view('components.cn.forms.display');
    }
}
