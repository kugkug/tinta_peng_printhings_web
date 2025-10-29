<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'product_code',
        'product_name',
        'product_description',
        'total_cost',
    ];

    protected $casts = [
        'total_cost' => 'decimal:2',
    ];

    /**
     * The items that belong to the product (Bill of Materials)
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'product_items')
            ->withPivot('quantity', 'unit_cost')
            ->withTimestamps();
    }

    /**
     * Calculate and update the total cost based on items
     */
    public function calculateTotalCost(): void
    {
        $totalCost = $this->items->sum(function ($item) {
            return $item->pivot->quantity * $item->pivot->unit_cost;
        });

        $this->update(['total_cost' => $totalCost]);
    }

    /**
     * Generate a unique product code
     */
    public static function generateProductCode(string $prefix = 'PROD'): string
    {
        $timestamp = now()->format('ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
        
        $code = "{$prefix}-{$timestamp}-{$random}";
        
        // Ensure uniqueness
        while (self::where('product_code', $code)->exists()) {
            $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
            $code = "{$prefix}-{$timestamp}-{$random}";
        }
        
        return $code;
    }

    /**
     * Check if a product code already exists
     */
    public static function productCodeExists(string $code): bool
    {
        return self::where('product_code', $code)->exists();
    }
}

