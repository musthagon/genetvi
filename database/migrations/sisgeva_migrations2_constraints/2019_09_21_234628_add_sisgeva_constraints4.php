<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSisgevaConstraints4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('curso_periodos_lectivos', function ($table) {
            $table->foreign('periodos_lectivo_id')->references('id')->on('periodos_lectivos');
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
        Schema::table('curso_periodos_lectivo', function ($table) {
            $table->dropForeign(['periodos_lectivo_id']);
            $table->dropForeign(['curso_id']);
        });
    }
}
