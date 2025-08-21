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
        Schema::table('reserved_products', function (Blueprint $table) {
            $table->dropForeign('reserved_products_mark_wh_foreign');
            $table->dropColumn('mark_wh');
            $table->renameColumn('name', 'product_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reserved_products', function (Blueprint $table) {
            $table->unsignedBigInteger('mark_wh')->after('warehouse_id')->nullable();
            $table->foreign('mark_wh')->references('id')->on('warehouses')->nullOnDelete();
            $table->renameColumn('product_title', 'name');
        });
    }
};
