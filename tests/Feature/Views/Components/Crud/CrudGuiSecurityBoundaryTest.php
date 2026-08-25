<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudActionFilter;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Components\Cn\Crud\CrudActionView;
use Tests\TestCase;

final class CrudGuiSecurityBoundaryTest extends TestCase
{
    public function test_gui_accepts_only_crud_action_views(): void
    {
        $action = $this->createAction(true);

        $view = new CrudActionView(
            action: $action,
            label: 'Editar institución',
        );

        $rendered = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => [$view],
            ],
        );

        $rendered->assertSee('Editar institución');
    }

    public function test_gui_rejects_raw_crud_action(): void
    {
        $this->expectException(\Illuminate\View\ViewException::class);

        $this->expectExceptionMessage(
            'CRUD action must be an instance of ' . CrudActionView::class
        );

        $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => [
                    $this->createAction(true),
                ],
            ],
        )->render();
    }

    public function test_gui_rejects_arbitrary_objects(): void
    {
        $this->expectException(\Illuminate\View\ViewException::class);

        $this->expectExceptionMessage(
            'CRUD action must be an instance of ' . CrudActionView::class
        );

        $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => [
                    new \stdClass(),
                ],
            ],
        )->render();
    }

    public function test_gui_cannot_create_an_authorized_action_from_unfiltered_input(): void
    {
        $identity = $this->createIdentity();

        $unauthorized = $this->createAction(false);

        $filter = new CrudActionFilter();

        $authorizedActions = $filter->authorized(
            $identity,
            [$unauthorized],
        );

        self::assertSame([], $authorizedActions);

        $actions = [];

        foreach ($authorizedActions as $action) {
            $actions[] = new CrudActionView(
                action: $action,
                label: 'Acción no autorizada',
            );
        }

        $rendered = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => $actions,
            ],
        );

        $rendered->assertDontSee('Acción no autorizada');
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
