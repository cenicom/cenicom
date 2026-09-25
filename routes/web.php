<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
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
