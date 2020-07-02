<?php

use Illuminate\Database\Seeder;

class AllowedCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        {
            // Create default allowed currency pair
            $pairs = [
                "EURRUB","EURUSD","EURGBP","EURJPY","EURKZT","EURAED","EURBYN","USDRUB","USDGBP","USDJPY","USDKZT","USDKGS","USDAED","USDUAH","USDTHB","USDBYN","GBPRUB","GBPJPY","GBPAUD","JPYRUB","RUBKZT","RUBAED","BYNRUB","CNYRUB","CNYUSD","CNYEUR","BTCRUB","BTCUSD","BTCEUR","BTCGBP","BTCJPY","BTCBCH","BTCXRP","BCHUSD","BCHRUB","BCHGBP","BCHEUR","BCHJPY","BCHXRP","XRPUSD","XRPRUB","XRPGBP","XRPEUR","XRPJPY","GELUSD","GELRUB","THBEUR","THBRUB","BTGUSD","ETHUSD","ZECUSD","USDVND","USDMYR","RUBAUD","THBCNY","JPYAMD","JPYAZN","IDRUSD","EURTRY","USDAMD","USDILS","RUBNZD","RUBTRY","RUBSGD","RUBUAH","CADRUB","CHFRUB","USDAUD","USDCAD","EURAMD","EURBGN","USDBGN","GBPBYN","RUBAMD","RUBBGN","RUBMYR","MDLEUR","MDLRUB","MDLUSD","ETHRUB","ETHEUR","ETHGBP","ETHJPY","RSDRUB","RSDEUR","RSDUSD","LKRRUB","LKRUSD","LKREUR","MMKRUB","MMKUSD","MMKEUR"
            ];

            foreach ($pairs as $pair){
                $pair_model = new \App\Models\AllowedCurrencyModel();
                $pair_model->pair = $pair;
                $pair_model->save();
            }
        }
    }
}
