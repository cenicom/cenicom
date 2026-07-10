<?php

declare(strict_types=1);

namespace App\View\Components\Cn\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    /**
     * Crea una nueva instancia del componente.
     */
    public function __construct(
        public string $name,
        public ?string $id = null,
        public string $type = 'text',
        public mixed $value = null,
        public ?string $placeholder = null,
        public ?string $autocomplete = null,
        public bool $required = false,
        public bool $readonly = false,
        public bool $disabled = false,
        public bool $autofocus = false,
        public ?string $min = null,
        public ?string $max = null,
        public ?string $step = null,
        public ?int $minlength = null,
        public ?int $maxlength = null,
    ) {
        $this->id ??= $this->name;
    }

    /**
     * Renderiza el componente.
     */
    public function render(): View|Closure|string
    {
        return view('components.cn.forms.input');
    }
}
