<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Manifest;

use App\Core\Module\Manifest\ManifestFactory;
use App\Core\Module\Manifest\ModuleManifest;
use PHPUnit\Framework\TestCase;

final class ManifestFactoryTest extends TestCase
{
    private ManifestFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ManifestFactory();
    }

    public function test_creates_manifest_from_definition(): void
    {
        $manifest = $this->factory->create([
            'name' => 'Currency',
            'version' => '2.0.0',
            'description' => 'Currency module',

            'providers' => [
                'App\\Providers\\CurrencyServiceProvider',
            ],

            'dependencies' => [
                'Core',
            ],

            'permissions' => [
                'currencies.view',
            ],

            'navigation' => [
                [
                    'label' => 'Currencies',
                ],
            ],
        ]);

        $this->assertInstanceOf(
            ModuleManifest::class,
            $manifest
        );

        $this->assertSame(
            'Currency',
            $manifest->name()
        );

        $this->assertSame(
            '2.0.0',
            $manifest->version()
        );
    }

    public function test_uses_default_version_when_missing(): void
    {
        $manifest = $this->factory->create([
            'name' => 'Currency',
        ]);

        $this->assertSame(
            '1.0.0',
            $manifest->version()
        );
    }

    public function test_uses_empty_providers_when_missing(): void
    {
        $manifest = $this->factory->create([
            'name' => 'Currency',
        ]);

        $this->assertSame(
            [],
            $manifest->providers()
        );
    }

    public function test_uses_empty_dependencies_when_missing(): void
    {
        $manifest = $this->factory->create([
            'name' => 'Currency',
        ]);

        $this->assertSame(
            [],
            $manifest->dependencies()
        );
    }

    public function test_uses_empty_permissions_when_missing(): void
    {
        $manifest = $this->factory->create([
            'name' => 'Currency',
        ]);

        $this->assertSame(
            [],
            $manifest->permissions()
        );
    }

    public function test_uses_empty_navigation_when_missing(): void
    {
        $manifest = $this->factory->create([
            'name' => 'Currency',
        ]);

        $this->assertSame(
            [],
            $manifest->navigation()
        );
    }

    public function test_returns_module_manifest_instance(): void
    {
        $this->assertInstanceOf(
            ModuleManifest::class,
            $this->factory->create([
                'name' => 'Currency',
            ])
        );
    }
}
