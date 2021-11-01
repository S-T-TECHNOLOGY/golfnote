<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('image');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('quantity');
            $table->integer('quantity_remain');
            $table->integer('join_fee');
            $table->string('host');
            $table->string('organizational_unit');
            $table->integer('caddie_fee')->default(0);
            $table->integer('green_fee')->default(0);
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
        Schema::dropIfExists('events');
    }
}
