<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// ==========================================
// CN Generator Modules
// ==========================================

foreach (glob(base_path('routes/modules/*.php')) as $routeFile) {
    require $routeFile;
}
