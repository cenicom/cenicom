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
 * Componente  : x-cn.number
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Campo especializado para valores numéricos.
 *
 * Extiende:
 * - x-cn.input
 *
 * @package App\View\Components\Cn\Forms
 */

class Number extends Input
{
    public function __construct(
        string $name,
        ?string $id = null,
        mixed $value = null,
        ?string $placeholder = null,
        ?string $min = null,
        ?string $max = null,
        string $step = 'any',
        string $inputmode = 'decimal',
        ?string $pattern = null,
        bool $required = false,
        bool $readonly = false,
        bool $disabled = false,
        bool $autofocus = false,
    ) {
        parent::__construct(
            name: $name,
            id: $id,
            type: 'number',
            value: $value,
            placeholder: $placeholder,
            inputmode: $inputmode,
            pattern: $pattern,
            required: $required,
            readonly: $readonly,
            disabled: $disabled,
            autofocus: $autofocus,
            min: $min,
            max: $max,
            step: $step,
        );
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.forms.number');
    }
}
