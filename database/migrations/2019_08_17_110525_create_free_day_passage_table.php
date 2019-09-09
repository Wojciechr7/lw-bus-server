<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFreeDayPassageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('free_day_passage', function (Blueprint $table) {
            $table->integer('free_day_id')->unsigned()->nullable();
            $table->foreign('free_day_id')->references('id')
                ->on('free_days')->onDelete('cascade');

            $table->integer('passage_id')->unsigned()->nullable();
            $table->foreign('passage_id')->references('id')
                ->on('passages')->onDelete('cascade');
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
        Schema::dropIfExists('free_day_passage');
    }
}
