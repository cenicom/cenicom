<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Manifest;


use App\Core\Module\Manifest\ModuleManifest;
use PHPUnit\Framework\TestCase;

final class ModuleManifestTest extends TestCase
{
    public function test_creates_manifest(): void
    {
        $manifest = $this->createManifest();

        $this->assertInstanceOf(
            ModuleManifest::class,
            $manifest
        );
    }

    public function test_returns_identity_information(): void
    {
        $manifest = $this->createManifest();

        $this->assertSame('Currency', $manifest->name());
        $this->assertSame('currency', $manifest->slug());
        $this->assertSame('Currency module', $manifest->description());
        $this->assertSame('1.0.0', $manifest->version());
    }

    public function test_returns_provider_list(): void
    {
        $manifest = $this->createManifest();

        $this->assertSame(
            [
                'Modules\\Currency\\CurrencyServiceProvider',
            ],
            $manifest->providers()
        );
    }

    public function test_returns_dependencies(): void
    {
        $manifest = $this->createManifest();

        $this->assertSame(
            [
                'Core',
                'Security',
            ],
            $manifest->dependencies()
        );
    }

    public function test_returns_permissions(): void
    {
        $manifest = $this->createManifest();

        $this->assertSame(
            [
                'currencies.view',
                'currencies.create',
                'currencies.update',
                'currencies.delete',
            ],
            $manifest->permissions()
        );
    }

    public function test_returns_navigation(): void
    {
        $manifest = $this->createManifest();

        $this->assertSame(
            [
                'group' => 'Configuration',
                'label' => 'Currencies',
                'icon' => 'bi-currency-dollar',
                'route' => 'currencies.index',
            ],
            $manifest->navigation()
        );
    }

    public function test_exports_manifest_as_array(): void
    {
        $manifest = $this->createManifest();

        $data = $manifest->toArray();

        $this->assertIsArray($data);

        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('slug', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('providers', $data);
        $this->assertArrayHasKey('dependencies', $data);
        $this->assertArrayHasKey('permissions', $data);
        $this->assertArrayHasKey('navigation', $data);
    }

    public function test_manifest_is_json_serializable(): void
    {
        $manifest = $this->createManifest();

        $json = json_encode(
            $manifest->toArray(),
            JSON_THROW_ON_ERROR
        );

        $this->assertJson($json);
    }

    private function createManifest(): ModuleManifest
    {
        return new ModuleManifest(
            name: 'Currency',
            slug: 'currency',
            description: 'Currency module',
            version: '1.0.0',
            providers: [
                'Modules\\Currency\\CurrencyServiceProvider',
            ],
            dependencies: [
                'Core',
                'Security',
            ],
            permissions: [
                'currencies.view',
                'currencies.create',
                'currencies.update',
                'currencies.delete',
            ],
            navigation: [
                'group' => 'Configuration',
                'label' => 'Currencies',
                'icon' => 'bi-currency-dollar',
                'route' => 'currencies.index',
            ],
        );
    }
}
