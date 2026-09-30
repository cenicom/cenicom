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
 * ID          : CN-FORMS-004
 * Componente  : x-cn.forms.search
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Campo especializado para valores numéricos.
 *
 * Extiende:
 * - x-cn.forms.input
 *
 * @package App\View\Components\Cn\Forms
 */

class Search extends Input
{
    /**
     * Renderiza el componente.
     */
    public function render(): View|Closure|string
    {
        return view('components.cn.forms.search');
    }
}
