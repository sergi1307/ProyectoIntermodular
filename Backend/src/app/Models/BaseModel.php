<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    public $timestamps = false;

    public function getKeyName()
    {
        return 'id_'.Str::snake(class_basename($this));
    }
}

