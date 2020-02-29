<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token', 191)->unique();
            $table->bigInteger('estatus_invitacion_id')->unsigned()->nullable();
            $table->bigInteger('tipo_invitacion_id')->unsigned()->nullable();
            $table->bigInteger('instrumento_id')->unsigned();
            $table->bigInteger('curso_id')->unsigned()->nullable();
            $table->bigInteger('periodo_lectivo_id')->unsigned()->nullable();
            $table->bigInteger('momento_evaluacion_id')->unsigned()->nullable();
            $table->bigInteger('cvucv_user_id')->unsigned()->nullable();
            $table->integer('usuario_id')->unsigned()->nullable();
            $table->integer('cantidad_recordatorios')->default(0)->nullable();
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
        Schema::dropIfExists('invitaciones');
    }
}
