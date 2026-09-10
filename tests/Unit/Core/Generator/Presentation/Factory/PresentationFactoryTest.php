<?php

namespace Tests\Unit\Core\Generator\Presentation\Factory;

use App\Core\Generator\Presentation\Factory\PresentationFactory;
use Tests\Support\GeneratorTestCase;

final class PresentationFactoryTest extends GeneratorTestCase
{
    public function test_show_excludes_only_soft_delete_and_preserves_column_order(): void
    {
        $module = $this->createModuleData([
            'fields' => [
                $this->idField(),
                $this->stringField('name'),
                $this->field('created_at', 'dateTime'),
                $this->field('deleted_at', 'dateTime'),
                $this->field('updated_at', 'dateTime'),
            ],
        ]);

        $presentation = (new PresentationFactory())->show($module);

        $fields = $presentation->fields();

        $this->assertSame(
            ['id', 'name', 'created_at', 'updated_at'],
            array_map(
                static fn($field): string => $field->name,
                $fields,
            ),
        );

        $this->assertSame(
            [
                '$' . $module->variable() . '->id',
                '$' . $module->variable() . '->name',
                '$' . $module->variable() . '->created_at',
                '$' . $module->variable() . '->updated_at',
            ],
            array_map(
                static fn($field): string => $field->binding,
                $fields,
            ),
        );

        $this->assertTrue($presentation->hasFields());
    }
}
