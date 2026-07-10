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
 * ID          : CN-FORMS-009
 * Componente  : x-cn.radio
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Campo especializado para selección única entre múltiples opciones.
 *
 * @package App\View\Components\Cn\Forms
 */
class Radio extends Component
{
    public function __construct(
        public string $name,
        public string $value,
        public ?string $id = null,
        public bool $checked = false,
        public bool $required = false,
        public bool $disabled = false,
    ) {
        $this->id ??= $this->name . '_' . str_replace(' ', '_', $this->value);
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.forms.radio');
    }
}
