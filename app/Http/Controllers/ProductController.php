<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Item;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Get list of all products
     */
    public function apiProductsList(Request $request)
    {
        try {
            $products = Product::with('items')->orderBy('created_at', 'desc')->get();
            
            $data = [];
            foreach ($products as $product) {
                $data[] = [
                    'id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'product_description' => $product->product_description ?? '',
                    'total_cost' => number_format($product->total_cost, 2),
                    'items_count' => $product->items->count(),
                    'created_at' => $product->created_at->format('Y-m-d H:i:s'),
                ];
            }
            
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

            $product = Product::with('items')->findOrFail($request->ProductId);
            
            $items = [];
            foreach ($product->items as $item) {
                $items[] = [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'sku' => $item->sku,
                    'quantity' => $item->pivot->quantity,
                    'unit_cost' => $item->pivot->unit_cost,
                    'subtotal' => $item->pivot->quantity * $item->pivot->unit_cost,
                ];
            }
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'product_description' => $product->product_description,
                    'total_cost' => $product->total_cost,
                    'items' => $items,
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
        try {
            $rules = [
                'ProductName' => 'required|string|max:255',
                'ProductDescription' => 'nullable|string',
                'ProductCode' => 'nullable|string|max:255',
                'Items' => 'required|array|min:1',
                'Items.*.item_id' => 'required|exists:items,id',
                'Items.*.quantity' => 'required|numeric|min:0.01',
            ];

            if ($request->has('ProductId') && $request->ProductId) {
                $rules['ProductId'] = 'required|exists:products,id';
                if ($request->ProductCode) {
                    $rules['ProductCode'] .= '|unique:products,product_code,' . $request->ProductId;
                }
            } else {
                if ($request->ProductCode) {
                    $rules['ProductCode'] .= '|unique:products,product_code';
                }
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            DB::beginTransaction();

            $isUpdate = $request->has('ProductId') && $request->ProductId;
            $oldItems = [];

            // If updating, get old items to restore inventory
            if ($isUpdate) {
                $existingProduct = Product::with('items')->findOrFail($request->ProductId);
                foreach ($existingProduct->items as $item) {
                    $oldItems[$item->id] = $item->pivot->quantity;
                }
            }

            // Check inventory availability for new items
            $insufficientItems = [];
            foreach ($request->Items as $itemData) {
                $item = Item::find($itemData['item_id']);
                $requestedQty = $itemData['quantity'];
                
                // If updating, calculate the net change
                if ($isUpdate && isset($oldItems[$item->id])) {
                    $netChange = $requestedQty - $oldItems[$item->id];
                    if ($netChange > $item->item_quantity) {
                        $insufficientItems[] = [
                            'name' => $item->item_name,
                            'sku' => $item->sku,
                            'available' => $item->item_quantity,
                            'needed' => $netChange
                        ];
                    }
                } else {
                    // New item, check if enough inventory
                    if ($requestedQty > $item->item_quantity) {
                        $insufficientItems[] = [
                            'name' => $item->item_name,
                            'sku' => $item->sku,
                            'available' => $item->item_quantity,
                            'needed' => $requestedQty
                        ];
                    }
                }
            }

            // If insufficient inventory, return error
            if (!empty($insufficientItems)) {
                DB::rollBack();
                $errorMsg = "Insufficient inventory for the following items:\n";
                foreach ($insufficientItems as $item) {
                    $errorMsg .= "- {$item['name']} ({$item['sku']}): Available {$item['available']}, Needed {$item['needed']}\n";
                }
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMsg,
                    'insufficient_items' => $insufficientItems
                ], 400);
            }

            // Restore inventory for old items if updating
            if ($isUpdate) {
                foreach ($oldItems as $itemId => $oldQty) {
                    $item = Item::find($itemId);
                    if ($item) {
                        $item->increment('item_quantity', $oldQty);
                    }
                }
            }

            // Generate product code if not provided
            $productCode = $request->ProductCode;
            if (!$productCode) {
                $productCode = Product::generateProductCode();
            }

            if ($isUpdate) {
                // Update existing product
                $product = Product::findOrFail($request->ProductId);
                $product->update([
                    'product_code' => $productCode,
                    'product_name' => $request->ProductName,
                    'product_description' => $request->ProductDescription,
                ]);
            } else {
                // Create new product
                $product = Product::create([
                    'product_code' => $productCode,
                    'product_name' => $request->ProductName,
                    'product_description' => $request->ProductDescription,
                ]);
            }

            // Sync items with the product and deduct from inventory
            $itemsData = [];
            $totalCost = 0;

            foreach ($request->Items as $itemData) {
                $item = Item::find($itemData['item_id']);
                
                // Use item's price per part as unit cost if not provided
                $unitCost = $itemData['unit_cost'] ?? $item->item_price_per_part;
                
                $itemsData[$itemData['item_id']] = [
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $unitCost,
                ];

                $totalCost += $itemData['quantity'] * $unitCost;

                // Deduct from inventory
                $item->decrement('item_quantity', $itemData['quantity']);
            }

            $product->items()->sync($itemsData);
            $product->update(['total_cost' => $totalCost]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $isUpdate ? 'Product updated successfully. Inventory adjusted.' : 'Product created successfully. Inventory deducted.',
                'data' => [
                    'id' => $product->id,
                    'product_code' => $product->product_code,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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

            $product = Product::with('items')->findOrFail($request->ProductId);
            
            // Restore inventory before deleting product
            foreach ($product->items as $item) {
                $quantityToRestore = $item->pivot->quantity;
                $item->increment('item_quantity', $quantityToRestore);
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

            if ($exists) {
                $product = Product::where('product_code', $request->ProductCode)->first();
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
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

