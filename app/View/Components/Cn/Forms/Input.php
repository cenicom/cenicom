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
 * ID          : CN-FORMS-001
 * Componente  : x-cn.forms.input
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Componente base para campos HTML input.
 *
 * Extensiones:
 * - x-cn.forms.email
 * - x-cn.forms.password
 * - x-cn.forms.number
 * - x-cn.forms.search
 *
 * @package App\View\Components\Cn\Forms
 */

class Input extends Component
{
    public function __construct(
        public string $name,
        public ?string $id = null,
        public string $type = 'text',
        public mixed $value = null,
        public ?string $placeholder = null,
        public ?string $autocomplete = null,
        public ?string $inputmode = null,
        public ?string $pattern = null,
        public bool $required = false,
        public bool $readonly = false,
        public bool $disabled = false,
        public bool $autofocus = false,
        public ?string $min = null,
        public ?string $max = null,
        public ?string $step = null,
        public ?int $minlength = null,
        public ?int $maxlength = null,
    ) {
        $this->id ??= $this->name;
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.forms.input');
    }
}
