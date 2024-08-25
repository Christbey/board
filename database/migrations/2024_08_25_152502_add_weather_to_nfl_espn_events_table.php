<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeatherToNflEspnEventsTable extends Migration
{
    public function up()
    {
        Schema::table('nfl_espn_events', function (Blueprint $table) {
            $table->string('weather_type')->nullable();
            $table->string('weather_display_value')->nullable();
            $table->string('weather_zip_code')->nullable();
            $table->timestamp('weather_last_updated')->nullable();
            $table->integer('weather_wind_speed')->nullable();
            $table->string('weather_wind_direction')->nullable();
            $table->integer('weather_temperature')->nullable();
            $table->integer('weather_high_temperature')->nullable();
            $table->integer('weather_low_temperature')->nullable();
            $table->integer('weather_condition_id')->nullable();
            $table->integer('weather_gust')->nullable();
            $table->integer('weather_precipitation')->nullable();
            $table->string('weather_link')->nullable();
        });
    }

    public function down()
    {
        Schema::table('nfl_espn_events', function (Blueprint $table) {
            $table->dropColumn([
                'weather_type',
                'weather_display_value',
                'weather_zip_code',
                'weather_last_updated',
                'weather_wind_speed',
                'weather_wind_direction',
                'weather_temperature',
                'weather_high_temperature',
                'weather_low_temperature',
                'weather_condition_id',
                'weather_gust',
                'weather_precipitation',
                'weather_link',
            ]);
        });
    }
}
