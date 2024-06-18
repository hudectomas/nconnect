<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateGalleriesTableForYears extends Migration
{
    public function up()
    {
        Schema::table('galleries', function (Blueprint $table) {
            // Check if the 'year' column exists before renaming it
            if (Schema::hasColumn('galleries', 'year')) {
                // Rename old 'year' column to 'year_old'
                $table->renameColumn('year', 'year_old');
            }

            // Check if the 'years' column already exists before adding it
            if (!Schema::hasColumn('galleries', 'years')) {
                // Add the new 'years' column as a JSON field
                $table->json('years')->nullable();
            }
        });

        // Transfer data from 'year_old' to 'years'
        DB::table('galleries')->get()->each(function ($gallery) {
            if (!is_null($gallery->year_old)) {
                DB::table('galleries')
                    ->where('id', $gallery->id)
                    ->update(['years' => json_encode([$gallery->year_old])]);
            }
        });

        // Drop the 'year_old' column if it exists
        Schema::table('galleries', function (Blueprint $table) {
            if (Schema::hasColumn('galleries', 'year_old')) {
                $table->dropColumn('year_old');
            }
        });
    }

    public function down()
    {
        Schema::table('galleries', function (Blueprint $table) {
            // Add the 'year' column back if it doesn't exist
            if (!Schema::hasColumn('galleries', 'year')) {
                $table->integer('year')->nullable();
            }

            // Drop the 'years' column if it exists
            if (Schema::hasColumn('galleries', 'years')) {
                $table->dropColumn('years');
            }
        });

        // Transfer data from 'years' back to 'year'
        DB::table('galleries')->get()->each(function ($gallery) {
            if (!is_null($gallery->years)) {
                $yearsArray = json_decode($gallery->years, true);
                $year = $yearsArray[0] ?? null;

                DB::table('galleries')
                    ->where('id', $gallery->id)
                    ->update(['year' => $year]);
            }
        });
    }
}
