<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdminChatingMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("rooms", function (Blueprint $table) {
            $table->unsignedBigInteger('id',true);
            $table->string('name')->nullable();
            $table->tinyInteger('isGroup')->default('0');
            $table->timestamps();
        });
        Schema::create("room_members", function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('room_id')
                ->references('id')
                ->on("rooms")
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on("users")
                ->onDelete('cascade');

        });
        Schema::create("messages", function (Blueprint $table) {
            $table->unsignedBigInteger('id',true);
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('user_id');
            $table->longText("message")->nullable(); // nullable in case where message will contain attachments only
            $table->longText("attachment_url")->nullable(); // nullable in case where message will contain message only
            $table->timestamps();

            $table->foreign('room_id')
                ->references('id')
                ->on("rooms")
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on("users")
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("messages");
        Schema::drop("room_members");
        Schema::drop("rooms");
    }
}
