<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id_sale';
    public $timestamps = false;

    protected $fillable = [
        'sale_date',
        'total',
        'collection_date'
    ];

    protected $hidden = [
        'id_product',
        'id_buyer',
        'id_seller',
        'id_delivery_point',
        'id_review',
    ];
     protected $casts = [
        'sale_date' => 'date',
        'collection_date' => 'date',
    ];
}
