<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Registro de usuário
    public function register(Request $request)
    {
        try {
            // Validação explícita para garantir que a resposta seja JSON
            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:8',
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'email.required' => 'O campo e-mail é obrigatório.',
                'email.email' => 'O e-mail informado não é válido.',
                'email.unique' => 'Este e-mail já está em uso.',
                'password.required' => 'O campo senha é obrigatório.',
                'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            ]);

            // Criação do usuário
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
            ]);

            // Log de sucesso
            Log::info("Novo usuário registrado: {$user->email}");

            return response()->json(['message' => 'Usuário registrado com sucesso'], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Erro interno: {$e->getMessage()}");
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
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
        // Obtém o usuário autenticado
        $user = $request->user();

        // Verifica se o usuário está autenticado
        if (!$user) {
            // Retorna erro 401 se o usuário não estiver autenticado
            return response()->json(['message' => 'Nenhum usuário autenticado'], 401);
        }

        // Revoga o token atual (se você deseja revogar todos os tokens, use o método 'tokens()->delete()')
        $request->user()->currentAccessToken()->delete();

        // Log de sucesso (opcional)
        Log::info('Usuário deslogado: ' . $user->email);

        // Retorna resposta de sucesso
        return response()->json(['message' => 'Logout realizado com sucesso'], 200);
    }

}
