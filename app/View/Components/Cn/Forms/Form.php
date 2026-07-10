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
 * ID          : CN-FORMS-100
 * Componente  : x-cn.form
 * Categoría   : Forms
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Contenedor para las acciones de un formulario.
 *
 * @package App\View\Components\Cn\Forms
 */
class Form extends Component
{
    public function __construct(
    public ?string $action = null,
    public string $method = 'POST',
    public ?string $id = null,
    public ?string $autocomplete = null,
    public bool $novalidate = false,
) {
    $this->method = strtoupper($method);
}


    public function render(): View|Closure|string
    {
        return view('components.cn.forms.form');
    }
}
