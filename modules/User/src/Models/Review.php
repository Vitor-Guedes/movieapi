<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'movie_id',
        'user_id',
        'review',
        'positive'
    ];
}