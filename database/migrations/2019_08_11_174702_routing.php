<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Routing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('routing', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->text('proxy');
            $table->text('direct');
            $table->text('block');
            $table->text('port');
            $table->string('network', 10);
            $table->text('protocol');
            $table->text('servers');
            $table->boolean('enable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
    }
}
