<?php

namespace App\View\Components\UI;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageHeader extends Component
{
    public function __construct(
        public string $title,
        public string $subtitle = '',
        public string $icon = ''
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.ui.page-header');
    }
}