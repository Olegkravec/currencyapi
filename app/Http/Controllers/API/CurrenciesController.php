<?php

namespace App\Http\Controllers\API;

use App\CurrenciesModel;
use App\Helpers\CurrencyHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\CompareCurrencyRequest;
use App\Http\Requests\API\ConvertCurrencyAPIRequest;
use App\Http\Requests\API\GetPairAPIRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Subscription;

class CurrenciesController extends Controller
{
    /**
     * Build array of all currencies that is present in DB
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function getAll(){
        $pairs = CurrenciesModel::groupBy("pair")->get("pair")->toArray();
        $prepared_pairs_array = [];
        $prepared_currencies_array = [];

        // Extract currency pairs from non understandable array to useful array
        array_walk($pairs, function ($key, $value) use (&$prepared_pairs_array, &$prepared_currencies_array){
            $prepared_pairs_array[] = $key['pair'];

            $unpaired = str_split($key['pair'], 3); // Split by 3 chars
            if(empty($prepared_currencies_array[$unpaired[0]]))
                $prepared_currencies_array[] = $unpaired[0];

            if(empty($prepared_currencies_array[$unpaired[1]]))
                $prepared_currencies_array[] = $unpaired[1];

        });
        return response([
            'status' => "success",
            "currencies" => $prepared_currencies_array,
            "pairs" => $prepared_pairs_array
        ]);
    }

    /**
     * Retrieve selected pair
     *
     * @param GetPairAPIRequest $request
     * @param $pair
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function getPair(GetPairAPIRequest $request, $pair){
        $pair_model = CurrenciesModel::where("pair", $pair);

        if(!empty($request->validated()['limit']))
            $pair_model = $pair_model->limit($request->validated()['limit']);

        $pairs = $pair_model->get();
        return response([
            'status' => "success",
            "pairs" => $pairs
        ]);
    }

    /**
     * Convert currency into another currency
     *
     * @param ConvertCurrencyAPIRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function convertPair(ConvertCurrencyAPIRequest $request){
        $pair = $request->validated()['from'] . $request->validated()['to'];
        $pair_model = CurrenciesModel::where("pair", $pair)->orderBy("created_at", "DESC")->first();
        $pair_price = 0.0;

        if(empty($pair_model)){
            $retrieved = CurrencyHelper::retrieve_pair($pair);
            if(empty($retrieved[$pair])){
                return response([
                    'status' => "error",
                    'message' => "Unknown currency pair",
                    "converted" => 0
                ]);
            }
            $pair_price = $retrieved[$pair];
        }else{
            $pair_price = $pair_model->price;
        }

        return response([
            'status' => "success",
            "converted" => $pair_price*$request->validated()['amount']
        ]);
    }


    /**
     * Get history of selected currency pair
     *
     * @param $pair
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function getPairHistory($pair){
        $user = Auth::guard("api")->user();

        // Check if has any subscription
        $subs = Subscription::where("stripe_status", "active")->where("user_id", $user->id)->first();
        if(empty($subs)){
            return response([
                'status' => "error",
                'message' => "Active subscription not found",
            ]);
        }
        $history = CurrenciesModel::where("pair", $pair)->jsonPaginate();
        return response([
            "status" => 'success',
            'history' => $history
        ]);
    }

    /**
     * Returns response model with comparations data about main and comparable currency pairs.
     *
     * @param CompareCurrencyRequest $request
     * @param $main_currency
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getPairsComparing(CompareCurrencyRequest $request, $main_currency){
        /**
         * TODO: REFACTOR THIS
         */
        $currencies = $request->validated()['compare_to'];
        if(strpos($currencies, ",") === false) // Because request should be like "?compare_to=USD,EUR"
            return response([
                "status" => 'error',
                'message' => "Bad request. You should explode currencies by ','"
            ]);

        $currencies = explode(",", $currencies);

        $response_model = [
            "main_currency" => $main_currency,
            "compares" => []
        ];

        foreach ($currencies as $currency){
            $pair = $main_currency.$currency;
            $default_pair_model = [
                "direction" => "same",
                "difference" => 0.0,
                "old_price" => 0.0,
                "new_price" => 0.0,
                "from" => $main_currency,
                "to" => $currency,
            ];
            $currency = CurrenciesModel::findOrRetrievePair($pair);

            $default_pair_model['new_price'] = (float)$currency['new']->{$pair};
            if(!empty($currency['old'])){
                $default_pair_model['old_price'] = (float)$currency['old']->{$pair};
                if($currency['old']->{$pair} > $currency['new']->{$pair}){
                    $default_pair_model['direction'] = "descending";
                    $default_pair_model['difference'] = "-" . ($currency['old']->{$pair} - $currency['new']->{$pair});
                }
                if($currency['new']->{$pair} > $currency['old']->{$pair}){
                    $default_pair_model['direction'] = "ascending";
                    $default_pair_model['difference'] = "+" . ($currency['new']->{$pair} - $currency['old']->{$pair});
                }
            }
            $response_model["compares"][$pair] = $default_pair_model;
        }

        return response([
            "status" => 'success',
            'data' => $response_model
        ]);
    }
}
