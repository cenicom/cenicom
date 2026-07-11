<?php

declare(strict_types=1);

namespace App\Http\Controllers;


use App\Core\Actions\Currency\CreateCurrencyAction;
use App\Core\Actions\Currency\DeleteCurrencyAction;
use App\Core\Actions\Currency\UpdateCurrencyAction;
use App\Core\Contracts\CurrencyServiceInterface;
use App\Core\Http\Controllers\BaseCrudController;
use App\Http\Requests\Currency\StoreCurrencyRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest;
use App\Models\Currency;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CurrencyController extends BaseCrudController
{
    public function __construct(
        CurrencyServiceInterface $service,
        protected CreateCurrencyAction $createAction,
        protected UpdateCurrencyAction $updateAction,
        protected DeleteCurrencyAction $deleteAction,
    ) {
        parent::__construct($service);
    }

    public function index(): View
    {
        return view('currency.index', [
            'currencies' => $this->service->paginate(),
        ]);
    }

    public function create(): View
    {
        return view('currency.create');
    }

    public function store(StoreCurrencyRequest $request): RedirectResponse
    {
        $this->createAction->execute(
            $request->validated()
        );

        return redirect()
            ->route('currencies.index')
            ->with('success', 'Moneda creada correctamente.');
    }

    public function show(Currency $currency): View
    {
        return view('currency.show', [
            'currency' => $currency,
        ]);
    }

    public function edit(Currency $currency): View
    {
        return view('currency.edit', [
            'currency' => $currency,
        ]);
    }

    public function update(
        UpdateCurrencyRequest $request,
        Currency $currency
    ): RedirectResponse {

        $this->updateAction->execute(
            $currency,
            $request->validated()
        );

        return redirect()
            ->route('currencies.index')
            ->with('success', 'Moneda actualizada correctamente.');
    }

    public function destroy(Currency $currency): RedirectResponse
    {
        $this->deleteAction->execute($currency);

        return redirect()
            ->route('currencies.index')
            ->with('success', 'Moneda eliminada correctamente.');
    }
}
