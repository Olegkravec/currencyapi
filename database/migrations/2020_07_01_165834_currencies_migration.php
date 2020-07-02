<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CurrenciesMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("currencies", function (Blueprint $table) {
            $table->unsignedBigInteger('id',true);
            $table->string('pair');
            $table->unsignedDouble('price');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->index('pair');
        });
        Schema::create("allowed_currency_pair", function (Blueprint $table) {
            $table->unsignedBigInteger('id',true);
            $table->string('pair');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("currencies");
        Schema::drop("allowed_currency_pair");
    }
}
