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
 * ID          : CN-FORMS-102
 * Componente  : x-cn.label
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Renderiza la etiqueta asociada a un control de formulario.
 *
 * @package App\View\Components\Cn\Forms
 */
class Label extends Component
{
    public function __construct(
        public ?string $for = null,
        public bool $required = false,
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.forms.label');
    }
}
