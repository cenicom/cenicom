<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Processors;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\Enums\FieldType;
use App\Core\Generator\Processors\MigrationFieldProcessor;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class MigrationFieldProcessorTest extends TestCase
{
    private MigrationFieldProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new MigrationFieldProcessor();
    }

    /*
    |--------------------------------------------------------------------------
    | Tipos Blueprint
    |--------------------------------------------------------------------------
    */

    #[DataProvider('simpleFieldTypesProvider')]
    public function test_builds_simple_field_types(
        FieldType $type,
        string $expected,
    ): void {
        $column = $this->column(
            name: 'field',
            type: $type,
        );

        $result = $this->processor->build($column);

        $this->assertSame(
            $expected,
            $result
        );
    }

    public static function simpleFieldTypesProvider(): array
    {
        return [
            'integer' => [
                FieldType::INTEGER,
                "\$table->integer('field');",
            ],

            'big integer' => [
                FieldType::BIG_INTEGER,
                "\$table->bigInteger('field');",
            ],

            'boolean' => [
                FieldType::BOOLEAN,
                "\$table->boolean('field');",
            ],

            'uuid' => [
                FieldType::UUID,
                "\$table->uuid('field');",
            ],

            'text' => [
                FieldType::TEXT,
                "\$table->text('field');",
            ],

            'date' => [
                FieldType::DATE,
                "\$table->date('field');",
            ],

            'datetime' => [
                FieldType::DATETIME,
                "\$table->dateTime('field');",
            ],

            'time' => [
                FieldType::TIME,
                "\$table->time('field');",
            ],

            'timestamp' => [
                FieldType::TIMESTAMP,
                "\$table->timestamp('field');",
            ],

            'float' => [
                FieldType::FLOAT,
                "\$table->float('field');",
            ],

            'double' => [
                FieldType::DOUBLE,
                "\$table->double('field');",
            ],

            'json' => [
                FieldType::JSON,
                "\$table->json('field');",
            ],

            'foreign id' => [
                FieldType::FOREIGN_ID,
                "\$table->foreignId('field');",
            ],
        ];
    }

    public function test_builds_string_with_explicit_length(): void
    {
        $column = $this->column(
            name: 'name',
            type: FieldType::STRING,
            length: 150,
        );

        $this->assertSame(
            "\$table->string('name', 150);",
            $this->processor->build($column)
        );
    }

    public function test_builds_decimal_with_precision_and_scale(): void
    {
        $column = $this->column(
            name: 'price',
            type: FieldType::DECIMAL,
            precision: 12,
            scale: 4,
        );

        $this->assertSame(
            "\$table->decimal('price', 12, 4);",
            $this->processor->build($column)
        );
    }

    public function test_builds_enum(): void
    {
        $column = $this->column(
            name: 'status',
            type: FieldType::ENUM,
            enumValues: [
                'active',
                'inactive',
            ],
        );

        $this->assertSame(
            "\$table->enum('status', array (\n  0 => 'active',\n  1 => 'inactive',\n));",
            $this->processor->build($column)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Modifiers
    |--------------------------------------------------------------------------
    */

    public function test_applies_nullable(): void
    {
        $column = $this->column(
            name: 'name',
            type: FieldType::STRING,
            nullable: true,
        );

        $this->assertSame(
            "\$table->string('name')->nullable();",
            $this->processor->build($column)
        );
    }

    public function test_applies_default_string(): void
    {
        $column = $this->column(
            name: 'status',
            type: FieldType::STRING,
            default: 'active',
        );

        $this->assertSame(
            "\$table->string('status')->default('active');",
            $this->processor->build($column)
        );
    }

    public function test_applies_default_boolean(): void
    {
        $column = $this->column(
            name: 'active',
            type: FieldType::BOOLEAN,
            default: true,
        );

        $this->assertSame(
            "\$table->boolean('active')->default(true);",
            $this->processor->build($column)
        );
    }

    public function test_applies_default_integer(): void
    {
        $column = $this->column(
            name: 'order',
            type: FieldType::INTEGER,
            default: 0,
        );

        $this->assertSame(
            "\$table->integer('order')->default(0);",
            $this->processor->build($column)
        );
    }

    public function test_applies_unsigned(): void
    {
        $column = $this->column(
            name: 'amount',
            type: FieldType::INTEGER,
            unsigned: true,
        );

        $this->assertSame(
            "\$table->integer('amount')->unsigned();",
            $this->processor->build($column)
        );
    }

    public function test_applies_unique(): void
    {
        $column = $this->column(
            name: 'code',
            type: FieldType::STRING,
            unique: true,
        );

        $this->assertSame(
            "\$table->string('code')->unique();",
            $this->processor->build($column)
        );
    }

    public function test_applies_index(): void
    {
        $column = $this->column(
            name: 'code',
            type: FieldType::STRING,
            index: true,
        );

        $this->assertSame(
            "\$table->string('code')->index();",
            $this->processor->build($column)
        );
    }

    public function test_applies_comment(): void
    {
        $column = $this->column(
            name: 'name',
            type: FieldType::STRING,
            comment: 'Currency name',
        );

        $this->assertSame(
            "\$table->string('name')->comment('Currency name');",
            $this->processor->build($column)
        );
    }

    public function test_applies_charset(): void
    {
        $column = $this->column(
            name: 'name',
            type: FieldType::STRING,
            charset: 'utf8mb4',
        );

        $this->assertSame(
            "\$table->string('name')->charset('utf8mb4');",
            $this->processor->build($column)
        );
    }

    public function test_applies_collation(): void
    {
        $column = $this->column(
            name: 'name',
            type: FieldType::STRING,
            collation: 'utf8mb4_unicode_ci',
        );

        $this->assertSame(
            "\$table->string('name')->collation('utf8mb4_unicode_ci');",
            $this->processor->build($column)
        );
    }

    public function test_applies_after(): void
    {
        $column = $this->column(
            name: 'symbol',
            type: FieldType::STRING,
            after: 'name',
        );

        $this->assertSame(
            "\$table->string('symbol')->after('name');",
            $this->processor->build($column)
        );
    }

    public function test_applies_first(): void
    {
        $column = $this->column(
            name: 'code',
            type: FieldType::STRING,
            first: true,
        );

        $this->assertSame(
            "\$table->string('code')->first();",
            $this->processor->build($column)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Foreign keys
    |--------------------------------------------------------------------------
    */

    public function test_applies_constrained(): void
    {
        $column = $this->column(
            name: 'user_id',
            type: FieldType::FOREIGN_ID,
            constrained: true,
        );

        $this->assertSame(
            "\$table->foreignId('user_id')->constrained();",
            $this->processor->build($column)
        );
    }

    public function test_applies_named_constraint(): void
    {
        $column = $this->column(
            name: 'owner_id',
            type: FieldType::FOREIGN_ID,
            constrained: 'users',
        );

        $this->assertSame(
            "\$table->foreignId('owner_id')->constrained('users');",
            $this->processor->build($column)
        );
    }

    public function test_applies_cascade_on_delete(): void
    {
        $column = $this->column(
            name: 'user_id',
            type: FieldType::FOREIGN_ID,
            constrained: true,
            cascadeOnDelete: true,
        );

        $this->assertSame(
            "\$table->foreignId('user_id')->constrained()->cascadeOnDelete();",
            $this->processor->build($column)
        );
    }

    public function test_applies_cascade_on_update(): void
    {
        $column = $this->column(
            name: 'user_id',
            type: FieldType::FOREIGN_ID,
            constrained: true,
            cascadeOnUpdate: true,
        );

        $this->assertSame(
            "\$table->foreignId('user_id')->constrained()->cascadeOnUpdate();",
            $this->processor->build($column)
        );
    }

    public function test_applies_restrict_on_delete(): void
    {
        $column = $this->column(
            name: 'user_id',
            type: FieldType::FOREIGN_ID,
            constrained: true,
            restrictOnDelete: true,
        );

        $this->assertSame(
            "\$table->foreignId('user_id')->constrained()->restrictOnDelete();",
            $this->processor->build($column)
        );
    }

    public function test_applies_null_on_delete(): void
    {
        $column = $this->column(
            name: 'user_id',
            type: FieldType::FOREIGN_ID,
            constrained: true,
            nullOnDelete: true,
        );

        $this->assertSame(
            "\$table->foreignId('user_id')->constrained()->nullOnDelete();",
            $this->processor->build($column)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | useCurrent
    |--------------------------------------------------------------------------
    */

    public function test_applies_use_current(): void
    {
        $column = $this->column(
            name: 'published_at',
            type: FieldType::TIMESTAMP,
            useCurrent: true,
        );

        $this->assertSame(
            "\$table->timestamp('published_at')->useCurrent();",
            $this->processor->build($column)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | process()
    |--------------------------------------------------------------------------
    */

    public function test_process_builds_multiple_columns(): void
    {
        $fields = [
            $this->column(
                name: 'name',
                type: FieldType::STRING,
            ),
            $this->column(
                name: 'active',
                type: FieldType::BOOLEAN,
            ),
        ];

        $result = $this->processor->process($fields);

        $this->assertSame(
            implode(PHP_EOL . PHP_EOL, [
                "\$table->string('name');",
                "\$table->boolean('active');",
            ]),
            $result
        );
    }

    public function test_process_skips_reserved_fields(): void
    {
        $fields = [
            $this->column(
                name: 'id',
                type: FieldType::ID,
            ),
            $this->column(
                name: 'name',
                type: FieldType::STRING,
            ),
            $this->column(
                name: 'created_at',
                type: FieldType::TIMESTAMP,
            ),
            $this->column(
                name: 'updated_at',
                type: FieldType::TIMESTAMP,
            ),
            $this->column(
                name: 'deleted_at',
                type: FieldType::TIMESTAMP,
            ),
        ];

        $result = $this->processor->process($fields);

        $this->assertSame(
            "\$table->string('name');",
            $result
        );
    }

    public function test_process_empty_fields_returns_empty_string(): void
    {
        $this->assertSame(
            '',
            $this->processor->process([])
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function column(
        string $name,
        FieldType $type,
        ?int $length = null,
        ?int $precision = null,
        ?int $scale = null,
        bool $nullable = false,
        mixed $default = null,
        bool $unsigned = false,
        bool $unique = false,
        bool $index = false,
        ?string $references = null,
        ?string $on = null,
        ?string $charset = null,
        ?string $collation = null,
        bool|string $constrained = false,
        bool $cascadeOnDelete = false,
        bool $cascadeOnUpdate = false,
        bool $restrictOnDelete = false,
        bool $nullOnDelete = false,
        array $enumValues = [],
        ?string $comment = null,
        ?string $after = null,
        bool $first = false,
        bool $useCurrent = false,
    ): ColumnDefinition {
        return new ColumnDefinition(
            name: $name,
            type: $type,
            length: $length,
            precision: $precision,
            scale: $scale,
            nullable: $nullable,
            default: $default,
            unsigned: $unsigned,
            unique: $unique,
            index: $index,
            references: $references,
            on: $on,
            charset: $charset,
            collation: $collation,
            constrained: $constrained,
            cascadeOnDelete: $cascadeOnDelete,
            cascadeOnUpdate: $cascadeOnUpdate,
            restrictOnDelete: $restrictOnDelete,
            nullOnDelete: $nullOnDelete,
            enumValues: $enumValues,
            comment: $comment,
            after: $after,
            first: $first,
            useCurrent: $useCurrent,
        );
    }
}
