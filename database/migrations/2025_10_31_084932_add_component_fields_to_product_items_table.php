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
            $table->string('component_type')->default('materials')->after('item_id');
            $table->decimal('total_cost', 12, 4)->default(0)->after('unit_cost');
            $table->unsignedInteger('pages_yield')->nullable()->after('total_cost');
            $table->decimal('cost_per_page', 12, 4)->nullable()->after('pages_yield');
            $table->unsignedInteger('total_pages_printed')->nullable()->after('cost_per_page');
            $table->json('meta')->nullable()->after('total_pages_printed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropColumn([
                'component_type',
                'total_cost',
                'pages_yield',
                'cost_per_page',
                'total_pages_printed',
                'meta',
            ]);
        });
    }
};
