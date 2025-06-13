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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('link')->nullable();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->boolean('visibility')->default(true);
            $table->string('offersign')->nullable();
            $table->unsignedBigInteger('measure')->nullable();
            $table->integer('sort')->default(50);
            $table->timestamps();

            $table->foreign('measure')->references('id')->on('entity_values')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
