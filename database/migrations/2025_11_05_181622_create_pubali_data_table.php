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
        Schema::create('pubali_data', function (Blueprint $table) {
            $table->id();
        $table->string('tid');
        $table->string('mid');
        $table->string('merchent');
        $table->string('address');
        $table->string('officer');
        $table->string('number');
        $table->string('pos_s');
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
        Schema::dropIfExists('pubali_data');
    }
};
