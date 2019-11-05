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
            $table->float('valor_porcentual', 8, 2)->unsigned();
            $table->bigInteger('categoria_id')->unsigned();
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
        Schema::dropIfExists('categorias_instrumentos');
    }
}
