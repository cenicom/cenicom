<?php

declare(strict_types=1);

namespace App\View\Components\Cn\Crud;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * -----------------------------------------------------------------------------
 * CENICOM ERP
 * CN UI Framework
 * -----------------------------------------------------------------------------
 *
 * ID          : CN-CRUD-008
 * Componente  : x-cn.modal
 * Categoría   : CRUD
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Renderiza un cuadro de diálogo modal utilizando el estilo
 * y las convenciones del CN UI Framework.
 *
 * @package App\View\Components\Cn
 */
class Modal extends Component
{
    /**
     * Crea una nueva instancia del componente.
     */
    public function __construct(
        public string $id,
        public ?string $title = null,
        public string $size = 'md',
    ) {
    }

    /**
     * Renderiza el componente.
     */
    public function render(): View|Closure|string
    {
        return view('components.cn.modal');
    }
}
