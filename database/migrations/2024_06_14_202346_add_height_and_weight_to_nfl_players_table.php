<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeightAndWeightToNflPlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nfl_players', function (Blueprint $table) {
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nfl_players', function (Blueprint $table) {
            $table->dropColumn(['height', 'weight']);
        });
    }
}
