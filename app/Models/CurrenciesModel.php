<?php

namespace App;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Model;

class CurrenciesModel extends Model
{
    public $timestamps = false;
    protected $table = "currencies";

    public static function findOrRetrievePair($pair){
        $pair_local = self::where("pair", $pair)->orderBy("created_at", "DESC")->first("price AS $pair");
        $pair_currate = CurrencyHelper::retrieve_pair($pair);

        {
            // Save last data to DB
            $currency = new CurrenciesModel();
            $currency->pair = $pair;
            $currency->price = $pair_currate->{$pair};
            $currency->save();
        }

        return [
            "old" => $pair_local,
            "new" => $pair_currate
        ];
    }
}
