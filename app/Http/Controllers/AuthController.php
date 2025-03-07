<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Registro de usuário
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Log::info('Novo usuário registrado: ' . $user->email);

        return response()->json(['message' => 'Usuário registrado com sucesso'], 201);
    }

    // Login de usuário
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::warning('Tentativa de login falhou para: ' . $request->email);
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        Log::info('Usuário logado: ' . $user->email);

        return response()->json(['token' => $token], 200);
    }

    // Logout de usuário
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        Log::info('Usuário deslogado: ' . $user->email);

        return response()->json(['message' => 'Logout realizado com sucesso'], 200);
    }
}
