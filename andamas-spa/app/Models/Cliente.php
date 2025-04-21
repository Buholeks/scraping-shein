<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'id_empresa',
        'id_sucursal',
        'nombre',
        'telefono',
        'direccion'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    // Cliente.php
    // Cliente.php
    public function abonosNoAplicados()
    {
        return $this->hasMany(Abono::class, 'id_cliente')->where('aplicado', false);
    }


    public function abonos()
    {
        return $this->hasMany(Abono::class, 'id_cliente');
    }
    public function productos()
    {
        return $this->hasMany(\App\Models\Producto::class, 'id_cliente');
    }
}
