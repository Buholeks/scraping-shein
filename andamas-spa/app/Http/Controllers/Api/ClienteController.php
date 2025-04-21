<?php

namespace App\Http\Controllers\Api;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
    
        $query = Cliente::query()
            ->where('id_empresa', $user->id_empresa); // 👈 solo empresa

        if ($request->filled('search')) {
            $search = $request->search;
    
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%$search%")
                  ->orWhere('telefono', 'like', "%$search%");
            });
        }
    
        return $query->limit(20)->get();
    }
    
    

    public function store(Request $request)
    {
        $user = $request->user();
    
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);
    
        // Agrega la empresa y sucursal activas
        $validated['id_empresa'] = $user->id_empresa;
        $validated['id_sucursal'] = $request->input('id_sucursal');
    
        return Cliente::create($validated);
    }
    
    
    protected function obtenerSucursalValida(Request $request, User $user)
    {
        $sucursalId = $request->session()->get('sucursal_id', $user->id_sucursal);
        
        // Verificar que el usuario tenga acceso a esta sucursal
        $tieneAcceso = $user->sucursales()
            ->where('sucursales.id', $sucursalId)
            ->exists();
    
        if (!$tieneAcceso) {
            throw new \Exception("El usuario no tiene acceso a la sucursal $sucursalId");
        }
    
        return $sucursalId;
    }

    public function show(Cliente $cliente)
    {
        return $cliente;
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
        ]);

        $cliente->update($validated);
        return $cliente;
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return response()->json(null, 204);
    }
}
