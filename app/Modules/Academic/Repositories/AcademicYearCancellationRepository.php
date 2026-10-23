<?php

declare(strict_types=1);

namespace App\Modules\Academic\Repositories;

use Illuminate\Support\Facades\DB;

final readonly class AcademicYearCancellationRepository
{
    /**
     * Conditional write gives the status predicate a final say even after row locking.
     * Returns true only if exactly one draft record was changed.
     */
    public function cancelDraft(string $academicYearId, string $reason, mixed $cancelledAt): bool
    {
        return DB::table('academic_years')
            ->where('id', $academicYearId)
            ->where('status', 'draft')
            ->whereNull('activated_at')
            ->whereNull('closed_at')
            ->whereNull('cancelled_at')
            ->update([
                'status' => 'cancelled',
                'cancelled_at' => $cancelledAt,
                'cancellation_reason' => $reason,
                'updated_at' => $cancelledAt,
            ]) === 1;
    }
}
