<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Layouts;

use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Security\Contracts\IdentityInterface;
use Tests\TestCase;

final class AppLayoutTest extends TestCase
{
    public function test_renders_application_layout(): void
    {
        // Arrange

        $this->app->instance(
            IdentityInterface::class,
            $this->createIdentity()
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
            '<x-layouts.app>
                <h1>CENICOM ERP</h1>
            </x-layouts.app>'
        );

        // Assert

        $view->assertSee('<!DOCTYPE html>', false);
        $view->assertSee('<html', false);
        $view->assertSee('<head>', false);
        $view->assertSee('<body>', false);
        $view->assertSee('CENICOM ERP');
    }

    public function test_renders_application_navigation_sidebar(): void
    {
        // Arrange

        $this->app->instance(
            IdentityInterface::class,
            $this->createIdentity()
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
            '<x-layouts.app>
                <main>Contenido</main>
            </x-layouts.app>'
        );

        // Assert

        $view->assertSee('cn-sidebar', false);
        $view->assertSee('<nav>', false);
        $view->assertSee('Contenido');
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

    public function test_renders_application_topbar(): void
    {
        // Arrange

        $this->app->instance(
            IdentityInterface::class,
            $this->createIdentity()
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
            '<x-layouts.app>
            <main>Contenido</main>
        </x-layouts.app>'
        );

        // Assert

        $view->assertSee(
            'cn-topbar',
            false
        );

        $view->assertSee(
            config('app.name')
        );
    }

    public function test_renders_application_shell(): void
    {
        $this->app->instance(
            IdentityInterface::class,
            $this->createIdentity()
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

        $view = $this->blade(
            '<x-layouts.app>
            <h1>CENICOM ERP</h1>
        </x-layouts.app>'
        );

        $view->assertSee('cn-app', false);
        $view->assertSee('cn-sidebar', false);
        $view->assertSee('cn-main', false);
        $view->assertSee('CENICOM ERP');
    }


}
