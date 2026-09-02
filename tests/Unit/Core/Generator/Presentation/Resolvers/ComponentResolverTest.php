<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Presentation\Resolvers;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\Presentation\DTO\ComponentMetadata;
use App\Core\Generator\Presentation\Resolvers\ComponentResolver;
use PHPUnit\Framework\TestCase;

final class ComponentResolverTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Component resolution
    |--------------------------------------------------------------------------
    */

    public function test_resolves_string_as_input(): void
    {
        $metadata = $this->resolve([
            'name' => 'name',
            'type' => 'string',
        ]);

        self::assertSame('input', $metadata->component);
        self::assertSame('x-cn.input', $metadata->bladeComponent);
        self::assertSame(ComponentMetadata::COL_HALF, $metadata->columnClass);
        self::assertSame('bi-type', $metadata->icon);
    }

    public function test_resolves_integer_as_number(): void
    {
        $metadata = $this->resolve([
            'name' => 'age',
            'type' => 'integer',
        ]);

        self::assertSame('number', $metadata->component);
        self::assertSame('x-cn.number', $metadata->bladeComponent);
        self::assertSame(ComponentMetadata::COL_HALF, $metadata->columnClass);
        self::assertSame('bi-123', $metadata->icon);
    }

    public function test_resolves_decimal_as_number(): void
    {
        $metadata = $this->resolve([
            'name' => 'amount',
            'type' => 'decimal',
            'precision' => 10,
            'scale' => 2,
        ]);

        self::assertSame('number', $metadata->component);
        self::assertSame('x-cn.number', $metadata->bladeComponent);
        self::assertSame('0.01', $metadata->attributes['step']);
        self::assertSame(10, $metadata->attributes['precision']);
    }

    public function test_resolves_boolean_as_checkbox(): void
    {
        $metadata = $this->resolve([
            'name' => 'active',
            'type' => 'boolean',
        ]);

        self::assertSame('checkbox', $metadata->component);
        self::assertSame('x-cn.checkbox', $metadata->bladeComponent);
        self::assertSame('bi-check-square', $metadata->icon);
    }

    public function test_resolves_text_as_textarea(): void
    {
        $metadata = $this->resolve([
            'name' => 'description',
            'type' => 'text',
        ]);

        self::assertSame('textarea', $metadata->component);
        self::assertSame('x-cn.textarea', $metadata->bladeComponent);
        self::assertSame(ComponentMetadata::COL_FULL, $metadata->columnClass);
        self::assertSame('bi-card-text', $metadata->icon);
    }

    public function test_resolves_json_as_textarea(): void
    {
        $metadata = $this->resolve([
            'name' => 'metadata',
            'type' => 'json',
        ]);

        self::assertSame('textarea', $metadata->component);
        self::assertSame('x-cn.textarea', $metadata->bladeComponent);
    }

    public function test_resolves_jsonb_as_textarea(): void
    {
        $metadata = $this->resolve([
            'name' => 'configuration',
            'type' => 'jsonb',
        ]);

        self::assertSame('textarea', $metadata->component);
        self::assertSame('x-cn.textarea', $metadata->bladeComponent);
    }

    public function test_resolves_enum_as_select(): void
    {
        $metadata = $this->resolve([
            'name' => 'status',
            'type' => 'enum',
            'enumValues' => [
                'active',
                'inactive',
            ],
        ]);

        self::assertSame('select', $metadata->component);
        self::assertSame('x-cn.select', $metadata->bladeComponent);
        self::assertSame(ComponentMetadata::COL_HALF, $metadata->columnClass);
        self::assertSame('bi-list', $metadata->icon);
    }

    public function test_resolves_date_as_date(): void
    {
        $metadata = $this->resolve([
            'name' => 'birth_date',
            'type' => 'date',
        ]);

        self::assertSame('date', $metadata->component);
        self::assertSame('x-cn.date', $metadata->bladeComponent);
        self::assertSame('bi-calendar', $metadata->icon);
    }

    public function test_resolves_time_using_default_input_type(): void
    {
        $metadata = $this->resolve([
            'name' => 'start_time',
            'type' => 'time',
        ]);

        self::assertSame('time', $metadata->component);
        self::assertSame('x-cn.time', $metadata->bladeComponent);
    }

    public function test_resolves_datetime_as_datetime_component(): void
    {
        $metadata = $this->resolve([
            'name' => 'created_at',
            'type' => 'dateTime',
        ]);

        self::assertSame('datetime', $metadata->component);
        self::assertSame('x-cn.datetime', $metadata->bladeComponent);
        self::assertSame('bi-calendar-event', $metadata->icon);
    }

    public function test_resolves_id_as_select(): void
    {
        $metadata = $this->resolve([
            'name' => 'institution_id',
            'type' => 'id',
        ]);

        self::assertSame('select', $metadata->component);
        self::assertSame('x-cn.select', $metadata->bladeComponent);
    }

    public function test_resolves_foreign_id_as_select(): void
    {
        $metadata = $this->resolve([
            'name' => 'country_id',
            'type' => 'foreignId',
        ]);

        self::assertSame('select', $metadata->component);
        self::assertSame('x-cn.select', $metadata->bladeComponent);
    }

    /*
    |--------------------------------------------------------------------------
    | Explicit input type
    |--------------------------------------------------------------------------
    */

    public function test_explicit_input_type_overrides_field_type_default(): void
    {
        $metadata = $this->resolve([
            'name' => 'status',
            'type' => 'string',
            'inputType' => 'select',
        ]);

        self::assertSame('select', $metadata->component);
        self::assertSame('x-cn.select', $metadata->bladeComponent);
        self::assertSame('bi-list', $metadata->icon);
    }

    public function test_explicit_textarea_overrides_string_default(): void
    {
        $metadata = $this->resolve([
            'name' => 'notes',
            'type' => 'string',
            'inputType' => 'textarea',
        ]);

        self::assertSame('textarea', $metadata->component);
        self::assertSame('x-cn.textarea', $metadata->bladeComponent);
        self::assertSame(ComponentMetadata::COL_FULL, $metadata->columnClass);
    }

    public function test_explicit_checkbox_overrides_string_default(): void
    {
        $metadata = $this->resolve([
            'name' => 'enabled',
            'type' => 'string',
            'inputType' => 'checkbox',
        ]);

        self::assertSame('checkbox', $metadata->component);
        self::assertSame('x-cn.checkbox', $metadata->bladeComponent);
        self::assertSame('bi-check-square', $metadata->icon);
    }

    /*
    |--------------------------------------------------------------------------
    | Metadata
    |--------------------------------------------------------------------------
    */

    public function test_builds_placeholder_from_column_name(): void
    {
        $metadata = $this->resolve([
            'name' => 'official_registration_value',
            'type' => 'string',
        ]);

        self::assertSame(
            'Enter Official Registration Value',
            $metadata->placeholder,
        );
    }

    public function test_preserves_length_as_maxlength_attribute(): void
    {
        $metadata = $this->resolve([
            'name' => 'code',
            'type' => 'string',
            'length' => 20,
        ]);

        self::assertSame(20, $metadata->attributes['maxlength']);
    }

    public function test_uses_half_width_for_standard_components(): void
    {
        $metadata = $this->resolve([
            'name' => 'name',
            'type' => 'string',
        ]);

        self::assertSame(
            ComponentMetadata::COL_HALF,
            $metadata->columnClass,
        );
    }

    public function test_uses_full_width_for_textarea(): void
    {
        $metadata = $this->resolve([
            'name' => 'description',
            'type' => 'text',
        ]);

        self::assertSame(
            ComponentMetadata::COL_FULL,
            $metadata->columnClass,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<string,mixed> $definition
     */
    private function resolve(array $definition): ComponentMetadata
    {
        $column = ColumnDefinition::fromArray($definition);

        return (new ComponentResolver($column))->resolve();
    }
}
