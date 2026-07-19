<?php

declare(strict_types=1);

namespace App\Core\Generator\Enums;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Enum que representa los tipos de relaciones soportadas
 * por el CN Generator.
 *
 * Centraliza la definición de relaciones Eloquent para
 * garantizar consistencia entre modelos, migraciones,
 * factories, seeders y generadores.
 *
 * @package App\Core\Generator\Enums
 * @since 2.0.0
 */
enum RelationType: string
{
    /*
    |--------------------------------------------------------------------------
    | Relaciones Laravel básicas
    |--------------------------------------------------------------------------
    */

    case BELONGS_TO = 'belongs_to';

    case HAS_ONE = 'has_one';

    case HAS_MANY = 'has_many';

    case BELONGS_TO_MANY = 'belongs_to_many';

    /*
    |--------------------------------------------------------------------------
    | Relaciones Polimórficas
    |--------------------------------------------------------------------------
    */

    case MORPH_ONE = 'morph_one';

    case MORPH_MANY = 'morph_many';

    case MORPH_TO = 'morph_to';

    case MORPH_TO_MANY = 'morph_to_many';

    case MORPHED_BY_MANY = 'morphed_by_many';

    /*
    |--------------------------------------------------------------------------
    | Through
    |--------------------------------------------------------------------------
    */

    case HAS_ONE_THROUGH = 'has_One_Through';

    case HAS_MANY_THROUGH = 'has_Many_Through';

    /*
    |--------------------------------------------------------------------------
    | isMany()
            Determina si la relación representa una colección de modelos.
            Será útil para:
                ModelGenerator
                ViewGenerator
                RepositoryGenerator
                Ejemplo:
                if ($relation->isMany()) {
                    // Generar colección
                }
    |--------------------------------------------------------------------------
    */
    public function isMany(): bool
    {
        return match ($this) {
            self::HAS_MANY,
            self::BELONGS_TO_MANY,
            self::MORPH_MANY,
            self::MORPH_TO_MANY,
            self::MORPHED_BY_MANY,
            self::HAS_MANY_THROUGH => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | isMorph()
        Identifica las relaciones polimórficas.
            Permitirá generar automáticamente:
                columnas *_type
                columnas *_id
                métodos polimórficos
            sin necesidad de switch.
    |--------------------------------------------------------------------------
    */
    public function isMorph(): bool
    {
        return match ($this) {
            self::MORPH_ONE,
            self::MORPH_MANY,
            self::MORPH_TO,
            self::MORPH_TO_MANY,
            self::MORPHED_BY_MANY => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | isThrough()
        Detecta relaciones "Through".
        Será útil cuando el generador de modelos deba importar modelos
        intermedios.
    |--------------------------------------------------------------------------
    */

    public function isThrough(): bool
    {
        return match ($this) {
            self::HAS_ONE_THROUGH,
            self::HAS_MANY_THROUGH => true,

            default => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | requiresPivot()
        Indica cuándo es necesaria una tabla pivote.
        Este método será utilizado principalmente por:
            MigrationGenerator
            SeederGenerator
            FactoryGenerator
    |--------------------------------------------------------------------------
    */

    public function requiresPivot(): bool
    {
        return match ($this) {
            self::BELONGS_TO_MANY,
            self::MORPH_TO_MANY,
            self::MORPHED_BY_MANY => true,

            default => false,
        };
    }

    public function isOneToOne(): bool
    {
        return match ($this) {
            self::HAS_ONE,
            self::BELONGS_TO,
            self::MORPH_ONE => true,

            default => false,
        };
    }


    public function isOneToMany(): bool
    {
        return match ($this) {
            self::HAS_MANY,
            self::BELONGS_TO,
            self::MORPH_MANY,
            self::HAS_MANY_THROUGH => true,

            default => false,
        };
    }


    public function isManyToMany(): bool
    {
        return match ($this) {
            self::BELONGS_TO_MANY,
            self::MORPH_TO_MANY,
            self::MORPHED_BY_MANY => true,

            default => false,
        };
    }


    public function isPolymorphic(): bool
    {
        return $this->isMorph();
    }

    public function requiresForeignKey(): bool
    {
        return match ($this) {

            self::BELONGS_TO,

            self::MORPH_TO

            => true,

            default

            => false,
        };
    }
    public function requiresPivotTable(): bool
    {
        return $this->requiresPivot();
    }
    public function requiresMorphColumns(): bool
    {
        return $this === self::MORPH_TO;
    }
    public function eloquentMethod(): string
    {
        return match ($this) {

            self::BELONGS_TO
            => 'belongsTo',

            self::HAS_ONE
            => 'hasOne',

            self::HAS_MANY
            => 'hasMany',

            self::BELONGS_TO_MANY
            => 'belongsToMany',

            self::MORPH_ONE
            => 'morphOne',

            self::MORPH_MANY
            => 'morphMany',

            self::MORPH_TO
            => 'morphTo',

            self::MORPH_TO_MANY
            => 'morphToMany',

            self::MORPHED_BY_MANY
            => 'morphedByMany',

            self::HAS_ONE_THROUGH
            => 'hasOneThrough',

            self::HAS_MANY_THROUGH
            => 'hasManyThrough',
        };
    }
    public function returnsCollection(): bool
    {
        return $this->isMany();
    }

    public function inverse(): self
    {
        return match ($this) {

            self::BELONGS_TO
            => self::HAS_MANY,

            self::HAS_ONE
            => self::BELONGS_TO,

            self::HAS_MANY
            => self::BELONGS_TO,

            self::BELONGS_TO_MANY
            => self::BELONGS_TO_MANY,

            self::MORPH_ONE
            => self::MORPH_TO,

            self::MORPH_MANY
            => self::MORPH_TO,

            self::MORPH_TO
            => self::MORPH_MANY,

            self::MORPH_TO_MANY
            => self::MORPHED_BY_MANY,

            self::MORPHED_BY_MANY
            => self::MORPH_TO_MANY,

            self::HAS_ONE_THROUGH
            => self::BELONGS_TO,

            self::HAS_MANY_THROUGH
            => self::BELONGS_TO,
        };
    }



}
