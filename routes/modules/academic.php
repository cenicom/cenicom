<?php

declare(strict_types=1);

use App\Modules\Academic\Http\Controllers\AcademicYearController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::post(
        '/academic-years',
        [AcademicYearController::class, 'store'],
    )
        ->middleware('permission:academic_year.create')
        ->name('academic-years.store');

    Route::post(
        '/academic-years/{academicYearId}/activate',
        [AcademicYearController::class, 'activate'],
    )
        ->middleware('permission:academic_year.activate')
        ->name('academic-years.activate');

    Route::post(
        '/academic-years/{academicYearId}/cancel',
        [AcademicYearController::class, 'cancel'],
    )
        ->middleware('permission:academic_year.cancel')
        ->name('academic-years.cancel');

    Route::post(
        '/academic-years/{academicYearId}/close',
        [AcademicYearController::class, 'close'],
    )
        ->middleware('permission:academic_year.close')
        ->name('academic-years.close');
});
