<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProductController;


Route::get('/', [ModuleController::class, 'index'])->name('home');

Route::group(['prefix' => 'inventory'], function () {
    Route::get('/', [ModuleController::class, 'inventoryList'])->name('inventory.list');
    Route::get('/add', [ModuleController::class, 'inventoryAdd'])->name('inventory.add');
    Route::get('/edit/{id}', [ModuleController::class, 'inventoryEdit'])->name('inventory.edit');
    Route::get('/barcode/pdf/{id}', [ItemController::class, 'downloadBarcodePDF'])->name('inventory.barcode.pdf');
    Route::post('/barcode/pdf/multiple', [ItemController::class, 'downloadMultipleBarcodePDF'])->name('inventory.barcode.pdf.multiple');
});

Route::group(['prefix' => 'products'], function () {
    Route::get('/', [ModuleController::class, 'productsList'])->name('products.list');
    Route::get('/add', [ModuleController::class, 'productsAdd'])->name('products.add');
    Route::get('/edit/{id}', [ModuleController::class, 'productsEdit'])->name('products.edit');
});

Route::group(['prefix' => 'settings'], function () {
    Route::get('/', [ModuleController::class, 'settingsIndex'])->name('settings.index');
});

Route::group(['prefix' => 'api'], function () {
    // Items API
    Route::post('/items/list', [ItemController::class, 'apiItemsList'])->name('api.items.list');
    Route::post('/items/get', [ItemController::class, 'apiItemsGet'])->name('api.items.get');
    Route::post('/items/save', [ItemController::class, 'apiItemsSave'])->name('api.items.save');
    Route::post('/items/delete', [ItemController::class, 'apiItemsDelete'])->name('api.items.delete');
    Route::post('/items/generate-barcode', [ItemController::class, 'apiItemsGenerateBarcode'])->name('api.items.generate-barcode');
    Route::post('/items/regenerate-sku', [ItemController::class, 'apiItemsRegenerateSKU'])->name('api.items.regenerate-sku');
    
    // Products API
    Route::post('/products/list', [ProductController::class, 'apiProductsList'])->name('api.products.list');
    Route::post('/products/get', [ProductController::class, 'apiProductsGet'])->name('api.products.get');
    Route::post('/products/save', [ProductController::class, 'apiProductsSave'])->name('api.products.save');
    Route::post('/products/delete', [ProductController::class, 'apiProductsDelete'])->name('api.products.delete');
    Route::post('/products/generate-code', [ProductController::class, 'apiProductsGenerateCode'])->name('api.products.generate-code');
    Route::post('/products/check-code', [ProductController::class, 'apiProductsCheckCode'])->name('api.products.check-code');
    Route::post('/products/history/list', [ProductController::class, 'apiProductsHistoryList'])->name('api.products.history.list');
    
    // Settings API
    Route::post('/settings/units/list', [SettingsController::class, 'apiSettingsUnitsList'])->name('api.settings.units.list');
    Route::post('/settings/units/save', [SettingsController::class, 'apiSettingsUnitsSave'])->name('api.settings.units.save');
    Route::post('/settings/units/delete', [SettingsController::class, 'apiSettingsUnitsDelete'])->name('api.settings.units.delete');
});