<?php

namespace Tests\Feature\v1;

use App\Helpers\CurrencyHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use RonasIT\Support\Traits\MigrationTrait;
use Tests\AuthorizedTestCase;
use Tests\TestCase;

class CurrencyComparingTest extends AuthorizedTestCase
{
    private $testable_from = "USD";
    private $testable_to = "UAH";
    private $testable_pair;

    public function setUp(): void
    {
        parent::setUp();
        $this->testable_pair = $this->testable_from . $this->testable_to;

        // WHY :? As database will be fully empty I want to force run job
        CurrencyHelper::retrieve_pair($this->testable_pair);
    }

    /**
     * Test should be failed as subscriptions missing
     */
    public function testGetPairHistoryWithoutSubscription()
    {
        $response = $this->json('get',"/api/v1/currencies/{$this->testable_pair}/compare",[],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $response->assertStatus(403);
    }

    public function testGetPairHistoryWithSubscription()
    {
        if(empty($this->payment_method)){
            parent::createDefaultPaymentMethod();
            parent::createDefaultSubscription();
        }

        $response = $this->json('get',"/api/v1/currencies/{$this->testable_from}/compare",[
            'compare_to' => $this->testable_to . ",RUB"
        ],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $content = json_decode($response->content());

        $response->assertStatus(200);
        $this->assertNotEmpty($content->status, "Response structure is not valid");
        $this->assertEquals($content->status, "success", "Response status is not 'success'");

        $this->assertEquals($content->data->main_currency, $this->testable_from, "Main response currency not valid");
    }
}
