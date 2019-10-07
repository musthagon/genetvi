<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstrumentosCursosParticipantesRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instrumentos_cursos_participantes_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('curso_participante_rol_id')->unsigned();
            $table->bigInteger('instrumento_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('instrumentos_cursos_participantes_roles');
    }
}
