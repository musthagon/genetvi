<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSisgevaConstraints1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categorias_instrumentos', function ($table) {
            $table->foreign('padre_id')->references('id')->on('categorias_instrumentos');
            $table->foreign('instrumento_id')->references('id')->on('instrumentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categorias_instrumentos', function ($table) {
            $table->dropForeign(['padre_id']);
            $table->dropForeign(['instrumento_id']);
        });
    }
}
