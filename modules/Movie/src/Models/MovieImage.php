<?php

namespace Modules\Movie\Models;

use Illuminate\Database\Eloquent\Model;

class MovieImage extends Model
{
    protected $fillable = [
        'movie_id',
        'image_list'
    ];

    protected $casts = [
        'image_list' => 'array'
    ];

    public $timestamps = false;
}