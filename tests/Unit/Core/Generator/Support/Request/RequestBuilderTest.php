<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\Request;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Support\Request\RequestBuilder;
use Tests\TestCase;

final class RequestBuilderTest extends TestCase
{
    public function test_build_generates_request_variables(): void
    {
        $builder = new RequestBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $this->assertArrayHasKey(
            'namespace',
            $variables
        );

        $this->assertArrayHasKey(
            'singular',
            $variables
        );

        $this->assertArrayHasKey(
            'storeRequest',
            $variables
        );

        $this->assertArrayHasKey(
            'updateRequest',
            $variables
        );

        $this->assertArrayHasKey(
            'rules',
            $variables
        );

        $this->assertSame(
            'StoreCurrencyRequest',
            $variables['storeRequest']
        );

        $this->assertSame(
            'UpdateCurrencyRequest',
            $variables['updateRequest']
        );

        $this->assertStringContainsString(
            "'name'",
            $variables['rules']
        );

        $this->assertStringContainsString(
            "'symbol'",
            $variables['rules']
        );
    }

    public function test_build_generates_required_rules(): void
    {
        $builder = new RequestBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $rules = $variables['rules'];

        $this->assertStringContainsString(
            'required',
            $rules
        );

        $this->assertStringContainsString(
            'string',
            $rules
        );
    }

    public function test_build_generates_nullable_rule(): void
    {
        $builder = new RequestBuilder();

        $module = (new ModuleDataFactory())->create([
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

            'fields' => [
                [
                    'name' => 'description',
                    'type' => 'string',
                    'nullable' => true,
                ],
            ],
        ]);

        $variables = $builder->build($module);

        $this->assertStringContainsString(
            'nullable',
            $variables['rules']
        );
    }

    private function module(): ModuleData
    {
        return (new ModuleDataFactory())->create([
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

            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                ],

                [
                    'name' => 'symbol',
                    'type' => 'string',
                ],
            ],
        ]);
    }

    public function test_build_accepts_only_module_data(): void
    {
        $builder = new RequestBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $this->assertArrayHasKey(
            'storeRequest',
            $variables
        );

        $this->assertArrayHasKey(
            'updateRequest',
            $variables
        );
    }

    public function test_build_signature(): void
    {
        $reflection = new \ReflectionMethod(
            RequestBuilder::class,
            'build'
        );

        $this->assertCount(
            1,
            $reflection->getParameters()
        );

        $this->assertSame(
            'module',
            $reflection->getParameters()[0]->getName()
        );
    }

    public function test_build_generates_integer_rule(): void
    {
        $module = (new ModuleDataFactory())->create([
            'identity' => [
                'name' => 'Product',
                'singular' => 'product',
                'plural' => 'products',
                'table' => 'products',
                'description' => 'Product module',
            ],
            'fields' => [
                [
                    'name' => 'quantity',
                    'type' => 'integer',
                ],
            ],
        ]);

        $variables = (new RequestBuilder())->build($module);

        $this->assertStringContainsString(
            "'quantity' => ['required', 'integer']",
            $variables['rules']
        );
    }

    public function test_build_generates_max_length_rule(): void
    {
        $module = (new ModuleDataFactory())->create([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'length' => 100,
                ],
            ],
        ]);

        $variables = (new RequestBuilder())->build($module);

        $this->assertStringContainsString(
            "'name' => ['required', 'string', 'max:100']",
            $variables['rules']
        );
    }

    public function test_build_generates_unique_rule(): void
    {
        $module = (new ModuleDataFactory())->create([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],
            'fields' => [
                [
                    'name' => 'code',
                    'type' => 'string',
                    'unique' => true,
                ],
            ],
        ]);

        $variables = (new RequestBuilder())->build($module);

        $this->assertStringContainsString(
            "'code' => ['required', 'string', 'unique:currencies,code']",
            $variables['rules']
        );
    }
}
