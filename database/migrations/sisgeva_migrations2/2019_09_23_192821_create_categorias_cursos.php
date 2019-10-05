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
            $table->bigInteger('id')->unsigned();
            $table->bigInteger('cvucv_category_parent_id')->unsigned()->nullable();
            $table->bigInteger('cvucv_category_super_parent_id')->unsigned()->nullable();
            $table->longText('cvucv_name')->nullable();
            $table->longText('cvucv_description')->nullable();
            $table->longText('cvucv_path')->nullable();
            $table->longText('cvucv_link')->nullable();
            $table->integer('cvucv_coursecount')->nullable();
            $table->integer('cvucv_depth')->nullable();   
            $table->boolean('cvucv_visible')->default(true);
            $table->bigInteger('periodo_lectivo')->unsigned()->nullable();        
            $table->timestamps();

            $table->primary('id');
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
