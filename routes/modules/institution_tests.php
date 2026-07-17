<?php

declare(strict_types=1);

use App\Http\Controllers\InstitutionTestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| institution_tests
|--------------------------------------------------------------------------
|
| Rutas del módulo institution_test.
|
*/

Route::resource(
    'institution_tests',
    InstitutionTestController::class
);
