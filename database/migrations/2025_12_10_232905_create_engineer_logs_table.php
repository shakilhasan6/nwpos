<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('engineer_logs', function (Blueprint $table) {
            $table->id();

            // Engineer info
            $table->string('engineer_name');

            // All dates + multiple rows stored as JSON
            // [
            //   {
            //     "date": "2025-12-12",
            //     "rows": [
            //        { "from":"X", "to":"Y", "transport":"Bus", "amount":100, ... },
            //        { ... }
            //     ]
            //   },
            //   { another date block }
            // ]
            $table->json('entries');

            // Summary
            $table->decimal('grand_total', 12, 2)->default(0);

            // Pending / approved
            $table->string('status')->default('pending');
            $table->string('verify')->default('pending');
            $table->string('completed')->default('pending');

            // Submitted time
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('engineer_logs');
    }
};
