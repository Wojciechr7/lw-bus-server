<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDayPassageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_passage', function (Blueprint $table) {
            $table->integer('day_id')->unsigned()->nullable();
            $table->foreign('day_id')->references('id')
                ->on('days')->onDelete('cascade');

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
        Schema::dropIfExists('day_passage');
    }
}
