<?php

declare(strict_types=1);

namespace App\Modules\Campus\Http\Controllers;

use App\Core\Security\Contracts\IdentityInterface;
use App\Http\Controllers\Controller;
use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Address\Domain\Contracts\GeographicQueryInterface;
use App\Modules\Campus\Domain\Contracts\CampusServiceInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Services\CampusCreationUseCase;
use App\Modules\Campus\Models\Campus;
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
        private readonly GeographicQueryInterface $geography,
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
                'view' => $this->authorization->can(
                    $this->identity,
                    'campuses.view',
                ),
                'update' => $this->authorization->can(
                    $this->identity,
                    'campuses.update',
                ),
                'delete' => $this->authorization->can(
                    $this->identity,
                    'campuses.delete',
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

    public function show(Campus $campus): View
    {
        $campus->load(['institution', 'address']);

        $address = $campus->address;

        if ($address === null) {
            abort(404, 'La dirección de la sede no está disponible.');
        }

        return view('campuses::show', [
            'campus' => $campus,
            'address' => $address,
            'countryName' => $this->geographicName(
                $this->geography->countries(),
                (int) $address->country_id,
            ),
            'stateName' => $this->geographicName(
                $this->geography->statesByCountry(
                    (int) $address->country_id,
                ),
                (int) $address->state_id,
            ),
            'cityName' => $this->geographicName(
                $this->geography->citiesByState(
                    (int) $address->state_id,
                ),
                (int) $address->city_id,
            ),
            'permissions' => [
                'update' => $this->authorization->can(
                    $this->identity,
                    'campuses.update'
                ),
            ],
        ]);
    }

    public function edit(Campus $campus): View
    {
        $campus->load(['institution', 'address']);

        if ($campus->address === null) {
            abort(404, 'La dirección de la sede no está disponible.');
        }

        return view('campuses::edit', [
            'campus' => $campus,
            'address' => $campus->address,
        ]);
    }

    public function update(
        Request $request,
        Campus $campus,
    ): RedirectResponse {
        $validated = $request->validate([
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

        if ($campus->address_id === null) {
            abort(404, 'La dirección de la sede no está disponible.');
        }

        try {
            DB::transaction(function () use (
                $campus,
                $validated,
            ): void {
                $campusUpdated = $this->service->update(
                    $campus->getKey(),
                    [
                        'name' => $validated['name'],
                        'ministry_code' => $validated['ministryCode'] ?? null,
                    ],
                );

                if (! $campusUpdated) {
                    throw ValidationException::withMessages([
                        'name' => 'No fue posible actualizar la sede.',
                    ]);
                }

                $addressUpdated = $this->addresses->update(
                    $campus->address_id,
                    $validated['address'],
                );

                if (! $addressUpdated) {
                    throw ValidationException::withMessages([
                        'address.city_id' => 'No fue posible actualizar la dirección.',
                    ]);
                }
            });
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'address.city_id' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('campuses.show', $campus)
            ->with('success', 'Sede actualizada correctamente.');
    }

    public function destroy(Campus $campus): RedirectResponse
    {
        $deleted = $this->service->delete(
            $campus->getKey(),
        );

        if (! $deleted) {
            return redirect()
                ->route('campuses.index')
                ->with(
                    'error',
                    'No se puede eliminar esta sede porque tiene registros relacionados. Elimine o reasigne primero las dependencias permitidas.',
                );
        }

        return redirect()
            ->route('campuses.index')
            ->with(
                'success',
                'Sede eliminada correctamente.',
            );
    }

    /**
     * @param array<int, object> $options
     */
    private function geographicName(
        array $options,
        int $id,
    ): string {
        foreach ($options as $option) {
            if ((int) $option->id === $id) {
                return (string) $option->name;
            }
        }

        return 'No disponible';
    }
}
