<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Producto;

class SheinController extends Controller
{
    // app/Http/Controllers/Api/SheinController.php

    // public function guardar(Request $request)
    // {
    //     $producto = json_decode($request->input('producto'), true);

    //     Producto::create([
    //         'sku' => $producto['sku'],
    //         'titulo' => $producto['title'],
    //         'imagenes' => json_encode($producto['images']),
    //         'url' => $producto['url'],
    //         'precio' => $producto['price'] ?? null,
    //         'precio_original' => $producto['price_original'] ?? null,
    //     ]);

    //     return response()->json(['mensaje' => 'Producto guardado con éxito.']);
    // }


    public function guardarLote(Request $request)
    {
        $request->validate([
            'id_empresa' => 'required|exists:empresas,id',
            'id_sucursal' => 'required|exists:sucursales,id',
            'id_cliente' => 'nullable|exists:clientes,id', // 👈 validación añadida
            'productos' => 'required|array',
            'productos.*.sku' => 'required|string',
            'productos.*.title' => 'required|string',
            'productos.*.price' => 'nullable|numeric',
            'productos.*.price_original' => 'nullable|numeric',
            'productos.*.commission' => 'nullable|numeric',
            'productos.*.total' => 'nullable|numeric',
            'productos.*.images' => 'nullable|array',
            'productos.*.images.0' => 'nullable|string',
        ]);

        foreach ($request->productos as $p) {
            Producto::create([
                'sku' => $p['sku'],
                'titulo' => $p['title'],
                'precio' => $p['price'],
                'precio_original' => $p['price_original'],
                'comision' => $p['commission'],
                'total' => $p['total'],
                'imagen' => $p['images'][0] ?? null,
                'id_empresa' => $request->id_empresa,
                'id_sucursal' => $request->id_sucursal,
                'id_cliente' => $request->id_cliente // 👈 lo guardamos
            ]);
        }

        return response()->json(['message' => 'Productos guardados']);
    }
}
