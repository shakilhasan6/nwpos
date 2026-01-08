<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('engineer_logs', function (Blueprint $table) {
            $table->string('verify')->default('pending')->after('status');
            $table->string('completed')->default('pending')->after('verify');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('engineer_logs', function (Blueprint $table) {
            $table->dropColumn(['verify', 'completed']);
        });
    }
};
