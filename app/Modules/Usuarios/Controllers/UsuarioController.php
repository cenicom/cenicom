<?php

namespace App\Modules\Usuarios\Controllers;

use App\Http\Controllers\Controller;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('usuarios::index');
    }
}
