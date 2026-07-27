<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Navigation;

use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Modules\Institution\Navigation\InstitutionNavigation;
use Mockery;
use Tests\TestCase;

final class InstitutionNavigationTest extends TestCase
{
    public function test_registers_institution_navigation(): void
    {
        // Arrange

        $navigation = Mockery::mock(
            NavigationRegistrarInterface::class
        );


        $navigation
            ->shouldReceive('group')
            ->once();

        $navigation
            ->shouldReceive('item')
            ->once();


        // Act

        $definition = new InstitutionNavigation();

        $definition->register(
            $navigation
        );


        // Assert

        $this->assertTrue(true);
    }


    public function test_registers_administration_group(): void
    {
        // Arrange

        $navigation = Mockery::mock(
            NavigationRegistrarInterface::class
        );


        $navigation
            ->shouldReceive('group')
            ->once()
            ->withArgs(function (
                NavigationGroupData $group
            ) {

                return
                    $group->id() === 'administration'
                    &&
                    $group->label() === 'Administración';
            });


        $navigation
            ->shouldReceive('item')
            ->once();


        // Act

        $definition = new InstitutionNavigation();

        $definition->register(
            $navigation
        );
    }


    public function test_registers_institutions_item(): void
    {
        // Arrange

        $navigation = Mockery::mock(
            NavigationRegistrarInterface::class
        );


        $navigation
            ->shouldReceive('group')
            ->once();


        $navigation
            ->shouldReceive('item')
            ->once()
            ->withArgs(function (
                NavigationItemData $item
            ) {

                return
                    $item->id() === 'institutions'
                    &&
                    $item->label() === 'Instituciones'
                    &&
                    $item->route() === 'institutions.index';
            });


        // Act

        $definition = new InstitutionNavigation();

        $definition->register(
            $navigation
        );
    }
}
