<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('championship', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tournament_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tournament_id')->references('id')->on('tournament')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('championship');
    }
};
