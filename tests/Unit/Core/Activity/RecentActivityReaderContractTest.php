<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Activity;

use App\Core\Activity\Contracts\RecentActivityReaderInterface;
use App\Core\Activity\DTO\ActivityReadModel;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class RecentActivityReaderContractTest extends TestCase
{
    public function test_recent_reader_contract_is_defined(): void
    {
        $method = new ReflectionMethod(
            RecentActivityReaderInterface::class,
            'recent',
        );

        $parameters = $method->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertSame('limit', $parameters[0]->getName());
        $this->assertSame('int', $parameters[0]->getType()?->getName());

        $this->assertSame(
            'iterable',
            $method->getReturnType()?->getName(),
        );
    }

    public function test_activity_read_model_is_the_declared_reader_item(): void
    {
        $activity = new ActivityReadModel(
            eventType: 'institution.created',
            occurredAt: new DateTimeImmutable(),
            actorId: '1',
            actorName: 'Test User',
            actorAuthenticated: true,
            subjectType: 'institution',
            subjectId: '01JTESTINSTITUTION000000000001',
            data: [],
            result: 'success',
        );

        $this->assertInstanceOf(
            ActivityReadModel::class,
            $activity,
        );
    }
}
