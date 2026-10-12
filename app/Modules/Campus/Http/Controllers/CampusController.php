<?php

declare(strict_types=1);

namespace App\Modules\Campus\Http\Controllers;

use App\Core\Security\Contracts\IdentityInterface;
use App\Http\Controllers\Controller;
use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Campus\Domain\Contracts\CampusServiceInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Services\CampusCreationUseCase;
use App\Modules\Institution\Models\Institution;
use App\View\Contracts\ViewAuthorizationInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

final class CampusController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly AddressServiceInterface $addresses,
        private readonly CampusCreationUseCase $creation,
        private readonly CampusServiceInterface $service,
        private readonly ViewAuthorizationInterface $authorization,
        private readonly IdentityInterface $identity,
    ) {}

    public function index(): View
    {
        $campuses = $this->service->paginate(
            perPage: self::PER_PAGE,
        );

        $campuses->getCollection()->load('institution');

        return view('campuses::index', [
            'campuses' => $campuses,
            'permissions' => [
                'create' => $this->authorization->can(
                    $this->identity,
                    'campuses.create',
                ),
            ],
        ]);
    }

    public function create(): View
    {
        return view('campuses::create', [
            'institutions' => Institution::query()
                ->orderBy('name')
                ->get([
                    'id',
                    'short_code',
                    'name',
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'institutionId' => [
                'required',
                'string',
                'exists:institutions,id',
            ],
            'shortCode' => [
                'required',
                'string',
                'regex:/^[A-Z]{6,15}$/',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'ministryCode' => [
                'nullable',
                'string',
                'max:50',
            ],
            'address.country_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'address.state_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'address.city_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'address.address' => [
                'nullable',
                'string',
                'max:255',
            ],
            'address.neighborhood' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        try {
            DB::transaction(function () use ($validated): void {
                $institution = Institution::query()
                    ->findOrFail($validated['institutionId']);

                $address = $this->addresses->create(
                    $validated['address'],
                );

                $this->creation->execute(
                    new CampusCreateData(
                        institutionId: (string) $institution->getKey(),
                        institutionShortCode: $institution->short_code,
                        shortCode: $validated['shortCode'],
                        name: $validated['name'],
                        addressId: (string) $address->getKey(),
                        ministryCode: $validated['ministryCode'] ?? null,
                    ),
                );
            });
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'address.city_id' => $exception->getMessage(),
            ]);
        } catch (ModelNotFoundException) {
            throw ValidationException::withMessages([
                'institutionId' => 'La institución seleccionada no existe.',
            ]);
        }

        return redirect()
            ->route('campuses.index')
            ->with('success', 'Sede creada correctamente.');
    }
}
