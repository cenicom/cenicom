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

        Route::get(
            '/campuses/{campus}/edit',
            [CampusController::class, 'edit'],
        )
            ->middleware('permission:campuses.update')
            ->name('campuses.edit');

        Route::put(
            '/campuses/{campus}',
            [CampusController::class, 'update'],
        )
            ->middleware('permission:campuses.update')
            ->name('campuses.update');

        Route::patch(
            '/campuses/{campus}',
            [CampusController::class, 'update'],
        )
            ->middleware('permission:campuses.update')
            ->name('campuses.update.patch');

        Route::get(
            '/campuses/{campus}',
            [CampusController::class, 'show'],
        )
            ->middleware('permission:campuses.view')
            ->name('campuses.show');

        Route::delete(
            '/campuses/{campus}',
            [CampusController::class, 'destroy'],
        )
            ->middleware('permission:campuses.delete')
            ->name('campuses.destroy');
    });
