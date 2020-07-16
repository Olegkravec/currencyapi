<?php

namespace Tests;

use App\Services\CustomSwaggerService;
use App\User;
use Faker\Factory;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Laravel\Cashier\PaymentMethod;
use Mockery;
use RonasIT\Support\AutoDoc\Http\Middleware\AutoDocMiddleware;
use RonasIT\Support\AutoDoc\Services\SwaggerService;
use RonasIT\Support\AutoDoc\Tests\AutoDocTestCase;
use Stripe\StripeClient;

abstract class AuthorizedTestCase extends TestCase
{
    use CreatesApplication;

    protected $skip_init = false;
    protected $docService;
    protected $generated_user_model = [];
    protected $generated_password = "";
    protected $authorized_user;
    protected $payment_method;
    protected $faker;
    protected $access_token;

    public function setUp(): void
    {
        parent::setUp();
        if(!$this->skip_init){
            $this->skip_init = true;
            \Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));
//            Artisan::call('migrate');
//            Artisan::call('db:seed');

            $this->faker = Factory::create();

            $this->generated_user_model['name'] = $this->faker->name;
            $this->generated_user_model['email'] = $this->faker->unique()->safeEmail;
            $this->generated_password = $this->faker->password;
            $this->generated_user_model['password'] = $this->generated_password;
            $this->generated_user_model['password_confirmation'] = $this->generated_password;
            {
                /**
                 * Create default user data
                 */
                $this->createDefaultUser();
                $this->loginDefaultUser();
            }
        }
    }


    public function createDefaultUser(){
        $response = $this->json('post', '/api/v1/users', $this->generated_user_model);

        $content = json_decode($response->content());
        $this->assertNotEmpty($content->status, "Returned response has not valid content");
        $this->assertEquals($content->status, "success","Response status is not successfully");
        $response->assertStatus(201);
    }

    /**
     * Login and retrieve token
     * @return string - JWT Access token
     */
    public function loginDefaultUser()
    {
        $response = $this->json('post', '/api/v1/signin', [
            "email" => $this->generated_user_model['email'],
            "password" => $this->generated_user_model['password'],
        ]);
        $content = json_decode($response->content(), true);
        $this->access_token = $response->headers->get("access-token") ?? null;

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($this->access_token, "Access token empty");

        $this->authorized_user = User::find($content['id']);
    }

    public function createDefaultPaymentMethod(){
        $this->assertNotEmpty($this->authorized_user, "Authorized user doesn't exist, but required");

        /**
         * Create payment method
         */
        $this->payment_method = \Stripe\PaymentMethod::create([
            'type' => 'card',
            'card' => [ // Test data
                'number' => '4242424242424242',
                'exp_month' => 7,
                'exp_year' => 2021,
                'cvc' => '314',
            ],
        ]);


        /**
         * Attach payment method to already created user
         */
        $stripe = new StripeClient(env("STRIPE_SECRET"));

        $stripe->paymentMethods->attach(
            $this->payment_method->id,
            ['customer' => $this->authorized_user->stripe_id]
        );
        $this->authorized_user->updateDefaultPaymentMethod( $this->payment_method->id );
    }

    public function createDefaultSubscription(){
        $this->assertNotEmpty($this->authorized_user, "Authorized user doesn't exist, but required");
        $result = $this->authorized_user->newSubscription("premium", env("STRIPE_PREMIUM_PLAN_ID"))->create($this->payment_method->id);
    }
}