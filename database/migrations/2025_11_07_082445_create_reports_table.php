<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pubali_id')->nullable();
            $table->string('tid');
            $table->string('mid');
            $table->string('merchent')->nullable();
            $table->string('address');
            $table->string('officer');
            $table->string('number');
            $table->string('pos_s');
            $table->string('engineer_name');
            $table->string('service_category')->default('Pubali Bank');
            $table->string('engineer_contact')->nullable();
            $table->date('assignment_date')->nullable();
            $table->enum('status', ['pending', 'assigned', 'completed'])->default('pending');
            $table->string('bank')->nullable();
            $table->text('remarks')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
