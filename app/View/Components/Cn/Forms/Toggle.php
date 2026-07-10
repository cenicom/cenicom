<?php

declare(strict_types=1);

namespace App\View\Components\Cn\Forms;

use Closure;
use Illuminate\Contracts\View\View;

/**
 * -----------------------------------------------------------------------------
 * CENICOM ERP
 * CN UI Framework
 * -----------------------------------------------------------------------------
 *
 * ID          : CN-FORMS-010
 * Componente  : x-cn.switch
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Interruptor visual para valores booleanos.
 *
 * Extiende:
 * - x-cn.checkbox
 */

class ToggleSwitch extends Checkbox
{
    public function render(): View|Closure|string
    {
        return view('components.cn.forms.switch');
    }
}
