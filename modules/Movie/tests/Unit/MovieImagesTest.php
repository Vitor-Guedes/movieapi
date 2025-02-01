<?php

namespace Modules\Movie\Tests\Unit;

use Modules\Movie\GoogleSearchClient;
use Modules\Movie\Services\MovieImageService;
use Tests\TestCase;

class MovieImagesTest extends TestCase
{
    public function test_should_be_able_to_create_a_GoogleSearchClient_class()
    {
        $client = app(GoogleSearchClient::class, config('movie.images'));

        $this->assertInstanceOf(GoogleSearchClient::class, $client);
    }

    public function test_should_be_able_to_create_the_search_url()
    {
        $settings = config('movie.images');
        $client = app(GoogleSearchClient::class, $settings);
        $term = 'Finding Nemo';
        $url = sprintf(
            "%s?q=%s&cx=%s&key=%s&searchType=image&num=%s",
            $settings['google_cse_url'],
            urlencode($term),
            $settings['google_cse_id'],
            $settings['google_cse_key'],
            10
        );

        $this->assertEquals($url, $client->buildUrl($term));
    }

    public function test_should_be_able_to_create_a_list_offilmer_images_from_the_term()
    {
        $term = 'Finding Nemo';
        $service = app(MovieImageService::class);
        $keys = ['link', 'image.thumbnailLink'];

        $imageSearchDto = $service->findByTerm($term);
        $images = $service->simplify(
            $imageSearchDto->image_list['items'], 
            $keys
        );

        $this->assertIsArray($images);
        $this->assertDatabaseCount('movie_images', 1);
        $this->assertArrayHasKey('link', $images[0]);
        $this->assertArrayHasKey('image.thumbnailLink', $images[0]);
    }
}