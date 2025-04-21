<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    protected $fillable = ['id_cliente', 'monto', 'fecha_abono','aplicado'];

}
