<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriasIndicadores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias_indicadores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('valor_porcentual', 8, 2)->unsigned();
            $table->bigInteger('categoria_id')->unsigned();
            $table->bigInteger('indicador_id')->unsigned();
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
        Schema::dropIfExists('categorias_indicadores');
    }
}
