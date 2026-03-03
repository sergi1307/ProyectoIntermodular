<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table="logs";
    protected $primaryKey = 'id_log';
    public $timestamps =false;
    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'data',
        'ip_address',
        'create_at',
    ];
    public function usuario(){
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
