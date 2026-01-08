<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_reports', function (Blueprint $table) {
            $table->id();
            $table->string('zone_name');
            $table->string('engineer_name');
            $table->string('tid');
            $table->string('pos_serial');
            $table->text('merchant_address');
            $table->enum('service_type', ['Merchant Deploy', 'Branch Deploy', 'Support', 'Replace', 'Roll Out', 'Roll Out Not Done']);
            $table->text('remarks')->nullable();
            $table->string('service_report_image_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_reports');
    }
};