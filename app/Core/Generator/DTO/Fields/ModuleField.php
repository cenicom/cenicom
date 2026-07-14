<?php

declare(strict_types=1);

namespace App\Core\Generator\DTO\Fields;

use App\Core\Generator\Enums\FieldType;
use App\Core\Generator\Enums\InputType;
use App\Core\Generator\Enums\RelationType;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Value Object que representa un campo de un módulo
 * del CN Generator.
 *
 * Esta clase centraliza toda la información necesaria para
 * generar automáticamente:
 *
 * - Migraciones
 * - Modelos Eloquent
 * - Form Requests
 * - Repositories
 * - Services
 * - Controllers
 * - Blade Views
 * - API Resources
 * - Factories
 * - Seeders
 * - Tests
 * - Relaciones
 *
 * Es un objeto inmutable (readonly) diseñado para ser
 * compartido entre todos los Generators del Core.
 *
 * @package App\Core\Generator\DTO\Fields
 * @since 2.0.0
 */
final readonly class ModuleField
{
    /*
    |--------------------------------------------------------------------------
    | Identidad
    |--------------------------------------------------------------------------
    */

    public function __construct(
        private string $name,
        private string $label,
        private string $type,

        /*
        |--------------------------------------------------------------------------
        | Base de datos
        |--------------------------------------------------------------------------
        */

        private ?int $length = null,
        private ?int $precision = null,
        private ?int $scale = null,

        private bool $nullable = false,
        private bool $unique = false,
        private bool $unsigned = false,

        private mixed $default = null,

        /*
        |--------------------------------------------------------------------------
        | Formularios
        |--------------------------------------------------------------------------
        */

        private bool $fillable = true,
        private bool $required = false,
        private bool $readonly = false,
        private bool $disabled = false,
        private bool $hidden = false,

        /*
        |--------------------------------------------------------------------------
        | Interfaz de Usuario
        |--------------------------------------------------------------------------
        */

        private ?string $inputType = null,
        private ?string $placeholder = null,
        private ?string $help = null,
        private ?string $icon = null,

        /*
        |--------------------------------------------------------------------------
        | Listados
        |--------------------------------------------------------------------------
        */

        private bool $searchable = false,
        private bool $sortable = false,
        private bool $filterable = false,
        private bool $visible = true,

        /*
        |--------------------------------------------------------------------------
        | Relaciones
        |--------------------------------------------------------------------------
        */

        private bool $relationship = false,
        private ?string $relationType = null,
        private ?string $relatedModel = null,
        private ?string $foreignKey = null,
        private ?string $ownerKey = null,

        /*
        |--------------------------------------------------------------------------
        | Validación
        |--------------------------------------------------------------------------
        */

        private array $rules = [],
        private array $messages = [],
        private array $attributes = [],

        /*
        |--------------------------------------------------------------------------
        | Generación
        |--------------------------------------------------------------------------
        */

        private bool $generateMigration = true,
        private bool $generateModel = true,
        private bool $generateRequest = true,
        private bool $generateFactory = true,
        private bool $generateSeeder = true,
        private bool $generateTest = true,
    ) {
    }

    /*
|--------------------------------------------------------------------------
| Identidad
|--------------------------------------------------------------------------
*/

    public function name(): string
    {
        return $this->name;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function type(): FieldType
    {
        return $this->type;
    }

    /*
|--------------------------------------------------------------------------
| Base de datos
|--------------------------------------------------------------------------
*/

    public function length(): ?int
    {
        return $this->length;
    }

    public function precision(): ?int
    {
        return $this->precision;
    }

    public function scale(): ?int
    {
        return $this->scale;
    }

    public function nullable(): bool
    {
        return $this->nullable;
    }

    public function unique(): bool
    {
        return $this->unique;
    }

    public function unsigned(): bool
    {
        return $this->unsigned;
    }

    public function default(): mixed
    {
        return $this->default;
    }

    /*
|--------------------------------------------------------------------------
| Formularios
|--------------------------------------------------------------------------
*/

    public function fillable(): bool
    {
        return $this->fillable;
    }

    public function required(): bool
    {
        return $this->required;
    }

    public function readonly(): bool
    {
        return $this->readonly;
    }

    public function disabled(): bool
    {
        return $this->disabled;
    }

    public function hidden(): bool
    {
        return $this->hidden;
    }

    /*
|--------------------------------------------------------------------------
| UI
|--------------------------------------------------------------------------
*/

    public function inputType(): ?InputType
    {
        return $this->inputType;
    }

    public function placeholder(): ?string
    {
        return $this->placeholder;
    }

    public function help(): ?string
    {
        return $this->help;
    }

    public function icon(): ?string
    {
        return $this->icon;
    }

    /*
|--------------------------------------------------------------------------
| Listados
|--------------------------------------------------------------------------
*/

    public function searchable(): bool
    {
        return $this->searchable;
    }

    public function sortable(): bool
    {
        return $this->sortable;
    }

    public function filterable(): bool
    {
        return $this->filterable;
    }

    public function visible(): bool
    {
        return $this->visible;
    }

    /*
|--------------------------------------------------------------------------
| Relaciones
|--------------------------------------------------------------------------
*/

    public function relationship(): bool
    {
        return $this->relationship;
    }

    public function relationType(): ?RelationType
    {
        return $this->relationType;
    }

    public function relatedModel(): ?string
    {
        return $this->relatedModel;
    }

    public function foreignKey(): ?string
    {
        return $this->foreignKey;
    }

    public function ownerKey(): ?string
    {
        return $this->ownerKey;
    }

    /*
|--------------------------------------------------------------------------
| Validación
|--------------------------------------------------------------------------
*/

    /**
     * @return array<int, string>
     */
    public function rules(): array
    {
        return $this->rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return $this->messages;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }
    /*
    |--------------------------------------------------------------------------
    | Generación
    |--------------------------------------------------------------------------
    */

    public function generateMigration(): bool
    {
        return $this->generateMigration;
    }

    public function generateModel(): bool
    {
        return $this->generateModel;
    }

    public function generateRequest(): bool
    {
        return $this->generateRequest;
    }

    public function generateFactory(): bool
    {
        return $this->generateFactory;
    }

    public function generateSeeder(): bool
    {
        return $this->generateSeeder;
    }

    public function generateTest(): bool
    {
        return $this->generateTest;
    }

    /*
|--------------------------------------------------------------------------
| Longitud
|--------------------------------------------------------------------------
*/

    public function hasLength(): bool
    {
        return $this->length !== null;
    }

    public function usesLength(): bool
    {
        return $this->hasLength();
    }

    /*
    |--------------------------------------------------------------------------
    | Precisión
    |--------------------------------------------------------------------------
    */

    public function hasPrecision(): bool
    {
        return $this->precision !== null;
    }

    public function hasScale(): bool
    {
        return $this->scale !== null;
    }

    public function usesPrecision(): bool
    {
        return $this->hasPrecision()
            && $this->hasScale();
    }

    /*
    |--------------------------------------------------------------------------
    | Valores por defecto
    |--------------------------------------------------------------------------
    */

    public function hasDefault(): bool
    {
        return $this->default !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | Nullable
    |--------------------------------------------------------------------------
    */

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function isRequired(): bool
    {
        return !$this->nullable;
    }

    /*
    |--------------------------------------------------------------------------
    | Restricciones
    |--------------------------------------------------------------------------
    */

    public function isUnique(): bool
    {
        return $this->unique;
    }

    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    /*
    |--------------------------------------------------------------------------
    | Identificadores
    |--------------------------------------------------------------------------
    */

    public function isPrimaryKey(): bool
    {
        return $this->name === 'id';
    }

    public function isForeignKey(): bool
    {
        return str_ends_with($this->name, '_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Auditoría
    |--------------------------------------------------------------------------
    */

    public function isTimestamp(): bool
    {
        return in_array(
            $this->name,
            [
                'created_at',
                'updated_at',
            ],
            true
        );
    }

    public function isDeletedAt(): bool
    {
        return $this->name === 'deleted_at';
    }

    public function isAuditField(): bool
    {
        return $this->isTimestamp()
            || $this->isDeletedAt();
    }

    /*
    |--------------------------------------------------------------------------
    | Blueprint
    |--------------------------------------------------------------------------
    */

    public function blueprintMethod(): string
    {
        return $this->type->value;
    }

    /*
    |--------------------------------------------------------------------------
    | SQL
    |--------------------------------------------------------------------------
    */

    public function columnDefinition(): string
    {
        return $this->type->value;
    }

    /*
    |--------------------------------------------------------------------------
    | Metadata
    |--------------------------------------------------------------------------
    */

    public function hasMetadata(): bool
    {
        return $this->hasLength()
            || $this->hasPrecision()
            || $this->hasDefault()
            || $this->isUnique();
    }

    /*
    |--------------------------------------------------------------------------
    | Tipos de texto
    |--------------------------------------------------------------------------
    */

    public function isString(): bool
    {
        return $this->type === FieldType::STRING;
    }

    public function isChar(): bool
    {
        return $this->type === FieldType::CHAR;
    }

    public function isText(): bool
    {
        return $this->type === FieldType::TEXT;
    }

    public function isMediumText(): bool
    {
        return $this->type === FieldType::MEDIUM_TEXT;
    }

    public function isLongText(): bool
    {
        return $this->type === FieldType::LONG_TEXT;
    }

    /*
    |--------------------------------------------------------------------------
    | Tipos numéricos
    |--------------------------------------------------------------------------
    */

    public function isInteger(): bool
    {
        return $this->type === FieldType::INTEGER;
    }

    public function isBigInteger(): bool
    {
        return $this->type === FieldType::BIG_INTEGER;
    }

    public function isSmallInteger(): bool
    {
        return $this->type === FieldType::SMALL_INTEGER;
    }

    public function isTinyInteger(): bool
    {
        return $this->type === FieldType::TINY_INTEGER;
    }

    public function isMediumInteger(): bool
    {
        return $this->type === FieldType::MEDIUM_INTEGER;
    }

    public function isDecimal(): bool
    {
        return $this->type === FieldType::DECIMAL;
    }

    public function isFloat(): bool
    {
        return $this->type === FieldType::FLOAT;
    }

    public function isDouble(): bool
    {
        return $this->type === FieldType::DOUBLE;
    }

    public function isBoolean(): bool
    {
        return $this->type === FieldType::BOOLEAN;
    }

    /*
    |--------------------------------------------------------------------------
    | Tipos fecha
    |--------------------------------------------------------------------------
    */

    public function isDate(): bool
    {
        return $this->type === FieldType::DATE;
    }

    public function isTime(): bool
    {
        return $this->type === FieldType::TIME;
    }

    public function isYear(): bool
    {
        return $this->type === FieldType::YEAR;
    }

    public function isDateTime(): bool
    {
        return $this->type === FieldType::DATETIME;
    }

    public function isDateTimeTz(): bool
    {
        return $this->type === FieldType::DATETIME_TZ;
    }

    public function isTimestampType(): bool
    {
        return $this->type === FieldType::TIMESTAMP;
    }

    public function isTimestampTz(): bool
    {
        return $this->type === FieldType::TIMESTAMP_TZ;
    }

    /*
    |--------------------------------------------------------------------------
    | Identificadores
    |--------------------------------------------------------------------------
    */

    public function isId(): bool
    {
        return $this->type === FieldType::ID;
    }

    public function isUuid(): bool
    {
        return $this->type === FieldType::UUID;
    }

    public function isUlid(): bool
    {
        return $this->type === FieldType::ULID;
    }

    public function isForeignId(): bool
    {
        return $this->type === FieldType::FOREIGN_ID;
    }

    /*
    |--------------------------------------------------------------------------
    | Tipos especiales
    |--------------------------------------------------------------------------
    */

    public function isJson(): bool
    {
        return $this->type === FieldType::JSON;
    }

    public function isJsonb(): bool
    {
        return $this->type === FieldType::JSONB;
    }

    public function isEnum(): bool
    {
        return $this->type === FieldType::ENUM;
    }

    public function isBinary(): bool
    {
        return $this->type === FieldType::BINARY;
    }

    /*
    |--------------------------------------------------------------------------
    | Tipos avanzados
    |--------------------------------------------------------------------------
    */

    public function isIpAddress(): bool
    {
        return $this->type === FieldType::IP_ADDRESS;
    }

    public function isMacAddress(): bool
    {
        return $this->type === FieldType::MAC_ADDRESS;
    }

    public function isGeometry(): bool
    {
        return $this->type === FieldType::GEOMETRY;
    }

    public function isPoint(): bool
    {
        return $this->type === FieldType::POINT;
    }

    /*
    |--------------------------------------------------------------------------
    | Agrupaciones
    |--------------------------------------------------------------------------
    */

    public function isTextType(): bool
    {
        return $this->type->isText();
    }

    public function isNumericType(): bool
    {
        return $this->type->isNumeric();
    }

    public function isDateType(): bool
    {
        return $this->type->isDate();
    }

    public function isIdentifierType(): bool
    {
        return $this->type->isIdentifier();
    }

    public function isBinaryType(): bool
    {
        return $this->type->isBinary();
    }

    /*
    |--------------------------------------------------------------------------
    | Tipos de Input
    |--------------------------------------------------------------------------
    */

    public function isTextInput(): bool
    {
        return $this->inputType === InputType::TEXT;
    }

    public function isTextarea(): bool
    {
        return $this->inputType === InputType::TEXTAREA;
    }

    public function isEmail(): bool
    {
        return $this->inputType === InputType::EMAIL;
    }

    public function isPassword(): bool
    {
        return $this->inputType === InputType::PASSWORD;
    }

    public function isNumber(): bool
    {
        return $this->inputType === InputType::NUMBER;
    }

    public function isDateInput(): bool
    {
        return $this->inputType === InputType::DATE;
    }

    public function isTimeInput(): bool
    {
        return $this->inputType === InputType::TIME;
    }

    public function isDateTimeInput(): bool
    {
        return $this->inputType === InputType::DATETIME_LOCAL;
    }

    public function isSelect(): bool
    {
        return $this->inputType === InputType::SELECT;
    }

    public function isCheckbox(): bool
    {
        return $this->inputType === InputType::CHECKBOX;
    }

    public function isRadio(): bool
    {
        return $this->inputType === InputType::RADIO;
    }

    public function isSwitch(): bool
    {
        return $this->inputType === InputType::SWITCH;
    }

    public function isFile(): bool
    {
        return $this->inputType === InputType::FILE;
    }

    public function isImage(): bool
    {
        return $this->inputType === InputType::IMAGE;
    }

    public function isSelectionInput(): bool
    {
        return $this->inputType?->isSelection() ?? false;
    }

    public function isTextInputType(): bool
    {
        return $this->inputType?->isText() ?? false;
    }

    public function isNumericInput(): bool
    {
        return $this->inputType?->isNumeric() ?? false;
    }

    public function isDateInputType(): bool
    {
        return $this->inputType?->isDate() ?? false;
    }

    public function isFileInput(): bool
    {
        return $this->inputType?->isFile() ?? false;
    }

    public function isBooleanInput(): bool
    {
        return $this->inputType?->isBoolean() ?? false;
    }

    public function bladeComponent(): string
    {
        return $this->inputType?->bladeComponent()
            ?? 'x-cn.input';
    }

    public function htmlType(): string
    {
        return $this->inputType?->htmlType()
            ?? 'text';
    }

    public function requiresOptions(): bool
    {
        return $this->inputType?->requiresOptions()
            ?? false;
    }

    public function acceptsFiles(): bool
    {
        return $this->inputType?->acceptsFiles()
            ?? false;
    }

    public function placeholderOrLabel(): string
    {
        return $this->placeholder
            ?? $this->label;
    }

    public function hasIcon(): bool
    {
        return $this->icon !== null;
    }

    public function hasHelp(): bool
    {
        return $this->help !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function hasRelationship(): bool
    {
        return $this->relationship;
    }

    public function hasRelation(): bool
    {
        return $this->hasRelationship();
    }

    public function isRelationship(): bool
    {
        return $this->hasRelationship();
    }

    public function isBelongsTo(): bool
    {
        return $this->relationType === RelationType::BELONGS_TO;
    }

    public function isHasOne(): bool
    {
        return $this->relationType === RelationType::HAS_ONE;
    }

    public function isHasMany(): bool
    {
        return $this->relationType === RelationType::HAS_MANY;
    }

    public function isBelongsToMany(): bool
    {
        return $this->relationType === RelationType::BELONGS_TO_MANY;
    }

    public function isMorphOne(): bool
    {
        return $this->relationType === RelationType::MORPH_ONE;
    }

    public function isMorphMany(): bool
    {
        return $this->relationType === RelationType::MORPH_MANY;
    }

    public function isMorphTo(): bool
    {
        return $this->relationType === RelationType::MORPH_TO;
    }

    public function isMorphToMany(): bool
    {
        return $this->relationType === RelationType::MORPH_TO_MANY;
    }

    public function isMorphedByMany(): bool
    {
        return $this->relationType === RelationType::MORPHED_BY_MANY;
    }

    public function isHasOneThrough(): bool
    {
        return $this->relationType === RelationType::HAS_ONE_THROUGH;
    }

    public function isHasManyThrough(): bool
    {
        return $this->relationType === RelationType::HAS_MANY_THROUGH;
    }

    public function isOneToOne(): bool
    {
        return $this->relationType?->isOneToOne() ?? false;
    }

    public function isOneToMany(): bool
    {
        return $this->relationType?->isOneToMany() ?? false;
    }

    public function isManyToMany(): bool
    {
        return $this->relationType?->isManyToMany() ?? false;
    }

    public function isPolymorphic(): bool
    {
        return $this->relationType?->isPolymorphic() ?? false;
    }

    public function isThrough(): bool
    {
        return $this->relationType?->isThrough() ?? false;
    }

    public function requiresForeignKey(): bool
    {
        return $this->relationType?->requiresForeignKey() ?? false;
    }

    public function requiresPivotTable(): bool
    {
        return $this->relationType?->requiresPivotTable() ?? false;
    }

    public function requiresMorphColumns(): bool
    {
        return $this->relationType?->requiresMorphColumns() ?? false;
    }

    public function eloquentMethod(): ?string
    {
        return $this->relationType?->eloquentMethod();
    }

    public function inverseRelation(): ?RelationType
    {
        return $this->relationType?->inverse();
    }

    public function hasRelatedModel(): bool
    {
        return $this->relatedModel !== null;
    }

    public function hasForeignKey(): bool
    {
        return $this->foreignKey !== null;
    }

    public function hasOwnerKey(): bool
    {
        return $this->ownerKey !== null;
    }

    public function isValidRelationship(): bool
    {
        if (!$this->hasRelationship()) {
            return false;
        }

        return $this->relationType !== null
            && $this->relatedModel !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    public function hasValidation(): bool
    {
        return $this->rules !== [];
    }

    public function hasRules(): bool
    {
        return $this->hasValidation();
    }

    public function hasRule(string $rule): bool
    {
        return in_array(
            $rule,
            $this->rules,
            true
        );
    }

    public function isRequiredRule(): bool
    {
        return $this->hasRule('required');
    }

    public function isNullableRule(): bool
    {
        return $this->hasRule('nullable');
    }

    public function isUniqueRule(): bool
    {
        return $this->hasRule('unique');
    }

    public function isExistsRule(): bool
    {
        return $this->hasRule('exists');
    }

    public function hasMinRule(): bool
    {
        return collect($this->rules)
            ->contains(
                fn(string $rule) => str_starts_with($rule, 'min:')
            );
    }

    public function hasMaxRule(): bool
    {
        return collect($this->rules)
            ->contains(
                fn(string $rule) => str_starts_with($rule, 'max:')
            );
    }

    public function validatesEmail(): bool
    {
        return $this->hasRule('email');
    }

    public function validatesNumeric(): bool
    {
        return $this->hasRule('numeric');
    }

    public function validatesInteger(): bool
    {
        return $this->hasRule('integer');
    }

    public function validatesBoolean(): bool
    {
        return $this->hasRule('boolean');
    }

    public function validatesArray(): bool
    {
        return $this->hasRule('array');
    }

    public function validatesFile(): bool
    {
        return $this->hasRule('file');
    }

    public function validatesImage(): bool
    {
        return $this->hasRule('image');
    }

    public function validatesMimes(): bool
    {
        return collect($this->rules)
            ->contains(
                fn(string $rule) => str_starts_with($rule, 'mimes:')
            );
    }

    public function validatesMaxFileSize(): bool
    {
        return collect($this->rules)
            ->contains(
                fn(string $rule) => str_starts_with($rule, 'max:')
            );
    }

    public function validatesExists(): bool
    {
        return $this->hasRule('exists');
    }

    public function validatesUnique(): bool
    {
        return $this->hasRule('unique');
    }

    public function hasMessages(): bool
    {
        return $this->messages !== [];
    }

    public function hasAttributes(): bool
    {
        return $this->attributes !== [];
    }

    public function validationRules(): array
    {
        return $this->rules;
    }


    public function validationRuleString(): string
    {
        return implode('|', $this->rules);
    }

    public function inferredRules(): array
    {
        if ($this->hasValidation()) {
            return $this->rules;
        }

        return $this->type->defaultValidationRules();
    }

    public function isValidatable(): bool
    {
        return $this->generateRequest();
    }

    /*
    |--------------------------------------------------------------------------
    | Generación - Migraciones
    |--------------------------------------------------------------------------
    */

    public function shouldGenerateMigration(): bool
    {
        return $this->generateMigration;
    }

    public function participatesInMigration(): bool
    {
        return $this->shouldGenerateMigration();
    }

    public function shouldGenerateModel(): bool
    {
        return $this->generateModel;
    }

    public function participatesInModel(): bool
    {
        return $this->shouldGenerateModel();
    }

    public function shouldGenerateRequest(): bool
    {
        return $this->generateRequest;
    }

    public function participatesInRequest(): bool
    {
        return $this->shouldGenerateRequest();
    }

    public function shouldGenerateFactory(): bool
    {
        return $this->generateFactory;
    }

    public function shouldGenerateSeeder(): bool
    {
        return $this->generateSeeder;
    }

    public function shouldGenerateTest(): bool
    {
        return $this->generateTest;
    }

    public function shouldRenderInForm(): bool
    {
        return $this->fillable()
            && !$this->hidden()
            && !$this->isAuditField();
    }

    public function shouldRenderInTable(): bool
    {
        return $this->visible()
            && !$this->hidden();
    }

    public function shouldRenderFilter(): bool
    {
        return $this->filterable();
    }

    public function shouldBeSearchable(): bool
    {
        return $this->searchable();
    }

    public function shouldBeSortable(): bool
    {
        return $this->sortable();
    }

    public function shouldExport(): bool
    {
        return $this->visible()
            && !$this->hidden();
    }

    public function shouldAppearInApi(): bool
    {
        return !$this->hidden();
    }

    public function shouldSerialize(): bool
    {
        return !$this->hidden();
    }

    public function participatesInGeneration(): bool
    {
        return $this->shouldGenerateMigration()
            || $this->shouldGenerateModel()
            || $this->shouldGenerateRequest()
            || $this->shouldGenerateFactory()
            || $this->shouldGenerateSeeder()
            || $this->shouldGenerateTest();
    }

    public function generationSummary(): array
    {
        return [
            'migration' => $this->shouldGenerateMigration(),
            'model' => $this->shouldGenerateModel(),
            'request' => $this->shouldGenerateRequest(),
            'factory' => $this->shouldGenerateFactory(),
            'seeder' => $this->shouldGenerateSeeder(),
            'test' => $this->shouldGenerateTest(),
        ];
    }






}
