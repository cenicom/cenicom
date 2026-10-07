<?php

declare(strict_types=1);

namespace App\Modules\Address\Http\Controllers;

use App\Modules\Address\Domain\Contracts\GeographicQueryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

final class GeographicController extends Controller
{
    public function __construct(
        private readonly GeographicQueryInterface $query,
    ) {}

    public function countries(): JsonResponse
    {
        return response()->json([
            'data' => $this->query->countries(),
        ]);
    }

    public function states(int $country): JsonResponse
    {
        return response()->json([
            'data' => $this->query->statesByCountry($country),
        ]);
    }

    public function cities(int $state): JsonResponse
    {
        return response()->json([
            'data' => $this->query->citiesByState($state),
        ]);
    }
}
