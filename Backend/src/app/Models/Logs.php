<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table = 'logs';
    protected $primaryKey = 'id_log';
    public $timestamps = false;
    protected $fillable = ['id_user','action','table_name','data','created_at'];
}
