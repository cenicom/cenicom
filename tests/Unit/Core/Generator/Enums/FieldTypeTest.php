<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Enums;

use App\Core\Generator\Enums\FieldType;
use App\Core\Generator\Enums\InputType;
use PHPUnit\Framework\TestCase;

final class FieldTypeTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Enum contract
    |--------------------------------------------------------------------------
    */

    public function test_all_supported_field_types_are_defined(): void
    {
        $this->assertSame(
            [
                'integer',
                'bigInteger',
                'smallInteger',
                'tinyInteger',
                'mediumInteger',
                'decimal',
                'float',
                'double',
                'boolean',
                'string',
                'char',
                'text',
                'mediumText',
                'longText',
                'json',
                'jsonb',
                'enum',
                'email',
                'date',
                'time',
                'year',
                'dateTime',
                'dateTimeTz',
                'timestamp',
                'timestampTz',
                'id',
                'uuid',
                'ulid',
                'foreignId',
                'binary',
                'ipAddress',
                'macAddress',
                'geometry',
                'point',
            ],
            array_map(
                static fn (FieldType $type): string => $type->value,
                FieldType::cases()
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Classification
    |--------------------------------------------------------------------------
    */

    public function test_numeric_types_are_classified_correctly(): void
    {
        $numeric = [
            FieldType::INTEGER,
            FieldType::BIG_INTEGER,
            FieldType::SMALL_INTEGER,
            FieldType::TINY_INTEGER,
            FieldType::MEDIUM_INTEGER,
            FieldType::DECIMAL,
            FieldType::FLOAT,
            FieldType::DOUBLE,
            FieldType::YEAR,
        ];

        foreach (FieldType::cases() as $type) {
            $expected = in_array($type, $numeric, true);

            $this->assertSame(
                $expected,
                $type->isNumeric(),
                $type->value
            );
        }
    }

    public function test_text_types_are_classified_correctly(): void
    {
        $text = [
            FieldType::STRING,
            FieldType::CHAR,
            FieldType::TEXT,
            FieldType::MEDIUM_TEXT,
            FieldType::LONG_TEXT,
            FieldType::JSON,
            FieldType::JSONB,
            FieldType::ENUM,
        ];

        foreach (FieldType::cases() as $type) {
            $expected = in_array($type, $text, true);

            $this->assertSame(
                $expected,
                $type->isText(),
                $type->value
            );
        }
    }

    public function test_date_types_are_classified_correctly(): void
    {
        $dates = [
            FieldType::DATE,
            FieldType::TIME,
            FieldType::YEAR,
            FieldType::DATETIME,
            FieldType::DATETIME_TZ,
            FieldType::TIMESTAMP,
            FieldType::TIMESTAMP_TZ,
        ];

        foreach (FieldType::cases() as $type) {
            $expected = in_array($type, $dates, true);

            $this->assertSame(
                $expected,
                $type->isDate(),
                $type->value
            );
        }
    }

    public function test_boolean_type_is_classified_correctly(): void
    {
        foreach (FieldType::cases() as $type) {
            $this->assertSame(
                $type === FieldType::BOOLEAN,
                $type->isBoolean(),
                $type->value
            );
        }
    }

    public function test_json_types_are_classified_correctly(): void
    {
        foreach (FieldType::cases() as $type) {
            $expected = in_array(
                $type,
                [
                    FieldType::JSON,
                    FieldType::JSONB,
                ],
                true
            );

            $this->assertSame(
                $expected,
                $type->isJson(),
                $type->value
            );
        }
    }

    public function test_identifier_types_are_classified_correctly(): void
    {
        $identifiers = [
            FieldType::ID,
            FieldType::UUID,
            FieldType::ULID,
            FieldType::FOREIGN_ID,
        ];

        foreach (FieldType::cases() as $type) {
            $expected = in_array($type, $identifiers, true);

            $this->assertSame(
                $expected,
                $type->isIdentifier(),
                $type->value
            );
        }
    }

    public function test_binary_type_is_classified_correctly(): void
    {
        foreach (FieldType::cases() as $type) {
            $this->assertSame(
                $type === FieldType::BINARY,
                $type->isBinary(),
                $type->value
            );
        }
    }

    public function test_geometry_types_are_classified_correctly(): void
    {
        $geometry = [
            FieldType::GEOMETRY,
            FieldType::POINT,
        ];

        foreach (FieldType::cases() as $type) {
            $expected = in_array($type, $geometry, true);

            $this->assertSame(
                $expected,
                $type->isGeometry(),
                $type->value
            );
        }
    }

    public function test_enum_type_is_classified_correctly(): void
    {
        foreach (FieldType::cases() as $type) {
            $this->assertSame(
                $type === FieldType::ENUM,
                $type->isEnum(),
                $type->value
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Migration
    |--------------------------------------------------------------------------
    */

    public function test_migration_method_matches_enum_value(): void
    {
        foreach (FieldType::cases() as $type) {
            $this->assertSame(
                $type->value,
                $type->migrationMethod(),
                $type->value
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PHP type
    |--------------------------------------------------------------------------
    */

    public function test_php_types_are_resolved_correctly(): void
    {
        $expected = [
            FieldType::BOOLEAN->value => 'bool',

            FieldType::INTEGER->value => 'int',
            FieldType::BIG_INTEGER->value => 'int',
            FieldType::SMALL_INTEGER->value => 'int',
            FieldType::MEDIUM_INTEGER->value => 'int',
            FieldType::TINY_INTEGER->value => 'int',
            FieldType::FOREIGN_ID->value => 'int',
            FieldType::ID->value => 'int',

            FieldType::FLOAT->value => 'float',
            FieldType::DOUBLE->value => 'float',
            FieldType::DECIMAL->value => 'float',

            FieldType::JSON->value => 'array',
            FieldType::JSONB->value => 'array',

            FieldType::DATE->value => '\Carbon\Carbon',
            FieldType::TIME->value => '\Carbon\Carbon',
            FieldType::YEAR->value => '\Carbon\Carbon',
            FieldType::DATETIME->value => '\Carbon\Carbon',
            FieldType::DATETIME_TZ->value => '\Carbon\Carbon',
            FieldType::TIMESTAMP->value => '\Carbon\Carbon',
            FieldType::TIMESTAMP_TZ->value => '\Carbon\Carbon',
        ];

        foreach (FieldType::cases() as $type) {
            $this->assertSame(
                $expected[$type->value] ?? 'string',
                $type->phpType(),
                $type->value
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Eloquent casts
    |--------------------------------------------------------------------------
    */

    public function test_eloquent_casts_are_resolved_correctly(): void
    {
        $expected = [
            FieldType::BOOLEAN->value => 'boolean',

            FieldType::JSON->value => 'array',
            FieldType::JSONB->value => 'array',

            FieldType::DATE->value => 'date',

            FieldType::DATETIME->value => 'datetime',
            FieldType::DATETIME_TZ->value => 'datetime',
            FieldType::TIMESTAMP->value => 'datetime',
            FieldType::TIMESTAMP_TZ->value => 'datetime',
        ];

        foreach (FieldType::cases() as $type) {
            $this->assertSame(
                $expected[$type->value] ?? null,
                $type->eloquentCast(),
                $type->value
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    public function test_default_validation_rules_are_resolved_correctly(): void
    {
        $expected = [
            FieldType::INTEGER->value => ['integer'],
            FieldType::BIG_INTEGER->value => ['integer'],
            FieldType::SMALL_INTEGER->value => ['integer'],
            FieldType::MEDIUM_INTEGER->value => ['integer'],
            FieldType::TINY_INTEGER->value => ['integer'],

            FieldType::DECIMAL->value => ['numeric'],
            FieldType::FLOAT->value => ['numeric'],
            FieldType::DOUBLE->value => ['numeric'],

            FieldType::BOOLEAN->value => ['boolean'],

            FieldType::STRING->value => ['string'],
            FieldType::CHAR->value => ['string'],
            FieldType::TEXT->value => ['string'],
            FieldType::MEDIUM_TEXT->value => ['string'],
            FieldType::LONG_TEXT->value => ['string'],

            FieldType::JSON->value => ['array'],
            FieldType::JSONB->value => ['array'],

            FieldType::ENUM->value => ['string'],

            FieldType::EMAIL->value => ['email'],

            FieldType::DATE->value => ['date'],
            FieldType::TIME->value => ['date'],
            FieldType::YEAR->value => ['date'],
            FieldType::DATETIME->value => ['date'],
            FieldType::DATETIME_TZ->value => ['date'],
            FieldType::TIMESTAMP->value => ['date'],
            FieldType::TIMESTAMP_TZ->value => ['date'],

            FieldType::UUID->value => ['uuid'],
            FieldType::ULID->value => ['ulid'],
            FieldType::ID->value => ['integer'],
            FieldType::FOREIGN_ID->value => ['integer'],

            FieldType::BINARY->value => ['string'],

            FieldType::IP_ADDRESS->value => ['ip'],
            FieldType::MAC_ADDRESS->value => ['mac_address'],

            FieldType::GEOMETRY->value => [],
            FieldType::POINT->value => [],
        ];

        foreach (FieldType::cases() as $type) {
            $this->assertSame(
                $expected[$type->value],
                $type->defaultValidationRules(),
                $type->value
            );
        }
    }

    public function test_default_validation_rule_string_is_resolved_correctly(): void
    {
        foreach (FieldType::cases() as $type) {
            $this->assertSame(
                implode('|', $type->defaultValidationRules()),
                $type->defaultValidationRuleString(),
                $type->value
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Input type
    |--------------------------------------------------------------------------
    */

    public function test_default_input_types_are_resolved_correctly(): void
    {
        $expected = [
            FieldType::INTEGER->value => InputType::NUMBER,
            FieldType::BIG_INTEGER->value => InputType::NUMBER,
            FieldType::SMALL_INTEGER->value => InputType::NUMBER,
            FieldType::MEDIUM_INTEGER->value => InputType::NUMBER,
            FieldType::TINY_INTEGER->value => InputType::NUMBER,
            FieldType::DECIMAL->value => InputType::NUMBER,
            FieldType::FLOAT->value => InputType::NUMBER,
            FieldType::DOUBLE->value => InputType::NUMBER,

            FieldType::BOOLEAN->value => InputType::CHECKBOX,

            FieldType::STRING->value => InputType::TEXT,
            FieldType::CHAR->value => InputType::TEXT,

            FieldType::EMAIL->value => InputType::EMAIL,

            FieldType::TEXT->value => InputType::TEXTAREA,
            FieldType::MEDIUM_TEXT->value => InputType::TEXTAREA,
            FieldType::LONG_TEXT->value => InputType::TEXTAREA,
            FieldType::JSON->value => InputType::TEXTAREA,
            FieldType::JSONB->value => InputType::TEXTAREA,

            FieldType::ENUM->value => InputType::SELECT,

            FieldType::DATE->value => InputType::DATE,
            FieldType::TIME->value => InputType::TIME,
            FieldType::YEAR->value => InputType::NUMBER,

            FieldType::DATETIME->value => InputType::DATETIME_LOCAL,
            FieldType::DATETIME_TZ->value => InputType::DATETIME_LOCAL,
            FieldType::TIMESTAMP->value => InputType::DATETIME_LOCAL,
            FieldType::TIMESTAMP_TZ->value => InputType::DATETIME_LOCAL,

            FieldType::UUID->value => InputType::TEXT,
            FieldType::ULID->value => InputType::TEXT,

            FieldType::ID->value => InputType::SELECT,
            FieldType::FOREIGN_ID->value => InputType::SELECT,

            FieldType::BINARY->value => InputType::TEXT,
            FieldType::IP_ADDRESS->value => InputType::TEXT,
            FieldType::MAC_ADDRESS->value => InputType::TEXT,
            FieldType::GEOMETRY->value => InputType::TEXT,
            FieldType::POINT->value => InputType::TEXT,
        ];

        foreach (FieldType::cases() as $type) {
            $this->assertSame(
                $expected[$type->value],
                $type->defaultInputType(),
                $type->value
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Capabilities
    |--------------------------------------------------------------------------
    */

    public function test_length_support_is_limited_to_string_and_char(): void
    {
        foreach (FieldType::cases() as $type) {
            $expected = in_array(
                $type,
                [
                    FieldType::STRING,
                    FieldType::CHAR,
                ],
                true
            );

            $this->assertSame(
                $expected,
                $type->requiresLength(),
                $type->value
            );

            $this->assertSame(
                $expected,
                $type->supportsLength(),
                $type->value
            );
        }
    }

    public function test_precision_support_is_limited_to_decimal(): void
    {
        foreach (FieldType::cases() as $type) {
            $expected = $type === FieldType::DECIMAL;

            $this->assertSame(
                $expected,
                $type->requiresPrecision(),
                $type->value
            );

            $this->assertSame(
                $expected,
                $type->supportsPrecision(),
                $type->value
            );
        }
    }

    public function test_scale_support_is_numeric_decimal_float_and_double(): void
    {
        $supported = [
            FieldType::DECIMAL,
            FieldType::FLOAT,
            FieldType::DOUBLE,
        ];

        foreach (FieldType::cases() as $type) {
            $this->assertSame(
                in_array($type, $supported, true),
                $type->supportsScale(),
                $type->value
            );
        }
    }

    public function test_values_are_supported_only_by_enum(): void
    {
        foreach (FieldType::cases() as $type) {
            $this->assertSame(
                $type === FieldType::ENUM,
                $type->requiresValues(),
                $type->value
            );

            $this->assertSame(
                $type === FieldType::ENUM,
                $type->supportsValues(),
                $type->value
            );
        }
    }

    public function test_foreign_keys_are_supported_by_identifier_types(): void
    {
        $supported = [
            FieldType::ID,
            FieldType::UUID,
            FieldType::ULID,
            FieldType::FOREIGN_ID,
        ];

        foreach (FieldType::cases() as $type) {
            $this->assertSame(
                in_array($type, $supported, true),
                $type->supportsForeignKey(),
                $type->value
            );
        }
    }

    public function test_geometry_does_not_support_default_or_unique_values(): void
    {
        foreach ([
            FieldType::GEOMETRY,
            FieldType::POINT,
        ] as $type) {
            $this->assertFalse($type->supportsDefaultValue());
            $this->assertFalse($type->supportsUnique());
        }
    }

    public function test_all_other_types_support_default_and_unique_values(): void
    {
        foreach (FieldType::cases() as $type) {
            if (
                $type === FieldType::GEOMETRY
                || $type === FieldType::POINT
            ) {
                continue;
            }

            $this->assertTrue(
                $type->supportsDefaultValue(),
                $type->value
            );

            $this->assertTrue(
                $type->supportsUnique(),
                $type->value
            );
        }
    }

    public function test_all_field_types_support_indexes(): void
    {
        foreach (FieldType::cases() as $type) {
            $this->assertTrue(
                $type->supportsIndex(),
                $type->value
            );
        }
    }

    public function test_form_components_are_not_supported_by_binary_and_geometry_types(): void
    {
        $unsupported = [
            FieldType::BINARY,
            FieldType::GEOMETRY,
            FieldType::POINT,
        ];

        foreach (FieldType::cases() as $type) {
            $expected = ! in_array($type, $unsupported, true);

            $this->assertSame(
                $expected,
                $type->supportsFormComponent(),
                $type->value
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    */

    public function test_default_lengths_are_defined_only_for_string_and_char(): void
    {
        $this->assertSame(
            255,
            FieldType::STRING->defaultLength()
        );

        $this->assertSame(
            1,
            FieldType::CHAR->defaultLength()
        );

        foreach (FieldType::cases() as $type) {
            if (
                $type === FieldType::STRING
                || $type === FieldType::CHAR
            ) {
                continue;
            }

            $this->assertNull(
                $type->defaultLength(),
                $type->value
            );
        }
    }

    public function test_default_precision_and_scale_are_defined_for_numeric_decimal_types(): void
    {
        $this->assertSame(
            10,
            FieldType::DECIMAL->defaultPrecision()
        );

        $this->assertSame(
            2,
            FieldType::DECIMAL->defaultScale()
        );

        $this->assertSame(
            8,
            FieldType::FLOAT->defaultPrecision()
        );

        $this->assertSame(
            2,
            FieldType::FLOAT->defaultScale()
        );

        $this->assertSame(
            16,
            FieldType::DOUBLE->defaultPrecision()
        );

        $this->assertSame(
            4,
            FieldType::DOUBLE->defaultScale()
        );

        foreach (FieldType::cases() as $type) {
            if (
                $type === FieldType::DECIMAL
                || $type === FieldType::FLOAT
                || $type === FieldType::DOUBLE
            ) {
                continue;
            }

            $this->assertNull(
                $type->defaultPrecision(),
                $type->value
            );

            $this->assertNull(
                $type->defaultScale(),
                $type->value
            );
        }
    }

    public function test_migration_argument_counts_are_resolved_correctly(): void
    {
        foreach (FieldType::cases() as $type) {
            $expected = match ($type) {
                FieldType::STRING,
                FieldType::CHAR => 2,

                FieldType::DECIMAL,
                FieldType::FLOAT,
                FieldType::DOUBLE => 3,

                default => 1,
            };

            $this->assertSame(
                $expected,
                $type->migrationArgumentCount(),
                $type->value
            );
        }
    }
}
