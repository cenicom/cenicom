<?php

namespace App\View\Components\UI;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $icon = null
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.ui.card');
    }
}