<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function cambiarSucursal(Request $request)
    {
        $request->validate([
            'sucursal_id' => 'required|exists:sucursales,id',
        ]);

        $user = $request->user();

        // Asegúrate de que tenga acceso a esa sucursal
        if (!$user->sucursales()->where('sucursales.id', $request->sucursal_id)->exists()) {
            return response()->json(['error' => 'No autorizado para acceder a esta sucursal.'], 403);
        }
        
        $sucursal = $user->sucursales()->where('sucursales.id', $request->sucursal_id)->first();
        

    }
}
