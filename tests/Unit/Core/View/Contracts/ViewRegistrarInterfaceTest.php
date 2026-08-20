<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View\Contracts;

use App\Core\View\Contracts\ViewRegistrarInterface;
use PHPUnit\Framework\TestCase;

final class ViewRegistrarInterfaceTest extends TestCase
{
    public function test_view_registrar_contract_registers_a_namespace_and_path(): void
    {
        $registrar = new class implements ViewRegistrarInterface {
            public ?string $namespace = null;

            public ?string $path = null;

            public function register(string $namespace, string $path): void
            {
                $this->namespace = $namespace;
                $this->path = $path;
            }
        };

        $path = 'app/Modules/Institution/resources/views';

        $registrar->register(
            'institutions',
            $path,
        );

        self::assertSame(
            'institutions',
            $registrar->namespace,
        );

        self::assertSame(
            $path,
            $registrar->path,
        );
    }
}
