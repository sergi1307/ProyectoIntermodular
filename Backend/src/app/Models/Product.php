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
        'id_user',
        'id_delivery_point',
        'id_category',
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
        'id_category',
    ];
    protected $casts = [
        'price' => 'double',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user','id_user');
    }

    public function delivery_point()
    {
        return $this->belongsTo(Delivery_Point::class, 'id_delivery_point','id_delivery_point');
    }

    public function categorie()
    {
        return $this->belongsToMany(Category::class,'category_product','id_product','id_category');
    }
    public function sale()
    {
        return $this->hasOne(Sale::class, 'id_product', 'id_product');
    }
}