<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;

class CountryService
{
    public function getAll(): Collection
    {
        return Country::ordered()->get();
    }

    public function getActive(): Collection
    {
        return Country::active()->ordered()->get();
    }

    public function create(array $data): Country
    {
        return Country::create($data);
    }

    public function update(Country $country, array $data): Country
    {
        $country->update($data);

        return $country->refresh();
    }

    public function delete(Country $country): void
    {
        $country->delete();
    }
}