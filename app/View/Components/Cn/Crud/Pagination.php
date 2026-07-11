<?php

declare(strict_types=1);

namespace App\View\Components\Cn\Crud;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * -----------------------------------------------------------------------------
 * CENICOM ERP
 * CN UI Framework
 * -----------------------------------------------------------------------------
 *
 * ID          : CN-CRUD-006
 * Componente  : x-cn.pagination
 * Categoría   : CRUD
 * Versión     : 1.0.0
 * Estado      : Gold Standard
 *
 * Responsabilidad:
 * Renderiza la navegación de un paginador utilizando el estilo
 * del CN UI Framework.
 *
 * @package App\View\Components\Cn
 */
class Pagination extends Component
{
    /**
     * Crea una nueva instancia del componente.
     */
    public function __construct(
        public LengthAwarePaginator|Paginator $paginator,
    ) {
    }

    /**
     * Renderiza el componente.
     */
    public function render(): View|Closure|string
    {
        return view('components.cn.pagination');
    }
}
