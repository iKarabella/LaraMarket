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
        Schema::table('shippings', function (Blueprint $table) {
            $table->dropForeign('shippings_status_foreign');
            $table->dropColumn('status');
            
            $table->after('courier', function($table){
                $table->json('cancelled')->nullable();
                $table->json('delivered')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shippings', function (Blueprint $table) {
            $table->dropColumn(['cancelled', 'delivered']);
            $table->unsignedBigInteger('status')->nullable();
            $table->foreign('status')->references('id')->on('entity_values')->nullOnDelete();
        });
    }
};
