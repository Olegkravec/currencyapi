<?php

namespace Tests\Feature\v1;

use App\Helpers\CurrencyHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\AuthorizedTestCase;

class ConvertCurrencyTest extends AuthorizedTestCase
{
    private $testable_from = "USD";
    private $testable_to = "UAH";
    private $testable_pair;

    public function setUp(): void
    {
        parent::setUp();
        $this->testable_pair = $this->testable_from . $this->testable_to;

        // WHY :? As database will be fully empty I want to force run job
//        CurrencyHelper::retrieve_pair($this->testable_pair);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testConvertCurrencyWithGoodInputs()
    {
        $response = $this->json('get',"/api/v1/currencies/convert",[
            'from' => $this->testable_from,
            'to' => $this->testable_to,
            'amount' => 12
        ],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $content = json_decode($response->content());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($content->status, "success","Response error contain another of 'unauthorized' message");
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testConvertCurrencyWithWrongInputs()
    {
        $response = $this->json('get',"/api/v1/currencies/convert",[
            'from' => $this->testable_from,
            'to' => $this->testable_to,
//            'amount' => 12
            // here is no amount
        ],[
            "Authorization" => "Bearer {$this->access_token}"
        ]);

        $content = json_decode($response->content());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertNotEmpty($content->errors, "Response must have return errors list");
    }
}
