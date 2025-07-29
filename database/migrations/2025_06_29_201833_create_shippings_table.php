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
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('shipping');
            $table->json('positions');
            $table->json('customer')->nullable();
            $table->string('address')->nullable();
            $table->string('track')->nullable();
            $table->string('carrier_key')->nullable();
            $table->string('carrier')->nullable();
            $table->unsignedBigInteger('courier')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->integer('weight')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('length')->nullable();
            $table->timestamps();
            
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('courier')->references('id')->on('users')->nullOnDelete();
            $table->foreign('status')->references('id')->on('entity_values')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
