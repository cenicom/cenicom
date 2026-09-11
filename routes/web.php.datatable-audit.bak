<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    return view('home');
});

// ==========================================
// CN Generator Modules
// ==========================================

foreach (glob(base_path('routes/modules/*.php')) as $routeFile) {
    require $routeFile;
}
