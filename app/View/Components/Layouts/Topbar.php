<?php

declare(strict_types=1);

namespace App\View\Components\Layouts;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Topbar extends Component
{
    public function render(): View
    {
        return view('components.layouts.topbar');
    }
}
