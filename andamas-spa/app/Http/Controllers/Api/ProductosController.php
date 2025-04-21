<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Abono;
use App\Models\Producto;

class ProductosController extends Controller
{
    public function noRecogidos(Request $request)
    {
        $query = Producto::where('estado', 'pendiente');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('sku', 'like', "%{$buscar}%");
            });
        }

        return $query->orderByDesc('created_at')->get();
    }

    public function disponible(Request $request)
    {
        $query = Producto::with('cliente') // 👈 esto es la clave
            ->where('estado', 'en_tienda');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('sku', 'like', "%{$buscar}%")
                    ->orWhereHas('cliente', function ($subQuery) use ($buscar) {
                        $subQuery->where('nombre', 'like', "%{$buscar}%");
                    });
            });
        }

        return $query->orderByDesc('created_at')->get()->map(function ($producto) {
            $producto->precio = (float) $producto->precio; // 💥 clave
            return $producto;
        });
    }


    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:en_tienda,pendiente,recogido',
        ]);

        $producto = Producto::findOrFail($id);
        $producto->estado = $request->estado;
        $producto->save();

        return response()->json(['mensaje' => 'Estado actualizado']);
    }

    public function confirmarVenta(Request $request)
    {
        $request->validate([
            'productos' => 'required|array',
            'productos.*' => 'exists:productos,id',
        ]);

        Producto::whereIn('id', $request->productos)
            ->update(['estado' => 'recogido']);

        return response()->json(['mensaje' => 'Productos marcados como recogidos']);
    }


    public function productosDelCliente($id)
    {
        return Producto::where('id_cliente', $id)
            ->where('estado', 'en_tienda')
            ->get();
    }


    public function confirmar(Request $request)
    {
        $request->validate([
            'id_cliente' => 'required|exists:clientes,id',
            'productos' => 'required|array',
            'productos.*' => 'exists:productos,id',
            'forma_pago' => 'required|string',
        ]);

        $cliente = Cliente::findOrFail($request->id_cliente);
        $productos = Producto::whereIn('id', $request->productos)->get();
        $total = $productos->sum('precio');

        $abonos = Abono::where('id_cliente', $cliente->id)
            ->where('aplicado', false)
            ->orderBy('fecha_abono')->get();

        $abonoTotal = $abonos->sum('monto');
        $abonoAplicado = min($abonoTotal, $total);
        $totalPagado = $total - $abonoAplicado;

        $venta = Venta::create([
            'id_cliente' => $cliente->id,
            'total' => $total,
            'abono_aplicado' => $abonoAplicado,
            'total_pagado' => $totalPagado,
            'forma_pago' => $request->forma_pago,
        ]);

        foreach ($productos as $producto) {
            $producto->estado = 'recogido';
            $producto->id_venta = $venta->id; // ✅ Aquí está la clave
            $producto->save();
        }
        

        $saldo = $abonoAplicado;
        foreach ($abonos as $abono) {
            if ($saldo <= 0) break;
            $saldo -= $abono->monto;
            $abono->aplicado = true;
            $abono->save();
        }

        return response()->json([
            'mensaje' => 'Venta registrada con éxito',
            'venta_id' => $venta->id,
            'total_pagado' => $totalPagado,
            'abono_aplicado' => $abonoAplicado,
        ]);
    }
}
