<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Domain\Services;

use App\Modules\Campus\Domain\Contracts\CampusCreatorInterface;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Entity\Campus;
use App\Modules\Campus\Domain\Services\CampusCreationUseCase;
use PDOException;
use RuntimeException;
use Tests\TestCase;

final class CampusCreationUseCaseTest extends TestCase
{
    public function test_retries_creation_after_a_concurrency_error(): void
    {
        $creator = $this->createMock(CampusCreatorInterface::class);
        $repository = $this->createMock(CampusRepositoryInterface::class);

        $campus = $this->campus();
        $attempts = 0;

        $creator
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnCallback(
                function (CampusCreateData $data) use (
                    &$attempts,
                    $campus,
                ): Campus {
                    $attempts++;

                    if ($attempts === 1) {
                        throw new PDOException(
                            'Simulated serialization failure.',
                            40001,
                        );
                    }

                    return $campus;
                }
            );

        $repository
            ->expects($this->once())
            ->method('save')
            ->with($campus)
            ->willReturn($campus);

        $useCase = new CampusCreationUseCase($creator, $repository);

        $result = $useCase->execute($this->data());

        $this->assertSame($campus, $result);
        $this->assertSame(2, $attempts);
    }

    public function test_stops_after_three_concurrency_failures(): void
    {
        $creator = $this->createMock(CampusCreatorInterface::class);
        $repository = $this->createMock(CampusRepositoryInterface::class);

        $attempts = 0;

        $creator
            ->expects($this->exactly(3))
            ->method('create')
            ->willReturnCallback(
                function (CampusCreateData $data) use (&$attempts): Campus {
                    $attempts++;

                    throw new PDOException(
                        "Simulated concurrency failure {$attempts}.",
                        40001,
                    );
                }
            );

        $repository
            ->expects($this->never())
            ->method('save');

        $useCase = new CampusCreationUseCase($creator, $repository);

        try {
            $useCase->execute($this->data());
            $this->fail('Expected the concurrency exception to be propagated.');
        } catch (PDOException $exception) {
            $this->assertSame(40001, $exception->getCode());
            $this->assertSame(3, $attempts);
            $this->assertSame(
                'Simulated concurrency failure 3.',
                $exception->getMessage(),
            );
        }
    }

    public function test_does_not_retry_a_definitive_error(): void
    {
        $creator = $this->createMock(CampusCreatorInterface::class);
        $repository = $this->createMock(CampusRepositoryInterface::class);

        $error = new RuntimeException('Definitive creation failure.');

        $creator
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(
                fn (CampusCreateData $data): bool =>
                    $data->institutionId === 'institution-01'
            ))
            ->willThrowException($error);

        $repository
            ->expects($this->never())
            ->method('save');

        $useCase = new CampusCreationUseCase($creator, $repository);

        try {
            $useCase->execute($this->data());
            $this->fail('Expected the definitive exception to be propagated.');
        } catch (RuntimeException $exception) {
            $this->assertSame($error, $exception);
        }
    }

    private function data(): CampusCreateData
    {
        return new CampusCreateData(
            institutionId: 'institution-01',
            institutionShortCode: 'IESBSC',
            shortCode: 'SBBXYZ',
            name: 'Campus Principal',
            addressId: 'address-01',
        );
    }

    private function campus(): Campus
    {
        return new Campus(
            id: '01JTESTCAMPUS00000000000001',
            institutionId: 'institution-01',
            code: 'CEN-IESBSC-SBBXYZ-001',
            shortCode: 'SBBXYZ',
            name: 'Campus Principal',
            addressId: 'address-01',
        );
    }
}
