<?php

namespace Modules\Movie\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionCountry extends Model
{
    protected $fillable = [
        'name',
        'iso_3166_1'
    ];

    public $timestamps = false;

    protected $hidden = ['pivot'];
}