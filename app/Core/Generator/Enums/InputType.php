<?php

declare(strict_types=1);

namespace App\Core\Generator\Enums;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Enum que representa los componentes de entrada soportados
 * por el CN UI Framework y el CN Generator.
 *
 * Cada tipo define el componente visual recomendado para
 * representar un campo dentro de formularios generados.
 *
 * @package App\Core\Generator\Enums
 * @since 2.0.0
 */

enum InputType: string
{
    public const SEARCH = 'bi bi-search';

    public const EMAIL = 'bi bi-envelope';

    public const CALENDAR = 'bi bi-calendar';

    /*
    |--------------------------------------------------------------------------
    | Entradas básicas
    |--------------------------------------------------------------------------
    */

    case TEXT = 'text';

    case EMAIL = 'email';

    case PASSWORD = 'password';

    case NUMBER = 'number';

    case TEL = 'tel';

    case URL = 'url';

    case SEARCH = 'search';

    case HIDDEN = 'hidden';

        /*
    |--------------------------------------------------------------------------
    | Fechas
    |--------------------------------------------------------------------------
    */

    case DATE = 'date';

    case TIME = 'time';

    case DATETIME_LOCAL = 'datetime-local';

    case MONTH = 'month';

    case WEEK = 'week';

        /*
    |--------------------------------------------------------------------------
    | Selección
    |--------------------------------------------------------------------------
    */

    case SELECT = 'select';

    case RADIO = 'radio';

    case CHECKBOX = 'checkbox';

    case SWITCH = 'switch';

        /*
    |--------------------------------------------------------------------------
    | Texto
    |--------------------------------------------------------------------------
    */

    case TEXTAREA = 'textarea';

    case EDITOR = 'editor';

        /*
    |--------------------------------------------------------------------------
    | Archivos
    |--------------------------------------------------------------------------
    */

    case FILE = 'file';

    case IMAGE = 'image';

        /*
    |--------------------------------------------------------------------------
    | Especiales
    |--------------------------------------------------------------------------
    */

    case COLOR = 'color';

    case RANGE = 'range';

    case JSON = 'json';

    case UUID = 'uuid';

    case SLUG = 'slug';

    case CURRENCY = 'currency';

    case MONEY = 'money';

    /*
    |--------------------------------------------------------------------------
    | Controles de texto
    |--------------------------------------------------------------------------
    */
    private const TEXT_INPUTS = [

        self::TEXT,
        self::EMAIL,
        self::PASSWORD,
        self::TEL,
        self::URL,
        self::SEARCH,

    ];

    /*
    |--------------------------------------------------------------------------
    | Texto extendido
    |--------------------------------------------------------------------------
    */

    private const TEXT_AREA_INPUTS = [

        self::TEXTAREA,
        self::EDITOR,
        self::JSON,

    ];

    /*
    |--------------------------------------------------------------------------
    | Numéricos
    |--------------------------------------------------------------------------
    */
    private const NUMERIC_INPUTS = [

        self::NUMBER,
        self::RANGE,
        self::CURRENCY,
        self::MONEY,

    ];

    /*
    |--------------------------------------------------------------------------
    | Fechas
    |--------------------------------------------------------------------------
    */

    private const DATE_INPUTS = [

        self::DATE,
        self::TIME,
        self::DATETIME_LOCAL,
        self::MONTH,
        self::WEEK,

    ];

    /*
    |--------------------------------------------------------------------------
    | Archivos
    |--------------------------------------------------------------------------
    */

    private const FILE_INPUTS = [

        self::FILE,
        self::IMAGE,

    ];

    /*
    |--------------------------------------------------------------------------
    | Selección
    |--------------------------------------------------------------------------
    */

    private const SELECTION_INPUTS = [

        self::SELECT,
        self::RADIO,
        self::CHECKBOX,
        self::SWITCH,

    ];

    /*
    |--------------------------------------------------------------------------
    | Booleanos
    |--------------------------------------------------------------------------
    */

    private const BOOLEAN_INPUTS = [

        self::CHECKBOX,
        self::SWITCH,

    ];

    /*
    |--------------------------------------------------------------------------
    | Especiales
    |--------------------------------------------------------------------------
    */
    private const SPECIAL_INPUTS = [

        self::COLOR,
        self::JSON,
        self::UUID,
        self::SLUG,
        self::CURRENCY,

    ];

    /*
    |--------------------------------------------------------------------------
    | Crear un método privado.
    |--------------------------------------------------------------------------
    */

    private function supportsStandardRendering(): bool
    {
        return $this->isVisible();
    }

    /*
    |--------------------------------------------------------------------------
    | Crear otro helper privado.
    |--------------------------------------------------------------------------
    */

    private function supportsTextDecoration(): bool
    {
        return
            $this->isText()
            || $this->isDate()
            || $this->isNumeric();
    }

    /*
    |--------------------------------------------------------------------------
    | Crear otro helper.
    |--------------------------------------------------------------------------
    */

    private function supportsInteractiveInput(): bool
    {
        return
            !$this->isHidden();
    }


    /*
    |--------------------------------------------------------------------------
    | Crear helper para controles de texto.
    |--------------------------------------------------------------------------
    */

    private function isEditableText(): bool
    {
        return
            $this->isText()
            || $this == self::EMAIL
            || $this == self::PASSWORD
            || $this == self::SEARCH
            || $this == self::URL
            || $this == self::TEL;
    }

    /*
    |--------------------------------------------------------------------------
    | Crear otro helper.
    |--------------------------------------------------------------------------
    */

    private function supportsNumericConfiguration(): bool
    {
        return
            $this->isNumeric()
            || $this->isDate();
    }

    /*
    |--------------------------------------------------------------------------
    | acceptsTextInput()
    |--------------------------------------------------------------------------
    */

    /**
     * Indica si el control acepta entrada textual.
     */
    public function acceptsTextInput(): bool
    {
        return match ($this) {

            self::TEXT,
            self::EMAIL,
            self::PASSWORD,
            self::TEL,
            self::URL,
            self::SEARCH,
            self::TEXTAREA,
            self::EDITOR,
            self::UUID,
            self::SLUG,
            self::JSON => true,

            default => false,
        };
    }



    /*
    |--------------------------------------------------------------------------
    | acceptsNumericInput()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control acepta valores numéricos.
     */
    public function acceptsNumericInput(): bool
    {
        return in_array($this, self::NUMERIC_INPUTS, true);
    }

    /*
    |--------------------------------------------------------------------------
    | acceptsDateInput()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control acepta fechas u horas.
     */
    public function acceptsDateInput(): bool
    {
        return in_array($this, self::DATE_INPUTS, true);
    }

    /*
    |--------------------------------------------------------------------------
    | isSelection()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control permite seleccionar opciones.
     */
    public function isSelection(): bool
    {
        return in_array($this, self::SELECTION_INPUTS, true);
    }

    /*
    |--------------------------------------------------------------------------
    | isBoolean()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si representa un valor booleano.
     */
    public function isBoolean(): bool
    {
        return in_array($this, self::BOOLEAN_INPUTS, true);
    }

    /*
    |--------------------------------------------------------------------------
    | acceptsFileInput()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control recibe archivos.
     */
    public function acceptsFileInput(): bool
    {
        return in_array($this, self::FILE_INPUTS, true);
    }

    /*
    |--------------------------------------------------------------------------
    | isHidden()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control es oculto.
     */
    public function isHidden(): bool
    {
        return $this === self::HIDDEN;
    }

    /**
     * Indica si el control es visible para el usuario.
     */
    public function isVisible(): bool
    {
        return !$this->isHidden();
    }

    /*
    |--------------------------------------------------------------------------
    | isRichText()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control utiliza un editor enriquecido.
     */
    public function isRichText(): bool
    {
        return $this === self::EDITOR;
    }

    /*
    |--------------------------------------------------------------------------
    | isSpecial()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control pertenece a una categoría especial.
     */
    public function isSpecial(): bool
    {
        return in_array($this, self::SPECIAL_INPUTS, true);
    }

    /*
    |--------------------------------------------------------------------------
    | supportsUserInput()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el usuario puede introducir información directamente.
     */
    public function supportsUserInput(): bool
    {
        return $this->supportsStandardRendering();
    }

    /*
    |--------------------------------------------------------------------------
    | htmlType()
            Aunque el enum ya almacena el valor HTML ($this->value),
            este método desacopla a los generadores del detalle de
            implementación.
    |--------------------------------------------------------------------------
    */
    /**
     * Obtiene el tipo HTML del control.
     * <input type="{{ $field->inputType()->htmlType() }}">
     */
    public function htmlType(): string
    {
        return $this->value;
    }

    /*
    |--------------------------------------------------------------------------
    | supportsPlaceholder()
        No todos los controles admiten placeholder.
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control admite placeholder.
     */
    public function supportsPlaceholder(): bool
    {
        return $this->supportsTextDecoration();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsReadonly()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite readonly.
     */
    public function supportsReadonly(): bool
    {
        return $this->supportsTextDecoration();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsAutocomplete()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite autocomplete.
     */
    public function supportsAutocomplete(): bool
    {
        return $this->isEditableText();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsMultiple()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite múltiples valores.
     */
    public function supportsMultiple(): bool
    {
        return match ($this) {

            self::SELECT,
            self::FILE => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | supportsAccept()
            El atributo accept es propio de controles de archivos.
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite el atributo accept.
     */
    public function supportsAccept(): bool
    {
        return match ($this) {

            self::FILE,
            self::IMAGE => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | supportsMinMax()
            Agrupa el soporte para min y max.
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite min y max.
     */
    public function supportsMinMax(): bool
    {
        return $this->supportsNumericConfiguration();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsStep()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite step.
     */
    public function supportsStep(): bool
    {
        return $this->supportsNumericConfiguration();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsPattern()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite pattern.
     */
    public function supportsPattern(): bool
    {
        return $this->isEditableText();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsSpellcheck()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite spellcheck.
     */
    public function supportsSpellcheck(): bool
    {
        return $this->isEditableText();
    }

    /*
    |--------------------------------------------------------------------------
    | componentName()
            Recomiendo este nombre en lugar de bladeComponent().
            ¿Por qué?
            Porque devuelve un identificador lógico, no un componente
            Blade específico.
    |--------------------------------------------------------------------------
    */
    /**
     * Obtiene el nombre lógico del componente UI.
     */
    public function componentName(): string
    {
        return match ($this) {

            self::TEXT,
            self::EMAIL,
            self::PASSWORD,
            self::NUMBER,
            self::TEL,
            self::URL,
            self::SEARCH,
            self::UUID,
            self::SLUG,
            self::CURRENCY
            => 'input',

            self::TEXTAREA,
            self::EDITOR,
            self::JSON
            => 'textarea',

            self::SELECT
            => 'select',

            self::CHECKBOX,
            self::SWITCH
            => 'checkbox',

            self::RADIO
            => 'radio',

            self::FILE => 'file',
            self::IMAGE
            => 'image',

            self::DATE,
            self::TIME,
            self::DATETIME_LOCAL,
            self::MONTH,
            self::WEEK
            => 'date',

            self::COLOR
            => 'color',

            self::RANGE
            => 'range',

            self::HIDDEN
            => 'hidden',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | supportsFloatingLabel()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite etiquetas flotantes.
     */
    public function supportsFloatingLabel(): bool
    {
        return match ($this) {

            self::CHECKBOX,
            self::RADIO,
            self::SWITCH,
            self::HIDDEN => false,

            default => true,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | supportsPrefixSuffix()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite prefijos y sufijos.
     */
    public function supportsPrefixSuffix(): bool
    {
        return match ($this) {

            self::TEXT,
            self::NUMBER,
            self::EMAIL,
            self::TEL,
            self::URL,
            self::SEARCH,
            self::CURRENCY => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | supportsIcon()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite iconos.
     */
    public function supportsIcon(): bool
    {
        return $this !== self::HIDDEN;
    }

    /*
    |--------------------------------------------------------------------------
    | supportsMask()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite máscaras de entrada.
     */
    public function supportsMask(): bool
    {
        return match ($this) {

            self::TEL,
            self::CURRENCY,
            self::UUID,
            self::SLUG => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | supportsClearButton()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite botón para limpiar el valor.
     */
    public function supportsClearButton(): bool
    {
        return match ($this) {

            self::TEXT,
            self::EMAIL,
            self::SEARCH,
            self::URL,
            self::NUMBER,
            self::DATE,
            self::TIME,
            self::DATETIME_LOCAL => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    |   Helpers de Generación
        Objetivo
        Esta entrega incorpora métodos que permitirán a los generadores
        tomar decisiones sin recurrir a grandes bloques de match o if.
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | supportsLabel()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control admite una etiqueta visible.
     */
    public function supportsLabel(): bool
    {
        return $this->supportsStandardRendering();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsRequiredIndicator()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si puede mostrar el indicador de campo requerido.
     */
    public function supportsRequiredIndicator(): bool
    {
        return $this->supportsLabel();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsErrorMessage()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite mensajes de validación.
     */
    public function supportsErrorMessage(): bool
    {
        return $this->supportsInteractiveInput();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsDescription()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite texto descriptivo o de ayuda.
     */
    public function supportsDescription(): bool
    {
        return $this->supportsStandardRendering();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsGridLayout()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si puede participar en el layout estándar del formulario.
     */
    public function supportsGridLayout(): bool
    {
        return $this->supportsStandardRendering();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsAutoFocus()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite autofocus.
     */
    public function supportsAutoFocus(): bool
    {
        return $this->supportsTextDecoration();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsTabIndex()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si puede participar en la navegación por teclado.
     */
    public function supportsTabIndex(): bool
    {
        return $this->supportsInteractiveInput();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsLiveValidation()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si puede utilizar validación en tiempo real.
     */
    public function supportsLiveValidation(): bool
    {
        return $this->supportsInteractiveInput();
    }

    /*
    |--------------------------------------------------------------------------
    | supportsModelBinding()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control admite enlace directo con modelos.
     */
    public function supportsModelBinding(): bool
    {
        return $this->supportsStandardRendering();
    }

    /*
    |--------------------------------------------------------------------------
    |  supportsStatePersistence()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control conserva estado entre solicitudes.
     */
    public function supportsStatePersistence(): bool
    {
        return $this->supportsStandardRendering();
    }

    public function isText(): bool
    {
        return in_array($this, self::TEXT_INPUTS, true)
            || in_array($this, self::TEXT_AREA_INPUTS, true);
    }

    public function isNumeric(): bool
    {
        return in_array($this, self::NUMERIC_INPUTS, true);
    }


    public function isDate(): bool
    {
        return in_array($this, self::DATE_INPUTS, true);
    }

    public function isFile(): bool
    {
        return in_array($this, self::FILE_INPUTS, true);
    }

    public function requiresOptions(): bool
    {
        return match ($this) {
            self::SELECT,
            self::RADIO => true,
            default => false,
        };
    }

    public function acceptsFiles(): bool
    {
        return $this->isFile();
    }

    public function bladeComponentName(): string
    {
        return $this->componentName();
    }

    public function bladeComponentTag(): string
    {
        return 'x-cn.' . $this->bladeComponentName();
    }

    /**
     * Obtiene los atributos HTML sugeridos para este tipo
     * de componente.
     *
     * @return array<string,mixed>
     */
    public function defaultAttributes(): array
    {
        return match ($this) {

            /*
        |--------------------------------------------------------------------------
        | Entradas numéricas
        |--------------------------------------------------------------------------
        */
            self::NUMBER => [
                'type' => 'number',
            ],

            self::RANGE => [
                'type' => 'range',
            ],

            self::CURRENCY,
            self::MONEY => [
                'type' => 'number',
                'step' => '0.01',
            ],

            /*
        |--------------------------------------------------------------------------
        | Fechas
        |--------------------------------------------------------------------------
        */
            self::DATE,
            self::TIME,
            self::DATETIME_LOCAL,
            self::MONTH,
            self::WEEK => [
                'type' => $this->htmlType(),
            ],

            /*
        |--------------------------------------------------------------------------
        | Archivos
        |--------------------------------------------------------------------------
        */
            self::FILE => [
                'type' => 'file',
            ],

            self::IMAGE => [
                'type' => 'file',
                'accept' => 'image/*',
            ],

            /*
        |--------------------------------------------------------------------------
        | Texto enriquecido
        |--------------------------------------------------------------------------
        */
            self::EDITOR => [
                'data-editor' => 'true',
            ],

            /*
        |--------------------------------------------------------------------------
        | JSON
        |--------------------------------------------------------------------------
        */
            self::JSON => [
                'spellcheck' => 'false',
            ],

            /*
        |--------------------------------------------------------------------------
        | Color
        |--------------------------------------------------------------------------
        */
            self::COLOR => [
                'type' => 'color',
            ],

            /*
        |--------------------------------------------------------------------------
        | Controles sin atributos especiales
        |--------------------------------------------------------------------------
        */
            default => [],
        };
    }

    /**
     * Obtiene la clase CSS recomendada para este tipo de control.
     */
    public function defaultCssClass(): string
    {
        return match ($this) {

            self::TEXT,
            self::EMAIL,
            self::PASSWORD,
            self::TEL,
            self::URL,
            self::SEARCH,
            self::UUID,
            self::SLUG
            => 'cn-input',

            self::NUMBER,
            self::RANGE,
            self::CURRENCY,
            self::MONEY
            => 'cn-number',

            self::DATE,
            self::TIME,
            self::DATETIME_LOCAL,
            self::MONTH,
            self::WEEK
            => 'cn-date',

            self::TEXTAREA,
            self::EDITOR,
            self::JSON
            => 'cn-textarea',

            self::SELECT
            => 'cn-select',

            self::CHECKBOX,
            self::SWITCH
            => 'cn-checkbox',

            self::RADIO
            => 'cn-radio',

            self::FILE,
            self::IMAGE
            => 'cn-file',

            self::COLOR
            => 'cn-color',

            self::HIDDEN
            => 'cn-hidden',
        };
    }

    /**
     * Obtiene el icono sugerido para este tipo de control.
     */
    public function defaultIcon(): string
    {
        return match ($this) {

            self::TEXT,
            self::TEXTAREA
            => 'bi bi-fonts',

            self::EMAIL
            => 'bi bi-envelope',

            self::PASSWORD
            => 'bi bi-lock',

            self::NUMBER,
            self::RANGE
            => 'bi bi-123',

            self::CURRENCY,
            self::MONEY
            => 'bi bi-cash-stack',

            self::DATE,
            self::TIME,
            self::DATETIME_LOCAL,
            self::MONTH,
            self::WEEK
            => 'bi bi-calendar-event',

            self::TEL
            => 'bi bi-telephone',

            self::URL
            => 'bi bi-link-45deg',

            self::SEARCH
            => 'bi bi-search',

            self::SELECT
            => 'bi bi-list-ul',

            self::CHECKBOX,
            self::SWITCH
            => 'bi bi-check-square',

            self::RADIO
            => 'bi bi-record-circle',

            self::FILE
            => 'bi bi-file-earmark',

            self::IMAGE
            => 'bi bi-image',

            self::EDITOR
            => 'bi bi-pencil-square',

            self::COLOR
            => 'bi bi-palette',

            self::JSON
            => 'bi bi-braces',

            self::UUID
            => 'bi bi-fingerprint',

            self::SLUG
            => 'bi bi-tag',

            self::HIDDEN
            => 'bi bi-eye-slash',
        };
    }

    /**
     * Obtiene el placeholder sugerido para este tipo.
     */
    public function defaultPlaceholder(): string
    {
        return match ($this) {

            self::TEXT
            => 'Ingrese un valor...',

            self::EMAIL
            => 'usuario@dominio.com',

            self::PASSWORD
            => 'Ingrese la contraseña...',

            self::NUMBER
            => '0',

            self::CURRENCY,
            self::MONEY
            => '0.00',

            self::TEL
            => 'Ingrese el teléfono...',

            self::URL
            => 'https://...',

            self::SEARCH
            => 'Buscar...',

            self::TEXTAREA
            => 'Escriba aquí...',

            self::EDITOR
            => 'Comience a escribir...',

            self::DATE
            => 'Seleccione una fecha',

            self::TIME
            => 'Seleccione una hora',

            self::DATETIME_LOCAL
            => 'Seleccione fecha y hora',

            self::MONTH
            => 'Seleccione un mes',

            self::WEEK
            => 'Seleccione una semana',

            self::SELECT
            => 'Seleccione una opción',

            self::FILE,
            self::IMAGE
            => 'Seleccione un archivo',

            self::COLOR
            => 'Seleccione un color',

            default
            => '',
        };
    }

    public function supportsOldValue(): bool
    {
        return !$this->isHidden();
    }

    /**
     * Obtiene la expresión Blade recomendada para enlazar el valor.
     */
    public function defaultBladeBinding(): string
    {
        return match ($this) {

            self::CHECKBOX,
            self::SWITCH
            => ':checked',

            self::RADIO
            => ':checked',

            self::FILE,
            self::IMAGE
            => '',

            default
            => ':value',
        };
    }

    /**
     * Obtiene la regla de validación sugerida.
     */
    public function defaultValidationRule(): string
    {
        return match ($this) {

            self::TEXT,
            self::TEXTAREA,
            self::EDITOR
            => 'string',

            self::EMAIL
            => 'email',

            self::PASSWORD
            => 'string|min:8',

            self::NUMBER
            => 'integer',

            self::CURRENCY,
            self::MONEY
            => 'numeric',

            self::RANGE
            => 'numeric',

            self::DATE,
            self::MONTH,
            self::WEEK,
            self::TIME,
            self::DATETIME_LOCAL
            => 'date',

            self::CHECKBOX,
            self::SWITCH
            => 'boolean',

            self::SELECT
            => 'exists',

            self::RADIO
            => 'string',

            self::FILE
            => 'file',

            self::IMAGE
            => 'image',

            self::URL
            => 'url',

            self::TEL
            => 'string',

            self::COLOR
            => 'string|size:7',

            self::UUID
            => 'uuid',

            self::SLUG
            => 'alpha_dash',

            self::JSON
            => 'json',

            self::SEARCH
            => 'string',

            self::HIDDEN
            => 'string',
        };
    }

    /**
     * Obtiene el ancho recomendado dentro del grid del formulario.
     */
    public function preferredColumnWidth(): string
    {
        return match ($this) {

            self::TEXTAREA,
            self::EDITOR,
            self::JSON
            => 'col-md-12',

            self::CHECKBOX,
            self::SWITCH,
            self::RADIO
            => 'col-md-12',

            self::FILE,
            self::IMAGE
            => 'col-md-12',

            default
            => 'col-md-6',
        };
    }

    /**
     * Obtiene todos los metadatos utilizados por el CN Generator.
     *
     * Este método constituye el contrato oficial entre el dominio
     * (InputType) y la capa de presentación del Generator.
     *
     * @return array<string,mixed>
     */
    public function generatorMetadata(): array
    {
        return [

            /*
        |--------------------------------------------------------------------------
        | Identidad del componente
        |--------------------------------------------------------------------------
        */

            'type' => $this->value,

            'component' => $this->componentName(),

            'blade' => $this->bladeComponentTag(),

            /*
        |--------------------------------------------------------------------------
        | Presentación
        |--------------------------------------------------------------------------
        */

            'cssClass' => $this->defaultCssClass(),

            'icon' => $this->defaultIcon(),

            'placeholder' => $this->defaultPlaceholder(),

            'columnWidth' => $this->preferredColumnWidth(),

            /*
        |--------------------------------------------------------------------------
        | Blade
        |--------------------------------------------------------------------------
        */

            'binding' => $this->defaultBladeBinding(),

            'attributes' => $this->defaultAttributes(),

            /*
        |--------------------------------------------------------------------------
        | Validación
        |--------------------------------------------------------------------------
        */

            'validation' => $this->defaultValidationRule(),

            /*
        |--------------------------------------------------------------------------
        | Capacidades
        |--------------------------------------------------------------------------
        */

            'supportsLabel' => $this->supportsLabel(),

            'supportsPlaceholder' => $this->supportsPlaceholder(),

            'supportsReadonly' => $this->supportsReadonly(),

            'supportsAutocomplete' => $this->supportsAutocomplete(),

            'supportsIcon' => $this->supportsIcon(),

            'supportsPrefixSuffix' => $this->supportsPrefixSuffix(),

            'supportsFloatingLabel' => $this->supportsFloatingLabel(),

            'supportsMask' => $this->supportsMask(),

            'supportsMultiple' => $this->supportsMultiple(),

            'supportsAccept' => $this->supportsAccept(),

            'supportsMinMax' => $this->supportsMinMax(),

            'supportsStep' => $this->supportsStep(),

            'supportsPattern' => $this->supportsPattern(),

            'supportsSpellcheck' => $this->supportsSpellcheck(),

            'supportsOldValue' => $this->supportsOldValue(),

            'supportsErrorMessage' => $this->supportsErrorMessage(),

            'supportsDescription' => $this->supportsDescription(),

            'supportsGridLayout' => $this->supportsGridLayout(),

            'supportsLiveValidation' => $this->supportsLiveValidation(),

            'supportsStatePersistence' => $this->supportsStatePersistence(),

        ];
    }
}
