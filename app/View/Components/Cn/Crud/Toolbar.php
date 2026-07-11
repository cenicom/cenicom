<?php

/**
 * -----------------------------------------------------------------------------
 * CENICOM ERP
 * -----------------------------------------------------------------------------
 * Framework : Laravel
 * Módulo    : CN UI Framework
 * Componente: Toolbar
 *
 * Copyright (C) CENICOM
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace App\View\Components\Cn\Crud;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Toolbar extends Component
{
    /**
     * Crea una nueva instancia del componente.
     */
    public function __construct(
        public string $justify = 'between',
        public bool $wrap = true,
        public bool $responsive = true,
    ) {
    }

    /**
     * Renderiza el componente.
     */
    public function render(): View|Closure|string
    {
        return view('components.cn.toolbar');
    }
}
