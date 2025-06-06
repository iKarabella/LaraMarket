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
        Schema::create('roles_has_permissions', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');

            $table->index('role_id', 'role_permission_role_idx');
            $table->index('permission_id', 'role_permission_permission_idx');

            $table->foreign('role_id', 'role_permission_role_fk')->references('id')->on('roles')->cascadeOnDelete();
            $table->foreign('permission_id', 'role_permission_permission_fk')->references('id')->on('permissions')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_has_permissions');
    }
};
