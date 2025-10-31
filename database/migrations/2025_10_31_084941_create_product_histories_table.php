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
        Schema::create('product_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_code');
            $table->string('product_name')->nullable();
            $table->string('configuration_hash')->unique();
            $table->json('materials')->nullable();
            $table->json('inks')->nullable();
            $table->json('packaging')->nullable();
            $table->unsignedInteger('reuse_count')->default(0);
            $table->timestamps();
            $table->index('product_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_histories');
    }
};
