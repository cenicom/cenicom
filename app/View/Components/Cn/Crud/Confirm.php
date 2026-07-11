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
 * ID          : CN-CRUD-009
 * Componente  : x-cn.confirm
 * Categoría   : CRUD
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Renderiza un diálogo de confirmación basado en el componente modal
 * del CN UI Framework.
 *
 * @package App\View\Components\Cn
 */
class Confirm extends Component
{
    /**
     * Crea una nueva instancia del componente.
     */
    public function __construct(
        public string $id,
        public string $title = 'Confirmar acción',
        public string $message = '¿Está seguro de continuar?',
        public string $confirmText = 'Confirmar',
        public string $cancelText = 'Cancelar',
    ) {
    }

    /**
     * Renderiza el componente.
     */
    public function render(): View|Closure|string
    {
        return view('components.cn.confirm');
    }
}
