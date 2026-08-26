<?php

declare(strict_types=1);

namespace App\View\Composition\Contracts;

use App\Core\Crud\Contracts\CrudActionPresentationInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Components\Cn\Crud\CrudActionView;

interface CrudActionViewComposerInterface
{
    /**
     * Compone acciones visuales autorizadas para una identidad.
     *
     * @param array<int, CrudActionPresentationInterface> $presentations
     *
     * @return array<int, CrudActionView>
     */
    public function compose(
        IdentityInterface $identity,
        array $presentations,
    ): array;
}
