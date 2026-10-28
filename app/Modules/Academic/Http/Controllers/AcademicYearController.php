<?php

declare(strict_types=1);

namespace App\Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Domain\DTO\AcademicYearCreateData;
use App\Modules\Academic\Domain\Services\AcademicYearActivator;
use App\Modules\Academic\Domain\Services\AcademicYearCloser;
use App\Modules\Academic\Domain\Services\AcademicYearCreator;
use App\Modules\Academic\Domain\Services\CancelAcademicYear;
use App\Modules\Academic\Http\Requests\StoreAcademicYearRequest;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class AcademicYearController extends Controller
{
    public function __construct(
        private readonly AcademicYearCreator $creator,
        private readonly AcademicYearActivator $activator,
        private readonly CancelAcademicYear $canceller,
        private readonly AcademicYearCloser $closer,
    ) {}

    public function store(
        StoreAcademicYearRequest $request,
    ): RedirectResponse {
        $data = $request->validated();

        $id = $this->creator->execute(
            new AcademicYearCreateData(
                institutionId: $data['institutionId'],
                publicationVersionId: $data['publicationVersionId'],
                cycleCode: $data['cycleCode'],
                calendarCode: $data['calendarCode'],
            ),
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Año lectivo creado en estado borrador. Identificador: ' . $id,
            );
    }

    public function activate(string $academicYearId): RedirectResponse
    {
        try {
            $this->activator->execute($academicYearId);
        } catch (DomainException $exception) {
            throw ValidationException::withMessages([
                'academicYear' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Año lectivo activado correctamente.');
    }

    public function cancel(
        Request $request,
        string $academicYearId,
    ): RedirectResponse {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $this->canceller->execute(
                $academicYearId,
                $data['reason'],
            );
        } catch (DomainException $exception) {
            throw ValidationException::withMessages([
                'reason' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Año lectivo cancelado correctamente.');
    }

    public function close(string $academicYearId): RedirectResponse
    {
        try {
            $this->closer->execute($academicYearId);
        } catch (DomainException $exception) {
            throw ValidationException::withMessages([
                'academicYear' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Año lectivo cerrado correctamente.');
    }
}
