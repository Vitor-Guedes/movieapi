<?php

namespace Modules\Movie\Pipes\Images;

use Modules\Movie\Dto\ImageSearchDto;
use Modules\Movie\Models\Movie;

class FindTermInDatabase
{
    /**
     * @param strign $term
     * @param Closure $next
     * 
     * @return Closure|string
     */
    public function handle(string $term, $next)
    {
        $movie = Movie::where('original_title', $term)
            ->orWhere('title', $term)
            ->first();

        $imageSearchDto = new ImageSearchDto([
            'term' => $term,
            'movie' => $movie
        ]);

        if ($movie->id && $movie->images()->exists()) {
            $imageSearchDto->image_list = $movie->images->image_list;
            return $imageSearchDto;
        }

        return $next($imageSearchDto);
    }
}