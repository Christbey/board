<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnSplitsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_splits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('athlete_id');
            $table->string('split_category');
            $table->string('split_type');
            $table->string('display_name');

            // Add dynamic columns for the stats
            $table->string('TOT')->nullable();
            $table->string('SOLO')->nullable();
            $table->string('AST')->nullable();
            $table->string('SACK')->nullable();
            $table->string('STF')->nullable();
            $table->string('STFYDS')->nullable();
            $table->string('FF')->nullable();
            $table->string('FR')->nullable();
            $table->string('KB')->nullable();
            $table->string('INT')->nullable();
            $table->string('YDS')->nullable();
            $table->string('AVG')->nullable();
            $table->string('TD')->nullable();
            $table->string('LNG')->nullable();
            $table->string('PD')->nullable();

            $table->timestamps();

            $table->foreign('athlete_id')->references('athlete_id')->on('nfl_espn_athletes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_splits');
    }
}
