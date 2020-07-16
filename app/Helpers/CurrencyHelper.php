<?php


namespace App\Helpers;


use App\CurrenciesModel;
use App\Models\Responses\PairComparingResponseModel;
use GuzzleHttp\Client;

class CurrencyHelper
{

    public static function retrieve_pair($pair){
        $client = new Client();
        $response = $client->request('GET', 'https://currate.ru/api/', [
            'query' => [
                'get' => 'rates',
                'key' => env('CURRATE_API_TOKEN'),
                'pairs' => $pair
            ]
        ]);
        $body = $response->getBody();
        $response_content = $body->getContents();

        $parsed_response = json_decode($response_content);

        if($parsed_response->status !== 200)
            throw new \Exception("CurrateUnsuccessfulRequestException");

        foreach ($parsed_response->data as $pair_key => $pair_value){
            $pair = new CurrenciesModel();
            $pair->pair = $pair_key;
            $pair->price = $pair_value;
            $pair->save();
        }

        return $parsed_response->data;
    }

    public static function comparePair(string $from, string $to) : PairComparingResponseModel{
        $pair = $from.$to;
        $default_pair_model = new PairComparingResponseModel();
        $default_pair_model->from = $from;
        $default_pair_model->to = $to;

        $currency = CurrenciesModel::findOrRetrievePair($pair);

        $default_pair_model->new_price = (float)$currency['new']->{$pair};
        if(!empty($currency['old'])){
            $default_pair_model->old_price = (float)$currency['old']->{$pair};
            if($currency['old']->{$pair} > $currency['new']->{$pair}){
                $default_pair_model->direction = "descending";
                $default_pair_model->difference = "-" . ($currency['old']->{$pair} - $currency['new']->{$pair});
            }
            if($currency['new']->{$pair} > $currency['old']->{$pair}){
                $default_pair_model->direction = "ascending";
                $default_pair_model->difference = "+" . ($currency['new']->{$pair} - $currency['old']->{$pair});
            }
            if($currency['new']->{$pair} == $currency['old']->{$pair})
                $default_pair_model->direction = "same";
        }

        return $default_pair_model;
    }
}