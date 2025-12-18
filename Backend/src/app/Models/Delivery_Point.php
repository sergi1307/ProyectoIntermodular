<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery_Point extends Model
{
    protected $table = 'delivery_points';
    protected $fillable = [
        'id_user',
        'name',
        'direction',
        'latitude',
        'length'
    ];
}
