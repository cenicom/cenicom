<?php

declare(strict_types=1);

namespace Tests\Unit\View\Components\Cn\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Components\Cn\Crud\CrudActionView;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

final class ActionsRenderingTest extends TestCase
{
    public function test_renders_allowed_view_actions(): void
    {
        $view = new CrudActionView(
            action: $this->createCrudAction(
                CrudOperations::UPDATE,
            ),
            label: 'Editar institución',
            href: '/institutions/1/edit',
            variant: 'primary',
            size: 'md',
            icon: 'fas fa-edit',
        );

        $html = Blade::render(
            '<x-cn.crud.actions :actions="$actions" />',
            ['actions' => [$view]],
        );

        self::assertStringContainsString(
            'Editar institución',
            $html,
        );

        self::assertStringContainsString(
            '/institutions/1/edit',
            $html,
        );
    }

    public function test_renders_only_received_actions(): void
    {
        $allowed = new CrudActionView(
            action: $this->createCrudAction(
                CrudOperations::VIEW,
            ),
            label: 'Ver',
            href: '/institutions/1',
        );

        $html = Blade::render(
            '<x-cn.crud.actions :actions="$actions" />',
            ['actions' => [$allowed]],
        );

        self::assertStringContainsString(
            'Ver',
            $html,
        );

        self::assertStringNotContainsString(
            'Editar',
            $html,
        );

        self::assertStringNotContainsString(
            'Eliminar',
            $html,
        );
    }

    public function test_does_not_render_authorization_controls(): void
    {
        $view = new CrudActionView(
            action: $this->createCrudAction(
                CrudOperations::DELETE,
            ),
            label: 'Eliminar',
        );

        $html = Blade::render(
            '<x-cn.crud.actions :actions="$actions" />',
            ['actions' => [$view]],
        );

        self::assertStringContainsString(
            'Eliminar',
            $html,
        );

        self::assertStringNotContainsString(
            'authorized',
            strtolower($html),
        );

        self::assertStringNotContainsString(
            'permission',
            strtolower($html),
        );

        self::assertStringNotContainsString(
            'authorization',
            strtolower($html),
        );
    }

    private function createCrudAction(
        string $operation,
    ): CrudAction {
        $authorization = new class
            implements CrudActionAuthorizationInterface
        {
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
            new CrudOperation($operation),
            $authorization,
        );
    }
}
