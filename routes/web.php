<?php

use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)
    ->name('dashboard');

Route::prefix('cds')->group(function () {

    Route::get('/', function () {

        return view('cds.index');

    })->name('cds.index');

});

Route::resource('countries', CountryController::class);
Route::get('/states/by-country/{country}', function ($countryId) {
    return \App\Models\State::where('country_id', $countryId)
        ->orderBy('name')
        ->get();
});

Route::view('/demo/users', 'demo.users');

Route::get('/cities/by-state/{state}', function ($stateId) {
    return \App\Models\City::where('state_id', $stateId)
        ->orderBy('name')
        ->get();
});

Route::resource('organizations', OrganizationController::class);

Route::resource('currencies', CurrencyController::class);

require __DIR__.'/cds.php';
