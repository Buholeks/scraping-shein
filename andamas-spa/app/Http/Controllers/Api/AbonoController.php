<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Abono;

class AbonoController extends Controller
{
    public function index($clienteId)
    {
        return Abono::where('id_cliente', $clienteId)
            ->orderByDesc('fecha_abono')
            ->get();
    }

    public function store(Request $request, $clienteId)
    {
        $request->validate([
            'monto' => 'required|numeric',
            'fecha_abono' => 'required|date',
        ]);

        return Abono::create([
            'id_cliente' => $clienteId,
            'monto' => $request->monto,
            'fecha_abono' => $request->fecha_abono,
            'nota' => $request->nota,
        ]);
    }

    public function totalDisponible($clienteId)
    {
        $total = Abono::where('id_cliente', $clienteId)
            ->where('aplicado', false)
            ->sum('monto');

        return response()->json(['abono' => $total]);
    }
}
