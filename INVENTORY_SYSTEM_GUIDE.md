# Inventory Management System with Barcode Generation

## Overview

This is a complete inventory management web application built with Laravel, jQuery, and integrated barcode generation. The system allows you to manage items in your online store with automatic SKU generation and barcode creation.

## Features

### 1. **Item Management (CRUD Operations)**

-   **Add Items**: Create new inventory items with detailed information
-   **Edit Items**: Update existing item details
-   **Delete Items**: Remove items from inventory with confirmation
-   **View Items**: Display all items in a responsive DataTable

### 2. **Automatic SKU Generation**

-   Each item automatically gets a unique SKU when created
-   SKU Format: `ITEM-YYYYMMDD-XXXX` (e.g., `ITEM-20251027-A3F2`)
-   SKUs can be regenerated if needed

### 3. **Barcode Generation**

-   Generate barcodes for any item with a click
-   Uses CODE-128 barcode format (industry standard)
-   View barcodes in a modal dialog
-   Print barcodes directly from the browser

### 4. **Item Details Tracked**

-   Item Name and Description
-   Bundle Price (total price of the item bundle)
-   Packs Per Bundle (quantity in each bundle)
-   Price Per Pack
-   Parts Per Piece
-   Price Per Part
-   Price Per Part of Piece

## Installation & Setup

### 1. Run Database Migrations

```bash
php artisan migrate
```

This will create the necessary database tables including:

-   `items` table with all required fields
-   `sku` field for unique item identification
-   `item_description` field for detailed descriptions

### 2. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Usage Guide

### Accessing the Inventory System

1. Navigate to **Inventory** from the sidebar menu
2. You'll see a list of all items in a DataTable

### Adding a New Item

1. Click the **"Add New"** button in the top right
2. Fill in the item details:
    - **Item Name**: Required - Name or title of the item
    - **Description**: Optional - Detailed description
    - **Bundle Price**: Required - Price of the complete bundle
    - **Packs Per Bundle**: Required - Number of packs in the bundle
    - **Price Per Pack**: Required - Cost per individual pack
    - **Parts Per Piece**: Required - Number of parts in each piece
    - **Price Per Part**: Required - Cost per individual part
    - **Price Per Part of Piece**: Required - Cost calculation per part of piece
3. Click **"Save"** to create the item
4. A unique SKU will be automatically generated

### Editing an Item

1. From the inventory list, click the **Edit** button (blue pencil icon)
2. Modify the item details as needed
3. Click **"Save"** to update
4. Click **"Clear"** to reset to original values

### Deleting an Item

1. From the inventory list, click the **Delete** button (red trash icon)
2. Confirm the deletion in the popup dialog
3. The item will be permanently removed

### Generating and Viewing Barcodes

1. From the inventory list, click the **Barcode** button (blue barcode icon)
2. A modal will display:
    - Item name
    - Generated barcode
    - SKU number
3. Options:
    - **Print**: Print the barcode directly
    - **Close**: Close the modal

## File Structure

### Backend (Laravel)

```
app/
├── Http/Controllers/
│   ├── ItemController.php       # Handles all item CRUD operations
│   └── ModuleController.php     # Handles view rendering
├── Models/
│   └── Item.php                 # Item model with fillable fields
database/
└── migrations/
    ├── 2025_08_12_131902_create_items_table.php
    └── 2025_10_27_113622_add_sku_to_items_table.php
```

### Frontend (Views & JavaScript)

```
resources/views/
├── inventory/
│   ├── list.blade.php           # Inventory listing page
│   └── add.blade.php            # Add/Edit form page
└── partials/
    └── auth/
        ├── header.blade.php     # Header with navigation
        ├── footer.blade.php     # Footer with scripts
        └── sidebar.blade.php    # Sidebar navigation

public/assets/app/js/
├── inventory/
│   ├── list.js                  # DataTable and CRUD operations
│   └── add.js                   # Form handling and validation
└── main-scripts.js              # Utility functions
```

## API Endpoints

All API endpoints use POST method and require CSRF token:

| Endpoint                      | Purpose            | Parameters                      |
| ----------------------------- | ------------------ | ------------------------------- |
| `/api/items/list`             | Get all items      | None                            |
| `/api/items/get`              | Get single item    | `ItemId`                        |
| `/api/items/save`             | Create/Update item | Item fields + optional `ItemId` |
| `/api/items/delete`           | Delete item        | `ItemId`                        |
| `/api/items/generate-barcode` | Generate barcode   | `ItemId`                        |
| `/api/items/regenerate-sku`   | Regenerate SKU     | `ItemId`                        |

## Technologies Used

-   **Backend**: Laravel 12
-   **Frontend**: jQuery, Bootstrap 4
-   **Database**: SQLite (configurable to MySQL/PostgreSQL)
-   **Barcode Generation**: picqer/php-barcode-generator (CODE-128)
-   **DataTables**: For responsive item listing
-   **SweetAlert**: For confirmation dialogs
-   **Toastr**: For notification messages

## Key Features Explained

### 1. Automatic SKU Generation

When you create a new item, the system automatically generates a unique SKU:

-   Format: `ITEM-[DATE]-[RANDOM]`
-   Example: `ITEM-20251027-3A8F`
-   Ensures no duplicate SKUs in the database

### 2. Barcode Standards

-   Uses CODE-128 barcode format
-   Industry-standard, scannable by most barcode readers
-   HTML-based rendering for easy printing

### 3. Responsive Design

-   Works on desktop, tablet, and mobile devices
-   DataTables automatically adapts to screen size
-   Mobile-friendly forms

### 4. Form Validation

-   Client-side validation for required fields
-   Server-side validation for data integrity
-   Numeric validation for prices and quantities
-   User-friendly error messages

## Customization

### Changing SKU Format

Edit `ItemController.php`, method `generateSKU()`:

```php
private function generateSKU()
{
    // Customize the SKU format here
    $sku = 'ITEM-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
    return $sku;
}
```

### Changing Barcode Type

In `ItemController.php`, method `apiItemsGenerateBarcode()`:

```php
$generator = new BarcodeGeneratorHTML();
// Change TYPE_CODE_128 to other types:
// TYPE_CODE_39, TYPE_EAN_13, TYPE_UPC_A, etc.
$barcode = $generator->getBarcode($item->sku, $generator::TYPE_CODE_128);
```

## Troubleshooting

### Barcode Not Displaying

-   Ensure the picqer/php-barcode-generator package is installed
-   Run: `composer require picqer/php-barcode-generator`

### DataTable Not Loading

-   Check browser console for JavaScript errors
-   Ensure jQuery and DataTables libraries are loaded
-   Verify CSRF token is present in meta tags

### Items Not Saving

-   Check Laravel logs: `storage/logs/laravel.log`
-   Verify database connection in `.env`
-   Ensure all required fields are filled

## Security Features

-   CSRF token protection on all API endpoints
-   Server-side validation on all inputs
-   Unique constraint on SKU field
-   SQL injection protection via Eloquent ORM

## Future Enhancements

Possible additions to consider:

-   Export items to CSV/Excel
-   Bulk barcode printing
-   Low stock alerts
-   Item categories/tags
-   Image upload for items
-   Barcode scanning for quick lookup
-   Stock adjustment history
-   Multi-currency support

## Support

For issues or questions:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console for JavaScript errors
3. Verify database migrations are run
4. Ensure all dependencies are installed

## License

This inventory system is built on Laravel and follows the MIT license.
