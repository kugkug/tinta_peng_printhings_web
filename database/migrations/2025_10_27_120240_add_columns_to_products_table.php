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
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_code')->unique()->after('id'); // Unique identifier that can be reused
            $table->string('product_name')->after('product_code');
            $table->text('product_description')->nullable()->after('product_name');
            $table->decimal('total_cost', 10, 2)->default(0)->after('product_description'); // Auto-calculated from items
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['product_code', 'product_name', 'product_description', 'total_cost']);
        });
    }
};
