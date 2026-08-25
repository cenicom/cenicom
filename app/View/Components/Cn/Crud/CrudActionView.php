<?php

declare(strict_types=1);

namespace App\View\Components\Cn\Crud;

use App\Core\Crud\CrudAction;

final readonly class CrudActionView
{
    public function __construct(
        public CrudAction $action,
        public string $label,
        public ?string $href = null,
        public string $variant = 'primary',
        public string $size = 'md',
        public ?string $icon = null,
    ) {
    }
}
