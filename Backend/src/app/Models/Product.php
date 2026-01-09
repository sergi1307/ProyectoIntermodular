<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Delivery_Point;
use App\Models\Category;

class Product extends Model
{
    protected $table = 'products';

    public $timestamps = false;

    protected $primaryKey = 'id_product';
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
        'type_stock',
        'state',
        'publication_date'
    ];

    protected $hidden = [
        'id_user',
        'id_delivery_point',
        'id_category'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function delivery_point()
    {
        return $this->belongsTo(Delivery_Point::class, 'id_delivery_point');
    }

    public function category()
    {
        return $this->hasMany(Category::class, 'id_category');
    }
}
