<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowedCurrencyModel extends Model
{
    public $timestamps = false;
    protected $table = "allowed_currency_pair";
}
