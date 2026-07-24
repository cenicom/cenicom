<?php

declare(strict_types=1);

namespace App\Core\Generator\DTO;

use InvalidArgumentException;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * DTO que representa la definición completa de un permiso
 * utilizado por el CN Generator.
 */
final readonly class PermissionDefinition
{
    /**
     * Constructor.
     */
    public function __construct(

        /*
        |--------------------------------------------------------------------------
        | Identidad
        |--------------------------------------------------------------------------
        */

        private string $name,

        private string $action,

        /*
        |--------------------------------------------------------------------------
        | Definición
        |--------------------------------------------------------------------------
        */

        private string $permission,

        private string $group,

        private string $guard = 'web',

        /*
        |--------------------------------------------------------------------------
        | Metadata
        |--------------------------------------------------------------------------
        */

        private ?string $description = null,

        /*
        |--------------------------------------------------------------------------
        | Configuración
        |--------------------------------------------------------------------------
        */

        private bool $enabled = true,

        private bool $generatePolicy = true,

        private bool $generateMiddleware = true,

        private bool $generateMenu = true,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Factory
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<string,mixed> $definition
     */
    public static function fromArray(array $definition): self
    {
        self::validateDefinition($definition);

        return new self(

            name: (string) $definition['name'],

            action: (string) $definition['action'],

            permission: (string) $definition['permission'],

            group: (string) $definition['group'],

            guard: (string) ($definition['guard'] ?? 'web'),

            description: isset($definition['description'])
                ? (string) $definition['description']
                : null,

            enabled: (bool) ($definition['enabled'] ?? true),

            generatePolicy: (bool) ($definition['generatePolicy'] ?? true),

            generateMiddleware: (bool) ($definition['generateMiddleware'] ?? true),

            generateMenu: (bool) ($definition['generateMenu'] ?? true),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validación
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<string,mixed> $definition
     */
    private static function validateDefinition(array $definition): void
    {
        foreach (
            [
                'name',
                'action',
                'permission',
                'group',
            ] as $field
        ) {

            if (
                ! array_key_exists($field, $definition)
                || $definition[$field] === ''
            ) {

                throw new InvalidArgumentException(
                    sprintf(
                        'The permission definition requires the "%s" attribute.',
                        $field
                    )
                );
            }
        }

        if (
            isset($definition['guard'])
            && ! is_string($definition['guard'])
        ) {

            throw new InvalidArgumentException(
                'The "guard" attribute must be a string.'
            );
        }

        if (
            array_key_exists('description', $definition)
            && ! is_null($definition['description'])
            && ! is_string($definition['description'])
        ) {

            throw new InvalidArgumentException(
                'The "description" attribute must be a string or null.'
            );
        }

        foreach (
            [
                'enabled',
                'generatePolicy',
                'generateMiddleware',
                'generateMenu',
            ] as $field
        ) {

            if (
                array_key_exists($field, $definition)
                && ! is_bool($definition[$field])
            ) {

                throw new InvalidArgumentException(
                    sprintf(
                        'The "%s" attribute must be boolean.',
                        $field
                    )
                );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    */

    public function name(): string
    {
        return $this->name;
    }

    public function action(): string
    {
        return $this->action;
    }

    public function permission(): string
    {
        return $this->permission;
    }

    public function group(): string
    {
        return $this->group;
    }

    public function guard(): string
    {
        return $this->guard;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }

    public function generatePolicy(): bool
    {
        return $this->generatePolicy;
    }

    public function generateMiddleware(): bool
    {
        return $this->generateMiddleware;
    }

    public function generateMenu(): bool
    {
        return $this->generateMenu;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isWebGuard(): bool
    {
        return $this->guard === 'web';
    }

    public function isApiGuard(): bool
    {
        return $this->guard === 'api';
    }

    public function hasDescription(): bool
    {
        return $this->description !== null;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function shouldGeneratePolicy(): bool
    {
        return $this->generatePolicy;
    }

    public function shouldGenerateMiddleware(): bool
    {
        return $this->generateMiddleware;
    }

    public function shouldGenerateMenu(): bool
    {
        return $this->generateMenu;
    }

    /*
    |--------------------------------------------------------------------------
    | Exportación
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Identidad
            |--------------------------------------------------------------------------
            */

            'name' => $this->name,

            'action' => $this->action,

            /*
            |--------------------------------------------------------------------------
            | Definición
            |--------------------------------------------------------------------------
            */

            'permission' => $this->permission,

            'group' => $this->group,

            'guard' => $this->guard,

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'description' => $this->description,

            /*
            |--------------------------------------------------------------------------
            | Configuración
            |--------------------------------------------------------------------------
            */

            'enabled' => $this->enabled,

            'generatePolicy' => $this->generatePolicy,

            'generateMiddleware' => $this->generateMiddleware,

            'generateMenu' => $this->generateMenu,
        ];
    }
}
