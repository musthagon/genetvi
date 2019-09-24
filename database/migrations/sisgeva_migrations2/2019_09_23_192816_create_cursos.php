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
            $table->bigIncrements('id');
            $table->bigInteger('cvucv_id');
            $table->string('cvucv_shortname');
            $table->bigInteger('cvucv_category_id');
            $table->string('cvucv_fullname');
            $table->string('cvucv_displayname');
            $table->string('cvucv_summary');
            $table->boolean('cvucv_visible')->default(true);
            $table->string('cvucv_link');
            $table->longText('cvucv_participantes')->nullable();
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
        Schema::dropIfExists('cursos');
    }
}
