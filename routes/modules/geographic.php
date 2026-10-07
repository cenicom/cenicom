<?php

declare(strict_types=1);

use App\Modules\Address\Http\Controllers\GeographicController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get(
        'countries',
        [GeographicController::class, 'countries'],
    )->name('geographic.countries');

    Route::get(
        'countries/{country}/states',
        [GeographicController::class, 'states'],
    )->name('geographic.states');

    Route::get(
        'states/{state}/cities',
        [GeographicController::class, 'cities'],
    )->name('geographic.cities');
});
