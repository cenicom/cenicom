<?php

declare(strict_types=1);

namespace App\Core\Generator\Specifications;

use App\Core\Generator\Specifications\Contracts\SpecificationInterface;

final class ModuleSpecification implements SpecificationInterface
{
    public function __construct(
        private readonly string $moduleName
    ) {
    }


    public function toArray(): array
    {
        return [
            ...$this->identity(),
            ...$this->database(),
            'fields' => $this->fields(),
        ];
    }


    public function identity(): array
    {
        $singular = str($this->moduleName)->snake()->toString();
        $plural = str($singular)->plural()->toString();

        return [
            'name' => $this->moduleName,
            'singular' => $singular,
            'plural' => $plural,
            'table' => $plural,
            'description' => "Module {$this->moduleName}",
        ];
    }

    public function database(): array
    {
        return [];
    }


    public function fields(): array
    {
        return [];
    }


    public function relations(): array
    {
        return [];
    }


    public function validation(): array
    {
        return [];
    }


    public function permissions(): array
    {
        return [];
    }


    public function navigation(): array
    {
        return [];
    }


    public function generation(): array
    {
        $singular = str($this->moduleName)->snake()->toString();
        $plural = str($singular)->plural()->toString();

        return [
            'routePrefix' => $plural,
            'routeName' => $plural,
            'viewPrefix' => $plural,

            'timestamps' => true,
            'softDeletes' => false,
            'uuid' => true,
            'api' => false,
            'tests' => true,
            'permissions' => true,
            'menu' => true,
            'icon' => 'bi-grid',
        ];
    }

    public function metadata(): array
    {
        return [
            'source' => 'artisan',
        ];
    }


    public function version(): string
    {
        return '1.0';
    }
}
