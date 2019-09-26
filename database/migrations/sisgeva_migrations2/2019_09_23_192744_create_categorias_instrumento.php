<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriasInstrumento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias_instrumentos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->longText('opciones')->nullable();
            $table->integer('orden')->nullable();
            $table->integer('padre_id')->unsigned()->nullable();
            $table->integer('instrumento_id')->unsigned();
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
        Schema::dropIfExists('categorias_instrumentos');
    }
}
