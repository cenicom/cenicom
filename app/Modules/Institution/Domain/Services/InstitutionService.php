<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\Services;

use App\Core\Services\BaseService;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionServiceInterface;
use Illuminate\Support\Facades\DB;

final class InstitutionService extends BaseService
    implements InstitutionServiceInterface
{
    public function __construct(
        InstitutionRepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }

    public function delete(int|string $id): bool
    {
        $hasDependencies = DB::table('campuses')
            ->where('institution_id', $id)
            ->exists()
            || DB::table('institution_addresses')
                ->where('institution_id', $id)
                ->exists()
            || DB::table('campus_code_sequences')
                ->where('institution_id', $id)
                ->exists();

        if ($hasDependencies) {
            return false;
        }

        return parent::delete($id);
    }
}
