<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id_category';
    protected $fillable = [
        'name',
    ];
    public $timestamps = false;

    public function products()
    {
        return $this->belongsToMany(
                Product::class,'category_product','id_category','id_product',
            );
    }
}