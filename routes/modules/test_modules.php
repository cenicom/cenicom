<?php

declare(strict_types=1);

use App\Http\Controllers\TestModuleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| test_modules
|--------------------------------------------------------------------------
|
| Rutas del módulo test_module.
|
*/

Route::resource(
    'test_modules',
    TestModuleController::class
);
