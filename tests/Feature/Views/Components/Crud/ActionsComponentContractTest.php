<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Components\Cn\Crud\Actions;
use App\View\Components\Cn\Crud\CrudActionView;
use Tests\TestCase;

final class ActionsComponentContractTest extends TestCase
{
    public function test_accepts_crud_action_views(): void
    {
        $action = $this->createCrudActionView();

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => [$action],
            ],
        );

        $view->assertSee('Editar');
    }

    public function test_rejects_invalid_action_objects(): void
    {
        $this->expectException(\Illuminate\View\ViewException::class);

        $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => [new \stdClass()],
            ],
        );
    }

    public function test_rejects_invalid_scalar_actions(): void
    {
        $this->expectException(\Illuminate\View\ViewException::class);

        $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => ['Editar'],
            ],
        );
    }

    public function test_accepts_empty_actions(): void
    {
        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => [],
            ],
        );

        $view->assertDontSee(
            'cn-crud__actions',
            false,
        );
    }

    public function test_preserves_crud_action_view_identity(): void
    {
        $action = $this->createCrudActionView();

        $component = new Actions(
            actions: [$action],
        );

        self::assertSame(
            $action,
            $component->actions[0],
        );
    }

    private function createCrudActionView(): CrudActionView
    {
        return new CrudActionView(
            action: $this->createCrudAction(),
            label: 'Editar',
            href: '/institutions/1/edit',
            variant: 'primary',
            size: 'md',
            icon: 'fas fa-edit',
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
