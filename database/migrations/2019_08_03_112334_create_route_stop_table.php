<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteStopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_stop', function (Blueprint $table) {
            $table->integer('route_id')->unsigned()->nullable();
            $table->foreign('route_id')->references('id')
                ->on('routes')->onDelete('cascade');

            $table->integer('stop_id')->unsigned()->nullable();
            $table->foreign('stop_id')->references('id')
                ->on('stops')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_stop');
    }
}
