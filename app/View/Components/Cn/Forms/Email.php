<?php

declare(strict_types=1);

namespace App\View\Components\Cn\Forms;

use Closure;
use Illuminate\Contracts\View\View;

class Email extends Input
{
    /**
     * Renderiza el componente.
     */
    public function render(): View|Closure|string
    {
        return view('components.cn.forms.email');
    }
}
