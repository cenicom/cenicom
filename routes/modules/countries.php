<?php

declare(strict_types=1);

use App\Modules\Country\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| countries
|--------------------------------------------------------------------------
|
| Rutas del módulo country.
|
*/

Route::resource(
    'countries',
    CountryController::class
)

->middleware([
    'auth',
    'verified'
])
->names('countries');
