<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Processors;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\Presentation\InputPresentation;
use App\Core\Generator\Processors\FormFieldProcessor;
use PHPUnit\Framework\TestCase;

final class FormFieldProcessorTest extends TestCase
{
    public function test_generates_enum_options_for_select(): void
    {
        $processor = new FormFieldProcessor(
            new InputPresentation(),
        );

        $field = ColumnDefinition::fromArray([
            'name' => 'status',
            'type' => 'enum',
            'enumValues' => [
                'active',
                'inactive',
            ],
            'nullable' => false,
        ]);

        $result = $processor->process(
            [$field],
            'institution',
        );
        //var_dump($result);

        self::assertStringContainsString(
            '<x-cn.select',
            $result,
        );

        self::assertStringContainsString(
            ':options="array (',
            $result,
        );

        self::assertStringContainsString(
            "'active' => 'active'",
            $result,
        );

        self::assertStringContainsString(
            "'inactive' => 'inactive'",
            $result,
        );

        self::assertStringContainsString(
            ':value="old(\'status\', $institution->status ?? \'\')"',
            $result,
        );

        self::assertStringContainsString(
            'required',
            $result,
        );

        self::assertStringNotContainsString(
            'Opciones generadas posteriormente',
            $result,
        );
    }
}
