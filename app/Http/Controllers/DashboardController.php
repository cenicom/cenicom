<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Activity\Contracts\RecentActivityReaderInterface;
use App\Modules\Campus\Domain\Contracts\CampusServiceInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionServiceInterface;
use Illuminate\Contracts\View\View;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly RecentActivityReaderInterface $activities,
        private readonly InstitutionServiceInterface $institutions,
        private readonly CampusServiceInterface $campuses,
    ) {
    }

    public function index(): View
    {
        return view(
            'dashboard',
            [
                'activities' => $this->activities->recent(5),
                'totalInstitutions' => $this->institutions->count(),
                'totalCampuses' => $this->campuses->count(),
            ],
        );
    }
}
