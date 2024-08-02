<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnTeamsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_teams', function (Blueprint $table) {
            $table->id('team_id')->unsignedBigInteger();
            $table->string('uid')->nullable();
            $table->string('slug')->nullable();
            $table->string('abbreviation')->nullable();
            $table->string('display_name')->nullable();
            $table->string('short_display_name')->nullable();
            $table->string('name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('location')->nullable();
            $table->string('color')->nullable();
            $table->string('alternate_color')->nullable();
            $table->boolean('is_active')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_teams');
    }
}
