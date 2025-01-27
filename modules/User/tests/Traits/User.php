<?php

namespace Modules\User\Tests\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait User
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('users')->truncate();
        DB::table('reviews')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Create random data user
     * 
     * @return array
     */
    protected function fakeUser(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => '123456',
            'password_confirmation' => '123456'
        ];
    }

    protected function fakeReview(): array
    {
        return [
            'movie_id' => $this->getRandomMovieId(),
            'review' => fake()->text(),
            'positive' => rand(0, 1)
        ];
    }

    /**
     * Create new user and return $credentials
     * 
     * @param array $columns
     * 
     * @return array 
     */
    protected function registerNewUser(array $colums = ['email', 'password']): array
    {
        $payload = $this->fakeUser();

        $this->postJson(route('v1.api.user.store'), $payload);

        return Arr::only($payload, $colums);
    }

    /**
     * Create new user, make auth e return user(name, email) and token
     * 
     * @return array
     */
    protected function generateTokenAndUserData(): array
    {
        $user = $this->registerNewUser(['name', 'password', 'email']);

        $response = $this->postJson(route('v1.api.user.token'), Arr::only($user, ['email', 'password']));
        
        return array_merge([
            'user' => Arr::only($user, ['email', 'name'])
        ], $response->json());
    }

    /**
     * Return array with access token headers 
     * 
     * @return array 
     */
    protected function createHeaderAuthorization(): array
    {
        $token = $this->generateTokenAndUserData();
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            $token['type'] => $token['token']
        ];
    }

    /**
     * Return random movie id 
     * 
     * @return int
     */
    protected function getRandomMovieId(): int
    {
        return \Modules\Movie\Models\Movie::inRandomOrder()->first('id')->id;
    }

    /**
     * Create a new review and retuen payload with response
     * 
     * @return array
     */
    protected function createReview(): array
    {
        $headers = $this->createHeaderAuthorization();
        $payload = $this->fakeReview();
        $response = $this->postJson(route('v1.api.review.store'), $payload, $headers);
        return [$payload, $response->json(), $headers];
    }

    /**
     * Create review and return listReview
     * 
     * @return array
     */
    protected function createReviewAndReturnList(): array
    {
        [$payload, $createResponse, $headers] = $this->createReview();
        $response = $this->getJson(route('v1.api.review.list'), $headers);
        return [$response->json('data'), $headers];        
    }
}