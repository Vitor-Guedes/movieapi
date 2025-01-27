<?php

namespace Modules\User\Tests\Feature;

use Tests\TestCase;
use Modules\User\Tests\Traits\User;

class ReviewApiTest extends TestCase
{
    use User;

    public function test_must_be_able_to_create_a_review_about_the_film_with_the_logged_in_user()
    {
        $headers = $this->createHeaderAuthorization();
        $payload = $this->fakeReview();

        $response = $this->postJson(route('v1.api.review.store'), $payload, $headers);
        
        $response->assertSuccessful();
        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseHas('reviews', [
            'movie_id' => $payload['movie_id'],
            'review' => $payload['review'],
            'positive' => $payload['positive'],
        ]);
    }

    public function test_must_be_able_to_bring_user_reviews_with_references_to_the_films()
    {
        [$payload, $createResponse, $headers] = $this->createReview();

        $response = $this->getJson(route('v1.api.review.list'), $headers);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'movie_id',
                    'user_id',
                    'review',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);        
    }

    public function test_should_be_able_to_change_the_user_review_of_the_film()
    {
        $payload = $this->fakeReview();
        [$reviewList, $headers] = $this->createReviewAndReturnList();

        $response = $this->putJson(route('v1.api.review.update', ['id' => $reviewList[0]['id']]), $payload, $headers);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
        $this->assertDatabaseHas('reviews', [
            'id' => $reviewList[0]['id'],
            'movie_id' => $reviewList[0]['movie_id'],
            'user_id' => $reviewList[0]['user_id'],
            'review' => $payload['review'],
            'positive' => $payload['positive']
        ]);
    }

    public function test_should_be_able_to_delete_user_review_about_the_film()
    {
        [$reviewList, $headers] = $this->createReviewAndReturnList();

        $response = $this->deleteJson(route('v1.api.review.destroy', ['id' => $reviewList[0]['id']]), $headers);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_must_inform_you_that_you_are_unable_to_perform_an_action_because_you_are_not_authenticated()
    {
        [$reviewList, $headers] = $this->createReviewAndReturnList();

        $responseLogout = $this->getJson(route('v1.api.user.logout'), $headers);

        $responseList =  $this->getJson(route('v1.api.review.list'), $headers);
        $responseStore =  $this->postJson(route('v1.api.review.store'), $this->fakeReview(), $headers);
        $responseUpdate =  $this->putJson(route('v1.api.review.update', ['id' => $reviewList[0]['id']]), $this->fakeReview(), $headers);
        $responseDelete =  $this->deleteJson(route('v1.api.review.destroy', ['id' => $reviewList[0]['id']]), $headers);

        $responseLogout->assertSuccessful();
        $responseList->assertUnauthorized();
        $responseStore->assertUnauthorized();
        $responseUpdate->assertUnauthorized();
        $responseDelete->assertUnauthorized();
        $this->assertDatabaseCount('reviews', 1);
    }
}