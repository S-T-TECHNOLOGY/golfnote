<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScoreSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_summaries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->integer('total_round');
            $table->integer('total_course');
            $table->integer('total_partner');
            $table->integer('high_score');
            $table->integer('total_score');
            $table->integer('total_hio');
            $table->integer('total_fail');
            $table->integer('total_punish');
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
        Schema::dropIfExists('score_summaries');
    }
}
