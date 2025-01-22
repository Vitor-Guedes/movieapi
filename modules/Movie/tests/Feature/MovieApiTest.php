<?php

namespace Modules\Movie\Tests\Feature;

use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class MovieApiTest extends TestCase
{
    protected function movieAttributes(array $extra = [])
    {
        return array_merge([
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
        ], $extra);
    }

    public function test_should_be_able_to_bring_up_a_list_of_movies()
    {
        $defaultPerPage = 10;

        $response = $this->getJson(route('v1.api.movie.get'));

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => 
            $json->has('data')
                ->has('current_page')
                ->has('first_page_url')
                ->has('from')
                ->has('next_page_url')
                ->has('path')
                ->has('per_page')
                ->has('prev_page_url')
                ->has('to')
        );

        $response->assertJsonStructure([
            'data' => [
                '*' => $this->movieAttributes()
            ]
        ]);                                        

        $this->assertCount($defaultPerPage, $response->json('data'));
        $this->assertEquals(1, $response->json('current_page'));
    }

    public function test_must_be_able_to_bring_n_items_in_the_search()
    {
        $nItems = rand(11, 50);
        $url = route('v1.api.movie.get') . "?limit=$nItems";

        $response = $this->getJson($url);

        $this->assertCount($nItems, $response->json('data'));
    }

    public function test_must_be_able_to_bring_the_records_from_page_x()
    {
        $nPage = rand(2, 50);
        $url = route('v1.api.movie.get') . "?page=$nPage";

        $response = $this->getJson($url);

        $this->assertEquals($nPage, $response->json('current_page'));
    }

    public function test_should_be_able_to_bring_up_the_films_with_the_genres()
    {
        $border = "genres";
        $url = route('v1.api.movie.get') . "?with=$border";

        $response = $this->getJson($url);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => $this->movieAttributes([
                    $border => ['*' => ['id', 'name']]
                ])
            ]
        ]);                      
    }

    public function test_should_be_able_to_bring_the_movies_with_all_your_relationships()
    {
        $borders = implode(",", ["genres", "keywords", "production_companies", "production_countries", "spoke_languages"]);
        $url = route('v1.api.movie.get') . "?with=$borders";

        $response = $this->getJson($url);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => $this->movieAttributes([
                    'genres' => ['*' => ['id','name']],
                    'keywords' => ['*' => ['id','name']],
                    'production_companies' => ['*' => ['id','name']],
                    'production_countries' => ['*' => ['id', 'name', 'iso_3166_1']],
                    'spoke_languages' => ['*' => ['id', 'name', 'iso_639_1']]
                ])
            ]
        ]);              
    }

    public function test_must_be_able_to_limit_the_fields_returned_in_the_search()
    {
        $attributes = $this->movieAttributes();
        $fields = Arr::random($attributes, rand(1, count($this->movieAttributes()) - 1));
        $fields = implode(',', $fields);
        $url = route('v1.api.movie.get') . "?fields=$fields";

        $response = $this->getJson($url);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => Arr::only($attributes, $fields)
            ]
        ]);              
    }

    public function test_must_be_able_to_limit_the_relationship_fields_not_returned_in_the_search()
    {
        $attributes = $this->movieAttributes();
        $fields = Arr::random($attributes, rand(1, count($this->movieAttributes()) - 1));
        $fields = array_merge($fields, ['production_countries.iso_3166_1,production_countries.id', 'genres.name']);
        $fields = implode(',', $fields);
        $url = route('v1.api.movie.get') . "?fields=$fields";

        $response = $this->getJson($url);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => array_merge(Arr::only($attributes, $fields), [
                    'genres' => ['*' => ['name']],
                    'production_countries' => ['*' => ['iso_3166_1']]
                ]),
            ]
        ]);
    }
}