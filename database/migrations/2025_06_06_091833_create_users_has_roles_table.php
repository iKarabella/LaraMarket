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
        Schema::create('users_has_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');

            $table->index('user_id', 'user_role_user_idx');
            $table->index('role_id', 'user_role_role_idx');

            $table->foreign('user_id', 'user_role_user_fk')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('role_id', 'user_role_role_fk')->references('id')->on('roles')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_has_roles');
    }
};
