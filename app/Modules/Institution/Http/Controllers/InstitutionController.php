<?php

declare(strict_types=1);

namespace App\Modules\Institution\Http\Controllers;

use App\Core\Security\Contracts\IdentityInterface;
use App\Http\Controllers\Controller;
use App\Modules\Institution\Actions\InstitutionAction;
use App\Modules\Institution\Domain\Contracts\InstitutionServiceInterface;
use App\Modules\Institution\Http\Requests\StoreInstitutionRequest;
use App\Modules\Institution\Http\Requests\UpdateInstitutionRequest;
use App\Modules\Institution\Models\Institution;
use App\View\Contracts\ViewAuthorizationInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class InstitutionController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly InstitutionServiceInterface $service,
        private readonly InstitutionAction $action,
        private readonly ViewAuthorizationInterface $authorization,
        private readonly IdentityInterface $identity,
    ) {}

    public function index(): View
    {
        return view('institutions::index', [
            'institutions' => $this->service->paginate(
                perPage: self::PER_PAGE,
            ),
            'permissions' => [
                'create' => $this->authorization->can(
                    $this->identity,
                    'institutions.create',
                ),
                'update' => $this->authorization->can(
                    $this->identity,
                    'institutions.update',
                ),
                'delete' => $this->authorization->can(
                    $this->identity,
                    'institutions.delete',
                ),
            ],
        ]);
    }

    public function create(): View
    {
        return view('institutions::create');
    }

    public function store(
        StoreInstitutionRequest $request,
    ): RedirectResponse {
        $this->action->create(
            $request->validated(),
        );

        return redirect()
            ->route('institutions.index')
            ->with(
                'success',
                'Institution creado correctamente.',
            );
    }

    public function edit(
        Institution $institution,
    ): View {
        return view('institutions::edit', [
            'institution' => $institution,
        ]);
    }

    public function update(
        UpdateInstitutionRequest $request,
        Institution $institution,
    ): RedirectResponse {
        $this->action->update(
            $institution->getKey(),
            $request->validated(),
        );

        return redirect()
            ->route('institutions.index')
            ->with(
                'success',
                'Institution actualizado correctamente.',
            );
    }

    public function destroy(
        Institution $institution,
    ): RedirectResponse {
        $this->action->delete(
            $institution->getKey(),
        );

        return redirect()
            ->route('institutions.index')
            ->with(
                'success',
                'Institution eliminado correctamente.',
            );
    }
}
