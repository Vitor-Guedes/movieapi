<?php

namespace Modules\Movie\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $fillable = [
        'id',
        'name'
    ];

    public $timestamps = false;
}