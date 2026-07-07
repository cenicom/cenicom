<?php

namespace App\View\Components\UI;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public function __construct(
        public string $variant = 'primary',
        public string $size = 'md',
        public string $icon = '',
        public string $type = 'button',
        public bool $disabled = false
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.ui.button');
    }
}