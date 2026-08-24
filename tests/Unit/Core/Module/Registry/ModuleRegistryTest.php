<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Registry;

use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Registry\ModuleRegistry;
use Tests\TestCase;

final class ModuleRegistryTest extends TestCase
{
    private ModuleRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new ModuleRegistry();
    }

    /**
     * Crea una definición de módulo para pruebas.
     */
    private function makeModule(
        string $name,
        bool $enabled = true,
    ): ModuleDefinition {
        return new ModuleDefinition(
            name: $name,
            namespace: "Tests\\Fixtures\\Modules\\{$name}",
            basePath: base_path("tests/Fixtures/Modules/{$name}"),
            manifestPath: base_path("tests/Fixtures/Modules/{$name}/module.php"),
            providers: [],
            permissionDefinitions: [],
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [],
            enabled: $enabled,
        );
    }

    public function test_register_registers_module(): void
    {
        $module = $this->makeModule('Blog');

        $this->registry->register($module);

        $this->assertTrue(
            $this->registry->has('Blog')
        );

        $this->assertSame(
            $module,
            $this->registry->get('Blog')
        );

        $this->assertCount(
            1,
            $this->registry->all()
        );

        $this->assertSame(
            1,
            $this->registry->count()
        );
    }

    public function test_has_returns_true_for_registered_module(): void
    {
        $this->registry->register(
            $this->makeModule('Blog')
        );

        $this->assertTrue(
            $this->registry->has('Blog')
        );

        $this->assertFalse(
            $this->registry->has('Users')
        );
    }

    public function test_get_returns_registered_module(): void
    {
        $module = $this->makeModule('Blog');

        $this->registry->register($module);

        $this->assertSame(
            $module,
            $this->registry->get('Blog')
        );

        $this->assertNull(
            $this->registry->get('Users')
        );
    }

    public function test_all_returns_all_registered_modules(): void
    {
        $blog = $this->makeModule('Blog');
        $users = $this->makeModule('Users');

        $this->registry->register($blog);
        $this->registry->register($users);

        $modules = $this->registry->all();

        $this->assertCount(2, $modules);

        $this->assertSame($blog, $modules[0]);
        $this->assertSame($users, $modules[1]);
    }

    public function test_remove_removes_registered_module(): void
    {
        $module = $this->makeModule('Blog');

        $this->registry->register($module);

        $this->assertTrue(
            $this->registry->has('Blog')
        );

        $this->registry->remove('Blog');

        $this->assertFalse(
            $this->registry->has('Blog')
        );

        $this->assertNull(
            $this->registry->get('Blog')
        );

        $this->assertCount(
            0,
            $this->registry->all()
        );

        $this->assertSame(
            0,
            $this->registry->count()
        );

        $this->assertSame(
            [],
            $this->registry->names()
        );
    }

    public function test_clear_removes_all_modules(): void
    {
        $this->registry->register(
            $this->makeModule('Blog')
        );

        $this->registry->register(
            $this->makeModule('Users')
        );

        $this->assertCount(
            2,
            $this->registry->all()
        );

        $this->registry->clear();

        $this->assertCount(
            0,
            $this->registry->all()
        );

        $this->assertSame(
            0,
            $this->registry->count()
        );

        $this->assertFalse(
            $this->registry->has('Blog')
        );

        $this->assertFalse(
            $this->registry->has('Users')
        );
    }

    public function test_count_returns_registered_modules_count(): void
    {
        $this->assertSame(
            0,
            $this->registry->count()
        );

        $this->registry->register(
            $this->makeModule('Blog')
        );

        $this->assertSame(
            1,
            $this->registry->count()
        );

        $this->registry->register(
            $this->makeModule('Users')
        );

        $this->assertSame(
            2,
            $this->registry->count()
        );

        $this->registry->remove('Blog');

        $this->assertSame(
            1,
            $this->registry->count()
        );
    }

    public function test_names_returns_registered_module_names(): void
    {
        $this->registry->register(
            $this->makeModule('Blog')
        );

        $this->registry->register(
            $this->makeModule('Users')
        );

        $this->registry->register(
            $this->makeModule('Inventory')
        );

        $this->assertSame(
            [
                'Blog',
                'Users',
                'Inventory',
            ],
            $this->registry->names()
        );
    }

    /*
    *🚢 ERP-INT-004.5.1.5 — MRG-005
        Registrar el mismo módulo dos veces no crea duplicados
        Objetivo
        Certificar que el ModuleRegistry mantiene un único registro por nombre de módulo.
        */
    public function test_registering_same_module_twice_keeps_single_entry(): void
    {
        $module = $this->makeModule('Blog');

        $this->registry->register($module);
        $this->registry->register($module);

        $this->assertSame(
            1,
            $this->registry->count()
        );

        $this->assertTrue(
            $this->registry->has('Blog')
        );

        $this->assertSame(
            $module,
            $this->registry->get('Blog')
        );

        $this->assertCount(
            1,
            $this->registry->all()
        );

        $this->assertSame(
            ['Blog'],
            $this->registry->names()
        );
    }
}
