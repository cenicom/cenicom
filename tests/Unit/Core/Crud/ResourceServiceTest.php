<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Contracts\ResourceRegistryInterface;
use App\Core\Crud\Contracts\ResourceServiceInterface;
use App\Core\Crud\ResourceService;
use PHPUnit\Framework\TestCase;

final class ResourceServiceTest extends TestCase
{
    public function test_resource_service_implements_contract(): void
    {
        $resources = $this->createMock(
            ResourceRegistryInterface::class
        );

        $service = new ResourceService(
            $resources
        );

        self::assertInstanceOf(
            ResourceServiceInterface::class,
            $service
        );
    }

    public function test_controller_returns_registered_controller(): void
    {
        $resources = $this->createMock(
            ResourceRegistryInterface::class
        );

        $resources
            ->expects(self::once())
            ->method('controller')
            ->with('tests')
            ->willReturn(
                'Tests\\Fixtures\\Crud\\TestCrudController'
            );

        $service = new ResourceService(
            $resources
        );

        self::assertSame(
            'Tests\\Fixtures\\Crud\\TestCrudController',
            $service->controller('tests')
        );
    }

    public function test_controller_returns_null_for_unknown_resource(): void
    {
        $resources = $this->createMock(
            ResourceRegistryInterface::class
        );

        $resources
            ->expects(self::once())
            ->method('controller')
            ->with('unknown')
            ->willReturn(null);

        $service = new ResourceService(
            $resources
        );

        self::assertNull(
            $service->controller('unknown')
        );
    }

    public function test_all_returns_registered_resources(): void
    {
        $resourcesData = [
            'tests' => 'Tests\\Fixtures\\Crud\\TestCrudController',
            'users' => 'Tests\\Fixtures\\Crud\\UserCrudController',
        ];

        $resources = $this->createMock(
            ResourceRegistryInterface::class
        );

        $resources
            ->expects(self::once())
            ->method('all')
            ->willReturn($resourcesData);

        $service = new ResourceService(
            $resources
        );

        self::assertSame(
            $resourcesData,
            $service->all()
        );
    }
}
