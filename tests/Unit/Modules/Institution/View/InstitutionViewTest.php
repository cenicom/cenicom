<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\View;

use App\Core\View\Contracts\ViewRegistrarInterface;
use App\Modules\Institution\View\InstitutionView;
use PHPUnit\Framework\TestCase;

final class InstitutionViewTest extends TestCase
{
    public function test_register_registers_institution_view_namespace(): void
    {
        $views = $this->createMock(
            ViewRegistrarInterface::class
        );

        $views
            ->expects($this->once())
            ->method('register')
            ->with(
                'institution',
                'app/Modules/Institution/resources/views',
            );

        $definition = new InstitutionView();

        $definition->register($views);
    }
}
