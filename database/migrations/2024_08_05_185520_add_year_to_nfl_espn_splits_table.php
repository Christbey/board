<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearToNflEspnSplitsTable extends Migration
{
    public function up()
    {
        Schema::table('nfl_espn_splits', function (Blueprint $table) {
            $table->integer('year')->after('display_name'); // Add the year column
        });
    }

    public function down()
    {
        Schema::table('nfl_espn_splits', function (Blueprint $table) {
            $table->dropColumn('year'); // Drop the year column if the migration is rolled back
        });
    }
}
