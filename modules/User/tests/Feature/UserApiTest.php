<?php

namespace Modules\User\Tests;

use Tests\TestCase;
use Modules\User\Tests\Traits\User;

class UserApiTest extends TestCase
{   
    use User;

    public function test_should_be_able_to_register_new_user()
    {
        $payload = $this->fakeUser();

        $response = $this->postJson(route('v1.api.user.store'), $payload);

        $response->assertSuccessful();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_must_be_able_to_login_and_obtain_an_access_token()
    {
        $payload = $this->registerNewUser();

        $response = $this->postJson(route('v1.api.user.token'), $payload);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'type',
            'token',
            'expire'
        ]);
    }

    public function test_must_be_able_to_obtain_user_data()
    {
        $token = $this->generateTokenAndUserData();
        $headers = [$token['type'] => $token['token']];

        $response = $this->getJson(route('v1.api.user'), $headers);

        $response->assertSuccessful();
        $this->assertEquals($token['user']['email'], $response->json('email'));
        $this->assertEquals($token['user']['name'], $response->json('name'));
    }

    public function test_should_be_able_to_disable_access_token()
    {
        $token = $this->generateTokenAndUserData();
        $headers = [$token['type'] => $token['token']];

        // test response logout
        $response = $this->getJson(route('v1.api.user.logout'), $headers);
        $response->assertSuccessful();

        // test unathorized after sucessful logout
        $response = $this->getJson(route('v1.api.user'), $headers);
        $response->assertUnauthorized();
    }
}