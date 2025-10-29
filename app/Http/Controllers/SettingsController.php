<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function apiSettingsUnitsList() {
        try {
            $tbody = "";
            $units = Setting::where('type', 'unit')->get();
            if ($units->count() > 0) {
                $arr_units = $units->toArray();
                foreach ($arr_units as $unit) {
                    $tbody .= "<tr>
                        <td>{$unit['unit']}</td>
                        <td>{$unit['initial']}</td>
                        <td><button class='btn btn-sm btn-danger' data-trigger='delete-unit' data-id='{$unit['id']}'>Delete</button>
                    </tr>";
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Units fetched successfully',
                'data' => $tbody,
                'js' => "$('#settings-units-tbody').html(\"" . preg_replace('/\s+/', ' ', $tbody) . "\"); _init_acitions();"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Units fetching failed',
                'data' => $e->getMessage()
            ]);
        }
    }

    public function apiSettingsUnitsSave(Request $request) {

        try {
            $validated = $request->validate([
                'unit' => 'required|string|max:150',
                'initial' => 'required|string|max:50',
            ]);

            if ($validated) {
                $validated['type'] = 'unit';
                $unit = Setting::create($validated);
                return response()->json([
                    'success' => true, 
                    'message' => 'Unit created successfully', 
                    'data' => $unit, 'js' => "_fetchSettings();"
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Unit creation failed', 'data' => $validated]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Unit creation failed', 'data' => $e->getMessage()]);
        }
    }

    public function apiSettingsUnitsDelete(Request $request) {
        try {
            $unit = Setting::find($request->id);
            $unit->delete();
            return response()->json(['success' => true, 'message' => 'Unit deleted successfully', 'data' => $unit, 'js' => "_fetchSettings();"]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Unit deletion failed', 'data' => $e->getMessage()]);
        }
    }   
}