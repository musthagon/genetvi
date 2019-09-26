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
            $table->bigInteger('cvucv_id')->nullable();
            $table->bigInteger('cvucv_parent_id')->nullable();
            $table->string('cvucv_name')->nullable();
            $table->integer('cvucv_coursecount')->nullable();
            $table->boolean('cvucv_visible')->default(true);
            $table->string('cvucv_path')->nullable();
            $table->string('cvucv_link')->nullable();
            $table->integer('padre_id')->unsigned()->nullable();
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
