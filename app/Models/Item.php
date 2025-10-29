<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    protected $fillable = [
        'sku',
        'item_name',
        'item_description',
        'item_price',
        'item_quantity',
        'item_price_per_piece',
        'item_parts_per_piece',
        'item_price_per_part',
        'item_price_per_part_of_piece',
    ];

    protected $casts = [
        'item_price' => 'decimal:2',
        'item_price_per_piece' => 'decimal:2',
        'item_price_per_part' => 'decimal:2',
        'item_price_per_part_of_piece' => 'decimal:2',
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
