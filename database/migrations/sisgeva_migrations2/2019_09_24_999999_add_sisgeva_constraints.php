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
            $table->foreign('curso_id')->references('id')->on('cursos');
            $table->foreign('periodo_lectivo_id')->references('id')->on('periodos_lectivos');
        });

        Schema::table('respuestas', function ($table) {
            $table->foreign('indicador_id')->references('id')->on('indicadores')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
            $table->foreign('evaluacion_id')->references('id')->on('evaluaciones')->onDelete('cascade');
        });

        Schema::table('cursos_participantes', function ($table) {
            $table->foreign('cvucv_curso_id')->references('id')->on('cursos')->onDelete('cascade');
            $table->foreign('cvucv_rol_id')->references('id')->on('cursos_participantes_roles')->onDelete('cascade');
        });

        Schema::table('categorias_cursos', function ($table) {
            $table->foreign('periodo_lectivo')->references('id')->on('periodos_lectivos');
        });

        Schema::table('invitaciones', function ($table) {
            $table->foreign('instrumento_id')->references('id')->on('instrumentos');
            $table->foreign('curso_id')->references('id')->on('cursos');
            $table->foreign('periodo_lectivo_id')->references('id')->on('periodos_lectivos');
            $table->foreign('estatus_invitacion_id')->references('id')->on('estatus_invitaciones');
            $table->foreign('tipo_invitacion_id')->references('id')->on('tipo_invitaciones');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {           
        
        /*Schema::table('categorias_instrumentos', function ($table) {
            $table->dropForeign('categorias_instrumentos_instrumento_id_foreign');
            $table->dropForeign('categorias_instrumentos_categoria_id_foreign');
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
            $table->dropForeign(['curso_id']);
            $table->dropForeign(['periodo_lectivo_id']);
        });*/


        Schema::table('respuestas', function ($table) {
            $table->dropForeign(['indicador_id']);
            $table->dropForeign(['categoria_id']);
            $table->dropForeign(['evaluacion_id']);
        });

        Schema::table('cursos_participantes', function ($table) {
            $table->dropForeign(['cvucv_curso_id']);
            $table->dropForeign(['cvucv_rol_id']);
        });

        Schema::table('categorias_cursos', function ($table) {
            $table->dropForeign(['periodo_lectivo']);
        });

        Schema::table('invitaciones', function ($table) {
            $table->dropForeign(['instrumento_id']);
            $table->dropForeign(['curso_id']);
            $table->dropForeign(['periodo_lectivo_id']);
            $table->dropForeign(['estatus_invitacion_id']);
            $table->dropForeign(['tipo_invitacion_id']);
        });
    }
}
