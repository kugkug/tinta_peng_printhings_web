<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function productItems(): HasMany
    {
        return $this->hasMany(ProductItem::class);
    }

    public function materials(): HasMany
    {
        return $this->productItems()->where('component_type', 'materials');
    }

    public function inks(): HasMany
    {
        return $this->productItems()->where('component_type', 'ink');
    }

    public function packaging(): HasMany
    {
        return $this->productItems()->where('component_type', 'packaging');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ProductHistory::class);
    }

    /**
     * Calculate and update the total cost based on items
     */
    public function calculateTotalCost(): void
    {
        $totalCost = $this->productItems->sum(function (ProductItem $component) {
            if (! is_null($component->total_cost)) {
                return $component->total_cost;
            }

            return ($component->quantity ?? 0) * ($component->unit_cost ?? 0);
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

