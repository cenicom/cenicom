<?php

declare(strict_types=1);

namespace App\View\Components\Cn;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Field extends Component
{
    public function __construct(
        public string $label = '',
        public bool $required = false,
        public ?string $help = null,
        public ?string $error = null,
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.field');
    }
}
