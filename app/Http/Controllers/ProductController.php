<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\ProductItem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ProductController extends Controller
{
    /**
     * Get list of all products
     */
    public function apiProductsList(Request $request)
    {
        try {
            $products = Product::with('productItems')
                ->orderByDesc('created_at')
                ->get();

            $data = $products->map(function (Product $product) {
                $materialsCount = $product->productItems->where('component_type', 'materials')->count();
                $inkCount = $product->productItems->where('component_type', 'ink')->count();
                $packagingCount = $product->productItems->where('component_type', 'packaging')->count();

                return [
                    'id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'product_description' => $product->product_description ?? '',
                    'total_cost' => number_format((float) $product->total_cost, 2),
                    'items_count' => $product->productItems->count(),
                    'materials_count' => $materialsCount,
                    'ink_count' => $inkCount,
                    'packaging_count' => $packagingCount,
                    'created_at' => $product->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray();

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single product details with items
     */
    public function apiProductsGet(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ProductId' => 'required|exists:products,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $product = Product::with(['productItems.item', 'histories' => function ($query) {
                $query->latest()->limit(5);
            }])->findOrFail($request->ProductId);

            $materials = [];
            $inks = [];
            $packaging = [];

            foreach ($product->productItems as $component) {
                $basePayload = [
                    'product_item_id' => $component->id,
                    'item_id' => $component->item_id,
                    'item_name' => optional($component->item)->item_name,
                    'sku' => optional($component->item)->sku,
                ];

                switch ($component->component_type) {
                    case 'ink':
                        $inks[] = array_merge($basePayload, [
                            'pages_yield' => $component->pages_yield,
                            'cost_per_page' => (float) $component->cost_per_page,
                            'total_pages_printed' => $component->total_pages_printed,
                            'total_cost' => (float) $component->total_cost,
                        ]);
                        break;
                    case 'packaging':
                        $packaging[] = array_merge($basePayload, [
                            'quantity_used' => (float) $component->quantity,
                            'total_cost' => (float) $component->total_cost,
                        ]);
                        break;
                    case 'materials':
                    default:
                        $materials[] = array_merge($basePayload, [
                            'quantity_used' => (float) $component->quantity,
                            'unit_price' => (float) $component->unit_cost,
                            'total_cost' => (float) $component->total_cost,
                        ]);
                        break;
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'product_description' => $product->product_description,
                    'total_cost' => $product->total_cost,
                    'materials' => $materials,
                    'inks' => $inks,
                    'packaging' => $packaging,
                    'histories' => $product->histories->map(function (ProductHistory $history) {
                        return [
                            'id' => $history->id,
                            'product_code' => $history->product_code,
                            'product_name' => $history->product_name,
                            'reuse_count' => $history->reuse_count,
                            'materials' => $history->materials,
                            'inks' => $history->inks,
                            'packaging' => $history->packaging,
                            'updated_at' => $history->updated_at?->format('Y-m-d H:i:s'),
                        ];
                    })->toArray(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save (Create or Update) product
     */
    public function apiProductsSave(Request $request)
    {
        $transactionStarted = false;

        try {
            $rules = [
                'ProductName' => 'required|string|max:255',
                'ProductDescription' => 'nullable|string',
                'ProductCode' => 'nullable|string|max:255',
                'Materials' => 'nullable|array',
                'Materials.*.item_id' => 'required_with:Materials|exists:items,id',
                'Materials.*.quantity_used' => 'required_with:Materials|numeric|min:0',
                'Materials.*.unit_price' => 'nullable|numeric|min:0',
                'Materials.*.total_cost' => 'nullable|numeric|min:0',
                'Inks' => 'nullable|array',
                'Inks.*.item_id' => 'required_with:Inks|exists:items,id',
                'Inks.*.pages_yield' => 'nullable|numeric|min:0',
                'Inks.*.cost_per_page' => 'nullable|numeric|min:0',
                'Inks.*.total_pages_printed' => 'nullable|numeric|min:0',
                'Inks.*.total_cost' => 'nullable|numeric|min:0',
                'Packaging' => 'nullable|array',
                'Packaging.*.item_id' => 'required_with:Packaging|exists:items,id',
                'Packaging.*.quantity_used' => 'required_with:Packaging|numeric|min:0',
                'Packaging.*.total_cost' => 'nullable|numeric|min:0',
            ];

            if ($request->filled('ProductId')) {
                $rules['ProductId'] = 'required|exists:products,id';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $materialsInput = collect($request->input('Materials', []))->filter(fn ($row) => !empty($row['item_id']));
            $inksInput = collect($request->input('Inks', []))->filter(fn ($row) => !empty($row['item_id']));
            $packagingInput = collect($request->input('Packaging', []))->filter(fn ($row) => !empty($row['item_id']));

            if ($materialsInput->isEmpty() && $inksInput->isEmpty() && $packagingInput->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please add at least one material, ink, or packaging entry before saving the product.'
                ], 400);
            }

            $isUpdate = $request->filled('ProductId');
            $existingProduct = null;
            $oldProductItems = collect();
            $oldTrackedQuantities = [];

            if ($isUpdate) {
                $existingProduct = Product::with('productItems')->findOrFail($request->ProductId);
                $oldProductItems = $existingProduct->productItems;

                foreach ($oldProductItems as $component) {
                    if (in_array($component->component_type, ['materials', 'packaging'], true)) {
                        $oldTrackedQuantities[$component->item_id] = ($oldTrackedQuantities[$component->item_id] ?? 0) + (float) $component->quantity;
                    }
                }
            }

            $itemIds = $materialsInput->pluck('item_id')
                ->merge($inksInput->pluck('item_id'))
                ->merge($packagingInput->pluck('item_id'))
                ->filter()
                ->unique();

            if ($isUpdate) {
                $itemIds = $itemIds->merge($oldProductItems->pluck('item_id'))->unique();
            }

            $items = Item::whereIn('id', $itemIds)->get()->keyBy('id');

            [$materialsRecords, $materialsHistory] = $this->normaliseMaterials($materialsInput, $items);
            [$inkRecords, $inkHistory] = $this->normaliseInks($inksInput, $items);
            [$packagingRecords, $packagingHistory] = $this->normalisePackaging($packagingInput, $items);

            if (empty($materialsRecords) && empty($inkRecords) && empty($packagingRecords)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to process product. Please ensure each component card has valid entries.'
                ], 400);
            }

            $insufficientItems = $this->checkInventoryAvailability(
                $materialsRecords,
                $packagingRecords,
                $items,
                $oldTrackedQuantities,
                $isUpdate
            );

            if (!empty($insufficientItems)) {
                $errorMsg = "Insufficient inventory for the following items:\n";
                foreach ($insufficientItems as $item) {
                    $errorMsg .= sprintf(
                        "- %s (%s): Available %s, Needed %s\n",
                        $item['name'],
                        $item['sku'] ?? 'N/A',
                        $item['available'],
                        $item['needed']
                    );
                }

                return response()->json([
                    'status' => 'error',
                    'message' => trim($errorMsg),
                    'insufficient_items' => $insufficientItems
                ], 400);
            }

            $materialsCost = $this->calculateComponentTotal($materialsRecords);
            $inkCost = $this->calculateComponentTotal($inkRecords);
            $packagingCost = $this->calculateComponentTotal($packagingRecords);
            $totalCost = $materialsCost + $inkCost + $packagingCost;

            $configurationHash = ProductHistory::configurationHash($materialsHistory, $inkHistory, $packagingHistory);
            $existingHistory = ProductHistory::where('configuration_hash', $configurationHash)->first();
            $reusedExistingCode = false;

            $productCode = trim((string) $request->ProductCode);
            if ($productCode === '') {
                if ($existingHistory) {
                    $productCode = $existingHistory->product_code;
                    $reusedExistingCode = true;
                } else {
                    $productCode = Product::generateProductCode();
                }
            }

            $transactionStarted = false;

            DB::beginTransaction();
            $transactionStarted = true;

            if ($isUpdate) {
                foreach ($oldProductItems as $component) {
                    if (in_array($component->component_type, ['materials', 'packaging'], true)) {
                        $item = $items->get($component->item_id);
                        if ($item) {
                            $item->increment('item_quantity', (float) $component->quantity);
                        }
                    }
                }

                $existingProduct->update([
                    'product_code' => $productCode,
                    'product_name' => $request->ProductName,
                    'product_description' => $request->ProductDescription,
                ]);

                $existingProduct->productItems()->delete();
                $product = $existingProduct;
            } else {
                $product = Product::create([
                    'product_code' => $productCode,
                    'product_name' => $request->ProductName,
                    'product_description' => $request->ProductDescription,
                    'total_cost' => 0,
                ]);
            }

            $this->storeComponents($product, $materialsRecords);
            $this->storeComponents($product, $packagingRecords);
            $this->storeComponents($product, $inkRecords);

            $this->adjustInventory($materialsRecords, $items, 'decrement');
            $this->adjustInventory($packagingRecords, $items, 'decrement');

            $product->update(['total_cost' => $totalCost]);

            $history = ProductHistory::firstOrNew(['configuration_hash' => $configurationHash]);
            $previousProductId = $history->product_id;
            $history->product_id = $product->id;
            $history->product_code = $productCode;
            $history->product_name = $product->product_name;
            $history->materials = $materialsHistory;
            $history->inks = $inkHistory;
            $history->packaging = $packagingHistory;

            if (!$history->exists) {
                $history->reuse_count = 1;
            } elseif (!$isUpdate || $previousProductId !== $product->id) {
                $history->reuse_count = $history->reuse_count + 1;
            }

            $history->save();

            DB::commit();
            $transactionStarted = false;

            return response()->json([
                'status' => 'success',
                'message' => $isUpdate
                    ? 'Product updated successfully.'
                    : 'Product created successfully.',
                'data' => [
                    'id' => $product->id,
                    'product_code' => $product->product_code,
                    'total_cost' => number_format($product->total_cost, 2),
                    'history_reused' => $reusedExistingCode || (!$isUpdate && $existingHistory !== null),
                ]
            ]);

        } catch (\Exception $e) {
            if ($transactionStarted) {
                DB::rollBack();
            }
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete product
     */
    public function apiProductsDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ProductId' => 'required|exists:products,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            DB::beginTransaction();

            $product = Product::with('productItems')->findOrFail($request->ProductId);

            $itemIds = $product->productItems->pluck('item_id')->filter()->unique();
            $items = Item::whereIn('id', $itemIds)->get()->keyBy('id');

            foreach ($product->productItems as $component) {
                if (in_array($component->component_type, ['materials', 'packaging'], true)) {
                    $item = $items->get($component->item_id);
                    if ($item) {
                        $item->increment('item_quantity', (float) $component->quantity);
                    }
                }
            }

            $product->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully. Inventory restored.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function apiProductsHistoryList(Request $request)
    {
        try {
            $limit = $request->input('Limit', $request->input('limit', 20));
            $limit = (int) max(1, min((int) $limit, 100));

            $query = ProductHistory::query();

            if ($productCode = trim((string) $request->input('ProductCode'))) {
                $query->where('product_code', 'like', $productCode . '%');
            }

            $histories = $query
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get()
                ->map(function (ProductHistory $history) {
                    return [
                        'id' => $history->id,
                        'product_code' => $history->product_code,
                        'product_name' => $history->product_name,
                        'reuse_count' => $history->reuse_count,
                        'materials' => $history->materials ?? [],
                        'inks' => $history->inks ?? [],
                        'packaging' => $history->packaging ?? [],
                        'updated_at' => $history->updated_at?->format('Y-m-d H:i:s'),
                    ];
                })
                ->toArray();

            return response()->json([
                'status' => 'success',
                'data' => $histories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate a unique product code
     */
    public function apiProductsGenerateCode(Request $request)
    {
        try {
            $prefix = $request->prefix ?? 'PROD';
            $code = Product::generateProductCode($prefix);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'product_code' => $code
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if product code exists
     */
    public function apiProductsCheckCode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ProductCode' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $exists = Product::productCodeExists($request->ProductCode);
            $product = null;
            $historySnapshot = null;

            if ($exists) {
                $product = Product::where('product_code', $request->ProductCode)->latest('updated_at')->first();
            }

            $history = ProductHistory::where('product_code', $request->ProductCode)
                ->latest('updated_at')
                ->first();

            if ($history) {
                $historySnapshot = [
                    'materials' => $history->materials ?? [],
                    'inks' => $history->inks ?? [],
                    'packaging' => $history->packaging ?? [],
                    'reuse_count' => $history->reuse_count,
                    'configuration_hash' => $history->configuration_hash,
                    'product_name' => $history->product_name,
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'exists' => $exists,
                    'product' => $product ? [
                        'id' => $product->id,
                        'product_name' => $product->product_name,
                        'product_description' => $product->product_description,
                    ] : null,
                    'history' => $historySnapshot,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    protected function normaliseMaterials(Collection $input, Collection $items): array
    {
        $records = [];
        $history = [];

        foreach ($input as $row) {
            $itemId = (int) Arr::get($row, 'item_id');
            if (!$itemId) {
                continue;
            }

            /** @var Item|null $item */
            $item = $items->get($itemId);

            $quantity = max((float) Arr::get($row, 'quantity_used', 0), 0);
            $unitPrice = Arr::has($row, 'unit_price') && Arr::get($row, 'unit_price') !== ''
                ? (float) Arr::get($row, 'unit_price')
                : $this->resolveDefaultUnitPrice($item);
            $totalCost = Arr::has($row, 'total_cost') && Arr::get($row, 'total_cost') !== ''
                ? (float) Arr::get($row, 'total_cost')
                : round($quantity * $unitPrice, 4);

            $records[] = [
                'item_id' => $itemId,
                'component_type' => 'materials',
                'quantity' => $quantity,
                'unit_cost' => $unitPrice,
                'total_cost' => $totalCost,
            ];

            $history[] = [
                'item_id' => $itemId,
                'sku' => optional($item)->sku,
                'item_name' => optional($item)->item_name,
                'quantity_used' => $quantity,
                'unit_price' => $unitPrice,
                'total_cost' => $totalCost,
            ];
        }

        return [$records, $history];
    }

    protected function normaliseInks(Collection $input, Collection $items): array
    {
        $records = [];
        $history = [];

        foreach ($input as $row) {
            $itemId = (int) Arr::get($row, 'item_id');
            if (!$itemId) {
                continue;
            }

            /** @var Item|null $item */
            $item = $items->get($itemId);

            $pagesYield = Arr::has($row, 'pages_yield') && Arr::get($row, 'pages_yield') !== ''
                ? (float) Arr::get($row, 'pages_yield')
                : null;
            $costPerPage = Arr::has($row, 'cost_per_page') && Arr::get($row, 'cost_per_page') !== ''
                ? (float) Arr::get($row, 'cost_per_page')
                : null;
            $totalPages = Arr::has($row, 'total_pages_printed') && Arr::get($row, 'total_pages_printed') !== ''
                ? (float) Arr::get($row, 'total_pages_printed')
                : null;

            $computedCost = ($costPerPage ?? 0) * ($totalPages ?? 0);
            $totalCost = Arr::has($row, 'total_cost') && Arr::get($row, 'total_cost') !== ''
                ? (float) Arr::get($row, 'total_cost')
                : round($computedCost, 4);

            $records[] = [
                'item_id' => $itemId,
                'component_type' => 'ink',
                'quantity' => 0,
                'unit_cost' => 0,
                'total_cost' => $totalCost,
                'pages_yield' => $pagesYield,
                'cost_per_page' => $costPerPage,
                'total_pages_printed' => $totalPages,
            ];

            $history[] = [
                'item_id' => $itemId,
                'sku' => optional($item)->sku,
                'item_name' => optional($item)->item_name,
                'pages_yield' => $pagesYield,
                'cost_per_page' => $costPerPage,
                'total_pages_printed' => $totalPages,
                'total_cost' => $totalCost,
            ];
        }

        return [$records, $history];
    }

    protected function normalisePackaging(Collection $input, Collection $items): array
    {
        $records = [];
        $history = [];

        foreach ($input as $row) {
            $itemId = (int) Arr::get($row, 'item_id');
            if (!$itemId) {
                continue;
            }

            /** @var Item|null $item */
            $item = $items->get($itemId);

            $quantity = max((float) Arr::get($row, 'quantity_used', 0), 0);
            $totalCost = Arr::has($row, 'total_cost') && Arr::get($row, 'total_cost') !== ''
                ? (float) Arr::get($row, 'total_cost')
                : 0.0;
            $unitCost = $quantity > 0 ? round($totalCost / $quantity, 4) : 0.0;

            $records[] = [
                'item_id' => $itemId,
                'component_type' => 'packaging',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
            ];

            $history[] = [
                'item_id' => $itemId,
                'sku' => optional($item)->sku,
                'item_name' => optional($item)->item_name,
                'quantity_used' => $quantity,
                'total_cost' => $totalCost,
                'unit_price' => $unitCost,
            ];
        }

        return [$records, $history];
    }

    protected function resolveDefaultUnitPrice(?Item $item): float
    {
        if (!$item) {
            return 0.0;
        }

        foreach ([
            $item->item_price_per_part ?? null,
            $item->item_price_per_piece ?? null,
            $item->item_price ?? null,
        ] as $candidate) {
            if ($candidate !== null && $candidate !== '') {
                return (float) $candidate;
            }
        }

        return 0.0;
    }

    protected function calculateComponentTotal(array $records): float
    {
        return collect($records)->sum(function ($record) {
            return (float) ($record['total_cost'] ?? 0);
        });
    }

    protected function checkInventoryAvailability(array $materialsRecords, array $packagingRecords, Collection $items, array $oldTracked, bool $isUpdate): array
    {
        $totals = [];

        foreach (array_merge($materialsRecords, $packagingRecords) as $record) {
            $itemId = $record['item_id'] ?? null;
            if (!$itemId) {
                continue;
            }

            $totals[$itemId] = ($totals[$itemId] ?? 0) + (float) ($record['quantity'] ?? 0);
        }

        $insufficient = [];

        foreach ($totals as $itemId => $requestedQty) {
            /** @var Item|null $item */
            $item = $items->get($itemId);
            if (!$item) {
                continue;
            }

            $requestedQty = max($requestedQty, 0);
            if ($requestedQty <= 0) {
                continue;
            }

            $availableQty = (float) ($item->item_quantity ?? 0);

            if ($isUpdate && array_key_exists($itemId, $oldTracked)) {
                $netChange = $requestedQty - (float) $oldTracked[$itemId];
                if ($netChange > $availableQty) {
                    $insufficient[] = [
                        'item_id' => $itemId,
                        'name' => $item->item_name,
                        'sku' => $item->sku,
                        'available' => $availableQty,
                        'needed' => $netChange,
                    ];
                }
            } elseif (!$isUpdate && $requestedQty > $availableQty) {
                $insufficient[] = [
                    'item_id' => $itemId,
                    'name' => $item->item_name,
                    'sku' => $item->sku,
                    'available' => $availableQty,
                    'needed' => $requestedQty,
                ];
            }
        }

        return $insufficient;
    }

    protected function storeComponents(Product $product, array $records): void
    {
        if (empty($records)) {
            return;
        }

        $product->productItems()->createMany(array_map(function (array $record) {
            $payload = Arr::only($record, [
                'item_id',
                'component_type',
                'quantity',
                'unit_cost',
                'total_cost',
                'pages_yield',
                'cost_per_page',
                'total_pages_printed',
                'meta',
            ]);

            return array_filter($payload, static function ($value) {
                return $value !== null;
            });
        }, $records));
    }

    protected function adjustInventory(array $records, Collection $items, string $operation = 'decrement'): void
    {
        foreach ($records as $record) {
            $itemId = $record['item_id'] ?? null;
            if (!$itemId) {
                continue;
            }

            $quantity = (float) ($record['quantity'] ?? 0);
            if ($quantity <= 0) {
                continue;
            }

            /** @var Item|null $item */
            $item = $items->get($itemId);
            if (!$item) {
                continue;
            }

            if ($operation === 'increment') {
                $item->increment('item_quantity', $quantity);
            } else {
                $item->decrement('item_quantity', $quantity);
            }
        }
    }
}

