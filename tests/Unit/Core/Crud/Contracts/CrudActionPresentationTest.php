<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud\Contracts;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\Contracts\CrudActionPresentationInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudActionPresentation;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use Tests\TestCase;

final class CrudActionPresentationTest extends TestCase
{
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


    public function test_implements_presentation_contract(): void
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

        self::assertInstanceOf(
            CrudActionPresentationInterface::class,
            $presentation,
        );
    }

    public function test_exposes_presentation_values(): void
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

        self::assertSame('Editar institución', $presentation->label());
        self::assertSame('/institutions/1/edit', $presentation->href());
        self::assertSame('primary', $presentation->variant());
        self::assertSame('md', $presentation->size());
        self::assertSame('fas fa-edit', $presentation->icon());
    }

    public function test_allows_nullable_href_and_icon(): void
    {
        $action = $this->createAction();

        $presentation = new CrudActionPresentation(
            action: $action,
            label: 'Ver institución',
            href: null,
            variant: 'secondary',
            size: 'sm',
            icon: null,
        );

        self::assertNull($presentation->href());
        self::assertNull($presentation->icon());
    }

    public function test_does_not_depend_on_gui_types(): void
    {
        $reflection = new \ReflectionClass(
            CrudActionPresentation::class,
        );

        foreach ($reflection->getMethods() as $method) {
            $returnType = $method->getReturnType();

            if ($returnType === null) {
                continue;
            }

            self::assertStringNotContainsString(
                'App\\View',
                (string) $returnType,
            );
        }

        foreach ($reflection->getConstructor()?->getParameters() ?? [] as $parameter) {
            $type = $parameter->getType();

            if ($type === null) {
                continue;
            }

            self::assertStringNotContainsString(
                'App\\View',
                (string) $type,
            );
        }
    }
}
