<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriasInstrumentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias_instrumentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('valor_porcentual', 8, 2)->unsigned()->default(0);
            $table->bigInteger('categoria_id')->unsigned();
            $table->bigInteger('instrumento_id')->unsigned();
            $table->longText('opciones')->nullable();
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
