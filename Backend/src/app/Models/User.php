<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // Utilizamos dos librerias para que el usuario pueda generar tokens y recibir notificaciones
    use HasApiTokens, Notifiable;

    // Obtenemos la tabla users
    protected $table = 'users';

    // Definimos la primary key
    protected $primaryKey = 'id_user';
    
    // Marcamos los timestamps en falso
    public $timestamps = false;

    // Definimos los campos que se pueden llenar
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'registration_date',
    ];

    // Definimos los campos que son diferentes
    protected $casts = [
        'registration_date' => 'date',
    ];

    // Definimos los campos ocultos
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Función para obtener los delivery points del usuario
    public function delivery_points()
    {
        return $this->hasMany(Delivery_Point::class, 'id_user', 'id_user');
    }

    // Función para obtener los productos del usuario
    public function products()
    {
        return $this->hasMany(Product::class, 'id_user', 'id_user');
    }

    // Función para obtener el comprador
    public function buyer()
    {
        return $this->hasMany(Sale::class, 'id_buyer', 'id_user');
    }

    // Función para obtener el vendedor
    public function seller()
    {
        return $this->hasMany(Sale::class, 'id_seller', 'id_user');
    }

    // Función para obtener el perfil del usuario
    public function profile()
    {
        return $this->hasOne(Profile::class, 'id_user', 'id_user');
    }

}
