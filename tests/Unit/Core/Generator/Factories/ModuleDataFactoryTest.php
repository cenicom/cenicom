<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Factories;

use App\Core\Generator\Factories\ModuleDataFactory;
use Tests\TestCase;

final class ModuleDataFactoryTest extends TestCase
{
    private ModuleDataFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ModuleDataFactory();
    }

    public function test_creates_module_data_with_default_generation_values(): void
    {
        $module = $this->factory->create([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],
            'fields' => [],
        ]);

        self::assertSame('currencies', $module->routePrefix());
        self::assertSame('currencies', $module->routeName());
        self::assertSame('currencies', $module->viewPrefix());
    }

    public function test_uses_custom_generation_values_when_provided(): void
    {
        $module = $this->factory->create([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],
            'generation' => [
                'routePrefix' => 'admin/currencies',
                'routeName' => 'admin.currencies',
                'viewPrefix' => 'backoffice.currencies',
            ],
            'fields' => [],
        ]);

        self::assertSame('admin/currencies', $module->routePrefix());
        self::assertSame('admin.currencies', $module->routeName());
        self::assertSame('backoffice.currencies', $module->viewPrefix());
    }

    public function test_factory_creates_module_without_generation_section(): void
    {
        $module = $this->factory->create([
            'identity' => [
                'name' => 'Product',
                'singular' => 'product',
                'plural' => 'products',
                'table' => 'products',
                'description' => 'Product module',
            ],
            'fields' => [],
        ]);

        self::assertNotNull($module);

        self::assertSame('products', $module->routePrefix());
        self::assertSame('products', $module->routeName());
        self::assertSame('products', $module->viewPrefix());
    }

    public function test_factory_returns_valid_module_data(): void
    {
        $module = $this->factory->create([
            'identity' => [
                'name' => 'Customer',
                'singular' => 'customer',
                'plural' => 'customers',
                'table' => 'customers',
                'description' => 'Customer module',
            ],
            'fields' => [],
        ]);

        self::assertSame('Customer', $module->name());
        self::assertSame('customer', $module->singular());
        self::assertSame('customers', $module->plural());
        self::assertSame('customers', $module->table());
        self::assertSame('Customer module', $module->description());

        self::assertCount(0, $module->columns());

        self::assertTrue($module->timestamps());
        self::assertTrue($module->softDeletes());
        self::assertTrue($module->uuid());
        self::assertTrue($module->permissions());
    }

    /**
     * Summary of test_builds_navigation_manifest
     * @return void
     */
    public function test_builds_navigation_manifest(): void
    {
        $factory = new ModuleDataFactory();

        $module = $factory->create([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],

            'generation' => [
                'routePrefix' => 'currencies',
                'routeName' => 'currencies',
                'viewPrefix' => 'currencies',
            ],

            'navigation' => [

                'groups' => [
                    [
                        'id' => 'catalogs',
                        'label' => 'Catalogs',
                        'icon' => 'bi-grid',
                        'order' => 10,
                    ],
                ],

                'items' => [
                    [
                        'id' => 'currencies',
                        'label' => 'Currencies',
                        'route' => 'currencies.index',
                        'permission' => 'currencies.view',
                        'icon' => 'bi-cash',
                        'order' => 10,
                        'group' => 'catalogs',
                    ],
                ],
            ],

            'fields' => [],
        ]);

        $navigation = $module->navigation();

        self::assertFalse($navigation->isEmpty());

        self::assertSame(
            'Currency',
            $navigation->module
        );

        self::assertSame(
            1,
            $navigation->groupCount()
        );

        self::assertSame(
            1,
            $navigation->itemCount()
        );

        self::assertSame(
            'catalogs',
            $navigation->groups[0]->id()
        );

        self::assertSame(
            'currencies',
            $navigation->items[0]->id()
        );

        self::assertSame(
            'currencies.index',
            $navigation->items[0]->route()
        );

        self::assertSame(
            'catalogs',
            $navigation->items[0]->group()
        );
    }

    public function test_full_crud_preset_contains_all_generation_options(): void
    {
        $module = $this->factory->fullCrud([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],
            'fields' => [],
        ]);

        $options = $module->options();

        self::assertArrayHasKey('timestamps', $options);
        self::assertArrayHasKey('softDeletes', $options);
        self::assertArrayHasKey('tests', $options);
        self::assertArrayHasKey('uuid', $options);
        self::assertArrayHasKey('api', $options);
        self::assertArrayHasKey('permissions', $options);
        self::assertArrayHasKey('menu', $options);
    }
}
