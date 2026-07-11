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
 * ID          : CN-CRUD-007
 * Componente  : x-cn.filters
 * Categoría   : CRUD
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Organiza y renderiza los controles de filtrado para un listado
 * de recursos del sistema.
 *
 * @package App\View\Components\Cn
 */
class Filters extends Component
{
    /**
     * Crea una nueva instancia del componente.
     */
    public function __construct(
        public string $action = '',
        public string $method = 'GET',
    ) {
    }

    /**
     * Renderiza el componente.
     */
    public function render(): View|Closure|string
    {
        return view('components.cn.filters');
    }
}
