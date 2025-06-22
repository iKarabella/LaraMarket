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
        Schema::create('warehouse_acts', function (Blueprint $table) {$table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->enum('type', ['write-off', 'receipt']);
            $table->json('act');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_acts');
    }
};
