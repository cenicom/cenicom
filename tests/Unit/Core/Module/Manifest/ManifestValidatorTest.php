<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Manifest;

use App\Core\Module\Manifest\ManifestException;
use App\Core\Module\Manifest\ModuleManifest;
use App\Core\Module\Manifest\ManifestValidator;
use PHPUnit\Framework\TestCase;

final class ManifestValidatorTest extends TestCase
{
    private ManifestValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new ManifestValidator();
    }

    public function test_accepts_valid_manifest(): void
    {
        $manifest = $this->createManifest();

        $this->assertTrue(
            $this->validator->validate($manifest)
        );
    }

    public function test_rejects_empty_name(): void
    {
        $manifest = new ModuleManifest(
            name: '',
            slug: 'currency',
            description: 'Currency module',
            version: '1.0.0'
        );

        $this->expectException(ManifestException::class);

        $this->validator->validate($manifest);
    }

    public function test_rejects_empty_slug(): void
    {
        $manifest = new ModuleManifest(
            name: 'Currency',
            slug: '',
            description: 'Currency module',
            version: '1.0.0'
        );

        $this->expectException(ManifestException::class);

        $this->validator->validate($manifest);
    }

    public function test_rejects_empty_version(): void
    {
        $manifest = new ModuleManifest(
            name: 'Currency',
            slug: 'currency',
            description: 'Currency module',
            version: ''
        );

        $this->expectException(ManifestException::class);

        $this->validator->validate($manifest);
    }

    public function test_accepts_empty_description(): void
    {
        $manifest = new ModuleManifest(
            name: 'Currency',
            slug: 'currency',
            description: '',
            version: '1.0.0'
        );

        $this->assertTrue(
            $this->validator->validate($manifest)
        );
    }

    public function test_accepts_empty_optional_arrays(): void
    {
        $manifest = new ModuleManifest(
            name: 'Currency',
            slug: 'currency',
            description: 'Currency module',
            version: '1.0.0',
            providers: [],
            dependencies: [],
            permissions: [],
            navigation: []
        );

        $this->assertTrue(
            $this->validator->validate($manifest)
        );
    }

    private function createManifest(): ModuleManifest
    {
        return new ModuleManifest(
            name: 'Currency',
            slug: 'currency',
            description: 'Currency module',
            version: '1.0.0',
            providers: [
                'App\\Providers\\CurrencyServiceProvider',
            ],
            dependencies: [
                'Users',
            ],
            permissions: [
                'currencies.view',
                'currencies.create',
            ],
            navigation: [
                'group' => 'Administration',
                'label' => 'Currencies',
                'route' => 'currencies.index',
                'icon'  => 'bi-currency-dollar',
            ]
        );
    }
}
