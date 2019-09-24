<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriasCursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias_cursos', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('cvucv_id');
            $table->bigInteger('cvucv_parent_id');
            $table->string('cvucv_name');
            $table->integer('cvucv_coursecount');
            $table->boolean('cvucv_visible')->default(true);
            $table->string('cvucv_path');
            $table->string('cvucv_link');
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
        Schema::dropIfExists('categorias_cursos');
    }
}
