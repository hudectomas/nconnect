<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGalleriesTableForYears extends Migration
{
    public function up()
    {
        Schema::table('galleries', function (Blueprint $table) {
            // Vytvorenie nového stĺpca `years` ako JSON pole
            $table->json('years')->nullable();
            // Premenovanie starého stĺpca `year` na `year_old` dočasne, aby sme nestratili dáta
            $table->renameColumn('year', 'year_old');
        });

        // Prenos údajov zo starého stĺpca `year` do nového stĺpca `years`
        DB::table('galleries')->get()->each(function ($gallery) {
            DB::table('galleries')
                ->where('id', $gallery->id)
                ->update(['years' => json_encode([$gallery->year_old])]);
        });

        // Odstránenie starého stĺpca `year_old`
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn('year_old');
        });
    }

    public function down()
    {
        Schema::table('galleries', function (Blueprint $table) {
            // Obnovenie stĺpca `year` a odstránenie stĺpca `years`
            $table->integer('year')->nullable();
            $table->dropColumn('years');
        });
    }
}
