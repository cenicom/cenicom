<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Providers;

use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
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
}
