<?php

declare(strict_types=1);

namespace App\View\Components\Cn;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * CN UI Framework
 *
 * Component: x-cn-col
 * Level: Structural
 * Version: 1.0.0
 *
 * Represents a responsive grid column.
 */
class Col extends Component
{
    public function __construct(
        public ?int $span = 12,
        public ?int $sm = null,
        public ?int $md = null,
        public ?int $lg = null,
        public ?int $xl = null,
        public ?int $xxl = null,
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.cn.col');
    }
}
