<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSisgevaConstraints5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluaciones', function ($table) {
            $table->foreign('instrumento_id')->references('id')->on('instrumentos');
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('curso_id')->references('id')->on('cursos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evaluaciones', function ($table) {
            $table->dropForeign(['instrumento_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['curso_id']);
        });
    }
}
