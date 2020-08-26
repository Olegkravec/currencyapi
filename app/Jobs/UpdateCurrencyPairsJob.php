<?php

namespace App\Jobs;

use App\CurrenciesModel;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCurrencyPairsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $pair;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $pair)
    {
        $this->pair = $pair;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://currate.ru/api/', [
            'query' => [
                'get' => 'rates',
                'key' => env('CURRATE_API_TOKEN'),
                'pairs' => $this->pair
            ]
        ]);
        $body = $response->getBody();
        $response_content = $body->getContents();

        $parsed_response = json_decode($response_content);
        if($parsed_response->status !== 200)
            throw new \Exception("CurrateRequestUnsuccessfullException");

        foreach ($parsed_response->data as $pair_key => $pair_value){
            echo "\nPair $pair_key has\t$pair_value";
            $pair = new CurrenciesModel();
            $pair->pair = $pair_key;
            $pair->price = $pair_value;
            $pair->save();
        }
    }

    public function failed(\Exception $exception){}
}
