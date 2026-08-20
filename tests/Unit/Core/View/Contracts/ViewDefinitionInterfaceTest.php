<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View\Contracts;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;
use PHPUnit\Framework\TestCase;

final class ViewDefinitionInterfaceTest extends TestCase
{
    public function test_view_definition_contract_is_implemented_by_a_definition(): void
    {
        $definition = new class implements ViewDefinitionInterface {
            public function register(
                ViewRegistrarInterface $views
            ): void {
            }
        };

        self::assertInstanceOf(
            ViewDefinitionInterface::class,
            $definition,
        );
    }
}
