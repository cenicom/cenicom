<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryRequest;
use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CountryController extends Controller
{
    public function __construct(
        private readonly CountryService $service
    ) {}

    public function index(): View
    {
        $countries = $this->service->getAll();

        return view('countries.index', compact('countries'));
    }

    public function create(): View
    {
        return view('countries.create');
    }

    public function store(CountryRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('countries.index')
            ->with('success', 'País creado correctamente.');
    }

    public function edit(Country $country): View
    {
        return view('countries.edit', compact('country'));
    }

    public function update(CountryRequest $request, Country $country): RedirectResponse
    {
        $this->service->update($country, $request->validated());

        return redirect()
            ->route('countries.index')
            ->with('success', 'País actualizado correctamente.');
    }

    public function destroy(Country $country): RedirectResponse
    {
        $this->service->delete($country);

        return redirect()
            ->route('countries.index')
            ->with('success', 'País eliminado correctamente.');
    }
}
