<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    protected $fillable = [
        'id_product',
        'id_buyer',
        'id_seller',
        'id_delivery_point',
        'id_review',
        'sale_date',
        'total',
        'collection_date'
    ];
}
