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
 * ID          : CN-FORMS-005
 * Componente  : x-cn.date
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Campo especializado para captura de fechas.
 *
 * Extiende:
 * - x-cn.input
 *
 * @package App\View\Components\Cn\Forms
 */

class Date extends Input
{
    public function __construct(
        string $name,
        ?string $id = null,
        mixed $value = null,
        ?string $placeholder = null,
        ?string $min = null,
        ?string $max = null,
        ?string $autocomplete = null,
        bool $required = false,
        bool $readonly = false,
        bool $disabled = false,
        bool $autofocus = false,
    ) {
        parent::__construct(
            name: $name,
            id: $id,
            type: 'date',
            value: $value,
            placeholder: $placeholder,
            autocomplete: $autocomplete,
            inputmode: 'numeric',
            min: $min,
            max: $max,
            required: $required,
            readonly: $readonly,
            disabled: $disabled,
            autofocus: $autofocus,
        );
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.forms.date');
    }
}
