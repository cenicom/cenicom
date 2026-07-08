<?php

declare(strict_types=1);

namespace App\Core\Factories;

use App\Core\ValueObjects\Module;

final class ModuleFactory
{
    public static function fromArray(array $manifest): Module
    {
        return new Module(
            id: $manifest['id'],
            name: $manifest['name'],
            description: $manifest['description'] ?? '',
            version: $manifest['version'] ?? '1.0.0',
            enabled: $manifest['enabled'] ?? true,
            icon: $manifest['icon'] ?? 'cube',
            color: $manifest['color'] ?? 'primary',
            order: $manifest['order'] ?? 0,
            providers: $manifest['providers'] ?? [],
            navigation: $manifest['navigation'] ?? [],
            permissions: $manifest['permissions'] ?? [],
        );
    }
}