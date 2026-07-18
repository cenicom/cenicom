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

    /*
    |--------------------------------------------------------------------------
    | 📦 acceptsTextInput()
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
    | 📦 acceptsNumericInput()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control acepta valores numéricos.
     */
    public function acceptsNumericInput(): bool
    {
        return match ($this) {

            self::NUMBER,
            self::RANGE,
            self::CURRENCY => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 acceptsDateInput()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control acepta fechas u horas.
     */
    public function acceptsDateInput(): bool
    {
        return match ($this) {

            self::DATE,
            self::TIME,
            self::MONTH,
            self::WEEK,
            self::DATETIME_LOCAL => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 isSelection()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control permite seleccionar opciones.
     */
    public function isSelection(): bool
    {
        return match ($this) {

            self::SELECT,
            self::RADIO,
            self::CHECKBOX,
            self::SWITCH => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 isBoolean()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si representa un valor booleano.
     */
    public function isBoolean(): bool
    {
        return match ($this) {

            self::CHECKBOX,
            self::SWITCH => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 acceptsFileInput()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control recibe archivos.
     */
    public function acceptsFileInput(): bool
    {
        return match ($this) {

            self::FILE,
            self::IMAGE => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 isHidden()
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
    | 📦 isRichText()
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
    | 📦 isSpecial()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control pertenece a una categoría especial.
     */
    public function isSpecial(): bool
    {
        return match ($this) {

            self::COLOR,
            self::JSON,
            self::UUID,
            self::SLUG,
            self::CURRENCY => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsUserInput()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el usuario puede introducir información directamente.
     */
    public function supportsUserInput(): bool
    {
        return $this->isVisible();
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 htmlType()
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
    | 📦 supportsPlaceholder()
        No todos los controles admiten placeholder.
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control admite placeholder.
     */
    public function supportsPlaceholder(): bool
    {
        return match ($this) {

            self::TEXT,
            self::EMAIL,
            self::PASSWORD,
            self::NUMBER,
            self::TEL,
            self::URL,
            self::SEARCH,
            self::TEXTAREA,
            self::DATE,
            self::TIME,
            self::DATETIME_LOCAL,
            self::MONTH,
            self::WEEK => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsReadonly()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite readonly.
     */
    public function supportsReadonly(): bool
    {
        return match ($this) {

            self::TEXT,
            self::EMAIL,
            self::PASSWORD,
            self::NUMBER,
            self::TEL,
            self::URL,
            self::SEARCH,
            self::DATE,
            self::TIME,
            self::DATETIME_LOCAL,
            self::MONTH,
            self::WEEK,
            self::TEXTAREA => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsAutocomplete()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite autocomplete.
     */
    public function supportsAutocomplete(): bool
    {
        return match ($this) {

            self::TEXT,
            self::EMAIL,
            self::PASSWORD,
            self::TEL,
            self::URL,
            self::SEARCH => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsMultiple()
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
    | 📦 supportsAccept()
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
    | 📦 supportsMinMax()
            Agrupa el soporte para min y max.
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite min y max.
     */
    public function supportsMinMax(): bool
    {
        return match ($this) {

            self::NUMBER,
            self::RANGE,
            self::DATE,
            self::TIME,
            self::DATETIME_LOCAL,
            self::MONTH,
            self::WEEK => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsStep()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite step.
     */
    public function supportsStep(): bool
    {
        return match ($this) {

            self::NUMBER,
            self::RANGE,
            self::DATE,
            self::TIME,
            self::DATETIME_LOCAL => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsPattern()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite pattern.
     */
    public function supportsPattern(): bool
    {
        return match ($this) {

            self::TEXT,
            self::EMAIL,
            self::PASSWORD,
            self::TEL,
            self::SEARCH,
            self::URL => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsSpellcheck()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite spellcheck.
     */
    public function supportsSpellcheck(): bool
    {
        return match ($this) {

            self::TEXT,
            self::TEXTAREA,
            self::EDITOR => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 componentName()
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

            self::FILE,
            self::IMAGE
            => 'file',

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
    | 📦 supportsFloatingLabel()
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
            self::SWITCH ,
            self::HIDDEN => false,

            default => true,

        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsHelpText()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite texto de ayuda.
     */
    public function supportsHelpText(): bool
    {
        return $this->isVisible();
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsValidationFeedback()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite mensajes de validación.
     */
    public function supportsValidationFeedback(): bool
    {
        return $this->isVisible();
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsPrefixSuffix()
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
    | 📦 supportsIcon()
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
    | 📦 supportsMask()
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
    | 📦 supportsClearButton()
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
    | 📦 supportsLabel()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control admite una etiqueta visible.
     */
    public function supportsLabel(): bool
    {
        return $this->isVisible();
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsRequiredIndicator()
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
    | 📦 supportsErrorMessage()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite mensajes de validación.
     */
    public function supportsErrorMessage(): bool
    {
        return $this !== self::HIDDEN;
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsHint()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite texto descriptivo o de ayuda.
     */
    public function supportsHint(): bool
    {
        return $this->isVisible();
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsGridLayout()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si puede participar en el layout estándar del formulario.
     */
    public function supportsGridLayout(): bool
    {
        return $this->isVisible();
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsAutoFocus()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si admite autofocus.
     */
    public function supportsAutoFocus(): bool
    {
        return match ($this) {

            self::TEXT,
            self::EMAIL,
            self::PASSWORD,
            self::NUMBER,
            self::TEL,
            self::URL,
            self::SEARCH,
            self::TEXTAREA,
            self::DATE,
            self::TIME,
            self::DATETIME_LOCAL => true,

            default => false,

        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsTabIndex()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si puede participar en la navegación por teclado.
     */
    public function supportsTabIndex(): bool
    {
        return $this->isVisible();
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsLiveValidation()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si puede utilizar validación en tiempo real.
     */
    public function supportsLiveValidation(): bool
    {
        return match ($this) {

            self::HIDDEN => false,

            default => true,

        };
    }

    /*
    |--------------------------------------------------------------------------
    | 📦 supportsModelBinding()
    |--------------------------------------------------------------------------
    */
    /**
     * Indica si el control admite enlace directo con modelos.
     */
    public function supportsModelBinding(): bool
    {
        return $this->isVisible();
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
        return $this->isVisible();
    }


    public function isText(): bool
    {
        return in_array($this, [
            self::TEXT,
            self::TEXTAREA,
        ]);
    }

    public function isNumeric(): bool
    {
        return in_array($this, [
            self::NUMBER,
            self::RANGE,
            self::CURRENCY,
        ], true);
    }


    public function isDate(): bool
    {
        return in_array($this, [
            self::DATE,
            self::TIME,
            self::DATETIME_LOCAL,
            self::MONTH,
            self::WEEK,
        ], true);
    }

    public function isFile(): bool
    {
        return in_array($this, [
            self::FILE,
            self::IMAGE,
        ]);
    }

    public function requiresOptions(): bool
    {
        return $this === self::SELECT;
    }

    public function acceptsFiles(): bool
    {
        return $this->isFile();
    }



}
