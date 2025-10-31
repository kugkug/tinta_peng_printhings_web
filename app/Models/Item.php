<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    protected $fillable = [
        'sku',
        'brand',
        'item_name',
        'variant_one',
        'variant_two',
        'size',
        'microns',
        'gsm',
        'sheets_per_pack',
        'price_without_shipping_fee',
        'estimated_shipping_fee',
        'date_purchased',
    ];

    protected $casts = [
        'price_without_shipping_fee' => 'decimal:2',
        'estimated_shipping_fee' => 'decimal:2',
        'date_purchased' => 'date',
    ];

    /**
     * The products that use this item
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_items')
            ->withPivot('quantity', 'unit_cost')
            ->withTimestamps();
    }
}
