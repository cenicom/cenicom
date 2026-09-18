<?php

declare(strict_types=1);

use App\Modules\City\Http\Controllers\CityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| cities
|--------------------------------------------------------------------------
|
| Rutas del módulo city.
|
*/

Route::resource(
    'cities',
    CityController::class
)

->middleware([
    'auth',
    'verified'
])
->names('cities');
