<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnAtsRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_ats_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedInteger('season');
            $table->unsignedInteger('type_id');
            $table->string('type_name');
            $table->string('type_description');
            $table->unsignedInteger('wins');
            $table->unsignedInteger('losses');
            $table->unsignedInteger('pushes');
            $table->timestamps();

            $table->foreign('team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_ats_records');
    }
}
