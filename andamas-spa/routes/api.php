<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\SheinController;
use App\Http\Controllers\Api\SucursalController;
use App\Http\Controllers\Api\ClienteResumenController;
use App\Http\Controllers\Api\AbonoController;
use App\Http\Controllers\Api\ProductosController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\VentaController;

// 🟢 Rutas públicas
Route::post('/login', [AuthController::class, 'login']);

// 🔒 Rutas protegidas con autenticación Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // 📦 CRUD clientes
    Route::apiResource('clientes', ClienteController::class);

    // 👤 Cierre de sesión
    Route::post('/logout', [AuthController::class, 'logout']);

    // 🧍 Usuario autenticado con empresa y sucursal
    Route::get('/user', fn(Request $request) => $request->user()->load('empresa', 'sucursal'));

    // 🌐 Cambiar sucursal activa
    Route::post('/cambiar-sucursal', [SucursalController::class, 'cambiarSucursal']);

    // 🏢 Sucursales del usuario
    Route::get('/user/sucursales', function () {
        $user = auth()->user();
        return $user->sucursales()->get(['id', 'nombre']);
    });

    // 🛍 Shein productos
    Route::post('/shein/guardar', [SheinController::class, 'guardar']);
    Route::post('/shein/guardar-lote', [SheinController::class, 'guardarLote']);

    // 📊 Cliente - Resumen, Abonos y Ventas
    Route::prefix('clientes/{cliente}')->group(function () {
        Route::get('/resumen', [ClienteResumenController::class, 'resumen']);

        // 💰 Abonos
        Route::get('/abonos', [AbonoController::class, 'index']);
        Route::post('/abonos', [AbonoController::class, 'store']);
    });

    // En api.php
    Route::get('/clientes/{clienteId}/abono', [AbonoController::class, 'totalDisponible']);

    Route::post('/ventas/confirmar', [ProductosController::class, 'confirmar']);

    Route::get('/clientes', [ClienteController::class, 'index']); // ya la tienes

    // Para entregas: productos aún pendientes
    Route::get('/productos/noRecogidos', [ProductosController::class, 'noRecogidos']);
    // Para ventas: productos disponibles en tienda
    Route::get('/productos/disponible', [ProductosController::class, 'disponible']);
    Route::put('/productos/{producto}/estado', [ProductosController::class, 'actualizarEstado']);
});
