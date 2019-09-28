<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeparturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('index');
            $table->string('name');
            $table->integer('hours');
            $table->integer('minutes');
            $table->integer('passage_id')->unsigned();
            $table->foreign('passage_id')->references('id')->on('passages')->onDelete('cascade');
            $table->integer('stop_id')->unsigned();
            $table->foreign('stop_id')->references('id')->on('stops')->onDelete('cascade');
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
        Schema::dropIfExists('departures');
    }
}
