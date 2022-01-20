<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnScoreSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('score_summaries', function (Blueprint $table) {
            $table->float('avg_score',8, 2);
            $table->dropColumn('total_course');
            $table->renameColumn('total_score', 'last_score');
            $table->float('set_error',8, 2);
            $table->float('punish',8, 2);
            $table->dropColumn('total_fail');
            $table->dropColumn('total_punish');
            $table->float('handicap_score',8, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
