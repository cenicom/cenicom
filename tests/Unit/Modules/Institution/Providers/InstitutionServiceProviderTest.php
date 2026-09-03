<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Providers;

use App\Modules\Institution\Domain\Contracts\InstitutionCodeGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionCodeSequenceInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionIdGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionServiceInterface;
use App\Modules\Institution\Domain\Services\InstitutionService;
use App\Modules\Institution\Repositories\InstitutionRepository;
use Tests\TestCase;

final class InstitutionServiceProviderTest extends TestCase
{
    public function test_institution_repository_interface_resolves_to_repository(): void
    {
        $repository = app(
            InstitutionRepositoryInterface::class
        );

        $this->assertInstanceOf(
            InstitutionRepository::class,
            $repository
        );
    }

    public function test_institution_service_interface_resolves_to_service(): void
    {
        $service = app(
            InstitutionServiceInterface::class
        );

        $this->assertInstanceOf(
            InstitutionService::class,
            $service
        );
    }

    public function test_institution_service_resolves_with_institution_repository(): void
    {
        $service = app(
            InstitutionServiceInterface::class
        );

        $this->assertInstanceOf(
            InstitutionService::class,
            $service
        );
    }

    public function test_institution_id_generator_interface_resolves_to_generator(): void
    {
        $generator = app(
            InstitutionIdGeneratorInterface::class
        );

        $this->assertInstanceOf(
            InstitutionIdGeneratorInterface::class,
            $generator
        );
    }

    public function test_institution_code_sequence_interface_resolves_to_sequence(): void
    {
        $sequence = app(
            InstitutionCodeSequenceInterface::class
        );

        $this->assertInstanceOf(
            InstitutionCodeSequenceInterface::class,
            $sequence
        );
    }

    public function test_institution_code_generator_interface_resolves_to_generator(): void
    {
        $generator = app(
            InstitutionCodeGeneratorInterface::class
        );

        $this->assertInstanceOf(
            InstitutionCodeGeneratorInterface::class,
            $generator
        );
    }

    public function test_institution_creator_interface_resolves_with_dependencies(): void
    {
        $creator = app(
            InstitutionCreatorInterface::class
        );

        $this->assertInstanceOf(
            InstitutionCreatorInterface::class,
            $creator
        );
    }
}
