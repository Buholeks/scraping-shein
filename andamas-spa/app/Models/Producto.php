<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'id_empresa',
        'id_sucursal',
        'id_user',
        'id_cliente',
        'sku',
        'marca',
        'modelo',
        'titulo',
        'precio',
        'precio_original',
        'comision',
        'total',
        'imagen',
    ];

    public function cliente()
{
    return $this->belongsTo(Cliente::class, 'id_cliente');
}

}
