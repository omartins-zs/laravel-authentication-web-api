<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Método para retornar os dados do usuário autenticado
    public function profile(Request $request)
    {
        return response()->json(Auth::user());
    }
}
