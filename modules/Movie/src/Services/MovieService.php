<?php

namespace Modules\Movie\Services;

use Closure;
use Modules\Movie\Models\Movie;
use Modules\Movie\Filters\MovieFilters;

class MovieService
{
    public function __construct(protected MovieFilters $movieFilters) {}

    /**
     * @param Closure $callback
     * 
     * @return mixed
     */
    public function filterApply(Closure $callback): mixed
    {
        $this->movieFilters->apply();
        return $callback($this);
    }

    /**
     * @return array
     */
    public function list(): array
    {
        $limit = request()->input('limit', 10);
        return Movie::simplePaginate($limit)->toArray();
    }

    /**
     * @param int $movie
     * @param string $relation
     */
    public function findWithRelation(int $movieId, string $relation)
    {
        if (! in_array($relation, $this->relationList())) {
            return [];
        }
        $movie = Movie::with($relation)->findOrFail($movieId, ['id']);
        return [
            'movie_id' => $movie->id,
            $relation => $movie->{$relation}->toArray()
        ];
    }

    /**
     * @return array
     */
    protected function relationList(): array
    {
        return [
            'genres', 
            'keywords', 
            'production_companies', 
            'production_countries', 
            'spoken_languages'
        ];
    }
}