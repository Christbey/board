<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventIdToNflEspnAthleteSplitsTable extends Migration
{
    public function up()
    {
        Schema::table('nfl_espn_athlete_splits', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->nullable()->after('athlete_id');
        });
    }

    public function down()
    {
        Schema::table('nfl_espn_athlete_splits', function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
    }
}
