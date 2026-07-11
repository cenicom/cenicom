<?php

/**
 * -----------------------------------------------------------------------------
 * CENICOM ERP
 * -----------------------------------------------------------------------------
 * Framework : Laravel
 * Módulo    : CN UI Framework
 * Componente: Crud
 *
 * Copyright (C) CENICOM
 * -----------------------------------------------------------------------------
 */


declare(strict_types=1);

namespace App\View\Components\Cn\Crud;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Crud extends Component
{
    /**
     * Crea una nueva instancia del componente.
     */
    public function __construct(
        public ?string $title = null,
        public ?string $subtitle = null,
        public ?string $icon = null,
        public bool $fluid = false,
    ) {
    }

    /**
     * Renderiza el componente.
     */
    public function render(): View|Closure|string
    {
        return view('components.cn.crud');
    }
}
