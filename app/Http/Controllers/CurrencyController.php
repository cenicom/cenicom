<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Actions\Currency\CreateCurrencyAction;
use App\Core\Actions\Currency\DeleteCurrencyAction;
use App\Core\Actions\Currency\UpdateCurrencyAction;
use App\Core\Contracts\ServiceInterface;
use App\Core\Http\Controllers\BaseCrudController;
//use App\Http\Requests\CurrencyStoreRequest;
use Illuminate\Database\Eloquent\Model;
//use App\Http\Requests\CurrencyUpdateRequest;
use Illuminate\Contracts\View\View;
use App\Http\Requests\Currency\StoreCurrencyRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest;

class CurrencyController extends BaseCrudController
{
    public function __construct(
        ServiceInterface $service,
        protected CreateCurrencyAction $createAction,
        protected UpdateCurrencyAction $updateAction,
        protected DeleteCurrencyAction $deleteAction
    ) {
        parent::__construct($service);
    }

    public function index(): View
    {
        return view(
            'currency.index',
            [
                'currencies' => $this->service->paginate()
            ]
        );
    }

    public function create(): View
    {
        return view(
            'currency.create'
        );
    }

    public function store(
        StoreCurrencyRequest $request
    ): Model {
        return $this->createAction->execute(
            $request->validated()
        );
    }

    public function show(
        int|string $id
    ): View {
        return view(
            'currency.show',
            [
                'currency' => $this->service->findById($id)
            ]
        );
    }

    public function edit(
        int|string $id
    ): View {
        return view(
            'currency.edit',
            [
                'currency' => $this->service->findById($id)
            ]
        );
    }

    public function update(
        UpdateCurrencyRequest $request,
        int|string $id
    ): bool {
        return $this->updateAction->execute(
            $id,
            $request->validated()
        );
    }

    public function destroy(
        int|string $id
    ): bool {
        return $this->deleteAction->execute($id);
    }
}
