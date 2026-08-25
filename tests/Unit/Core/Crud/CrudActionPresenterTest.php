<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\Contracts\CrudActionPresentationInterface;
use App\Core\Crud\Contracts\CrudActionPresenterInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudActionPresentation;
use App\Core\Crud\CrudActionPresenter;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use Tests\TestCase;

final class CrudActionPresenterTest extends TestCase
{
    public function test_implements_contract(): void
    {
        $presenter = new CrudActionPresenter();

        self::assertInstanceOf(
            CrudActionPresenterInterface::class,
            $presenter,
        );
    }

    public function test_presents_crud_actions_as_neutral_presentations(): void
    {
        $action = $this->createAction();

        $presenter = new CrudActionPresenter();

        $result = $presenter->present(
            [$action],
        );

        self::assertCount(1, $result);

        self::assertInstanceOf(
            CrudActionPresentationInterface::class,
            $result[0],
        );

        self::assertInstanceOf(
            CrudActionPresentation::class,
            $result[0],
        );

        self::assertSame(
            $action,
            $result[0]->action(),
        );
    }

    public function test_preserves_actions_and_order(): void
    {
        $first = $this->createAction();
        $second = $this->createAction();
        $third = $this->createAction();

        $presenter = new CrudActionPresenter();

        $result = $presenter->present(
            [$first, $second, $third],
        );

        self::assertCount(3, $result);

        self::assertSame(
            $first,
            $result[0]->action(),
        );

        self::assertSame(
            $second,
            $result[1]->action(),
        );

        self::assertSame(
            $third,
            $result[2]->action(),
        );
    }

    public function test_does_not_filter_actions(): void
    {
        $action = $this->createAction();

        $presenter = new CrudActionPresenter();

        $result = $presenter->present(
            [$action],
        );

        self::assertCount(1, $result);

        self::assertSame(
            $action,
            $result[0]->action(),
        );
    }

    public function test_presentation_is_neutral(): void
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
    }

    private function createAction(): CrudAction
    {
        $authorization = new class implements CrudActionAuthorizationInterface
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
