<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Subscribe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("subscribe", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->text("name");
            $table->text("url");
            $table->text("mux");
            $table->text("password")->nullable(true);
            $table->boolean("auto_update");
            $table->boolean("proxy_update");
            $table->boolean("last_success");
            $table->dateTime("update_at")->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
