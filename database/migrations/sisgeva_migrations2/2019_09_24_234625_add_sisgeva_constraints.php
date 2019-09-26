<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSisgevaConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categorias_instrumentos', function ($table) {
            $table->foreign('instrumento_id')->references('id')->on('instrumentos')->onDelete('cascade');
        });

        Schema::table('indicadores', function ($table) {
            $table->foreign('categorias_instrumento_id')->references('id')->on('categorias_instrumentos')->onDelete('cascade');
        });

        Schema::table('cursos', function ($table) {
            $table->foreign('categoria_id')->references('id')->on('categorias_cursos')->onDelete('cascade');
        });

        Schema::table('curso_periodos_lectivos', function ($table) {
            $table->foreign('periodos_lectivo_id')->references('id')->on('periodos_lectivos')->onDelete('cascade');
            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');
        });

        Schema::table('evaluaciones', function ($table) {
            $table->foreign('instrumento_id')->references('id')->on('instrumentos')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');
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
            $table->dropForeign(['instrumento_id']);
        });

        Schema::table('indicadores', function ($table) {
            $table->dropForeign(['categorias_instrumento_id']);
        });

        Schema::table('cursos', function ($table) {
            $table->dropForeign(['categoria_id']);
        });

        Schema::table('curso_periodos_lectivos', function ($table) {
            $table->dropForeign(['periodos_lectivo_id']);
            $table->dropForeign(['curso_id']);
        });

        Schema::table('evaluaciones', function ($table) {
            $table->dropForeign(['instrumento_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['curso_id']);
        });
    }
}
