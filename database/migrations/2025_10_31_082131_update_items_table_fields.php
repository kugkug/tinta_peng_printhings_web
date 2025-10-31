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
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'item_description')) {
                $table->dropColumn('item_description');
            }
            if (Schema::hasColumn('items', 'item_price')) {
                $table->dropColumn('item_price');
            }
            if (Schema::hasColumn('items', 'item_quantity')) {
                $table->dropColumn('item_quantity');
            }
            if (Schema::hasColumn('items', 'item_price_per_piece')) {
                $table->dropColumn('item_price_per_piece');
            }
            if (Schema::hasColumn('items', 'item_parts_per_piece')) {
                $table->dropColumn('item_parts_per_piece');
            }
            if (Schema::hasColumn('items', 'item_price_per_part')) {
                $table->dropColumn('item_price_per_part');
            }
            if (Schema::hasColumn('items', 'item_price_per_part_of_piece')) {
                $table->dropColumn('item_price_per_part_of_piece');
            }
        });

        Schema::table('items', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('sku');
            $table->string('variant_one')->nullable()->after('item_name');
            $table->string('variant_two')->nullable()->after('variant_one');
            $table->string('size')->nullable()->after('variant_two');
            $table->string('microns')->nullable()->after('size');
            $table->string('gsm')->nullable()->after('microns');
            $table->integer('sheets_per_pack')->nullable()->after('gsm');
            $table->decimal('price_without_shipping_fee', 10, 2)->default(0)->after('sheets_per_pack');
            $table->decimal('estimated_shipping_fee', 10, 2)->default(0)->after('price_without_shipping_fee');
            $table->date('date_purchased')->nullable()->after('estimated_shipping_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'brand')) {
                $table->dropColumn('brand');
            }
            if (Schema::hasColumn('items', 'variant_one')) {
                $table->dropColumn('variant_one');
            }
            if (Schema::hasColumn('items', 'variant_two')) {
                $table->dropColumn('variant_two');
            }
            if (Schema::hasColumn('items', 'size')) {
                $table->dropColumn('size');
            }
            if (Schema::hasColumn('items', 'microns')) {
                $table->dropColumn('microns');
            }
            if (Schema::hasColumn('items', 'gsm')) {
                $table->dropColumn('gsm');
            }
            if (Schema::hasColumn('items', 'sheets_per_pack')) {
                $table->dropColumn('sheets_per_pack');
            }
            if (Schema::hasColumn('items', 'price_without_shipping_fee')) {
                $table->dropColumn('price_without_shipping_fee');
            }
            if (Schema::hasColumn('items', 'estimated_shipping_fee')) {
                $table->dropColumn('estimated_shipping_fee');
            }
            if (Schema::hasColumn('items', 'date_purchased')) {
                $table->dropColumn('date_purchased');
            }
        });

        Schema::table('items', function (Blueprint $table) {
            $table->text('item_description')->nullable()->after('item_name');
            $table->decimal('item_price', 10, 2)->default(0)->after('item_description');
            $table->integer('item_quantity')->default(0)->after('item_price');
            $table->decimal('item_price_per_piece', 10, 2)->default(0)->after('item_quantity');
            $table->integer('item_parts_per_piece')->default(0)->after('item_price_per_piece');
            $table->decimal('item_price_per_part', 10, 2)->default(0)->after('item_parts_per_piece');
            $table->decimal('item_price_per_part_of_piece', 10, 2)->default(0)->after('item_price_per_part');
        });
    }
};
