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
 * ID          : CN-FORMS-006
 * Componente  : x-cn.select
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Campo especializado para selección de opciones.
 *
 * @package App\View\Components\Cn\Forms
 */

class Select extends Component
{
    public function __construct(
        public string $name,
        public ?string $id = null,
        public mixed $value = null,
        public array $options = [],
        public ?string $placeholder = null,
        public bool $required = false,
        public bool $disabled = false,
        public bool $multiple = false,
    ) {
        $this->id ??= $this->name;
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.forms.select');
    }
}
