<?php

namespace App\Http\Controllers\API;

use App\CurrenciesModel;
use App\Helpers\CurrencyHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\ConvertCurrencyAPIRequest;
use App\Http\Requests\API\GetPairAPIRequest;
use Illuminate\Http\Request;

class CurrenciesController extends Controller
{
    public function getAll(){
        $pairs = CurrenciesModel::groupBy("pair")->get("pair")->toArray();
        $prepared_pairs_array = [];

        // Extract currency pairs from non understandable array to useful array
        array_walk($pairs, function ($key, $value) use (&$prepared_pairs_array){
            $prepared_pairs_array[] = $key['pair'];
        });
        return response([
            'status' => "success",
            "pairs" => $prepared_pairs_array
        ]);
    }

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
}
