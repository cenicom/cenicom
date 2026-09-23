<?php

declare(strict_types=1);

use App\Modules\CnGeneratorProbe\Http\Controllers\CnGeneratorProbeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| cn_generator_probes
|--------------------------------------------------------------------------
|
| Rutas del módulo cn_generator_probe.
|
*/

Route::resource(
    'cn_generator_probes',
    CnGeneratorProbeController::class
)

->middleware([
    'auth',
    'verified'
])
->names('cn_generator_probes');
