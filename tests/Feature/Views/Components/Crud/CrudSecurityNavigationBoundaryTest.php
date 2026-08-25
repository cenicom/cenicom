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

final class CrudSecurityNavigationBoundaryTest extends TestCase
{
    private function presentActions(
        IdentityInterface $identity,
        array $views,
    ): array {
        $filter = new CrudActionFilter();

        $crudActions = array_map(
            static fn(CrudActionView $view): CrudAction =>
            $view->action,
            $views,
        );

        $authorizedActions = $filter->authorized(
            $identity,
            $crudActions,
        );

        return array_values(
            array_filter(
                $views,
                static fn(CrudActionView $view): bool =>
                in_array(
                    $view->action,
                    $authorizedActions,
                    true,
                ),
            ),
        );
    }
    public function test_authorized_crud_action_reaches_gui(): void
    {
        $identity = $this->createIdentity();

        $authorized = new CrudActionView(
            action: $this->createAction(true),
            label: 'Editar institución',
            href: '/institutions/1/edit',
            variant: 'primary',
            size: 'md',
            icon: 'fas fa-edit',
        );

        $actions = $this->presentActions(
            $identity,
            [$authorized],
        );

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => $actions,
            ],
        );

        $view->assertSee('Editar institución');
        $view->assertSee('/institutions/1/edit', false);
        $view->assertSee('fa-edit', false);
    }

    public function test_unauthorized_crud_action_never_reaches_gui(): void
    {
        $identity = $this->createIdentity();

        $unauthorized = new CrudActionView(
            action: $this->createAction(false),
            label: 'Eliminar institución',
            href: '/institutions/1/delete',
            variant: 'danger',
            size: 'sm',
            icon: 'fas fa-trash',
        );

        $actions = $this->presentActions(
            $identity,
            [$unauthorized],
        );

        self::assertSame([], $actions);

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => $actions,
            ],
        );

        $view->assertDontSee('Eliminar institución');
        $view->assertDontSee('/institutions/1/delete', false);
        $view->assertDontSee('fa-trash', false);
    }

    public function test_navigation_does_not_grant_crud_authorization(): void
    {
        $identity = $this->createIdentity();

        // La navegación puede exponer el recurso...
        $navigationResource = 'institutions';

        self::assertSame(
            'institutions',
            $navigationResource,
        );

        // ...pero no concede automáticamente la operación CRUD.
        $action = new CrudActionView(
            action: $this->createAction(false),
            label: 'Eliminar institución',
        );

        $actions = $this->presentActions(
            $identity,
            [$action],
        );

        self::assertSame([], $actions);
    }

    public function test_gui_receives_only_filtered_crud_action_views(): void
    {
        $identity = $this->createIdentity();

        $authorized = new CrudActionView(
            action: $this->createAction(true),
            label: 'Ver institución',
        );

        $unauthorized = new CrudActionView(
            action: $this->createAction(false),
            label: 'Eliminar institución',
        );

        $filter = new CrudActionFilter();

        $filtered = $filter->authorized(
            $identity,
            [
                $authorized->action,
                $unauthorized->action,
            ],
        );

        $presented = array_map(
            static fn(CrudAction $action): CrudActionView =>
            $action === $authorized->action
                ? $authorized
                : $unauthorized,
            $filtered,
        );

        $presenter = new CrudActionPresenter();

        $actions = $presenter->present($presented);

        self::assertCount(1, $actions);
        self::assertInstanceOf(
            CrudActionView::class,
            $actions[0],
        );

        self::assertSame(
            'Ver institución',
            $actions[0]->label,
        );
    }

    private function createAction(bool $allowed): CrudAction
    {
        $authorization = new class($allowed)
        implements CrudActionAuthorizationInterface
        {
            public function __construct(
                private bool $allowed,
            ) {}

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
