<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('anonimo')->default(true);
            $table->longText('respuestas')->nullable();
            $table->float('percentil_eva', 8, 2)->unsigned()->nullable();
            $table->bigInteger('instrumento_id')->unsigned();
            $table->bigInteger('curso_id')->unsigned();
            $table->bigInteger('periodo_lectivo_id')->unsigned();
            $table->bigInteger('momento_evaluacion_id')->unsigned();
            $table->bigInteger('cvucv_user_id')->unsigned()->nullable();
            $table->integer('usuario_id')->unsigned()->nullable();
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
        Schema::dropIfExists('evaluaciones');
    }
}
