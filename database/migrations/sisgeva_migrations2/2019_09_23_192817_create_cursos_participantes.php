<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCursosParticipantes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursos_participantes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->bigInteger('cvucv_user_id')->unsigned()->nullable();
            $table->bigInteger('cvucv_curso_id')->unsigned()->nullable();
            $table->bigInteger('cvucv_rol_id')->unsigned()->nullable();
            $table->boolean('user_sync')->default(false)->nullable();
            $table->boolean('curso_sync')->default(false)->nullable();
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
        Schema::dropIfExists('cursos_participantes');
    }
}
