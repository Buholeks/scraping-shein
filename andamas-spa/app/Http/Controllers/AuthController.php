<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.']
            ]);
        }

        $user = Auth::user()->load(['empresa', 'sucursal', 'sucursalesAdicionales']);

        // Verificar si el usuario está activo
        if (!$user->activo) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['Tu cuenta está desactivada.']
            ]);
        }

        // Obtener todas las sucursales (principal + adicionales)
        $sucursales = collect([$user->sucursal])
            ->merge($user->sucursalesAdicionales)
            ->filter() // Elimina valores nulos
            ->map(function ($sucursal) {
                return [
                    'id' => $sucursal->id,
                    'nombre' => $sucursal->nombre
                ];
            })
            ->unique('id') // Elimina duplicados
            ->values()
            ->toArray();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'activo' => $user->activo,
                'id_empresa' => $user->id_empresa,
                'empresa_nombre' => $user->empresa->nombre ?? 'Empresa no asignada',
                'sucursales' => $sucursales, // Todas las sucursales combinadas
                'sucursal_actual' => [
                    'id' => $user->id_sucursal,
                    'nombre' => $user->sucursal->nombre ?? 'Sucursal no asignada'
                ]
            ]
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'activo' => 'boolean'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'activo' => $validated['activo'] ?? true,
        ]);

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => $user
        ], 201);
    }
}
