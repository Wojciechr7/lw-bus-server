<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateStopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_stop', function (Blueprint $table) {
            $table->integer('order');
            $table->integer('template_id')->unsigned()->nullable();
            $table->foreign('template_id')->references('id')
                ->on('templates')->onDelete('cascade');
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
        Schema::dropIfExists('template_stop');
    }
}
