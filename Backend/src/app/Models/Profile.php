<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    // Obtenemos la tabla profiles
    protected $table = 'profiles';

    // Marcamos los timestamps como falso
    public $timestamps = false;

    // Definimos la primary key
    protected $primaryKey = 'id_profile';

    // Definimos que la primary key vaya subiendo de número inserción tras inserción
    public $incrementing = true;

    // Marcamos que la primary key tiene que ser un integer
    protected $keyType = 'int';

    // Definimos los campos que se pueden llenar
    protected $fillable = [
        'profile_img',
    ];

    // Función para obtener el usuario al que pertenece este perfil
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
