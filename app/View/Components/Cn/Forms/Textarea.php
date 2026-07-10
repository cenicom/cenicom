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
 * ID          : CN-FORMS-007
 * Componente  : x-cn.textarea
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Campo especializado para captura de texto multilínea.
 *
 * @package App\View\Components\Cn\Forms
 */

class Textarea extends Component
{
    public function __construct(
        public string $name,
        public ?string $id = null,
        public mixed $value = null,
        public ?string $placeholder = null,
        public int $rows = 4,
        public ?int $maxlength = null,
        public ?int $minlength = null,
        public bool $required = false,
        public bool $readonly = false,
        public bool $disabled = false,
        public bool $autofocus = false,
    ) {
        $this->id ??= $this->name;
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.forms.textarea');
    }
}
