<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use GuzzleHttp\Psr7\Query;

class ClienteResumenController extends Controller
{
    public function resumen($id)
{
    $cliente = Cliente::with(['productos', 'abonos', 'abonosNoAplicados'])->findOrFail($id);

    return response()->json([
        'totalProductos' => $cliente->productos->whereIn('estado', ['pendiente', 'en_tienda'])->count(),
        'totalRecogidos' => $cliente->productos->where('estado', 'pendiente')->count(),
        'totalEnTienda'  => $cliente->productos->where('estado', 'en_tienda')->count(),
        'totalAPagar'    => $cliente->productos->where('estado', 'en_tienda')->sum('precio'), // ✅ filtrado
        'totalAbonado'   => $cliente->abonosNoAplicados->sum('monto'),
        'ultimoAbono'    => optional($cliente->abonos->sortByDesc('fecha_abono')->first())->fecha_abono,
    ]);
}

}
