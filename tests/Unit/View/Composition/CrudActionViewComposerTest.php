<?php

declare(strict_types=1);

namespace Tests\Unit\View\Composition;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\Contracts\CrudActionPresentationInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudActionPresentation;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Composition\CrudActionViewComposer;
use App\View\Components\Cn\Crud\CrudActionView;
use App\View\Contracts\ViewAuthorizationInterface;
use Tests\TestCase;

final class CrudActionViewComposerTest extends TestCase
{
    public function test_implements_contract(): void
    {
        $composer = new CrudActionViewComposer(
            $this->createMock(ViewAuthorizationInterface::class),
        );

        self::assertInstanceOf(
            \App\View\Composition\Contracts\CrudActionViewComposerInterface::class,
            $composer,
        );
    }

    public function test_composes_allowed_presentation_into_view_action(): void
    {
        $identity = $this->createMock(IdentityInterface::class);

        $authorization = $this->createMock(
            ViewAuthorizationInterface::class,
        );

        $authorization
            ->expects($this->once())
            ->method('can')
            ->with(
                $identity,
                'institutions.update',
            )
            ->willReturn(true);

        $presentation = $this->createPresentation(
            operation: CrudOperations::UPDATE,
            label: 'Editar institución',
            href: '/institutions/1/edit',
        );

        $composer = new CrudActionViewComposer(
            $authorization,
        );

        $result = $composer->compose(
            $identity,
            [$presentation],
        );

        self::assertCount(1, $result);
        self::assertInstanceOf(
            CrudActionView::class,
            $result[0],
        );

        self::assertSame(
            $presentation->action(),
            $result[0]->action,
        );

        self::assertSame(
            'Editar institución',
            $result[0]->label,
        );

        self::assertSame(
            '/institutions/1/edit',
            $result[0]->href,
        );
    }

    public function test_omits_presentation_when_permission_is_denied(): void
    {
        $identity = $this->createMock(IdentityInterface::class);

        $authorization = $this->createMock(
            ViewAuthorizationInterface::class,
        );

        $authorization
            ->expects($this->once())
            ->method('can')
            ->with(
                $identity,
                'institutions.delete',
            )
            ->willReturn(false);

        $presentation = $this->createPresentation(
            operation: CrudOperations::DELETE,
            label: 'Eliminar institución',
        );

        $composer = new CrudActionViewComposer(
            $authorization,
        );

        $result = $composer->compose(
            $identity,
            [$presentation],
        );

        self::assertSame([], $result);
    }

    public function test_preserves_order_of_allowed_presentations(): void
    {
        $identity = $this->createMock(IdentityInterface::class);

        $authorization = $this->createMock(
            ViewAuthorizationInterface::class,
        );

        $authorization
            ->expects($this->exactly(3))
            ->method('can')
            ->willReturnMap([
                [$identity, 'institutions.view', true],
                [$identity, 'institutions.update', true],
                [$identity, 'institutions.delete', true],
            ]);

        $first = $this->createPresentation(
            operation: CrudOperations::VIEW,
            label: 'Ver',
        );

        $second = $this->createPresentation(
            operation: CrudOperations::UPDATE,
            label: 'Editar',
        );

        $third = $this->createPresentation(
            operation: CrudOperations::DELETE,
            label: 'Eliminar',
        );

        $composer = new CrudActionViewComposer(
            $authorization,
        );

        $result = $composer->compose(
            $identity,
            [$first, $second, $third],
        );

        self::assertCount(3, $result);

        self::assertSame(
            'Ver',
            $result[0]->label,
        );

        self::assertSame(
            'Editar',
            $result[1]->label,
        );

        self::assertSame(
            'Eliminar',
            $result[2]->label,
        );
    }

    public function test_keeps_only_allowed_presentations(): void
    {
        $identity = $this->createMock(IdentityInterface::class);

        $authorization = $this->createMock(
            ViewAuthorizationInterface::class,
        );

        $authorization
            ->expects($this->exactly(3))
            ->method('can')
            ->willReturnMap([
                [$identity, 'institutions.view', true],
                [$identity, 'institutions.update', false],
                [$identity, 'institutions.delete', true],
            ]);

        $view = $this->createPresentation(
            operation: CrudOperations::VIEW,
            label: 'Ver',
        );

        $update = $this->createPresentation(
            operation: CrudOperations::UPDATE,
            label: 'Editar',
        );

        $delete = $this->createPresentation(
            operation: CrudOperations::DELETE,
            label: 'Eliminar',
        );

        $composer = new CrudActionViewComposer(
            $authorization,
        );

        $result = $composer->compose(
            $identity,
            [$view, $update, $delete],
        );

        self::assertCount(2, $result);

        self::assertSame(
            'Ver',
            $result[0]->label,
        );

        self::assertSame(
            'Eliminar',
            $result[1]->label,
        );
    }

    private function createPresentation(
        string $operation,
        string $label,
        ?string $href = null,
    ): CrudActionPresentationInterface {
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

        return new CrudActionPresentation(
            action: new CrudAction(
                'institutions',
                new CrudOperation($operation),
                $authorization,
            ),
            label: $label,
            href: $href,
            variant: 'primary',
            size: 'md',
            icon: null,
        );
    }
}
