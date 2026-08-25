<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Components\Cn\Crud\CrudActionView;
use Tests\TestCase;

final class ActionsComponentTest extends TestCase
{
    public function test_does_not_render_when_empty(): void
    {
        $view = $this->blade(
            '<x-cn.crud.actions />'
        );

        $view->assertDontSee(
            'cn-crud__actions',
            false
        );
    }

    public function test_renders_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.crud.actions>
                <span>Editar</span>
            </x-cn.crud.actions>'
        );

        $view->assertSee('Editar');
        $view->assertSee(
            'cn-crud__actions',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $action = $this->createActionView(
            label: 'Editar'
        );

        $view = $this->blade(
            '<x-cn.crud.actions
                :actions="$actions"
                id="crud-actions"
            />',
            [
                'actions' => [$action],
            ],
        );

        $view->assertSee(
            'id="crud-actions"',
            false
        );
    }

    public function test_preserves_base_class_with_custom_class(): void
    {
        $action = $this->createActionView(
            label: 'Editar'
        );

        $view = $this->blade(
            '<x-cn.crud.actions
                :actions="$actions"
                class="custom-actions"
            />',
            [
                'actions' => [$action],
            ],
        );

        $view->assertSee(
            'cn-crud__actions',
            false
        );

        $view->assertSee(
            'custom-actions',
            false
        );
    }

    public function test_renders_crud_action_view(): void
    {
        $action = new CrudActionView(
            action: $this->createCrudAction(),
            label: 'Editar institución',
            href: '/institutions/1/edit',
            variant: 'primary',
            size: 'sm',
            icon: 'fas fa-edit',
        );

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => [$action],
            ],
        );

        $view->assertSee('Editar institución');
        $view->assertSee('/institutions/1/edit', false);
        $view->assertSee('cn-button--primary', false);
        $view->assertSee('cn-button--sm', false);
        $view->assertSee('fas fa-edit', false);
    }

    public function test_renders_action_href(): void
    {
        $action = $this->createActionView(
            label: 'Editar',
            href: '/institutions/1/edit',
        );

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => [$action],
            ],
        );

        $view->assertSee(
            'href="/institutions/1/edit"',
            false
        );
    }

    public function test_renders_action_variant_and_size(): void
    {
        $action = $this->createActionView(
            label: 'Eliminar',
            variant: 'danger',
            size: 'sm',
        );

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => [$action],
            ],
        );

        $view->assertSee(
            'cn-button--danger',
            false
        );

        $view->assertSee(
            'cn-button--sm',
            false
        );
    }

    public function test_renders_action_icon(): void
    {
        $action = $this->createActionView(
            label: 'Eliminar',
            icon: 'fas fa-trash',
        );

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => [$action],
            ],
        );

        $view->assertSee(
            'fas fa-trash',
            false
        );
    }

    public function test_renders_multiple_crud_actions(): void
    {
        $actions = [
            new CrudActionView(
                action: $this->createCrudAction(),
                label: 'Editar',
                href: '/institutions/1/edit',
                icon: 'fas fa-edit',
            ),
            new CrudActionView(
                action: $this->createCrudAction(),
                label: 'Eliminar',
                href: '/institutions/1/delete',
                variant: 'danger',
                icon: 'fas fa-trash',
            ),
        ];

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            compact('actions'),
        );

        $view->assertSee('Editar');
        $view->assertSee('Eliminar');
        $view->assertSee('fas fa-edit', false);
        $view->assertSee('fas fa-trash', false);
    }

    public function test_renders_actions_and_slot_together(): void
    {
        $actions = [
            new CrudActionView(
                action: $this->createCrudAction(),
                label: 'Editar',
            ),
        ];

        $view = $this->blade(
            <<<'BLADE'
<x-cn.crud.actions :actions="$actions">
    <button type="button">Acción adicional</button>
</x-cn.crud.actions>
BLADE,
            compact('actions'),
        );

        $view->assertSee('Editar');
        $view->assertSee('Acción adicional');
    }

    private function createActionView(
        string $label,
        ?string $href = null,
        string $variant = 'primary',
        string $size = 'md',
        ?string $icon = null,
    ): CrudActionView {
        return new CrudActionView(
            action: $this->createCrudAction(),
            label: $label,
            href: $href,
            variant: $variant,
            size: $size,
            icon: $icon,
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
