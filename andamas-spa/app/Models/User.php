<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'activo',
        'id_empresa',
        'id_sucursal', // Asegúrate que este campo existe en tu tabla users
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean', // Añade esto si activo es booleano
        ];
    }

    // app/Models/User.php
    public function sucursales()
    {
        return Sucursal::where(function ($query) {
            $query->whereIn('id', function ($sub) {
                $sub->select('id_sucursal')
                    ->from('sucursal_user')
                    ->where('id_user', $this->id);
            })->orWhere('id', $this->id_sucursal);
        });
    }


    // Relación con empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    // // Relación con sucursal principal (ajustado el nombre para claridad)
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    // Relación many-to-many con sucursales adicionales
    // app/Models/User.php
    public function sucursalesAdicionales()
    {
        return $this->belongsToMany(
            Sucursal::class,
            'sucursal_user',
            'id_user',
            'id_sucursal'
        )->withTimestamps();
    }

    // // app/Models/User.php
    // public function sucursales()
    // {
    //     return $this->belongsToMany(Sucursal::class, 'sucursal_user', 'id_user', 'id_sucursal')
    //         ->withTimestamps();
    // }
}
