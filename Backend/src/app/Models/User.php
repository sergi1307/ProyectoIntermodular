<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    //HE TENID QUE ÑADIR ESTA LINEA SINO NO PODIA CREAR UN USUARIO PARA PROBAR EL MAPA Y NO ME DEJABA
    public $timestamps = false;
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'registration_date',//HE TENID QUE ÑADIR ESTA LINEA SINO NO PODIA CREAR UN USUARIO PARA PROBAR EL MAPA Y NO ME DEJABA
    ];
    protected $hidden = [
        'password',
        'created_at',
    ];
}
