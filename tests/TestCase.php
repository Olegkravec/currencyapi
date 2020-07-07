<?php

namespace Tests;

use App\Services\CustomSwaggerService;
use Faker\Factory;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use RonasIT\Support\AutoDoc\Http\Middleware\AutoDocMiddleware;
use RonasIT\Support\AutoDoc\Services\SwaggerService;
use RonasIT\Support\AutoDoc\Tests\AutoDocTestCase;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    protected $docService;

    public function setUp(): void
    {
        parent::setUp();
        $this->docService = app(CustomSwaggerService::class); // I want to override the service


    }



    public function tearDown(): void
    {
        $currentTestCount = $this->getTestResultObject()->count();
        $allTestCount = $this->getTestResultObject()->topTestSuite()->count();

        if (($currentTestCount == $allTestCount) && (!$this->hasFailed())) {
            $this->docService->saveProductionData();
        }

        parent::tearDown();
    }

    /**
     * Disabling documentation collecting on current test
     */
    public function skipDocumentationCollecting()
    {
        AutoDocMiddleware::$skipped = true;
    }
}