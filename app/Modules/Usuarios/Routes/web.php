<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Usuarios\Controllers\UsuarioController;

Route::middleware(['web','auth'])

    ->prefix('usuarios')

    ->name('usuarios.')

    ->group(function () {

        Route::get('/', [UsuarioController::class, 'index'])
            ->name('index');

    });
