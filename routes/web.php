<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('home');
});

// ==========================================
// Dashboard
// ==========================================

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get(
        '/dashboard',
        [DashboardController::class, 'index'],
    )->name('dashboard');
});

// ==========================================
// CN Authentication
// ==========================================

require base_path('routes/auth.php');

// ==========================================
// CN Generator Modules
// ==========================================

foreach (glob(base_path('routes/modules/*.php')) as $routeFile) {
    require $routeFile;
}



Route::view('/__audit/datatable', '__audit.datatable');
