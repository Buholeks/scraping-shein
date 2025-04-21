<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'rfc',
        'correo',
        'telefono',
        'direccion',
    ];

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'id_empresa');
    }
}
