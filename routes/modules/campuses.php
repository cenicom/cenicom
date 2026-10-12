<?php

declare(strict_types=1);

use App\Modules\Campus\Http\Controllers\CampusController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->group(function (): void {
        Route::get(
            '/campuses',
            [CampusController::class, 'index'],
        )
            ->middleware('permission:campuses.view')
            ->name('campuses.index');

        Route::get(
            '/campuses/create',
            [CampusController::class, 'create'],
        )
            ->middleware('permission:campuses.create')
            ->name('campuses.create');

        Route::post(
            '/campuses',
            [CampusController::class, 'store'],
        )
            ->middleware('permission:campuses.create')
            ->name('campuses.store');
    });
