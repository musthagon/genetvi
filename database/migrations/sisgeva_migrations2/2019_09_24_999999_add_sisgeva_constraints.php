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
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
        });

        Schema::table('categorias_indicadores', function ($table) {
            $table->foreign('indicador_id')->references('id')->on('indicadores')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
        });

        Schema::table('instrumentos_habilitados', function ($table) {
            $table->foreign('instrumento_id')->references('id')->on('instrumentos')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('categorias_cursos')->onDelete('cascade');
        });

        Schema::table('evaluaciones', function ($table) {
            $table->foreign('instrumento_id')->references('id')->on('instrumentos');
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('curso_id')->references('id')->on('cursos');
            $table->foreign('periodo_lectivo_id')->references('id')->on('periodos_lectivos');
        });

        Schema::table('respuestas', function ($table) {
            $table->foreign('indicador_id')->references('id')->on('indicadores')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
            $table->foreign('evaluacion_id')->references('id')->on('evaluaciones')->onDelete('cascade');
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
            $table->dropForeign(['categoria_id']);
        });

        Schema::table('categorias_indicadores', function ($table) {
            $table->dropForeign(['indicador_id']);
            $table->dropForeign(['categoria_id']);
        });

        Schema::table('instrumentos_habilitados', function ($table) {
            $table->dropForeign(['instrumento_id']);
            $table->dropForeign(['categoria_id']);
        });

        Schema::table('evaluaciones', function ($table) {
            $table->dropForeign(['instrumento_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['curso_id']);
            $table->dropForeign(['periodo_lectivo_id']);
        });

        Schema::table('respuestas', function ($table) {
            $table->dropForeign(['indicador_id']);
            $table->dropForeign(['categoria_id']);
            $table->dropForeign(['evaluacion_id']);
        });
    }
}
