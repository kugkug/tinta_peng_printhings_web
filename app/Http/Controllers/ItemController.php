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
            
            $data = $items->map(function (Item $item) {
                $totalPrice = (float) $item->price_without_shipping_fee + (float) $item->estimated_shipping_fee;

                return [
                    'id' => $item->id,
                    'sku' => $item->sku ?? 'N/A',
                    'brand' => $item->brand ?? '',
                    'item_name' => $item->item_name,
                    'variant_one' => $item->variant_one ?? '',
                    'variant_two' => $item->variant_two ?? '',
                    'size' => $item->size ?? '',
                    'microns' => $item->microns ?? '',
                    'gsm' => $item->gsm ?? '',
                    'sheets_per_pack' => $item->sheets_per_pack,
                    'price_without_shipping_fee' => number_format((float) $item->price_without_shipping_fee, 2),
                    'estimated_shipping_fee' => number_format((float) $item->estimated_shipping_fee, 2),
                    'total_price' => number_format($totalPrice, 2),
                    'item_quantity' => $item->item_quantity ?? 0,
                    'item_price' => $item->item_price ?? 0,
                    'item_price_per_piece' => $item->item_price_per_piece ?? 0,
                    'item_price_per_part' => $item->item_price_per_part ?? 0,
                    'date_purchased' => optional($item->date_purchased)->format('Y-m-d'),
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray();
            
            return response()->json([
                'status' => 'success',
                'data' => $data,
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
                    'brand' => $item->brand,
                    'item_name' => $item->item_name,
                    'variant_one' => $item->variant_one,
                    'variant_two' => $item->variant_two,
                    'size' => $item->size,
                    'microns' => $item->microns,
                    'gsm' => $item->gsm,
                    'sheets_per_pack' => $item->sheets_per_pack,
                    'price_without_shipping_fee' => $item->price_without_shipping_fee,
                    'estimated_shipping_fee' => $item->estimated_shipping_fee,
                    'date_purchased' => optional($item->date_purchased)->format('Y-m-d'),
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
                'Brand' => 'required|string|max:255',
                'ItemName' => 'required|string|max:255',
                'VariantOne' => 'nullable|string|max:255',
                'VariantTwo' => 'nullable|string|max:255',
                'Size' => 'nullable|string|max:255',
                'Microns' => 'nullable|string|max:255',
                'Gsm' => 'nullable|string|max:255',
                'SheetsPerPack' => 'nullable|integer|min:0',
                'PriceWithoutShippingFee' => 'required|numeric|min:0',
                'EstimatedShippingFee' => 'nullable|numeric|min:0',
                'DatePurchased' => 'nullable|date',
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
                'brand' => $request->Brand,
                'item_name' => $request->ItemName,
                'variant_one' => $request->VariantOne,
                'variant_two' => $request->VariantTwo,
                'size' => $request->Size,
                'microns' => $request->Microns,
                'gsm' => $request->Gsm,
                'sheets_per_pack' => $request->SheetsPerPack !== null && $request->SheetsPerPack !== '' ? (int) $request->SheetsPerPack : null,
                'price_without_shipping_fee' => $request->PriceWithoutShippingFee,
                'estimated_shipping_fee' => $request->EstimatedShippingFee !== null && $request->EstimatedShippingFee !== '' ? $request->EstimatedShippingFee : 0,
                'date_purchased' => $request->DatePurchased ?: null,
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

            $totalPrice = (float) $item->price_without_shipping_fee + (float) $item->estimated_shipping_fee;

            $data = [
                'items' => [
                    [
                        'name' => $item->item_name,
                        'sku' => $item->sku,
                        'barcode' => $barcode,
                        'price' => number_format($totalPrice, 2),
                        'brand' => $item->brand,
                        'variant' => trim(($item->variant_one ? $item->variant_one : '') . ' ' . ($item->variant_two ? $item->variant_two : '')),
                        'price_without_shipping_fee' => number_format((float) $item->price_without_shipping_fee, 2),
                        'estimated_shipping_fee' => number_format((float) $item->estimated_shipping_fee, 2),
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
                    'price' => number_format((float) $item->price_without_shipping_fee + (float) $item->estimated_shipping_fee, 2),
                    'brand' => $item->brand,
                    'variant' => trim(($item->variant_one ? $item->variant_one : '') . ' ' . ($item->variant_two ? $item->variant_two : '')),
                    'price_without_shipping_fee' => number_format((float) $item->price_without_shipping_fee, 2),
                    'estimated_shipping_fee' => number_format((float) $item->estimated_shipping_fee, 2),
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