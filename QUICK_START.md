# Quick Start Guide

## Setup Instructions (5 minutes)

### Step 1: Verify Database Configuration

Make sure your `.env` file has the correct database settings. The default uses SQLite:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database/database.sqlite
```

### Step 2: Run Migrations

The migrations have already been run, but if you need to reset:

```bash
# Fresh migration (warning: deletes all data)
php artisan migrate:fresh

# Or just run pending migrations
php artisan migrate
```

### Step 3: Start the Server

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

### Step 4: Access the Inventory

1. Open your browser to `http://localhost:8000`
2. Click **"Inventory"** in the sidebar
3. Click **"Add New"** to create your first item

## Testing the Features

### Test 1: Create an Item

1. Navigate to Inventory → Add New
2. Fill in the form:
    - **Item Name**: "Sample Product"
    - **Description**: "This is a test product"
    - **Bundle Price**: 100.00
    - **Packs Per Bundle**: 10
    - **Price Per Pack**: 10.00
    - **Parts Per Piece**: 5
    - **Price Per Part**: 2.00
    - **Price Per Part of Piece**: 0.40
3. Click **"Save"**
4. You should be redirected to the inventory list with a success message

### Test 2: View Barcode

1. In the inventory list, find your item
2. Click the **blue barcode icon**
3. A modal should appear showing:
    - The item name
    - A barcode image
    - The auto-generated SKU (e.g., ITEM-20251027-A3F2)
4. Try clicking **"Print"** to print the barcode

### Test 3: Edit an Item

1. Click the **blue pencil icon** on any item
2. Modify some fields (e.g., change the price)
3. Click **"Save"**
4. Verify the changes appear in the list

### Test 4: Delete an Item

1. Click the **red trash icon** on any item
2. Confirm the deletion in the popup
3. The item should disappear from the list

## Features Overview

### ✅ Completed Features

-   [x] Add new items with automatic SKU generation
-   [x] Edit existing items
-   [x] Delete items with confirmation
-   [x] View all items in a sortable, searchable DataTable
-   [x] Generate barcodes (CODE-128 format)
-   [x] Print barcodes
-   [x] Responsive design (works on mobile/tablet/desktop)
-   [x] Form validation (client and server-side)
-   [x] Toast notifications for user feedback
-   [x] Unique SKU constraint

## Barcode Format

The system uses **CODE-128** barcodes, which are:

-   Industry standard
-   Scannable by most barcode readers
-   Compact and efficient
-   Support alphanumeric characters

## SKU Format

Auto-generated SKUs follow this pattern:

```
ITEM-[DATE]-[RANDOM]

Example: ITEM-20251027-3A8F
```

Where:

-   `ITEM` = Prefix (customizable)
-   `20251027` = Date (YYYYMMDD)
-   `3A8F` = Random 4-character hash (ensures uniqueness)

## Common Tasks

### Adding Multiple Items Quickly

1. Go to Inventory → Add New
2. Fill in the form and click "Save"
3. You'll be redirected to the list
4. Click "Add New" again to add another

### Printing Multiple Barcodes

1. Open each item's barcode in a new tab
2. Use your browser's print function
3. Each barcode displays the item name and SKU

### Searching for Items

-   Use the search box in the DataTable
-   Search works across all columns (name, SKU, price, etc.)

### Sorting Items

-   Click any column header to sort
-   Click again to reverse sort order

## Troubleshooting

### "No items found" message

-   This is normal if you haven't added any items yet
-   Click "Add New" to create your first item

### Barcode modal doesn't open

1. Check browser console (F12) for errors
2. Ensure jQuery and Bootstrap JS are loaded
3. Try refreshing the page

### Save button doesn't work

1. Check all required fields are filled (marked with \*)
2. Ensure prices are valid numbers
3. Check browser console for validation errors

### SKU not generated

-   SKU is generated automatically when saving a new item
-   You don't need to enter it manually
-   If editing an existing item, the SKU won't change

## Database Structure

The `items` table includes:

-   `id` - Primary key
-   `sku` - Unique identifier (auto-generated)
-   `item_name` - Required
-   `item_description` - Optional
-   `item_price` - Bundle price
-   `item_quantity` - Packs per bundle
-   `item_price_per_piece` - Price per pack
-   `item_parts_per_piece` - Parts per piece
-   `item_price_per_part` - Price per part
-   `item_price_per_part_of_piece` - Price per part of piece
-   `created_at` - Timestamp
-   `updated_at` - Timestamp

## Next Steps

1. **Customize the SKU format**: Edit `ItemController.php`
2. **Add item images**: Extend the form and database
3. **Export to Excel**: Add export functionality
4. **Add categories**: Create a categories system
5. **Low stock alerts**: Add quantity threshold notifications

## Need Help?

-   Check `INVENTORY_SYSTEM_GUIDE.md` for detailed documentation
-   Review Laravel logs: `storage/logs/laravel.log`
-   Check browser console: Press F12 → Console tab

## API Reference

All endpoints require CSRF token in headers:

```javascript
headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') }
```

### Get All Items

```
POST /api/items/list
Response: { status: 'success', data: [...] }
```

### Get Single Item

```
POST /api/items/get
Data: { ItemId: 1 }
Response: { status: 'success', data: {...} }
```

### Save Item

```
POST /api/items/save
Data: { ItemName, ItemPrice, ItemQuantity, ... }
Response: { status: 'success', message: '...', js: '...' }
```

### Delete Item

```
POST /api/items/delete
Data: { ItemId: 1 }
Response: { status: 'success', message: '...', js: '...' }
```

### Generate Barcode

```
POST /api/items/generate-barcode
Data: { ItemId: 1 }
Response: { status: 'success', barcode_html: '...', sku: '...', item_name: '...' }
```

---

**Enjoy your new inventory management system!** 🎉
