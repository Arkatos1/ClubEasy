<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTournamentDatesToDatetime extends Migration
{
    public function up()
    {
        Schema::table('tournament', function (Blueprint $table) {
            $table->datetime('dateIni')->change();
            $table->datetime('dateFin')->change();
            $table->datetime('registerDateLimit')->change();
        });
    }

    public function down()
    {
        Schema::table('tournament', function (Blueprint $table) {
            $table->date('dateIni')->change();
            $table->date('dateFin')->change();
            $table->date('registerDateLimit')->change();
        });
    }
}
