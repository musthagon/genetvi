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

        /*Schema::table('cursos', function ($table) {
            $table->foreign('categoria_id')->references('id')->on('categorias_cursos')->onDelete('cascade');
        });

        Schema::table('cursos_participantes', function ($table) {
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');
            $table->foreign('cursos_participantes_roles_id')->references('id')->on('cursos_participantes_roles')->onDelete('cascade');
        });*/

        Schema::table('evaluaciones', function ($table) {
            $table->foreign('instrumento_id')->references('id')->on('instrumentos');
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('curso_id')->references('id')->on('cursos');
            $table->foreign('periodo_lectivo_id')->references('id')->on('periodos_lectivos');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   
        /*if (Schema::hasTable('categorias_instrumentos')){
            if (Schema::hasColumn('categorias_instrumentos', 'instrumento_id')){
                Schema::table('categorias_instrumentos', function ($table) {
                    $table->dropForeign(['instrumento_id']);
                });
            }
            if (Schema::hasColumn('categorias_instrumentos', 'categoria_id')){
                Schema::table('categorias_instrumentos', function ($table) {
                    $table->dropForeign(['categoria_id']);
                });
            }
        }*/
        
        /*Schema::table('categorias_instrumentos', function ($table) {
            $table->dropForeign(['instrumento_id']);
            $table->dropForeign(['categoria_id']);
        });*/

        /*Schema::table('categorias_indicadores', function ($table) {
            $table->dropForeign(['indicadore_id']);
            $table->dropForeign(['categorias_id']);
        });*/

        /*Schema::table('cursos', function ($table) {
            $table->dropForeign(['categoria_id']);
        });

        Schema::table('cursos_participantes', function ($table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['curso_id']);
            $table->dropForeign(['cursos_participantes_roles_id']);
        });*/

        Schema::table('evaluaciones', function ($table) {
            $table->dropForeign(['instrumento_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['curso_id']);
        });
    }
}
