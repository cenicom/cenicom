<?php

declare(strict_types=1);

namespace App\View\Components\Cn\Crud;

use App\Core\Crud\Contracts\CrudActionPresentationInterface;
use App\View\Components\Cn\Crud\Contracts\CrudActionViewAdapterInterface;

final readonly class CrudActionViewAdapter implements
    CrudActionViewAdapterInterface
{
    public function adapt(
        CrudActionPresentationInterface $presentation,
    ): CrudActionView {
        return new CrudActionView(
            action: $presentation->action(),
            label: $presentation->label(),
            href: $presentation->href(),
            variant: $presentation->variant(),
            size: $presentation->size(),
            icon: $presentation->icon(),
        );
    }
}
