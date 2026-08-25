<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Crud\Contracts\CrudActionPresentationInterface;

final readonly class CrudActionPresentation implements
    CrudActionPresentationInterface
{
    public function __construct(
        private CrudAction $action,
        private string $label,
        private ?string $href,
        private string $variant,
        private string $size,
        private ?string $icon,
    ) {
    }

    public function action(): CrudAction
    {
        return $this->action;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function href(): ?string
    {
        return $this->href;
    }

    public function variant(): string
    {
        return $this->variant;
    }

    public function size(): string
    {
        return $this->size;
    }

    public function icon(): ?string
    {
        return $this->icon;
    }
}
