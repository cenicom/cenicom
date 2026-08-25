<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudActionFilter;
use App\Core\Crud\CrudActionPresenter;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Components\Cn\Crud\CrudActionView;
use Tests\TestCase;

final class CrudSecurityIntegrationTest extends TestCase
{
    public function test_authorized_actions_reach_gui_and_unauthorized_actions_do_not(): void
    {
        $identity = $this->createIdentity();

        $authorizedAction = $this->createAction(true);

        $unauthorizedAction = $this->createAction(false);

        $authorized = new CrudActionView(
            action: $authorizedAction,
            label: 'Editar institución',
            href: '/institutions/1/edit',
            variant: 'primary',
            size: 'md',
            icon: 'fas fa-edit',
        );

        $unauthorized = new CrudActionView(
            action: $unauthorizedAction,
            label: 'Eliminar institución',
            href: '/institutions/1/delete',
            variant: 'danger',
            size: 'sm',
            icon: 'fas fa-trash',
        );

        /*
         * Security boundary:
         * authorization is resolved before presentation.
         */
        $filter = new CrudActionFilter();

        $authorizedCrudActions = $filter->authorized(
            $identity,
            [
                $authorizedAction,
                $unauthorizedAction,
            ],
        );

        /*
         * Application layer maps authorized domain actions
         * to their presentation models.
         */
        $actions = array_values(
            array_filter(
                [
                    $authorized,
                    $unauthorized,
                ],
                static fn (CrudActionView $view): bool =>
                    in_array(
                        $view->action,
                        $authorizedCrudActions,
                        true,
                    ),
            ),
        );

        /*
         * Presentation layer does not authorize anything.
         */
        $presenter = new CrudActionPresenter();

        $actions = $presenter->present($actions);

        $view = $this->blade(
            <<<'BLADE'
<x-cn.crud.actions :actions="$actions" />
BLADE,
            [
                'actions' => $actions,
            ],
        );

        $view->assertSee('Editar institución');
        $view->assertSee('/institutions/1/edit', false);
        $view->assertSee('fa-edit', false);

        $view->assertDontSee('Eliminar institución');
        $view->assertDontSee('/institutions/1/delete', false);
        $view->assertDontSee('fa-trash', false);
    }

    public function test_authorization_is_resolved_before_gui_rendering(): void
    {
        $identity = $this->createIdentity();

        $authorizedAction = $this->createAction(true);

        $unauthorizedAction = $this->createAction(false);

        $authorized = new CrudActionView(
            action: $authorizedAction,
            label: 'Ver institución',
        );

        $unauthorized = new CrudActionView(
            action: $unauthorizedAction,
            label: 'Eliminar institución',
        );

        /*
         * Security layer.
         */
        $filter = new CrudActionFilter();

        $authorizedCrudActions = $filter->authorized(
            $identity,
            [
                $authorizedAction,
                $unauthorizedAction,
            ],
        );

        /*
         * Only authorized domain actions are allowed
         * to reach presentation.
         */
        $actions = array_values(
            array_filter(
                [
                    $authorized,
                    $unauthorized,
                ],
                static fn (CrudActionView $view): bool =>
                    in_array(
                        $view->action,
                        $authorizedCrudActions,
                        true,
                    ),
            ),
        );

        /*
         * Presentation layer.
         */
        $presenter = new CrudActionPresenter();

        $actions = $presenter->present($actions);

        self::assertCount(1, $actions);

        self::assertSame(
            'Ver institución',
            $actions[0]->label,
        );
    }

    private function createAction(bool $allowed): CrudAction
    {
        $authorization = new class ($allowed)
            implements CrudActionAuthorizationInterface
        {
            public function __construct(
                private bool $allowed,
            ) {
            }

            public function allows(
                IdentityInterface $identity,
                string $resource,
                CrudOperation $operation,
            ): bool {
                return $this->allowed;
            }
        };

        return new CrudAction(
            'institutions',
            new CrudOperation(CrudOperations::UPDATE),
            $authorization,
        );
    }

    private function createIdentity(): IdentityInterface
    {
        return new class implements IdentityInterface {
            public function id(): int|string|null
            {
                return 1;
            }

            public function name(): string
            {
                return 'Test User';
            }

            public function roles(): array
            {
                return [];
            }

            public function permissions(): array
            {
                return [];
            }

            public function can(string $permission): bool
            {
                return false;
            }

            public function authenticated(): bool
            {
                return true;
            }
        };
    }
}
