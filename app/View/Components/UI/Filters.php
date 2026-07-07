<?php

namespace App\View\Components\UI;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Filters extends Component
{
    public function __construct(
        public bool $collapsible = false,
        public bool $collapsed = false,
        public string $title = 'Filtros'
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.ui.filters');
    }
}