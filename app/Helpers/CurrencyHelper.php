<?php


namespace App\Helpers;


use App\CurrenciesModel;
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
}