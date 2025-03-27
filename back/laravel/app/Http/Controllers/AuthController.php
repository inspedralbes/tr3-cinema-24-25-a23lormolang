<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{

    // ✅ Verificar si el usuario está autenticado
    public function check(Request $request)
    {
        try {
            if ($request->user()) {
                return response()->json([
                    'user' => $request->user()->makeHidden(['created_at', 'updated_at']),
                    'valid' => true
                ]);
            }

            return response()->json(['valid' => false], 401);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication verification failed',
                'valid' => false
            ], 500);
        }
    }

    // ✅ Registro de usuario
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Usuario registrado exitosamente'], 201);
    }

    // ✅ Inicio de sesión
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 200);
    }

    // ✅ Restablecer contraseña 
    public function changePassword(Request $request)
    {
        // 1️⃣ Validar los datos de entrada
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user(); // Obtener el usuario autenticado

        // 2️⃣ Verificar si la contraseña actual es correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta'], 400);
        }

        // 3️⃣ Actualizar la contraseña en la base de datos
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Contraseña actualizada correctamente'], 200);
    }


    // ✅ Cierre de sesión
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // Revoca todos los tokens activos

        return response()->json(['message' => 'Sesión cerrada exitosamente'], 200);
    }
}
