<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConstraints extends Migration
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
            $table->foreign('instrumento_id')->references('id')->on('instrumentos')->onDelete('cascade');
            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');
            $table->foreign('periodo_lectivo_id')->references('id')->on('periodos_lectivos')->onDelete('cascade');
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
            $table->foreign('periodo_lectivo_id')->references('id')->on('periodos_lectivos')->onDelete('cascade');
        });

        Schema::table('periodos_lectivos', function ($table) {
            $table->foreign('momento_evaluacion_activo_id')->references('id')->on('periodos_lectivos_momentos_evaluacion')->onDelete('cascade');
        });

        Schema::table('periodos_lectivos_momentos_evaluacion', function ($table) {
            $table->foreign('periodo_lectivo_id','plme_periodo_lectivo_id_foreign')->references('id')->on('periodos_lectivos')->onDelete('cascade');
            $table->foreign('momento_evaluacion_id','plme_momento_evaluacion_id_foreign')->references('id')->on('momentos_evaluacion')->onDelete('cascade');
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
        
        if (Schema::hasColumn('categorias_instrumentos', 'instrumento_id') && 
            Schema::hasColumn('categorias_instrumentos', 'categoria_id')) {
            Schema::table('categorias_instrumentos', function ($table) {
                $table->dropForeign(['instrumento_id']);
                $table->dropForeign(['categoria_id']);
            });
        }

        if (Schema::hasColumn('categorias_indicadores', 'indicador_id') && 
            Schema::hasColumn('categorias_indicadores', 'categoria_id')) {
            Schema::table('categorias_indicadores', function ($table) {
                $table->dropForeign(['indicador_id']);
                $table->dropForeign(['categoria_id']);
            });
        }

        if (Schema::hasColumn('instrumentos_habilitados', 'instrumento_id') && 
            Schema::hasColumn('instrumentos_habilitados', 'categoria_id')) {
            Schema::table('instrumentos_habilitados', function ($table) {
                $table->dropForeign(['instrumento_id']);
                $table->dropForeign(['categoria_id']);
            });
        }

        if (Schema::hasColumn('evaluaciones', 'instrumento_id') && 
            Schema::hasColumn('evaluaciones', 'curso_id') && 
            Schema::hasColumn('evaluaciones', 'periodo_lectivo_id')) {
            Schema::table('evaluaciones', function ($table) {
                $table->dropForeign(['instrumento_id']);
                $table->dropForeign(['curso_id']);
                $table->dropForeign(['periodo_lectivo_id']);
            });
        }
        
        if (Schema::hasColumn('respuestas', 'indicador_id') && 
            Schema::hasColumn('respuestas', 'categoria_id') && 
            Schema::hasColumn('respuestas', 'evaluacion_id')) {
            Schema::table('respuestas', function ($table) {
                $table->dropForeign(['indicador_id']);
                $table->dropForeign(['categoria_id']);
                $table->dropForeign(['evaluacion_id']);
            });
        }
        
        if (Schema::hasColumn('cursos_participantes', 'cvucv_curso_id') && 
            Schema::hasColumn('cursos_participantes', 'cvucv_rol_id')) {
            Schema::table('cursos_participantes', function ($table) {
                $table->dropForeign(['cvucv_curso_id']);
                $table->dropForeign(['cvucv_rol_id']);
            });
        }

        if (Schema::hasColumn('categorias_cursos', 'periodo_lectivo_id')) {
            Schema::table('categorias_cursos', function ($table) {
                $table->dropForeign(['periodo_lectivo_id']);
            });
        }

        if (Schema::hasColumn('periodos_lectivos', 'momento_evaluacion_activo_id')) {
            Schema::table('periodos_lectivos', function ($table) {
                $table->dropForeign(['momento_evaluacion_activo_id']);
            });
        }

        if (Schema::hasColumn('periodos_lectivos_momentos_evaluacion', 'periodo_lectivo_id') && 
            Schema::hasColumn('periodos_lectivos_momentos_evaluacion', 'momento_evaluacion_id')) {
            Schema::table('periodos_lectivos_momentos_evaluacion', function ($table) {
                $table->dropForeign('plme_periodo_lectivo_id_foreign');
                $table->dropForeign('plme_momento_evaluacion_id_foreign');
            });
        }
        
        if (Schema::hasColumn('invitaciones', 'instrumento_id') && 
            Schema::hasColumn('invitaciones', 'curso_id') && 
            Schema::hasColumn('invitaciones', 'periodo_lectivo_id') && 
            Schema::hasColumn('invitaciones', 'estatus_invitacion_id') && 
            Schema::hasColumn('invitaciones', 'tipo_invitacion_id')) {
            Schema::table('invitaciones', function ($table) {
                $table->dropForeign(['instrumento_id']);
                $table->dropForeign(['curso_id']);
                $table->dropForeign(['periodo_lectivo_id']);
                $table->dropForeign(['estatus_invitacion_id']);
                $table->dropForeign(['tipo_invitacion_id']);
            });
        }
    }
}
