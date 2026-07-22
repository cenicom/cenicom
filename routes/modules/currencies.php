<?php

declare(strict_types=1);

use App\Http\Controllers\CurrencyController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| currencies
|--------------------------------------------------------------------------
|
| Rutas del módulo currency.
|
*/

Route::resource(
    'currencies',
    CurrencyController::class
);
