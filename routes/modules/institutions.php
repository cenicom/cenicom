<?php

declare(strict_types=1);

use App\Modules\Institution\Http\Controllers\InstitutionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource(
        'institutions',
        InstitutionController::class,
    )
        ->middlewareFor(
            ['index'],
            'permission:institutions.view',
        )
        ->except(['show'])
        ->middlewareFor(
            ['create', 'store'],
            'permission:institutions.create',
        )
        ->middlewareFor(
            ['edit', 'update'],
            'permission:institutions.update',
        )
        ->middlewareFor(
            ['destroy'],
            'permission:institutions.delete',
        )
        ->names('institutions');
});
