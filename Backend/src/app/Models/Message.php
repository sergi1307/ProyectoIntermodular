<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // Obtenemos la table messages
    protected $table = 'messages';

    // Definimos la tabla que se pueden llenar
    protected $fillable = [
        'id_product',
        'id_transmitter',
        'id_receiver',
        'content',
        'shipping_date'
    ];
}
