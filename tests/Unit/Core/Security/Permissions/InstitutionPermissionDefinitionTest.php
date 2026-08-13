<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use App\Core\Security\Permissions\DTO\PermissionDefinition;
use App\Modules\Institution\Security\InstitutionPermissionDefinition;
use PHPUnit\Framework\TestCase;

final class InstitutionPermissionDefinitionTest extends TestCase
{
    public function test_register_registers_institution_permissions(): void
    {
        $calls = [];

        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $registrar
            ->expects(self::exactly(4))
            ->method('register')
            ->willReturnCallback(
                function (
                    string $name,
                    string $description = '',
                    ?string $module = null,
                ) use (&$calls): PermissionDefinition {
                    $calls[] = [
                        'name' => $name,
                        'description' => $description,
                        'module' => $module,
                    ];

                    return new PermissionDefinition(
                        name: $name,
                        description: $description,
                        module: $module,
                    );
                }
            );

        $definition = new InstitutionPermissionDefinition();

        $definition->register($registrar);

        self::assertSame(
            [
                [
                    'name' => 'institutions.view',
                    'description' => 'Permite consultar instituciones.',
                    'module' => 'institution',
                ],
                [
                    'name' => 'institutions.create',
                    'description' => 'Permite crear instituciones.',
                    'module' => 'institution',
                ],
                [
                    'name' => 'institutions.update',
                    'description' => 'Permite actualizar instituciones.',
                    'module' => 'institution',
                ],
                [
                    'name' => 'institutions.delete',
                    'description' => 'Permite eliminar instituciones.',
                    'module' => 'institution',
                ],
            ],
            $calls
        );
    }
}
