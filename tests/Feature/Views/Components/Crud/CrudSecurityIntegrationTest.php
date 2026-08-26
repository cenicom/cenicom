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
use App\View\Components\Cn\Crud\CrudActionViewAdapter;
use Tests\TestCase;

final class CrudSecurityIntegrationTest extends TestCase
{
    public function test_authorized_actions_reach_gui_and_unauthorized_actions_do_not(): void
    {
        $identity = $this->createIdentity();

        $authorizedAction = $this->createAction(true);
        $unauthorizedAction = $this->createAction(false);

        /*
         * Security boundary:
         * authorization is resolved before presentation.
         */
        $filter = new CrudActionFilter();

        $filtered = $filter->authorized(
            $identity,
            [
                $authorizedAction,
                $unauthorizedAction,
            ],
        );

        self::assertCount(1, $filtered);

        self::assertSame(
            $authorizedAction,
            $filtered[0],
        );

        /*
         * Presentation layer:
         * only authorized domain actions reach the Presenter.
         */
        $presenter = new CrudActionPresenter();

        $presentations = $presenter->present(
            $filtered,
        );

        self::assertCount(1, $presentations);

        self::assertSame(
            $authorizedAction,
            $presentations[0]->action(),
        );

        self::assertSame(
            'Editar',
            $presentations[0]->label(),
        );

        /*
         * GUI boundary:
         * Presentation models are adapted to GUI view models.
         */
        $adapter = new CrudActionViewAdapter();

        $actions = array_map(
            static fn ($presentation): CrudActionView =>
                $adapter->adapt($presentation),
            $presentations,
        );

        self::assertCount(1, $actions);

        self::assertInstanceOf(
            CrudActionView::class,
            $actions[0],
        );

        $view = $this->blade(
            <<<'BLADE'
<x-cn.crud.actions :actions="$actions" />
BLADE,
            [
                'actions' => $actions,
            ],
        );

        $view->assertSee('Editar');

        $view->assertSee(
            'cn-button--primary',
            false,
        );

        $view->assertSee(
            'cn-button--md',
            false,
        );

        $view->assertDontSee('Eliminar');
    }

    public function test_authorization_is_resolved_before_gui_rendering(): void
    {
        $identity = $this->createIdentity();

        $authorizedAction = $this->createAction(true);
        $unauthorizedAction = $this->createAction(false);

        /*
         * Security layer:
         * unauthorized actions are removed before presentation.
         */
        $filter = new CrudActionFilter();

        $filtered = $filter->authorized(
            $identity,
            [
                $authorizedAction,
                $unauthorizedAction,
            ],
        );

        self::assertCount(1, $filtered);

        self::assertSame(
            $authorizedAction,
            $filtered[0],
        );

        /*
         * Presentation layer:
         * only authorized CrudAction instances are presented.
         */
        $presenter = new CrudActionPresenter();

        $presentations = $presenter->present(
            $filtered,
        );

        self::assertCount(1, $presentations);

        self::assertSame(
            $authorizedAction,
            $presentations[0]->action(),
        );

        self::assertSame(
            'Editar',
            $presentations[0]->label(),
        );

        /*
         * GUI boundary:
         * presentation models are adapted only after authorization
         * and presentation have completed.
         */
        $adapter = new CrudActionViewAdapter();

        $actions = array_map(
            static fn ($presentation): CrudActionView =>
                $adapter->adapt($presentation),
            $presentations,
        );

        self::assertCount(1, $actions);

        self::assertInstanceOf(
            CrudActionView::class,
            $actions[0],
        );

        self::assertSame(
            $authorizedAction,
            $actions[0]->action,
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
            new CrudOperation(
                CrudOperations::UPDATE,
            ),
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
