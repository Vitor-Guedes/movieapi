<?php

namespace Modules\Movie\Models;

use Illuminate\Database\Eloquent\Model;

class SpokenLanguage extends Model
{
    protected $fillable = [
        'name',
        'iso_639_1'
    ];

    public $timestamps = false;
}