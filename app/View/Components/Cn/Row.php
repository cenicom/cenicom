<?php

declare(strict_types=1);

namespace App\View\Components\Cn;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Row extends Component
{
    public function __construct(
        public string $spacing = 'normal',
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.row');
    }
}
