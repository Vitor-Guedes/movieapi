<?php

namespace Modules\Movie\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'id',
        'budget',
        'homepage',
        'original_language',
        'original_title',
        'overview',
        'popularity',
        'release_date',
        'revenue',
        'runtime',
        'status',
        'tagline',
        'title',
        'vote_average',
        'vote_count'
    ];

    public $timestamps = false;

    public function genres()
    {
        return $this->belongsToMany(Genres::class, 'movie_genres', 'movie_id', 'genre_id');
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'movie_keywords', 'movie_id', 'keyword_id');
    }

    public function production_companies()
    {
        return $this->belongsToMany(ProductionCompany::class, 'movie_production_companies', 'movie_id', 'production_company_id');
    }

    public function production_countries()
    {
        return $this->belongsToMany(ProductionCountry::class, 'movie_production_countries', 'movie_id', 'production_country_id');
    }

    public function spoke_languages()
    {
        return $this->belongsToMany(SpokenLanguage::class, 'movie_spoken_languages', 'movie_id', 'spoken_language_id');
    }
}