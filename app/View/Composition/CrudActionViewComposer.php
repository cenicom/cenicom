<?php

declare(strict_types=1);

namespace App\View\Composition;

use App\Core\Crud\Contracts\CrudActionPresentationInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Components\Cn\Crud\CrudActionView;
use App\View\Contracts\ViewAuthorizationInterface;
use App\View\Composition\Contracts\CrudActionViewComposerInterface;

final readonly class CrudActionViewComposer implements
    CrudActionViewComposerInterface
{
    public function __construct(
        private ViewAuthorizationInterface $authorization,
    ) {
    }

    /**
     * @param array<int, CrudActionPresentationInterface> $presentations
     *
     * @return array<int, CrudActionView>
     */
    public function compose(
        IdentityInterface $identity,
        array $presentations,
    ): array {
        $views = [];

        foreach ($presentations as $presentation) {
            $permission = sprintf(
                '%s.%s',
                $presentation->action()->resource(),
                $presentation->action()->operation()->name(),
            );

            if (! $this->authorization->can(
                $identity,
                $permission,
            )) {
                continue;
            }

            $views[] = new CrudActionView(
                action: $presentation->action(),
                label: $presentation->label(),
                href: $presentation->href(),
                variant: $presentation->variant(),
                size: $presentation->size(),
                icon: $presentation->icon(),
            );
        }

        return $views;
    }
}
