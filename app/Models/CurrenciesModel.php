<?php

namespace App;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Model;

class CurrenciesModel extends Model
{
    public $timestamps = false;
    protected $table = "currencies";

    public static function findOrRetrievePair($pair){
        $pair_local = self::where("pair", $pair)
            ->whereRaw("created_at > NOW() - INTERVAL 1 HOUR")
            ->orderBy("created_at", "DESC")
            ->first("price AS $pair");
        $pair_currate = CurrencyHelper::retrieve_pair($pair);

        {
            // Save last data to DB
            $currency = new CurrenciesModel();
            $currency->pair = $pair;
            $currency->price = $pair_currate->{$pair};
            $currency->save();
        }

        if(empty($pair_local->{$pair}))
            $pair_local = $pair_currate;

        return [
            "old" => $pair_local,
            "new" => $pair_currate
        ];
    }
}
