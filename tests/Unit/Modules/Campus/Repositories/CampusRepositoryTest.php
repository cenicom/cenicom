<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Repositories;

use App\Core\Contracts\RepositoryInterface;
use App\Core\Repositories\BaseRepository;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Models\Campus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CampusRepositoryTest extends TestCase
{
    #[Test]
    public function campus_repository_implements_campus_repository_contract(): void
    {
        $repository = new \App\Modules\Campus\Repositories\CampusRepository(
            new Campus()
        );

        $this->assertInstanceOf(
            CampusRepositoryInterface::class,
            $repository
        );
    }

    #[Test]
    public function campus_repository_extends_base_repository(): void
    {
        $repository = new \App\Modules\Campus\Repositories\CampusRepository(
            new Campus()
        );

        $this->assertInstanceOf(
            BaseRepository::class,
            $repository
        );

        $this->assertInstanceOf(
            RepositoryInterface::class,
            $repository
        );
    }
}
