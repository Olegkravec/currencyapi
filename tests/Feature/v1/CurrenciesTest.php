<?php

namespace Tests\Unit\v1;

use App\CurrenciesModel;
use App\Helpers\CurrencyHelper;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Tests\AuthorizedTestCase;
use Tests\TestCase;


class CurrenciesTest extends AuthorizedTestCase
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
     * Test if response of retrieving all currencies contains previously added data
     *
     * @return void
     */
    public function testGetAllCurrencies()
    {
        $response = $this->json('get','/api/v1/currencies');
        $content = json_decode($response->content());

        $this->assertContains($this->testable_from, $content->currencies);
        $this->assertContains($this->testable_to, $content->currencies);

        $response->assertStatus(200);
    }

    /**
     * Test if response of retrieving all currencies contains previously added data
     *
     * @return void
     */
    public function testGetCurrencyPair()
    {
        $response = $this->json('get','/api/v1/currencies/'. $this->testable_pair);
        $content = json_decode($response->content());

        $response->assertStatus(200);
        $this->assertNotEmpty($content->status, "Response structure is not valid");
        $this->assertEquals($content->status, "success", "Response status is not successfully");

        $this->assertNotEmpty($content->pairs, "Response structure is not valid");
        $this->assertNotEmpty($content->pairs[0], "Response has not payload");
        $this->assertNotEmpty($content->pairs[0]->price, "Response payload: missing required field (price)");
        $this->assertTrue($content->pairs[0]->price > 0, "Response payload: required field has not valid data");

    }
}
