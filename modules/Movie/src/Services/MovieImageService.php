<?php

namespace Modules\Movie\Services;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Modules\Movie\Dto\ImageSearchDto;
use Modules\Movie\GoogleSearchClient;
use Modules\Movie\Models\Movie;
use Modules\Movie\Pipes\Images\FindTermInDatabase;
use Modules\Movie\Pipes\Images\GoogleSearchApi;
use Modules\Movie\Pipes\Images\StoreInDatabase;

class MovieImageService
{
    protected array $terms = [];

    /**
     * @param string $term
     * 
     * @return ImageSearchDto
     */
    public function findByTerm(string $term): ImageSearchDto
    {
        return app(Pipeline::class)
            ->send($term)
            ->through([
                FindTermInDatabase::class,
                GoogleSearchApi::class,
                StoreInDatabase::class
            ])
            ->thenReturn(fn ($content) => [
                $this->terms[$term] = $content
            ]);
    }

    /**
     * @param array $data
     * @param array $keys
     * 
     * @return array
     */
    public function simplify(array $data, array $keys = []): array
    {
        return collect($data)->map(
            fn ($item) => Arr::only(Arr::dot($item), $keys)
        )->toArray();
    }

    /**
     * @param string $term
     * 
     * @return array
     */
    public function googleSearch(string $term): array
    {
        return app(GoogleSearchClient::class)->get($term);

        // @todo replace by class
        return json_decode(
            file_get_contents(base_path('public') . '/busca.json'),
            true
        );
    }

    /**
     * @param Movie $movie
     * @param array $data
     * 
     * @return $movie
     */
    public function store(Movie $movie, array $data = [])
    {
        return $movie->images()->create([
            'image_list' => $data
        ]);
    }
}