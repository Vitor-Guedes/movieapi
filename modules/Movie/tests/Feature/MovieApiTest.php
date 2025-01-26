<?php

namespace Modules\Movie\Tests\Feature;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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

    protected function tearDown(): void
    {
        $this->beforeApplicationDestroyed(fn () => DB::disconnect());
        parent::tearDown();
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
        $borders = implode(",", ["genres", "keywords", "production_companies", "production_countries", "spoken_languages"]);
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
                    'spoken_languages' => ['*' => ['id', 'name', 'iso_639_1']]
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

    public function test_must_be_able_to_bring_data_on_the_relationships_of_a_specific_film()
    {
        $movieId = \Modules\Movie\Models\Movie::inRandomOrder()->first();

        $realtions = ['genres', 'keywords', 'production_companies', 'production_countries', 'spoken_languages'];
        $realtion = $realtions[rand(0, count($realtions) - 1)];

        $response = $this->getJson(route('v1.api.movie.relation.find', [
            'id' => $movieId,
            'relation' => $realtion
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'movie_id',
            $realtion => [
                '*' => [
                    'id'
                ]
            ]
        ]);
    }

    public function test_must_be_able_to_use_a_query_with_like_operator_to_filter_results()
    {
        $query = http_build_query([
            'query' => [
                'and' => [
                    [   
                        'field' => 'title',
                        'operator' => 'lk',
                        'value' => 'ava'
                    ],
                    [
                        'field' => 'status',
                        'operator' => 'lk',
                        'value' => 'Released'
                    ]
                ]
            ],
            'fields' => 'id,title,status'
        ]);

        $url = route('v1.api.movie.get'). "?" . $query;

        $response = $this->getJson($url);

        $response->assertStatus(200);
        $this->assertMatchesRegularExpression('/ava/', $response->json('data')[0]['title']);
        $this->assertMatchesRegularExpression('/Released/', $response->json('data')[0]['status']);
    }

    public function test_should_be_able_to_filter_using_query_condition_or()
    {
        $query = http_build_query([
            'query' => [
                'or' => [
                    [   
                        'field' => 'runtime',
                        'operator' => 'eq',
                        'value' => 124
                    ],
                    [   
                        'field' => 'runtime',
                        'operator' => 'eq',
                        'value' => 88
                    ],
                ]
            ],
            'fields' => 'id,runtime',
            'limit' => 100
        ]);

        $url = route('v1.api.movie.get'). "?" . $query;

        $response = $this->getJson($url);

        $response->assertStatus(200);
        $this->assertEquals(124, $response->json('data')[0]['runtime']);
        $this->assertEquals(88, $response->json('data')[30]['runtime']);
    }

    public function test_must_be_able_to_bring_the_records_with_the_equal_query_the_attribute()
    {
        $query = [
            'query' => [
                'field' => 'budget',
                'operator' => 'eq',
                'value' => 28000000
            ]
        ];
        $query = http_build_query($query);
        $url = route('v1.api.movie.get'). "?" . $query;

        $response = $this->getJson($url);

        $response->assertStatus(200);
        $this->assertEquals(28000000, $response->json('data')[0]['budget']);
    }

    public function test_must_be_able_to_bring_records_with_the_query_greater_than()
    {
        $query = [
            'query' => [
                'field' => 'runtime',
                'operator' => 'gt',
                'value' => 124
            ],
            'fields' => 'id,runtime'
        ];
        $query = http_build_query($query);
        $url = route('v1.api.movie.get'). "?" . $query;

        $response = $this->getJson($url);

        $response->assertStatus(200);
        $this->assertGreaterThan(124, $response->json('data')[0]['runtime']);
    }

    public function test_must_be_able_to_bring_records_with_the_query_lass_than()
    {
        $query = [
            'query' => [
                'field' => 'runtime',
                'operator' => 'lt',
                'value' => 118
            ],
            'fields' => 'id,runtime'
        ];
        $query = http_build_query($query);
        $url = route('v1.api.movie.get'). "?" . $query;

        $response = $this->getJson($url);

        $response->assertStatus(200);
        $this->assertLessThan(118, $response->json('data')[0]['runtime']);
    }
}