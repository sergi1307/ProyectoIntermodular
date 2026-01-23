<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery_Point extends Model
{
    protected $table = 'delivery_points';
    public $timestamps = false;
    protected $primaryKey = 'id_delivery_point';
    protected $fillable = [
        'id_user',
        'name',
        'direction',
        'latitude',
        'length',
    ];
    protected $hidden = [
        'id_user',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'id_delivery_point','id_delivery_point');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_user');
    }

} 
