<?php

declare(strict_types=1);

namespace App\Http\Controllers\Catalogs;

use App\Core\Http\Controllers\BaseController;
use App\Contracts\CurrencyServiceInterface;
use App\Http\Requests\Currency\StoreCurrencyRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CurrencyController extends BaseController
{
    public function __construct(
        private readonly CurrencyServiceInterface $service
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Currency::class);

        $currencies = $this->service->paginate(
            $request->all()
        );

        return view(
            'currencies.index',
            compact('currencies')
        );
    }

    public function create(): View
    {
        $this->authorize('create', Currency::class);

        return view('currencies.create');
    }

    public function store(
        StoreCurrencyRequest $request
    ): RedirectResponse {

        $this->authorize('create', Currency::class);

        $this->service->create(
            $request->validated()
        );

        return $this->success(
            'currencies.index',
            'Moneda creada correctamente.'
        );
    }

    public function show(
        Currency $currency
    ): View {

        $this->authorize('view', $currency);

        return view(
            'currencies.show',
            compact('currency')
        );
    }

    public function edit(
        Currency $currency
    ): View {

        $this->authorize('update', $currency);

        return view(
            'currencies.edit',
            compact('currency')
        );
    }

    public function update(
        UpdateCurrencyRequest $request,
        Currency $currency
    ): RedirectResponse {

        $this->authorize('update', $currency);

        $this->service->update(
            $currency,
            $request->validated()
        );

        return $this->success(
            'currencies.index',
            'Moneda actualizada correctamente.'
        );
    }

    public function destroy(
        Currency $currency
    ): RedirectResponse {

        $this->authorize('delete', $currency);

        $this->service->delete($currency);

        return $this->success(
            'currencies.index',
            'Moneda eliminada correctamente.'
        );
    }


}
