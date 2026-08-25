<?php

declare(strict_types=1);

namespace Tests\Unit\View\Components\Cn\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Components\Cn\Crud\CrudActionView;
use Tests\TestCase;

final class CrudActionViewTest extends TestCase
{
    public function test_exposes_crud_action(): void
    {
        $action = $this->createCrudAction();

        $view = new CrudActionView(
            action: $action,
            label: 'Editar',
        );

        self::assertSame(
            $action,
            $view->action,
        );
    }

    public function test_exposes_label(): void
    {
        $view = new CrudActionView(
            action: $this->createCrudAction(),
            label: 'Editar institución',
        );

        self::assertSame(
            'Editar institución',
            $view->label,
        );
    }

    public function test_uses_default_presentation_values(): void
    {
        $view = new CrudActionView(
            action: $this->createCrudAction(),
            label: 'Editar',
        );

        self::assertNull($view->href);
        self::assertSame('primary', $view->variant);
        self::assertSame('md', $view->size);
        self::assertNull($view->icon);
    }

    public function test_exposes_custom_presentation_values(): void
    {
        $view = new CrudActionView(
            action: $this->createCrudAction(),
            label: 'Eliminar',
            href: '/institutions/1/delete',
            variant: 'danger',
            size: 'sm',
            icon: 'fas fa-trash',
        );

        self::assertSame(
            '/institutions/1/delete',
            $view->href,
        );

        self::assertSame(
            'danger',
            $view->variant,
        );

        self::assertSame(
            'sm',
            $view->size,
        );

        self::assertSame(
            'fas fa-trash',
            $view->icon,
        );
    }

    public function test_is_readonly(): void
    {
        $reflection = new \ReflectionClass(
            CrudActionView::class
        );

        self::assertTrue(
            $reflection->isReadOnly()
        );
    }

    private function createCrudAction(): CrudAction
    {
        $authorization = new class implements CrudActionAuthorizationInterface {
            public function allows(
                IdentityInterface $identity,
                string $resource,
                CrudOperation $operation,
            ): bool {
                return true;
            }
        };

        return new CrudAction(
            'institutions',
            new CrudOperation(CrudOperations::UPDATE),
            $authorization,
        );
    }
}
