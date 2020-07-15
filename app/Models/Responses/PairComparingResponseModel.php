<?php


namespace App\Models\Responses;


class PairComparingResponseModel
{
    // @property string
    public $direction = "unknown";
    // @property float|string
    public $difference = 0.0;
    // @property float
    public $old_price = 0.0;
    // @property float
    public $new_price = 0.0;
    // @property string
    public $from = "UNK";
    // @property string
    public $to = "UNK";
}