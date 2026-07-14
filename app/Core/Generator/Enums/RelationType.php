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
    | Relaciones Uno a Uno
    |--------------------------------------------------------------------------
    */

    case HAS_ONE = 'hasOne';

    case BELONGS_TO = 'belongsTo';

    /*
    |--------------------------------------------------------------------------
    | Relaciones Uno a Muchos
    |--------------------------------------------------------------------------
    */

    case HAS_MANY = 'hasMany';

    case BELONGS_TO_MANY = 'belongsToMany';

    /*
    |--------------------------------------------------------------------------
    | Relaciones Polimórficas
    |--------------------------------------------------------------------------
    */

    case MORPH_ONE = 'morphOne';

    case MORPH_MANY = 'morphMany';

    case MORPH_TO = 'morphTo';

    case MORPH_TO_MANY = 'morphToMany';

    case MORPHED_BY_MANY = 'morphedByMany';

    /*
    |--------------------------------------------------------------------------
    | Through
    |--------------------------------------------------------------------------
    */

    case HAS_ONE_THROUGH = 'hasOneThrough';

    case HAS_MANY_THROUGH = 'hasManyThrough';

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

}
