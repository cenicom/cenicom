<?php

declare(strict_types=1);

namespace App\Core\Generator\Enums;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\Enums\InputType;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Enum que representa todos los tipos de campos soportados
 * por el CN Generator.
 *
 * Este enum constituye la fuente oficial para la generación
 * de:
 *
 * - Migraciones
 * - Modelos
 * - Requests
 * - Formularios
 * - Componentes UI
 * - Validaciones
 * - Relaciones
 *
 * @package App\Core\Generator\Enums
 * @since 2.0.0
 */
enum FieldType: string
{
    /*
    |--------------------------------------------------------------------------
    | Numéricos
    |--------------------------------------------------------------------------
    */

    case INTEGER = 'integer';

    case BIG_INTEGER = 'bigInteger';

    case SMALL_INTEGER = 'smallInteger';

    case TINY_INTEGER = 'tinyInteger';

    case MEDIUM_INTEGER = 'mediumInteger';

    case DECIMAL = 'decimal';

    case FLOAT = 'float';

    case DOUBLE = 'double';

    case BOOLEAN = 'boolean';

        /*
    |--------------------------------------------------------------------------
    | Texto
    |--------------------------------------------------------------------------
    */

    case STRING = 'string';

    case CHAR = 'char';

    case TEXT = 'text';

    case MEDIUM_TEXT = 'mediumText';

    case LONG_TEXT = 'longText';

    case JSON = 'json';

    case JSONB = 'jsonb';

    case ENUM = 'enum';

    case EMAIL = 'email';

        //case PASSWORD = 'password';

        /*
    |--------------------------------------------------------------------------
    | Fecha y hora
    |--------------------------------------------------------------------------
    */

    case DATE = 'date';

    case TIME = 'time';

    case YEAR = 'year';

    case DATETIME = 'dateTime';

    case DATETIME_TZ = 'dateTimeTz';

    case TIMESTAMP = 'timestamp';

    case TIMESTAMP_TZ = 'timestampTz';

        /*
    |--------------------------------------------------------------------------
    | Identificadores
    |--------------------------------------------------------------------------
    */

    case ID = 'id';

    case UUID = 'uuid';

    case ULID = 'ulid';

    case FOREIGN_ID = 'foreignId';

        /*
    |--------------------------------------------------------------------------
    | Binarios
    |--------------------------------------------------------------------------
    */

    case BINARY = 'binary';

        /*
    |--------------------------------------------------------------------------
    | Especiales
    |--------------------------------------------------------------------------
    */

    case IP_ADDRESS = 'ipAddress';

    case MAC_ADDRESS = 'macAddress';

    case GEOMETRY = 'geometry';

    case POINT = 'point';


    /*
    |--------------------------------------------------------------------------
    | 📦 Clasificación Numérica
    |--------------------------------------------------------------------------
    */

    public function isNumeric(): bool
    {
        return match ($this) {
            self::INTEGER,
            self::BIG_INTEGER,
            self::SMALL_INTEGER,
            self::TINY_INTEGER,
            self::MEDIUM_INTEGER,
            self::DECIMAL,
            self::FLOAT,
            self::DOUBLE,
            self::YEAR => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 Clasificación de Texto
    |--------------------------------------------------------------------------
    */
    public function isText(): bool
    {
        return match ($this) {
            self::STRING,
            self::CHAR,
            self::TEXT,
            self::MEDIUM_TEXT,
            self::LONG_TEXT,
            self::JSON,
            self::JSONB,
            self::ENUM => true,


            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 Fechas
    |--------------------------------------------------------------------------
    */
    public function isDate(): bool
    {
        return match ($this) {
            self::DATE,
            self::TIME,
            self::YEAR,
            self::DATETIME,
            self::DATETIME_TZ,
            self::TIMESTAMP,
            self::TIMESTAMP_TZ => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 Booleanos
    |--------------------------------------------------------------------------
    */
    public function isBoolean(): bool
    {
        return $this === self::BOOLEAN;
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 JSON
    |--------------------------------------------------------------------------
    */
    public function isJson(): bool
    {
        return match ($this) {
            self::JSON,
            self::JSONB => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 Identificadores
    |--------------------------------------------------------------------------
    */
    public function isIdentifier(): bool
    {
        return match ($this) {
            self::ID,
            self::UUID,
            self::ULID,
            self::FOREIGN_ID => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 Binarios
    |--------------------------------------------------------------------------
    */
    public function isBinary(): bool
    {
        return $this === self::BINARY;
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 Geometría
    |--------------------------------------------------------------------------
    */
    public function isGeometry(): bool
    {
        return match ($this) {
            self::GEOMETRY,
            self::POINT => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 Enumeraciones
    |--------------------------------------------------------------------------
    */
    public function isEnum(): bool
    {
        return $this === self::ENUM;
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 migrationMethod()
    |--------------------------------------------------------------------------
    */
    /**
     * Obtiene el nombre del método Blueprint de Laravel.
     */
    public function migrationMethod(): string
    {
        return $this->value;
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 phpType()
            Este helper define el tipo PHP natural de cada FieldType.
    |--------------------------------------------------------------------------
    */
    /**
     * Obtiene el tipo PHP asociado.
     */
    public function phpType(): string
    {
        return match ($this) {

            self::BOOLEAN => 'bool',

            self::INTEGER,
            self::BIG_INTEGER,
            self::SMALL_INTEGER,
            self::MEDIUM_INTEGER,
            self::TINY_INTEGER,
            self::FOREIGN_ID,
            self::ID => 'int',

            self::FLOAT,
            self::DOUBLE,
            self::DECIMAL => 'float',

            self::JSON,
            self::JSONB => 'array',

            self::DATE,
            self::TIME,
            self::YEAR,
            self::DATETIME,
            self::DATETIME_TZ,
            self::TIMESTAMP,
            self::TIMESTAMP_TZ => '\Carbon\Carbon',

            default => 'string',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 eloquentCast()
    |--------------------------------------------------------------------------
    */
    /**
     * Obtiene el cast Eloquent recomendado.
     */
    public function eloquentCast(): ?string
    {
        return match ($this) {

            self::BOOLEAN => 'boolean',

            self::JSON,
            self::JSONB => 'array',

            self::DATE => 'date',

            self::DATETIME,
            self::DATETIME_TZ,
            self::TIMESTAMP,
            self::TIMESTAMP_TZ => 'datetime',

            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 requiresLength()
        No todos los tipos aceptan longitud.
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el tipo requiere longitud.
     */
    public function requiresLength(): bool
    {
        return match ($this) {

            self::STRING,
            self::CHAR => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 requiresPrecision()
        Los decimales necesitan precisión y escala.
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el tipo requiere precisión y escala.
     */
    public function requiresPrecision(): bool
    {
        return $this === self::DECIMAL;
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 requiresValues()
        Solo ENUM necesita un arreglo de valores.
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el tipo requiere un listado de valores.
     */
    public function requiresValues(): bool
    {
        return $this === self::ENUM;
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsUnsigned()
        Aunque actualmente no tengas tipos UNSIGNED_*, este helper permite expresar
        si el tipo puede marcarse como unsigned sin acoplar esa lógica al generador.
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el tipo admite unsigned.
     */
    public function supportsUnsigned(): bool
    {
        return match ($this) {

            self::INTEGER,
            self::BIG_INTEGER,
            self::SMALL_INTEGER,
            self::MEDIUM_INTEGER,
            self::TINY_INTEGER,
            self::DECIMAL,
            self::FLOAT,
            self::DOUBLE => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 defaultValidationRules()
        Este será el método principal.
    |--------------------------------------------------------------------------
    */
    /**
     * Obtiene las reglas de validación base para el tipo.
     *
     * @return array<int, string>
     */
    public function defaultValidationRules(): array
    {
        return match ($this) {

            /*
            |--------------------------------------------------------------------------
            | Numéricos
            |--------------------------------------------------------------------------
            */

            self::INTEGER,
            self::BIG_INTEGER,
            self::SMALL_INTEGER,
            self::MEDIUM_INTEGER,
            self::TINY_INTEGER => ['integer'],

            self::DECIMAL,
            self::FLOAT,
            self::DOUBLE => ['numeric'],

            self::BOOLEAN => ['boolean'],

            /*
            |--------------------------------------------------------------------------
            | Texto
            |--------------------------------------------------------------------------
            */

            self::STRING,
            self::CHAR,
            self::TEXT,
            self::MEDIUM_TEXT,
            self::LONG_TEXT => ['string'],

            self::JSON,
            self::JSONB => ['array'],

            self::ENUM => ['string'],

            /*
            |--------------------------------------------------------------------------
            | Fecha
            |--------------------------------------------------------------------------
            */

            self::DATE,
            self::TIME,
            self::YEAR,
            self::DATETIME,
            self::DATETIME_TZ,
            self::TIMESTAMP,
            self::TIMESTAMP_TZ => ['date'],

            /*
            |--------------------------------------------------------------------------
            | Identificadores
            |--------------------------------------------------------------------------
            */

            self::UUID => ['uuid'],

            self::ULID => ['ulid'],

            self::ID,
            self::FOREIGN_ID => ['integer'],

            /*
            |--------------------------------------------------------------------------
            | Binarios
            |--------------------------------------------------------------------------
            */

            self::BINARY => ['string'],

            /*
            |--------------------------------------------------------------------------
            | Especiales
            |--------------------------------------------------------------------------
            */

            self::IP_ADDRESS => ['ip'],

            self::MAC_ADDRESS => ['mac_address'],

            self::GEOMETRY,
            self::POINT => [],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 defaultValidationRuleString()
    |--------------------------------------------------------------------------
    */
    /**
     * Obtiene las reglas base en formato Laravel.
     */
    public function defaultValidationRuleString(): string
    {
        return implode('|', $this->defaultValidationRules());
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 isScalar()
        Muy útil para DTOs y exportaciones.
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el tipo representa un valor escalar.
     */
    public function isScalar(): bool
    {
        return match ($this) {

            self::STRING,
            self::CHAR,

            self::INTEGER,
            self::BIG_INTEGER,
            self::SMALL_INTEGER,
            self::MEDIUM_INTEGER,
            self::TINY_INTEGER,

            self::DECIMAL,
            self::FLOAT,
            self::DOUBLE,

            self::BOOLEAN => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsNullable()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite nullable.
     */
    public function supportsNullable(): bool
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsDefaultValue()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite valores por defecto.
     */
    public function supportsDefaultValue(): bool
    {
        return match ($this) {

            self::GEOMETRY,
            self::POINT => false,

            default => true,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsUnique()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite restricción UNIQUE.
     */
    public function supportsUnique(): bool
    {
        return match ($this) {

            self::GEOMETRY,
            self::POINT => false,

            default => true,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsIndex()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite índices.
     */
    public function supportsIndex(): bool
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsForeignKey()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si puede participar como clave foránea.
     */
    public function supportsForeignKey(): bool
    {
        return match ($this) {

            self::FOREIGN_ID,
            self::ID,
            self::UUID,
            self::ULID => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 defaultInputType()
        Este será probablemente el helper más utilizado por todo el Generator.
    |--------------------------------------------------------------------------
    */
    /**
     * Obtiene el InputType recomendado.
     */
    public function defaultInputType(): InputType
    {
        return match ($this) {

            /*
            |--------------------------------------------------------------------------
            | Numéricos
            |--------------------------------------------------------------------------
            */

            self::INTEGER,
            self::BIG_INTEGER,
            self::SMALL_INTEGER,
            self::MEDIUM_INTEGER,
            self::TINY_INTEGER,
            self::FLOAT,
            self::DOUBLE,
            self::DECIMAL
            => InputType::NUMBER,

            self::BOOLEAN
            => InputType::CHECKBOX,

            /*
            |--------------------------------------------------------------------------
            | Texto
            |--------------------------------------------------------------------------
            */

            self::STRING,
            self::CHAR
            => InputType::TEXT,

            self::TEXT,
            self::MEDIUM_TEXT,
            self::LONG_TEXT,
            self::JSON,
            self::JSONB
            => InputType::TEXTAREA,

            self::ENUM
            => InputType::SELECT,

            /*
            |--------------------------------------------------------------------------
            | Fecha
            |--------------------------------------------------------------------------
            */

            self::DATE
            => InputType::DATE,

            self::TIME
            => InputType::TIME,

            self::YEAR
            => InputType::NUMBER,

            self::DATETIME,
            self::DATETIME_TZ,
            self::TIMESTAMP,
            self::TIMESTAMP_TZ
            => InputType::DATETIME_LOCAL,

            /*
            |--------------------------------------------------------------------------
            | Identificadores
            |--------------------------------------------------------------------------
            */

            self::UUID,
            self::ULID
            => InputType::TEXT,

            self::ID,
            self::FOREIGN_ID
            => InputType::SELECT,

            /*
            |--------------------------------------------------------------------------
            | Especiales
            |--------------------------------------------------------------------------
            */

            self::IP_ADDRESS
            => InputType::TEXT,

            self::MAC_ADDRESS
            => InputType::TEXT,

            self::BINARY,
            self::GEOMETRY,
            self::POINT
            => InputType::TEXT,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsFormComponent()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el tipo puede renderizarse automáticamente.
     */
    public function supportsFormComponent(): bool
    {
        return match ($this) {

            self::GEOMETRY,
            self::POINT,
            self::BINARY => false,

            default => true,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsTableColumn()
    |--------------------------------------------------------------------------
    */
    public function supportsTableColumn(): bool
    {
        return match ($this) {

            self::BINARY => false,

            default => true,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsFilter()
    |--------------------------------------------------------------------------
    */
    public function supportsFilter(): bool
    {
        return match ($this) {

            self::TEXT,
            self::MEDIUM_TEXT,
            self::LONG_TEXT,
            self::STRING,
            self::CHAR,

            self::INTEGER,
            self::BIG_INTEGER,
            self::SMALL_INTEGER,

            self::BOOLEAN,

            self::DATE,
            self::DATETIME,

            self::ENUM => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsSorting()
    |--------------------------------------------------------------------------
    */
    public function supportsSorting(): bool
    {
        return match ($this) {

            self::GEOMETRY,
            self::POINT,
            self::JSON,
            self::JSONB,
            self::BINARY => false,

            default => true,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsFactory()
    |--------------------------------------------------------------------------
    */
    public function supportsFactory(): bool
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsSeeder()
    |--------------------------------------------------------------------------
    */
    public function supportsSeeder(): bool
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsApiResource()
    |--------------------------------------------------------------------------
    */
    public function supportsApiResource(): bool
    {
        return true;
    }

    public function inputType(): InputType
    {
        return match ($this) {

            /*
            |--------------------------------------------------------------------------
            | Texto
            |--------------------------------------------------------------------------
            */

            self::STRING,
            self::CHAR
            => InputType::TEXT,

            self::TEXT,
            self::MEDIUM_TEXT,
            self::LONG_TEXT
            => InputType::TEXTAREA,

            self::JSON,
            self::JSONB
            => InputType::TEXTAREA,

            self::ENUM
            => InputType::SELECT,

            /*
            |--------------------------------------------------------------------------
            | Numéricos
            |--------------------------------------------------------------------------
            */

            self::INTEGER,
            self::BIG_INTEGER,
            self::SMALL_INTEGER,
            self::MEDIUM_INTEGER,
            self::TINY_INTEGER,
            self::DECIMAL,
            self::FLOAT,
            self::DOUBLE,
            self::YEAR
            => InputType::NUMBER,

            /*
            |--------------------------------------------------------------------------
            | Booleanos
            |--------------------------------------------------------------------------
            */

            self::BOOLEAN
            => InputType::CHECKBOX,

            /*
            |--------------------------------------------------------------------------
            | Fechas
            |--------------------------------------------------------------------------
            */

            self::DATE
            => InputType::DATE,

            self::TIME
            => InputType::TIME,

            self::DATETIME,
            self::DATETIME_TZ,
            self::TIMESTAMP,
            self::TIMESTAMP_TZ
            => InputType::DATETIME_LOCAL,

            /*
            |--------------------------------------------------------------------------
            | Identificadores
            |--------------------------------------------------------------------------
            */

            self::ID,
            self::FOREIGN_ID
            => InputType::SELECT,

            self::UUID,
            self::ULID
            => InputType::TEXT,

            /*
            |--------------------------------------------------------------------------
            | Especiales
            |--------------------------------------------------------------------------
            */

            self::IP_ADDRESS,
            self::MAC_ADDRESS,
            self::BINARY,
            self::GEOMETRY,
            self::POINT
            => InputType::TEXT,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Compatibilidad con ColumnDefinition
    |--------------------------------------------------------------------------
    */

    /**
     * Indica si el tipo soporta longitud.
     */
    public function supportsLength(): bool
    {
        return $this->requiresLength();
    }

    /**
     * Indica si el tipo soporta precisión.
     */
    public function supportsPrecision(): bool
    {
        return $this->requiresPrecision();
    }

    /**
     * Indica si el tipo soporta escala.
     */
    public function supportsScale(): bool
    {
        return match ($this) {
            self::DECIMAL,
            self::FLOAT,
            self::DOUBLE => true,

            default => false,
        };
    }

    /**
     * Indica si el tipo soporta valores por defecto.
     */
    public function supportsDefault(): bool
    {
        return $this->supportsDefaultValue();
    }

    /**
     * Obtiene el cast Eloquent asociado.
     */
    public function cast(): ?string
    {
        return $this->eloquentCast();
    }

    /**
     * Indica si el tipo admite charset.
     */

    public function supportsCharset(): bool
    {
        return match ($this) {

            self::STRING,
            self::CHAR,
            self::TEXT,
            self::MEDIUM_TEXT,
            self::LONG_TEXT => true,

            default => false,
        };
    }

    /**
     * Indica si el tipo admite collation.
     */
    public function supportsCollation(): bool
    {
        return $this->supportsCharset();
    }

    /**
     * Indica si el tipo admite una lista de valores.
     */
    public function supportsValues(): bool
    {
        return $this->requiresValues();
    }

    /**
     * Indica si la longitud es configurable.
     */
    public function supportsVariableLength(): bool
    {
        return match ($this) {
            self::STRING,
            self::CHAR => true,

            default => false,
        };
    }

    /**
     * Determina si el tipo soporta comentarios.
     */
    public function supportsComment(): bool
    {
        return match ($this) {

            self::GEOMETRY,
            self::POINT => false,

            default => true,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | defaultLength()
    |   Con esto podrás hacer en cualquier generador:
    |   $length = $column->length() ?? $fieldType->defaultLength();
    |--------------------------------------------------------------------------
    */

    public function defaultLength(): ?int
    {
        return match ($this) {
            self::STRING => 255,
            self::CHAR => 1,
            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | defaultPrecision()
    |   Después el MigrationGenerator no necesitará conocer esos valores.
    |--------------------------------------------------------------------------
    */

    public function defaultPrecision(): ?int
    {
        return match ($this) {
            self::DECIMAL => 10,
            self::FLOAT => 8,
            self::DOUBLE => 16,
            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | migrationArgumentCount()
    |    Este helper será una joya.
    |    Así el generador sabrá automáticamente cuántos parámetros debe enviar al Blueprint.
    |--------------------------------------------------------------------------
    */
    public function migrationArgumentCount(): int
    {
        return match ($this) {

            self::STRING,
            self::CHAR => 2,

            self::DECIMAL,
            self::FLOAT,
            self::DOUBLE => 3,

            default => 1,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | defaultScale()
    |   Después el MigrationGenerator no necesitará conocer esos valores.
    |--------------------------------------------------------------------------
    */
    public function defaultScale(): ?int
    {
        return match ($this) {
            self::DECIMAL => 2,
            self::FLOAT => 2,
            self::DOUBLE => 4,
            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | migrationArguments()
    |   Este me gusta aún más.
    |
    |   En lugar de devolver un número, devuelve los argumentos.
    |   Con esto, MigrationGenerator prácticamente deja de tener lógica específica por tipo.
    |--------------------------------------------------------------------------
    */
    public function migrationArguments(ColumnDefinition $column): array
    {
        return match ($this) {

            self::STRING,
            self::CHAR => [
                $column->name(),
                $column->length() ?? $this->defaultLength(),
            ],

            self::DECIMAL => [
                $column->name(),
                $column->precision() ?? $this->defaultPrecision(),
                $column->scale() ?? $this->defaultScale(),
            ],

            default => [
                $column->name(),
            ],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | displayName()
    |   Muy útil para mensajes de error.
    |    Entonces los errores serán mucho más legibles.
    |   Field "price" requires Precision.
    |   Supported type:
    |       Decimal
    |--------------------------------------------------------------------------
    */
    public function displayName(): string
    {
        return match ($this) {

            self::BIG_INTEGER => 'Big Integer',

            self::SMALL_INTEGER => 'Small Integer',

            self::FOREIGN_ID => 'Foreign ID',

            default => ucfirst($this->value),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | category()
    |   Este método tendrá mucho recorrido.
    |   Primero crea un nuevo enum:
    |   enum FieldCategory
            {
                case Numeric;
                case Text;
                case Date;
                case Identifier;
                case Boolean;
                case Json;
                case Binary;
                case Geometry;
            }
    |--------------------------------------------------------------------------
    */
}
