<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstrumentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instrumentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('nombre');
            $table->longText('nombre_corto');
            $table->longText('descripcion');
            $table->boolean('habilitar')->default(true);
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
        Schema::dropIfExists('instrumentos');
    }
}
