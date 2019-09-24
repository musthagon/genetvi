<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSisgevaConstraints2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('indicadores', function ($table) {
            $table->foreign('categorias_instrumento_id')->references('id')->on('categorias_instrumentos');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('indicadores', function ($table) {
            $table->dropForeign(['categorias_instrumento_id']);
        });
    }
}
