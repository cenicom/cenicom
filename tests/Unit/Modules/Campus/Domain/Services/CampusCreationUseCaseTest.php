<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Domain\Services;

use App\Modules\Campus\Domain\Contracts\CampusCreatorInterface;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Entity\Campus;
use App\Modules\Campus\Domain\Services\CampusCreationUseCase;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Entity\Institution;
use DomainException;
use PDOException;
use RuntimeException;
use Tests\TestCase;

final class CampusCreationUseCaseTest extends TestCase
{
    public function test_creates_campus_when_institution_is_active(): void
    {
        $creator = $this->createMock(CampusCreatorInterface::class);
        $repository = $this->createMock(CampusRepositoryInterface::class);
        $institutions = $this->createMock(
            InstitutionRepositoryInterface::class,
        );

        $data = $this->data();
        $campus = $this->campus();

        $institutions
            ->expects($this->once())
            ->method('lockForUpdate')
            ->with($data->institutionId)
            ->willReturn($this->institution('active'));

        $creator
            ->expects($this->once())
            ->method('create')
            ->with($data)
            ->willReturn($campus);

        $repository
            ->expects($this->once())
            ->method('save')
            ->with($campus)
            ->willReturn($campus);

        $useCase = new CampusCreationUseCase(
            $creator,
            $repository,
            $institutions,
        );

        $this->assertSame($campus, $useCase->execute($data));
    }

    public function test_rejects_creation_when_institution_does_not_exist(): void
    {
        $creator = $this->createMock(CampusCreatorInterface::class);
        $repository = $this->createMock(CampusRepositoryInterface::class);
        $institutions = $this->createMock(
            InstitutionRepositoryInterface::class,
        );

        $data = $this->data();

        $institutions
            ->expects($this->once())
            ->method('lockForUpdate')
            ->with($data->institutionId)
            ->willReturn(null);

        $creator->expects($this->never())->method('create');
        $repository->expects($this->never())->method('save');

        $useCase = new CampusCreationUseCase(
            $creator,
            $repository,
            $institutions,
        );

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'No se puede crear una sede porque la institución no existe.',
        );

        $useCase->execute($data);
    }

    public function test_rejects_creation_when_institution_is_not_active(): void
    {
        $creator = $this->createMock(CampusCreatorInterface::class);
        $repository = $this->createMock(CampusRepositoryInterface::class);
        $institutions = $this->createMock(
            InstitutionRepositoryInterface::class,
        );

        $data = $this->data();

        $institutions
            ->expects($this->once())
            ->method('lockForUpdate')
            ->with($data->institutionId)
            ->willReturn($this->institution('draft'));

        $creator->expects($this->never())->method('create');
        $repository->expects($this->never())->method('save');

        $useCase = new CampusCreationUseCase(
            $creator,
            $repository,
            $institutions,
        );

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Solo se pueden crear sedes para instituciones activas.',
        );

        $useCase->execute($data);
    }

    public function test_retries_creation_after_a_concurrency_error(): void
    {
        $creator = $this->createMock(CampusCreatorInterface::class);
        $repository = $this->createMock(CampusRepositoryInterface::class);
        $institutions = $this->createMock(
            InstitutionRepositoryInterface::class,
        );

        $data = $this->data();
        $campus = $this->campus();
        $attempts = 0;

        $institutions
            ->expects($this->exactly(2))
            ->method('lockForUpdate')
            ->with($data->institutionId)
            ->willReturn($this->institution('active'));

        $creator
            ->expects($this->exactly(2))
            ->method('create')
            ->with($data)
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
                },
            );

        $repository
            ->expects($this->once())
            ->method('save')
            ->with($campus)
            ->willReturn($campus);

        $useCase = new CampusCreationUseCase(
            $creator,
            $repository,
            $institutions,
        );

        $result = $useCase->execute($data);

        $this->assertSame($campus, $result);
        $this->assertSame(2, $attempts);
    }

    public function test_stops_after_three_concurrency_failures(): void
    {
        $creator = $this->createMock(CampusCreatorInterface::class);
        $repository = $this->createMock(CampusRepositoryInterface::class);
        $institutions = $this->createMock(
            InstitutionRepositoryInterface::class,
        );

        $data = $this->data();
        $attempts = 0;

        $institutions
            ->expects($this->exactly(3))
            ->method('lockForUpdate')
            ->with($data->institutionId)
            ->willReturn($this->institution('active'));

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
                },
            );

        $repository->expects($this->never())->method('save');

        $useCase = new CampusCreationUseCase(
            $creator,
            $repository,
            $institutions,
        );

        try {
            $useCase->execute($data);
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

    public function test_does_not_retry_a_definitive_creation_error(): void
    {
        $creator = $this->createMock(CampusCreatorInterface::class);
        $repository = $this->createMock(CampusRepositoryInterface::class);
        $institutions = $this->createMock(
            InstitutionRepositoryInterface::class,
        );

        $data = $this->data();
        $error = new RuntimeException('Definitive creation failure.');

        $institutions
            ->expects($this->once())
            ->method('lockForUpdate')
            ->with($data->institutionId)
            ->willReturn($this->institution('active'));

        $creator
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(
                fn (CampusCreateData $received): bool =>
                    $received->institutionId === 'institution-01',
            ))
            ->willThrowException($error);

        $repository->expects($this->never())->method('save');

        $useCase = new CampusCreationUseCase(
            $creator,
            $repository,
            $institutions,
        );

        try {
            $useCase->execute($data);
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

    private function institution(string $status): Institution
    {
        return new Institution(
            id: 'institution-01',
            name: 'Institución de prueba',
            code: 'CEN-TEST01',
            shortCode: 'IESBSC',
            status: $status,
        );
    }
}
