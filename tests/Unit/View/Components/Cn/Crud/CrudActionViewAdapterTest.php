<?php

declare(strict_types=1);

namespace Tests\Unit\View\Components\Cn\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\Contracts\CrudActionPresentationInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudActionPresentation;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Components\Cn\Crud\Contracts\CrudActionViewAdapterInterface;
use App\View\Components\Cn\Crud\CrudActionView;
use App\View\Components\Cn\Crud\CrudActionViewAdapter;
use Tests\TestCase;

final class CrudActionViewAdapterTest extends TestCase
{
    public function test_implements_adapter_contract(): void
    {
        self::assertInstanceOf(
            CrudActionViewAdapterInterface::class,
            new CrudActionViewAdapter(),
        );
    }

    public function test_adapts_neutral_presentation_to_gui_view(): void
    {
        $action = $this->createAction();

        $presentation = new CrudActionPresentation(
            action: $action,
            label: 'Editar institución',
            href: '/institutions/1/edit',
            variant: 'primary',
            size: 'md',
            icon: 'fas fa-edit',
        );

        $view = (new CrudActionViewAdapter())->adapt($presentation);

        self::assertInstanceOf(
            CrudActionView::class,
            $view,
        );

        self::assertSame($action, $view->action);
        self::assertSame('Editar institución', $view->label);
        self::assertSame('/institutions/1/edit', $view->href);
        self::assertSame('primary', $view->variant);
        self::assertSame('md', $view->size);
        self::assertSame('fas fa-edit', $view->icon);
    }

    public function test_preserves_nullable_presentation_values(): void
    {
        $presentation = new CrudActionPresentation(
            action: $this->createAction(),
            label: 'Ver institución',
            href: null,
            variant: 'secondary',
            size: 'sm',
            icon: null,
        );

        $view = (new CrudActionViewAdapter())->adapt($presentation);

        self::assertNull($view->href);
        self::assertNull($view->icon);
    }

    public function test_accepts_only_neutral_presentation_contract(): void
    {
        $reflection = new \ReflectionMethod(
            CrudActionViewAdapter::class,
            'adapt',
        );

        $parameters = $reflection->getParameters();

        self::assertCount(1, $parameters);

        self::assertSame(
            CrudActionPresentationInterface::class,
            (string) $parameters[0]->getType(),
        );

        self::assertSame(
            CrudActionView::class,
            (string) $reflection->getReturnType(),
        );
    }

    private function createAction(): CrudAction
    {
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
            new CrudOperation(CrudOperations::UPDATE),
            $authorization,
        );
    }
}
