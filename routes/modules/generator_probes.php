<?php

declare(strict_types=1);

use App\Http\Controllers\GeneratorProbeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| generator_probes
|--------------------------------------------------------------------------
|
| Rutas del módulo generator_probe.
|
*/

Route::resource(
    'generator_probes',
    GeneratorProbeController::class
)

->middleware([
    'auth',
    'verified'
])
->names('generator_probes');
