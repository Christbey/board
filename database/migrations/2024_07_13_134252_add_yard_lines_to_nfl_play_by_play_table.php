<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // In the migration file
    public function up()
    {
        Schema::table('nfl_play_by_play', function (Blueprint $table) {
            $table->integer('start_yard_line')->nullable();
            $table->integer('end_yard_line')->nullable();
        });
    }

    public function down()
    {
        Schema::table('nfl_play_by_play', function (Blueprint $table) {
            $table->dropColumn(['start_yard_line', 'end_yard_line']);
        });
    }
};
