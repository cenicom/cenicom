<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Presentation\Presenters;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\Presentation\Presenters\ColumnPresenter;
use PHPUnit\Framework\TestCase;

final class ColumnPresenterTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function present(array $definition): mixed
    {
        $column = ColumnDefinition::fromArray($definition);

        return (new ColumnPresenter($column))->present();
    }

    /*
    |--------------------------------------------------------------------------
    | Basic presentation
    |--------------------------------------------------------------------------
    */

    public function test_presents_column_name(): void
    {
        $presentation = $this->present([
            'name' => 'first_name',
            'type' => 'string',
        ]);

        self::assertSame('first_name', $presentation->name);
    }

    public function test_builds_human_readable_label(): void
    {
        $presentation = $this->present([
            'name' => 'first_name',
            'type' => 'string',
        ]);

        self::assertSame('First Name', $presentation->label);
    }

    public function test_preserves_field_type(): void
    {
        $presentation = $this->present([
            'name' => 'age',
            'type' => 'integer',
        ]);

        self::assertSame('integer', $presentation->type);
    }

    public function test_builds_placeholder_from_label(): void
    {
        $presentation = $this->present([
            'name' => 'first_name',
            'type' => 'string',
        ]);

        self::assertSame('Enter First Name', $presentation->placeholder);
    }

    /*
    |--------------------------------------------------------------------------
    | Component resolution
    |--------------------------------------------------------------------------
    */

    public function test_resolves_string_component(): void
    {
        $presentation = $this->present([
            'name' => 'name',
            'type' => 'string',
        ]);

        self::assertSame('input', $presentation->component->component);
        self::assertSame('x-cn.input', $presentation->component->bladeComponent);
    }

    public function test_resolves_integer_component(): void
    {
        $presentation = $this->present([
            'name' => 'age',
            'type' => 'integer',
        ]);

        self::assertSame('number', $presentation->component->component);
        self::assertSame('x-cn.number', $presentation->component->bladeComponent);
    }

    public function test_resolves_text_component(): void
    {
        $presentation = $this->present([
            'name' => 'description',
            'type' => 'text',
        ]);

        self::assertSame('textarea', $presentation->component->component);
        self::assertSame('x-cn.textarea', $presentation->component->bladeComponent);
    }

    public function test_resolves_boolean_component(): void
    {
        $presentation = $this->present([
            'name' => 'active',
            'type' => 'boolean',
        ]);

        self::assertSame('checkbox', $presentation->component->component);
        self::assertSame('x-cn.checkbox', $presentation->component->bladeComponent);
    }

    public function test_resolves_enum_component(): void
    {
        $presentation = $this->present([
            'name' => 'status',
            'type' => 'enum',
            'enumValues' => [
                'active',
                'inactive',
            ],
        ]);

        self::assertSame('select', $presentation->component->component);
        self::assertSame('x-cn.select', $presentation->component->bladeComponent);
    }

    public function test_resolves_date_component(): void
    {
        $presentation = $this->present([
            'name' => 'birth_date',
            'type' => 'date',
        ]);

        self::assertSame('date', $presentation->component->component);
        self::assertSame('x-cn.date', $presentation->component->bladeComponent);
    }

    public function test_resolves_time_component(): void
    {
        $presentation = $this->present([
            'name' => 'start_time',
            'type' => 'time',
        ]);

        self::assertSame('time', $presentation->component->component);
        self::assertSame('x-cn.time', $presentation->component->bladeComponent);
    }

    public function test_resolves_datetime_component(): void
    {
        $presentation = $this->present([
            'name' => 'created_at',
            'type' => 'dateTime',
        ]);

        self::assertSame('datetime', $presentation->component->component);
        self::assertSame('x-cn.datetime', $presentation->component->bladeComponent);
    }

    /*
    |--------------------------------------------------------------------------
    | Explicit input type
    |--------------------------------------------------------------------------
    */

    public function test_explicit_input_type_is_respected(): void
    {
        $presentation = $this->present([
            'name' => 'status',
            'type' => 'string',
            'inputType' => 'select',
        ]);

        self::assertSame('select', $presentation->component->component);
        self::assertSame('x-cn.select', $presentation->component->bladeComponent);
    }

    public function test_explicit_textarea_overrides_string_default(): void
    {
        $presentation = $this->present([
            'name' => 'notes',
            'type' => 'string',
            'inputType' => 'textarea',
        ]);

        self::assertSame('textarea', $presentation->component->component);
        self::assertSame('x-cn.textarea', $presentation->component->bladeComponent);
    }

    /*
    |--------------------------------------------------------------------------
    | Component metadata propagation
    |--------------------------------------------------------------------------
    */

    public function test_propagates_component_placeholder(): void
    {
        $presentation = $this->present([
            'name' => 'email',
            'type' => 'email',
        ]);

        self::assertSame(
            $presentation->component->placeholder,
            $presentation->placeholder
        );
    }

    public function test_propagates_component_column_class(): void
    {
        $presentation = $this->present([
            'name' => 'description',
            'type' => 'text',
        ]);

        self::assertSame(
            $presentation->component->columnClass,
            $presentation->columnClass
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Required
    |--------------------------------------------------------------------------
    */

    public function test_non_nullable_column_is_required(): void
    {
        $presentation = $this->present([
            'name' => 'name',
            'type' => 'string',
            'nullable' => false,
        ]);

        self::assertTrue($presentation->required);
    }

    public function test_nullable_column_is_not_required(): void
    {
        $presentation = $this->present([
            'name' => 'description',
            'type' => 'text',
            'nullable' => true,
        ]);

        self::assertFalse($presentation->required);
    }

    /*
    |--------------------------------------------------------------------------
    | Readonly
    |--------------------------------------------------------------------------
    */

    public function test_primary_key_is_readonly(): void
    {
        $presentation = $this->present([
            'name' => 'id',
            'type' => 'id',
            'primary' => true,
        ]);

        self::assertTrue($presentation->readonly);
    }

    public function test_normal_column_is_not_readonly(): void
    {
        $presentation = $this->present([
            'name' => 'name',
            'type' => 'string',
        ]);

        self::assertFalse($presentation->readonly);
    }

    /*
    |--------------------------------------------------------------------------
    | Disabled
    |--------------------------------------------------------------------------
    */

    public function test_column_is_not_disabled_by_default(): void
    {
        $presentation = $this->present([
            'name' => 'name',
            'type' => 'string',
        ]);

        self::assertFalse($presentation->disabled);
    }

    /*
    |--------------------------------------------------------------------------
    | Default value
    |--------------------------------------------------------------------------
    */

    public function test_preserves_default_value(): void
    {
        $presentation = $this->present([
            'name' => 'status',
            'type' => 'string',
            'default' => 'active',
        ]);

        self::assertSame('active', $presentation->default);
    }

    public function test_null_default_is_preserved(): void
    {
        $presentation = $this->present([
            'name' => 'description',
            'type' => 'text',
            'default' => null,
        ]);

        self::assertNull($presentation->default);
    }
}
