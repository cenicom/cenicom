<?php

declare(strict_types=1);

use App\Http\Controllers\TestFormController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| test_forms
|--------------------------------------------------------------------------
|
| Rutas del módulo test_form.
|
*/

Route::resource(
    'test_forms',
    TestFormController::class
);
