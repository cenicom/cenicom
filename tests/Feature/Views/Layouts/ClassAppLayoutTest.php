<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Layouts;

use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Security\Contracts\IdentityInterface;
use Tests\TestCase;

final class ClassAppLayoutTest extends TestCase
{
    public function test_renders_class_based_application_layout(): void
    {
        // Arrange

        $identity = $this->createIdentity();

        $this->app->instance(
            IdentityInterface::class,
            $identity
        );

        $this->app->instance(
            NavigationServiceInterface::class,
            new class implements NavigationServiceInterface {
                public function tree(
                    IdentityInterface $identity
                ): NavigationTreeData {
                    return new NavigationTreeData();
                }
            }
        );

        // Act

        $view = $this->blade(
            '<x-layout.app>
                <x-slot:title>
                    Currency
                </x-slot:title>

                <div>Contenido de prueba</div>
            </x-layout.app>'
        );

        // Assert

        $view->assertSee('<!DOCTYPE html>', false);
        $view->assertSee('<html', false);
        $view->assertSee('<head>', false);
        $view->assertSee('<title>', false);
        $view->assertSee('Currency');
        $view->assertSee('<body>', false);

        $view->assertSee('cn-app', false);
        $view->assertSee('cn-sidebar', false);
        $view->assertSee('cn-main', false);
        $view->assertSee('cn-topbar', false);

        $view->assertSee('Contenido de prueba');
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

            public function can(
                string $permission
            ): bool {
                return false;
            }

            public function authenticated(): bool
            {
                return true;
            }
        };
    }
}
