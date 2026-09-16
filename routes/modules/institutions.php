<?php

declare(strict_types=1);

use App\Modules\Institution\Http\Controllers\InstitutionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource(
        'institutions',
        InstitutionController::class,
    )->names('institutions');
});
