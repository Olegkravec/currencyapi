<?php

namespace Tests\Feature\v1;

use App\Helpers\CurrencyHelper;
use App\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\AuthorizedTestCase;
use Tests\TestCase;

class SignUpTest extends AuthorizedTestCase
{

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    /**
     * Test request with valid request content
     *
     * @return void
     */
    public function testValidRequestData()
    {
        if(!$this->skip_init) {
            $response = $this->json('post', '/api/v1/users', $this->generated_user_model);

            $content = json_decode($response->content());

            $this->assertNotEmpty($content->status, "Returned response has not valid content");
            $this->assertEquals($content->status, "success", "Response status is not successfully");
            $response->assertStatus(201);
        }
    }

    /**
     * Test request with NOT valid request content
     *
     * @return void
     */
    public function testNotValidRequestData()
    {
        // WHY ?: I will unset one of required field for test if response content will be valid
        unset($this->generated_user_model['password_confirmation']);

        $response = $this->json('post', '/api/v1/users', $this->generated_user_model);

        $content = json_decode($response->content());


        $this->assertNotEmpty($content->errors, "Returned response has NOT errors");
        $this->assertNotEmpty($content->errors->password, "Returned response has NOT error that password confirmation does not match");

        $this->assertNotEmpty($content->message, "Returned response has not valid content(must have message field)");

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Test request with duplicate user data
     *
     * @return void
     */
    public function testDuplicateRequestData()
    {
        // WHAT !: I just make 2 similar requests
        $response = $this->json('post', '/api/v1/users', $this->generated_user_model);
        $response = $this->json('post', '/api/v1/users', $this->generated_user_model);

        $content = json_decode($response->content());

        $this->assertNotEmpty($content->status, "Returned response has not valid content (no status)");
        $this->assertEquals($content->status, "error","Response status is not 'error'");

        $this->assertNotEmpty($content->message, "Returned response has not valid content (no message)");
        $this->assertStringContainsString("already exist", $content->message,"Response message is not valid");

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }
}
