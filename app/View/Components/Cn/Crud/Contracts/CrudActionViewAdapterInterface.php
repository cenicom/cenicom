<?php

declare(strict_types=1);

namespace App\View\Components\Cn\Crud\Contracts;

use App\Core\Crud\Contracts\CrudActionPresentationInterface;
use App\View\Components\Cn\Crud\CrudActionView;

interface CrudActionViewAdapterInterface
{
    public function adapt(
        CrudActionPresentationInterface $presentation,
    ): CrudActionView;
}
