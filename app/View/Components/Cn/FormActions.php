<?php

declare(strict_types=1);

namespace App\View\Components\Cn;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormActions extends Component
{
    public function __construct(
        public string $align = 'end',
        public string $spacing = 'md',
        public ?string $class = null,
    ) {
    }


    public function render(): View|Closure|string
    {
        return view('components.cn.form-actions');
    }
}
