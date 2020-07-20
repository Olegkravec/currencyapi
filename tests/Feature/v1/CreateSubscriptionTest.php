<?php

namespace Tests\Feature\v1;

use App\Helpers\CurrencyHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\AuthorizedTestCase;
use Tests\TestCase;

class CreateSubscriptionTest extends AuthorizedTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreatePaymentMethodRequest()
    {
        $response = $this->json('get',"/api/v1/subscriptions/create",[ ],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $content = json_decode($response->content());

        $response->assertStatus(200);
        $this->assertNotEmpty($content->payment_link, $response->content());
        $this->assertNotEmpty($content->status, $response->content());
    }

    /**
     * Test payment method with NOT valid request
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function testStorePaymentMethodRequestNotValidRequest()
    {
        {
            // First lets create some payment method
            $this->payment_method = \Stripe\PaymentMethod::create([
                'type' => 'card',
                'card' => [ // Just test data
                    'number' => '4242424242424242',
                    'exp_month' => 7,
                    'exp_year' => 2021,
                    'cvc' => '314',
                ],
            ]);
        }


        $response = $this->json('post',"/api/v1/payments/methods",[
//            "payment_method" => $this->payment_method->id, // NO PAYMENT METHOD, REQUEST SHOULD RECEIVE ERROR
        ],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $content = json_decode($response->content());

        $this->assertNotEmpty($content->errors, $response->content());
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Test of storing payment method with valid request
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function testStorePaymentMethodRequestValidRequest()
    {
        {
            // First lets create some payment method
            $this->payment_method = \Stripe\PaymentMethod::create([
                'type' => 'card',
                'card' => [ // Just test data
                    'number' => '4242424242424242',
                    'exp_month' => 7,
                    'exp_year' => 2021,
                    'cvc' => '314',
                ],
            ]);
        }

        $response = $this->json('post',"/api/v1/payments/methods",[
            "payment_method" => $this->payment_method->id,
        ],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }




    public function testGetSubscriptions()
    {
        $response = $this->json('get',"/api/v1/subscriptions",[ ],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $content = json_decode($response->content());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEmpty($content->subscriptions, $response->content()); // User can not have any subscriptions at the moment
        $this->assertEmpty(NULL, $response->content());

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Create Valid subscription
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function testCreateValidSubscription()
    {
        $selected_stripe_plan_id = ""; // Will contain stripe plan id

        {
            /**
             * For start - lets create payment method. I will just call one of $this methods
             */
            $this->testStorePaymentMethodRequestValidRequest();
        }

        // Receive all plans
        $response = $this->json('get',"/api/v1/subscriptions/plans",[ ],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $content = json_decode($response->content());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($content->plans, "Returned response is not valid");
        $this->assertNotEmpty($content->plans->data, "Reponse doesn't contains any plans");
        $this->assertNotEmpty($content->plans->data[0], "Stripe Plan is empty");
        $this->assertNotEmpty($content->plans->data[0]->id, "Stripe Plan has not valid structure");

        $selected_stripe_plan_id = $content->plans->data[0]->id;


        // Now we will create new subscription
        $response = $this->json('post',"/api/v1/subscriptions",[
                "plan" => $selected_stripe_plan_id // Some stripe plan(basic or premium)
            ],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $content = json_decode($response->content());

        $this->assertNotEmpty($content->status, "Response has not valid structure(no status)");
        $this->assertEquals($content->status, "success" , "Status is not success");
        $this->assertNotEmpty($content->message, "Response has not valid structure(no message)");
        $this->assertNotEmpty($content->message->stripe_plan, "Response has not valid structure(no stripe_plan)");
        $this->assertEquals($content->message->stripe_plan, $selected_stripe_plan_id, "We was subscribed to another plan that we selected before");
    }



    /**
     * Create subscription without payment method
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function testCreateNotValidSubscription()
    {
        $selected_stripe_plan_id = ""; // Will contain stripe plan id

        // Receive all plans
        $response = $this->json('get',"/api/v1/subscriptions/plans",[ ],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $content = json_decode($response->content());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($content->plans, "Returned response is not valid");
        $this->assertNotEmpty($content->plans->data, "Reponse doesn't contains any plans");
        $this->assertNotEmpty($content->plans->data[0], "Stripe Plan is empty");
        $this->assertNotEmpty($content->plans->data[0]->id, "Stripe Plan has not valid structure");

        $selected_stripe_plan_id = $content->plans->data[0]->id;


        // Now we will create new subscription
        $response = $this->json('post',"/api/v1/subscriptions",[
                "plan" => $selected_stripe_plan_id // Some stripe plan(basic or premium)
            ],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $content = json_decode($response->content());

        $this->assertNotEmpty($content->status, "Response has not valid structure(no status)");
        $this->assertEquals($content->status, "error" , "Status is not error");
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }



    /**
     * Create 2X same subscriptions
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function testCreateDuplicateSubscriptions()
    {
        parent::createDefaultPaymentMethod();
        parent::createDefaultSubscription();

        // Receive all plans
        $response = $this->json('delete',"/api/v1/subscriptions/" . $this->subsiption_id,[
            "_method" => "DELETE"
        ],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $content = json_decode($response->content());

        $this->assertNotEmpty($content->status, "Returned response is not valid(no status)");
        $this->assertNotEmpty($content->message, "Returned response is not valid(no message)");
        $this->assertNotEmpty($content->message->id, "Returned response is not valid(no deleted subscription id)");
    }



}
