<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'id_cliente',
        'total',
        'abono_aplicado',
        'total_pagado',
        'forma_pago',
    ];
    

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_venta');
    }

    public function productosventa()
{
    return $this->belongsToMany(Producto::class, 'venta_productos');
}

}

