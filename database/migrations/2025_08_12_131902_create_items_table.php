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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->decimal('item_price', 10, 2);
            $table->integer('item_quantity');
            $table->decimal('item_price_per_piece', 10, 2);
            $table->integer('item_parts_per_piece');
            $table->decimal('item_price_per_part', 10, 2);
            $table->decimal('item_price_per_part_of_piece', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};