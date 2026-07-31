<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Module\Bootstrap\StageResult;
use Tests\TestCase;

final class StageResultTest extends TestCase
{
    public function test_success_result(): void
    {
        $result = StageResult::success(
            ['module' => 'Blog']
        );

        $this->assertTrue(
            $result->successful()
        );
    }


    public function test_failure_result(): void
    {
        $result = StageResult::failure(
            'Bootstrap failed'
        );

        $this->assertTrue(
            $result->failed()
        );
    }
}

