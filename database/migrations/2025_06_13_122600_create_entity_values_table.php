<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entity_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity')->nullable(false);
            $table->string('value');
            $table->string('description')->nullable();
            $table->boolean('available')->default(true);
            $table->timestamps();
            
            $table->foreign('entity')->references('id')->on('entities')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_values');
    }
};
