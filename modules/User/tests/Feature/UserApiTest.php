<?php

namespace Modules\User\Tests;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        DB::table('users')->truncate();
    }

    protected function fakeUser(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => '123456',
            'password_confirmation' => '123456'
        ];
    }

    public function test_should_be_able_to_register_new_user()
    {
        $payload = $this->fakeUser();

        $response = $this->postJson(route('v1.api.user.store'), $payload);

        $response->assertSuccessful();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_must_be_able_to_login_and_obtain_an_access_token()
    {
        $payload = $this->fakeUser();
        $this->postJson(route('v1.api.user.store'), $payload);

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
        $payload = $this->fakeUser();
        $this->postJson(route('v1.api.user.store'), $payload);
        $response = $this->postJson(route('v1.api.user.token'), $payload);
        $headers = [
            $response->json('type') => $response->json('token')
        ];

        $response = $this->getJson(route('v1.api.user'), $headers);

        $response->assertSuccessful();
        $this->assertEquals($payload['email'], $response->json('email'));
        $this->assertEquals($payload['name'], $response->json('name'));
    }

    public function test_should_be_able_to_disable_access_token()
    {
        $payload = $this->fakeUser();
        $this->postJson(route('v1.api.user.store'), $payload);
        $response = $this->postJson(route('v1.api.user.token'), $payload);
        $headers = [
            $response->json('type') => $response->json('token')
        ];

        $response = $this->getJson(route('v1.api.user'), $headers);

        $response = $this->getJson(route('v1.api.user.logout'), $headers);

        $response->assertSuccessful();

        $response = $this->getJson(route('v1.api.user'), $headers);
        $response->assertUnauthorized();
    }
}