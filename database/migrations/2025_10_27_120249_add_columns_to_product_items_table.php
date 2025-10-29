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
        Schema::table('product_items', function (Blueprint $table) {
            $table->foreignId('product_id')->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->after('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2)->after('item_id'); // Quantity of this item used in the product
            $table->decimal('unit_cost', 10, 2)->default(0)->after('quantity'); // Cost per unit at time of adding
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['item_id']);
            $table->dropColumn(['product_id', 'item_id', 'quantity', 'unit_cost']);
        });
    }
};
