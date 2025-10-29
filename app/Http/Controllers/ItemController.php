<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemController extends Controller
{
    /**
     * Get list of all items
     */
    public function apiItemsList(Request $request)
    {
        try {
            $items = Item::orderBy('created_at', 'desc')->get();
            
            $data = [];
            $lowStockCount = 0;
            $lowStockThreshold = 10;
            
            foreach ($items as $item) {
                $isLowStock = $item->item_quantity <= $lowStockThreshold;
                if ($isLowStock) {
                    $lowStockCount++;
                }
                
                $data[] = [
                    'id' => $item->id,
                    'sku' => $item->sku ?? 'N/A',
                    'item_name' => $item->item_name,
                    'item_description' => $item->item_description ?? '',
                    'item_price' => number_format($item->item_price, 2),
                    'item_quantity' => $item->item_quantity,
                    'item_price_per_piece' => number_format($item->item_price_per_piece, 2),
                    'item_parts_per_piece' => $item->item_parts_per_piece,
                    'item_price_per_part' => number_format($item->item_price_per_part, 2),
                    'item_price_per_part_of_piece' => number_format($item->item_price_per_part_of_piece, 2),
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'is_low_stock' => $isLowStock,
                    'stock_status' => $isLowStock ? 'low' : 'normal',
                ];
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $data,
                'low_stock_count' => $lowStockCount,
                'low_stock_threshold' => $lowStockThreshold
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single item details
     */
    public function apiItemsGet(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ItemId' => 'required|exists:items,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $item = Item::findOrFail($request->ItemId);
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $item->id,
                    'sku' => $item->sku,
                    'item_name' => $item->item_name,
                    'item_description' => $item->item_description,
                    'item_price' => $item->item_price,
                    'item_quantity' => $item->item_quantity,
                    'item_price_per_piece' => $item->item_price_per_piece,
                    'item_parts_per_piece' => $item->item_parts_per_piece,
                    'item_price_per_part' => $item->item_price_per_part,
                    'item_price_per_part_of_piece' => $item->item_price_per_part_of_piece,
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
     * Save (Create or Update) item
     */
    public function apiItemsSave(Request $request)
    {
        try {
            $rules = [
                'ItemName' => 'required|string|max:255',
                'ItemDescription' => 'nullable|string',
                'ItemPrice' => 'required|numeric|min:0',
                'ItemQuantity' => 'required|integer|min:0',
                'ItemPricePerPiece' => 'required|numeric|min:0',
                'ItemPartsPerPiece' => 'required|integer|min:0',
                'ItemPricePerPart' => 'required|numeric|min:0',
                'ItemPricePerPartOfPiece' => 'required|numeric|min:0',
            ];

            if ($request->has('ItemId') && $request->ItemId != '') {
                $rules['ItemId'] = 'required|exists:items,id';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $data = [
                'item_name' => $request->ItemName,
                'item_description' => $request->ItemDescription,
                'item_price' => $request->ItemPrice,
                'item_quantity' => $request->ItemQuantity,
                'item_price_per_piece' => $request->ItemPricePerPiece,
                'item_parts_per_piece' => $request->ItemPartsPerPiece,
                'item_price_per_part' => $request->ItemPricePerPart,
                'item_price_per_part_of_piece' => $request->ItemPricePerPartOfPiece,
            ];

            if ($request->has('ItemId') && $request->ItemId != '') {
                // Update existing item
                $item = Item::findOrFail($request->ItemId);
                $item->update($data);
                $message = 'Item updated successfully!';
            } else {
                // Create new item with auto-generated SKU
                $data['sku'] = $this->generateSKU();
                $item = Item::create($data);
                $message = 'Item created successfully!';
            }

            $js = "
                _show_toastr('success', '{$message}', 'Success');
                setTimeout(function() {
                    window.location.href = '" . route('inventory.list') . "';
                }, 1500);
            ";

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'js' => $js
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete item
     */
    public function apiItemsDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ItemId' => 'required|exists:items,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $item = Item::findOrFail($request->ItemId);
            $item->delete();

            $js = "
                _show_toastr('success', 'Item deleted successfully!', 'Success');
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            ";

            return response()->json([
                'status' => 'success',
                'message' => 'Item deleted successfully!',
                'js' => $js
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate barcode for an item
     */
    public function apiItemsGenerateBarcode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ItemId' => 'required|exists:items,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $item = Item::findOrFail($request->ItemId);

            // If no SKU exists, generate one
            if (!$item->sku) {
                $item->sku = $this->generateSKU();
                $item->save();
            }

            $generator = new BarcodeGeneratorHTML();
            $barcode = $generator->getBarcode($item->sku, $generator::TYPE_CODE_128);

            return response()->json([
                'status' => 'success',
                'barcode_html' => $barcode,
                'sku' => $item->sku,
                'item_name' => $item->item_name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique SKU
     */
    private function generateSKU()
    {
        do {
            // Generate SKU format: ITEM-YYYYMMDD-XXXX (where XXXX is random)
            $sku = 'ITEM-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
        } while (Item::where('sku', $sku)->exists());

        return $sku;
    }

    /**
     * Regenerate SKU for an item
     */
    public function apiItemsRegenerateSKU(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ItemId' => 'required|exists:items,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $item = Item::findOrFail($request->ItemId);
            $item->sku = $this->generateSKU();
            $item->save();

            $js = "
                _show_toastr('success', 'SKU regenerated successfully!', 'Success');
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            ";

            return response()->json([
                'status' => 'success',
                'message' => 'SKU regenerated successfully!',
                'sku' => $item->sku,
                'js' => $js
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download single barcode as PDF
     */
    public function downloadBarcodePDF($id)
    {
        try {
            $item = Item::findOrFail($id);

            // If no SKU exists, generate one
            if (!$item->sku) {
                $item->sku = $this->generateSKU();
                $item->save();
            }

            $generator = new BarcodeGeneratorHTML();
            $barcode = $generator->getBarcode($item->sku, $generator::TYPE_CODE_128, 3, 60);

            $data = [
                'items' => [
                    [
                        'name' => $item->item_name,
                        'sku' => $item->sku,
                        'barcode' => $barcode,
                        'price' => number_format($item->item_price, 2)
                    ]
                ],
                'title' => 'Barcode - ' . $item->item_name
            ];

            $pdf = PDF::loadView('inventory.barcode-pdf', $data);
            $pdf->setPaper('a4', 'portrait');
            
            return $pdf->download('barcode-' . $item->sku . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download multiple barcodes as PDF
     */
    public function downloadMultipleBarcodePDF(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_ids' => 'required|array|min:1',
                'item_ids.*' => 'exists:items,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please select at least one item'
                ], 400);
            }

            $items = Item::whereIn('id', $request->item_ids)->get();
            
            if ($items->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No items found'
                ], 404);
            }

            $generator = new BarcodeGeneratorHTML();
            $barcodeData = [];

            foreach ($items as $item) {
                // Generate SKU if not exists
                if (!$item->sku) {
                    $item->sku = $this->generateSKU();
                    $item->save();
                }

                $barcode = $generator->getBarcode($item->sku, $generator::TYPE_CODE_128, 3, 60);
                
                $barcodeData[] = [
                    'name' => $item->item_name,
                    'sku' => $item->sku,
                    'barcode' => $barcode,
                    'price' => number_format($item->item_price, 2)
                ];
            }

            $data = [
                'items' => $barcodeData,
                'title' => 'Barcodes - ' . count($barcodeData) . ' Items'
            ];

            $pdf = PDF::loadView('inventory.barcode-pdf', $data);
            $pdf->setPaper('a4', 'portrait');
            
            $filename = 'barcodes-' . date('Ymd-His') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}