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
 * ID          : CN-FORMS-105
 * Componente  : x-cn.hint
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Renderiza información contextual o restricciones de un campo.
 *
 * @package App\View\Components\Cn\Forms
 */
class Hint extends Component
{
    public function __construct(
        public ?string $id = null,
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.forms.hint');
    }
}
