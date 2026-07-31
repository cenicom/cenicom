<?php

declare(strict_types=1);

namespace App\Core\Contracts\Module\Bootstrap;

interface StageResultInterface
{
    public function successful(): bool;

    public function failed(): bool;

    public function data(): mixed;

    public function message(): ?string;

    public function warnings(): array;

    public function errors(): array;
}
