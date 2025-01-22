<?php

namespace Modules\Movie\Services;

use Modules\Movie\Models\Movie;
use Modules\Movie\Facets\MovieFacet;

class MovieService
{
    public function __construct(MovieFacet $movieFacets)
    {
        $movieFacets->apply();
    }

    public function list()
    {
        $limit = request()->input('limit', 10);
        return Movie::simplePaginate($limit)->toArray();
    }
}