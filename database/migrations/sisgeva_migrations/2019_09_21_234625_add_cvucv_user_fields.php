<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCvucvUserFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            if (!Schema::hasColumn('users', 'cvucv_token')) {
                $table->string('cvucv_token')->unique()->after('settings');
            }
            if (!Schema::hasColumn('users', 'cvucv_suspended')) {
                $table->boolean('cvucv_suspended')->after('settings')->default(false);
            }
            if (!Schema::hasColumn('users', 'cvucv_lastname')) {
                $table->string('cvucv_lastname')->nullable()->after('settings');
            }
            if (!Schema::hasColumn('users', 'cvucv_username')) {
                $table->string('cvucv_username')->unique()->after('settings');
            }
            if (!Schema::hasColumn('users', 'cvucv_id')) {
                $table->bigInteger('cvucv_id')->unique()->after('settings');
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
        if (Schema::hasColumn('users', 'cvucv_id')) {
            Schema::table('users', function ($table) {
                $table->dropColumn('cvucv_id');
            });
        }
        if (Schema::hasColumn('users', 'cvucv_username')) {
            Schema::table('users', function ($table) {
                $table->dropColumn('cvucv_username');
            });
        }
        if (Schema::hasColumn('users', 'cvucv_lastname')) {
            Schema::table('users', function ($table) {
                $table->dropColumn('cvucv_lastname');
            });
        }
        if (Schema::hasColumn('users', 'cvucv_suspended')) {
            Schema::table('users', function ($table) {
                $table->dropColumn('cvucv_suspended');
            });
        }
        if (Schema::hasColumn('users', 'cvucv_token')) {
            Schema::table('users', function ($table) {
                $table->dropColumn('cvucv_token');
            });
        }
    }
}
