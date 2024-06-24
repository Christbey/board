<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGameTimeToNflWeatherTable extends Migration
{
    public function up()
    {
        Schema::table('nfl_weather', function (Blueprint $table) {
            $table->string('game_time')->after('date');
        });
    }

    public function down()
    {
        Schema::table('nfl_weather', function (Blueprint $table) {
            $table->dropColumn('game_time');
        });
    }
}
