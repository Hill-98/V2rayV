<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Server extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('servers', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->bigInteger('subscribe_id');
            $table->string('address', 50);
            $table->integer('port');
            $table->string('protocol', 20);
            $table->text('protocol_setting');
            $table->string('network', 20);
            $table->text('network_setting');
            $table->string('security', 10);
            $table->string('security_setting', 10);
            $table->text('mux');
            $table->integer('local_port');
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
