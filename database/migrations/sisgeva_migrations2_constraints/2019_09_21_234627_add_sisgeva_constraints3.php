<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSisgevaConstraints3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('curso_categorias_cursos', function ($table) {
            $table->foreign('categorias_curso_id')->references('id')->on('categorias_cursos');
            $table->foreign('curso_id')->references('id')->on('cursos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('curso_categorias_cursos', function ($table) {
            $table->dropForeign(['categorias_curso_id']);
            $table->dropForeign(['curso_id']);
        });
    }
}
