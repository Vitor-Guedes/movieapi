<?php

namespace Modules\Movie\Models;

use Illuminate\Database\Eloquent\Model;

class Genres extends Model
{
    protected $fillable = [
        'id',
        'name'
    ];

    public $timestamps = false;

    public function movies()
    {
        $this->belongsToMany(Movie::class, 'movie_genres', 'genre_id', 'movie_id');
    }
}