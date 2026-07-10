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
 * ID          : CN-FORMS-008
 * Componente  : x-cn.checkbox
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Campo especializado para valores booleanos.
 *
 * @package App\View\Components\Cn\Forms
 */

class Checkbox extends Component
{
    public function __construct(
        public string $name,
        public ?string $id = null,
        public mixed $value = 1,
        public bool $checked = false,
        public bool $disabled = false,
        public bool $required = false,
    ) {
        $this->id ??= $this->name;
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.forms.checkbox');
    }
}
