<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles';
    public $timestamps = false;

    protected $primaryKey = 'id_profile';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'profile_img',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

}
