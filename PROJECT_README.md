# Tinta Peng Printhings - Inventory Management System

A complete web-based inventory management system with barcode generation for managing your online store items.

## 🚀 Quick Start

```bash
# 1. Make sure migrations are run
php artisan migrate

# 2. Start the development server
php artisan serve

# 3. Open your browser
http://localhost:8000
```

## 📋 What You Can Do

-   ✅ **Add Items**: Create new inventory items with detailed pricing information
-   ✅ **Edit Items**: Update existing item details anytime
-   ✅ **Delete Items**: Remove items you no longer need
-   ✅ **View Items**: Browse all items in a searchable, sortable table
-   ✅ **Generate Barcodes**: Automatically create scannable barcodes for each item
-   ✅ **Print Barcodes**: Print barcodes directly from your browser
-   ✅ **Automatic SKU**: Every item gets a unique SKU automatically

## 📖 Documentation

We've created comprehensive documentation to help you:

### 1. **QUICK_START.md** - Start Here!

For new users, this guide walks you through:

-   Setting up the application
-   Creating your first item
-   Testing all features
-   Common tasks

### 2. **INVENTORY_SYSTEM_GUIDE.md** - Complete Reference

Detailed documentation covering:

-   All features explained
-   File structure
-   API endpoints
-   Customization options
-   Troubleshooting

### 3. **IMPLEMENTATION_SUMMARY.md** - Technical Details

For developers and technical users:

-   Implementation details
-   Code structure
-   Database schema
-   Security features

## 🎯 Main Features

### Inventory Management

-   Add, edit, delete items
-   Track pricing at multiple levels (bundle, pack, piece, part)
-   Store item descriptions
-   Automatic timestamps

### Barcode System

-   Auto-generated unique SKUs (e.g., `ITEM-20251027-3A8F`)
-   CODE-128 barcode format
-   One-click barcode generation
-   Print-ready barcodes

### User Interface

-   Clean, modern design
-   Responsive (works on phone, tablet, desktop)
-   Real-time search and filtering
-   Toast notifications for feedback
-   Confirmation dialogs for safety

## 📊 Item Information Tracked

Each item includes:

-   **Name & Description**: What the item is
-   **Bundle Price**: Total price for the bundle
-   **Packs Per Bundle**: Quantity in each bundle
-   **Price Per Pack**: Individual pack pricing
-   **Parts Per Piece**: How many parts in each piece
-   **Price Per Part**: Individual part pricing
-   **Price Per Part of Piece**: Calculated part pricing
-   **SKU**: Unique identifier (auto-generated)
-   **Barcode**: Scannable barcode (auto-generated)

## 🛠️ Technology Stack

-   **Backend**: Laravel 12 (PHP 8.2+)
-   **Frontend**: jQuery, Bootstrap 4
-   **Database**: SQLite (can use MySQL/PostgreSQL)
-   **Barcode**: picqer/php-barcode-generator
-   **UI Components**: DataTables, SweetAlert, Toastr

## 📱 Screenshots & Usage

### Viewing Inventory

Navigate to **Inventory** in the sidebar to see all your items in a table with:

-   Search functionality
-   Sort by any column
-   Pagination
-   Actions (Edit, Delete, Barcode)

### Adding New Items

1. Click **"Add New"** button
2. Fill in item details
3. Click **"Save"**
4. Item is created with automatic SKU

### Generating Barcodes

1. Click the **barcode icon** (blue button) on any item
2. Barcode appears in a popup
3. Print directly or close

### Editing Items

1. Click the **edit icon** (blue pencil) on any item
2. Modify the details
3. Click **"Save"**

### Deleting Items

1. Click the **delete icon** (red trash) on any item
2. Confirm deletion
3. Item is removed

## 🔐 Security Features

-   CSRF protection on all forms
-   Server-side validation
-   SQL injection protection
-   XSS protection
-   Unique SKU constraint

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── ItemController.php       # Item CRUD operations
│   └── ModuleController.php     # Page rendering
├── Models/
│   └── Item.php                 # Item model

database/migrations/
├── 2025_08_12_131902_create_items_table.php
└── 2025_10_27_113622_add_sku_to_items_table.php

resources/views/
├── inventory/
│   ├── list.blade.php           # Item listing
│   └── add.blade.php            # Add/Edit form
└── partials/
    └── auth/                    # Header, Footer, Sidebar

public/assets/app/js/
├── inventory/
│   ├── list.js                  # List page logic
│   └── add.js                   # Form logic
└── main-scripts.js              # Shared utilities

routes/
└── web.php                      # All routes
```

## 🎓 Learning Resources

### For Users

Start with **QUICK_START.md** - it has step-by-step instructions.

### For Developers

Check **INVENTORY_SYSTEM_GUIDE.md** for technical details and customization.

### For Management

Read **IMPLEMENTATION_SUMMARY.md** to understand what was built.

## 🐛 Troubleshooting

### Common Issues

**"No items found"**

-   Normal if you haven't added items yet
-   Click "Add New" to create your first item

**Barcode doesn't display**

-   Check that picqer/php-barcode-generator is installed
-   Run: `composer install`

**Save button doesn't work**

-   Fill in all required fields (marked with \*)
-   Check console (F12) for errors

**Can't access the site**

-   Make sure `php artisan serve` is running
-   Check you're accessing `http://localhost:8000`

For more troubleshooting, see the **INVENTORY_SYSTEM_GUIDE.md**.

## 📈 What's Next?

The system is fully functional and ready to use. Possible enhancements:

1. **Add item images** - Upload photos of items
2. **Categories** - Organize items into categories
3. **Export data** - Export inventory to Excel/CSV
4. **Stock alerts** - Get notified when stock is low
5. **Barcode scanning** - Scan barcodes to look up items
6. **Multi-user** - Add user accounts and permissions

## 💡 Tips

1. **Use the search box** - Quickly find items in the table
2. **Sort columns** - Click column headers to sort
3. **Print barcodes** - Use the print button in the barcode modal
4. **Edit carefully** - SKU won't change when editing (unless regenerated)
5. **Back up data** - Regularly backup your database file

## 📞 Support

-   Check the documentation files
-   Review Laravel logs: `storage/logs/laravel.log`
-   Check browser console: Press F12

## 📄 License

Built on Laravel framework - MIT License

## 🎉 Ready to Go!

Your inventory management system is ready to use. Start by:

1. Opening `http://localhost:8000`
2. Clicking "Inventory" in the sidebar
3. Adding your first item

**Happy inventory managing!** 📦🔖

---

_For detailed instructions, see QUICK_START.md_  
_For complete documentation, see INVENTORY_SYSTEM_GUIDE.md_  
_For technical details, see IMPLEMENTATION_SUMMARY.md_
