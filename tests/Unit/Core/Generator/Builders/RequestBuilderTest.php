<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Builders;

use App\Core\Generator\Builders\RequestBuilder;

use App\Core\Generator\DTO\ModuleData;

use App\Core\Generator\Factories\ModuleDataFactory;
use Tests\TestCase;

final class RequestBuilderTest extends TestCase
{
    public function test_build_generates_request_variables(): void
    {
        $builder = new RequestBuilder();

        $module = $this->module();

        $variables = $builder->build(
            $module,
            'StoreCurrencyRequest'
        );

        $this->assertArrayHasKey(
            'namespace',
            $variables
        );

        $this->assertArrayHasKey(
            'request',
            $variables
        );

        $this->assertArrayHasKey(
            'rules',
            $variables
        );

        $this->assertSame(
            'StoreCurrencyRequest',
            $variables['request']
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
            $this->module(),
            'StoreCurrencyRequest'
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

        $variables = $builder->build(
            $module,
            'StoreCurrencyRequest'
        );

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

    public function test_laravel_is_booted(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Foundation\Application::class,
            app()
        );
    }
}
