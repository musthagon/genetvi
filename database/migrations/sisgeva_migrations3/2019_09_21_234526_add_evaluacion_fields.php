<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddEvaluacionFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluaciones', function ($table) {
            if (!Schema::hasColumn('evaluaciones', 'percentil_eva')) {
                $table->float('percentil_eva', 8, 2)->unsigned()->nullable()->after('respuestas');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('evaluaciones', 'percentil_eva')) {
            Schema::table('evaluaciones', function ($table) {
                $table->dropColumn('percentil_eva');
            });
        }
    }
}
