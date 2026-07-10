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
 * ID          : CN-FORMS-104
 * Componente  : x-cn.error
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Renderiza el primer mensaje de validación asociado a un campo.
 *
 * @package App\View\Components\Cn\Forms
 */
class Error extends Component
{
    public function __construct(
        public string $for,
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.forms.error');
    }
}
