<?php

namespace Tests\Feature\v1;

use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\AuthorizedTestCase;
use Tests\TestCase;
use Illuminate\Http\Response;

class SignInTest extends AuthorizedTestCase
{

    /**
     * Register new user
     *
     * @param \App\User $user
     */
    public function createDefaultUserRequest($user){
        $response = $this->json('post', '/api/v1/users', $this->generated_user_model);

        $content = json_decode($response->content());

        $this->assertNotEmpty($content->status, "Returned response has not valid content");
        $this->assertEquals($content->status, "success","Response status is not successfully");
        $response->assertStatus(201);
    }

    /**
     * Make test with valid request data
     *
     * @return void
     */
    public function testValidLoginRequest()
    {
        $response = $this->json('post', '/api/v1/signin', [
            "email" => $this->generated_user_model['email'],
            "password" => $this->generated_user_model['password'],
        ]);

        $content = json_decode($response->content());

        $this->access_token =$response->headers->get("access_token");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($this->access_token, "Access token empty");
        $this->assertNotEmpty($content->id, "Returned response has not valid content");
        $this->assertNotEmpty($content->name, "Returned response has not valid content");
        $this->assertEquals($content->name, $this->generated_user_model['name'],"Response name is not equals with generated name");
    }

    /**
     * Make test with unknown user(wrong login or pass)
     *
     * @return void
     */
    public function testUnknownUserRequest()
    {
        $response = $this->json('post', '/api/v1/signin', [
            "email" => $this->generated_user_model['email'],
            "password" => $this->generated_user_model['password'] . "just_not_valid_pass",
        ]);

        $content = json_decode($response->content());

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertNotEmpty($content->error, "Returned response has not valid content");
        $this->assertEquals($content->error, "Unauthorized","Response error contain another of 'unauthorized' message");
    }

    /**
     * ! Make test with wrong request structure
     *
     * @return void
     */
    public function testWrongRequest()
    {
        $response = $this->json('post', '/api/v1/signin', [ // ? I just removed password
            "email" => $this->generated_user_model['email'],
        ]);

        $content = json_decode($response->content());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertNotEmpty($content->errors, "Returned response has NOT errors");
        $this->assertNotEmpty($content->errors->password, "Returned response has NOT error that password confirmation does not match");

        $this->assertNotEmpty($content->message, "Returned response has not valid content(must have message field)");

    }
}
