<?php

declare(strict_types=1);

use App\Http\Controllers\PruebaTextController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| prueba_texts
|--------------------------------------------------------------------------
|
| Rutas del módulo prueba_text.
|
*/

Route::resource(
    'prueba_texts',
    PruebaTextController::class
);
