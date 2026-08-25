<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

use App\Core\Crud\CrudAction;

interface CrudActionPresentationInterface
{
    public function action(): CrudAction;

    public function label(): string;

    public function href(): ?string;

    public function variant(): string;

    public function size(): string;

    public function icon(): ?string;
}
