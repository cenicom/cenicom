<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\DTO;

use Tests\Support\GeneratorTestCase;

final class ModuleDataTest extends GeneratorTestCase
{
    public function test_exposes_module_relationships(): void
    {
        $relations = [
            [
                'type' => 'belongsTo',
                'method' => 'country',
                'model' => 'App\\Modules\\Country\\Models\\Country',
            ],
        ];

        $module = $this->createModuleData([
            'relations' => $relations,
        ]);

        self::assertSame(
            $relations,
            $module->relationships()
        );
    }
}
