<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->bigInteger('cvucv_category_id')->unsigned()->nullable();
            $table->string('cvucv_shortname')->nullable();
            $table->longText('cvucv_fullname')->nullable();
            $table->longText('cvucv_displayname')->nullable();
            $table->longText('cvucv_summary')->nullable();
            $table->string('cvucv_link')->nullable();
            $table->boolean('cvucv_visible')->default(true);           
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
        Schema::dropIfExists('cursos');
    }
}
