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
 * ID          : CN-FORMS-003
 * Componente  : x-cn.password
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Campo especializado para captura segura de contraseñas.
 *
 * Extiende:
 * - x-cn.input
 *
 * @package App\View\Components\Cn\Forms
 */

class Password extends Input
{
    public function __construct(
        string $name,
        ?string $id = null,
        mixed $value = null,
        ?string $placeholder = null,
        ?string $autocomplete = 'current-password',
        bool $required = false,
        bool $readonly = false,
        bool $disabled = false,
        bool $autofocus = false,
    ) {
        parent::__construct(
            name: $name,
            id: $id,
            type: 'password',
            value: $value,
            placeholder: $placeholder,
            autocomplete: $autocomplete,
            inputmode: 'text',
            required: $required,
            readonly: $readonly,
            disabled: $disabled,
            autofocus: $autofocus,
        );
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.forms.password');
    }
}
