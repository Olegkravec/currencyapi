<?php

namespace App\Console\Commands;

use App\CurrenciesModel;
use App\Jobs\UpdateCurrencyPairsJob;
use App\Models\AllowedCurrencyModel;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateCurrencyListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $allowed_pairs = AllowedCurrencyModel::all("pair")->keyBy('pair')->toArray();
        $prepared_pairs_array = [];

        // Extract currency pairs from non understandable array to useful array
        array_walk($allowed_pairs, function ($key, $value) use (&$prepared_pairs_array){
            $prepared_pairs_array[] = $key['pair'];
        });
        $prepared_pairs_string = implode(",", $prepared_pairs_array);


        $job = new UpdateCurrencyPairsJob($prepared_pairs_string);
        dispatch($job)->onConnection("redis")->onQueue('currencies');
    }
}
