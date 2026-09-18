<?php

declare(strict_types=1);

use App\Modules\State\Http\Controllers\StateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| states
|--------------------------------------------------------------------------
|
| Rutas del módulo state.
|
*/

Route::resource(
    'states',
    StateController::class
)

->middleware([
    'auth',
    'verified'
])
->names('states');
