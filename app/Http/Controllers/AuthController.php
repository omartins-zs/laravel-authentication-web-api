<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\LogoutUserRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Log::channel('database')->info('Novo usuário registrado', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'ip'      => $request->ip(),
        ]);

        return response()->json(['message' => 'Usuário registrado com sucesso'], 201);
    }

    public function login(LoginUserRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::channel('database')->warning('Tentativa de login falhou', [
                'email' => $request->email,
                'ip'    => $request->ip(),
            ]);

            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        Log::channel('database')->info('Usuário logado com sucesso', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'ip'      => $request->ip(),
        ]);

        return response()->json(['token' => $token], 200);
    }

    public function logout(LogoutUserRequest $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        Log::channel('database')->info('Usuário deslogado', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'ip'      => $request->ip(),
        ]);

        return response()->json(['message' => 'Logout realizado com sucesso'], 200);
    }
}
