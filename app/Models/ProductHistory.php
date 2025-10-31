<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_code',
        'product_name',
        'configuration_hash',
        'materials',
        'inks',
        'packaging',
        'reuse_count',
    ];

    protected $casts = [
        'materials' => 'array',
        'inks' => 'array',
        'packaging' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function configurationHash(array $materials, array $inks, array $packaging): string
    {
        $payload = [
            'materials' => self::normaliseCollection($materials),
            'inks' => self::normaliseCollection($inks),
            'packaging' => self::normaliseCollection($packaging),
        ];

        return hash('sha256', json_encode($payload));
    }

    protected static function normaliseCollection(array $items): array
    {
        return collect($items)
            ->map(function ($item) {
                if (is_array($item)) {
                    ksort($item);
                }
                return $item;
            })
            ->sortBy(function ($item) {
                if (is_array($item)) {
                    return ($item['item_id'] ?? '') . '|' . ($item['sku'] ?? '') . '|' . ($item['component_type'] ?? '');
                }
                return (string) $item;
            })
            ->values()
            ->all();
    }
}
