# Products Module - Quick Start Guide

## 🚀 Getting Started in 5 Minutes

### Step 1: Access the Products Module

1. Look at the sidebar menu on the left
2. Click on **"Products"** (icon with cubes)
3. You'll see the products list page

### Step 2: Create Your First Product

#### Click "Add New Product" button (green button on the right)

Fill in the form:

**Basic Information:**

-   **Product Code**: Click the "Generate" button to auto-create a unique code
    -   Or enter your own custom code (e.g., "SHIRT-001")
-   **Product Name**: Enter the name (e.g., "Custom T-Shirt")
-   **Description**: Add any details (optional)

**Add Items (Bill of Materials):**

1. Click the **"+ Add Item"** button
2. Select an item from the dropdown (must have items in inventory first!)
3. Enter the **quantity** needed (e.g., 2.5)
4. The **unit cost** is automatically filled from the item's price
5. Click **"+ Add Item"** again to add more items
6. To remove an item, click the red trash icon

**Save:**

-   Review the **Total Cost** at the bottom
-   Click **"Save Product"**
-   Done! You'll be redirected to the products list

### Step 3: View Your Product

1. In the products list, find your product
2. Click the **blue eye icon** to view details
3. A popup shows:
    - Product information
    - Complete list of items used
    - Total cost breakdown

### Step 4: Edit or Delete

-   **Edit**: Click the blue pencil icon → Make changes → Click "Update Product"
-   **Delete**: Click the red trash icon → Confirm deletion

## 🎯 Real-World Example

Let's say you make custom printed t-shirts:

### Product Setup:

-   **Product Code**: `SHIRT-CUSTOM-RED`
-   **Product Name**: `Custom Red Printed T-Shirt`
-   **Description**: `White t-shirt with red custom print`

### Bill of Materials:

1. **Plain White T-Shirt** - Quantity: 1 - Cost: ₱150.00
2. **Red Fabric Ink** - Quantity: 0.1 (liters) - Cost: ₱50.00
3. **Printing Service** - Quantity: 1 - Cost: ₱100.00

### Result:

-   **Total Cost**: ₱300.00 (automatically calculated)
-   Product saved and ready to track!

## 💡 Pro Tips

### Tip 1: Reusable Product Codes

If you make the same product multiple times:

1. Use the same product code (e.g., `SHIRT-CUSTOM-RED`)
2. The system allows duplicate codes
3. Each instance is tracked separately
4. Great for recurring products!

### Tip 2: Check Before Creating

Before saving a new product:

1. Enter the product code
2. Click **"Check"** button
3. See if the code already exists
4. If it does, you can see what product uses it
5. Decide if you want to reuse the code or create a new one

### Tip 3: Use Meaningful Codes

Instead of auto-generated codes like `PROD-250127-A1B2`, use:

-   `SHIRT-RED-M` (Red shirt, medium size)
-   `WIDGET-V2` (Widget version 2)
-   `CUSTOM-001` (Custom order 001)

This makes products easier to find and manage!

## ⚠️ Important Notes

### Before You Start:

-   **You must have items in your inventory first!**
-   If the item dropdown is empty, go to Inventory and add items

### Required Fields:

-   ✅ Product Name (required)
-   ✅ At least one item in the BOM (required)
-   ⭕ Product Code (optional - auto-generated if empty)
-   ⭕ Description (optional)

### Common Issues:

**Can't save product?**

-   Make sure you added at least one item
-   Check that all quantities are greater than 0
-   Ensure product name is filled in

**Items not showing?**

-   Go to Inventory module first
-   Add some inventory items
-   Then return to create products

**Product code warning?**

-   This is just informational
-   You can still use duplicate codes if you want
-   Each product entry remains separate

## 📊 What You Can Track

With this module you can:

-   ✅ See all your products in one place
-   ✅ Know exactly what items are needed for each product
-   ✅ Track the total cost of making each product
-   ✅ Reuse product codes for similar items
-   ✅ View detailed breakdowns anytime
-   ✅ Update products when costs or items change

## 🎨 Interface Guide

### Products List Page

-   **DataTable**: Search, sort, and filter products
-   **Product Code Badge**: Blue badge showing the code
-   **Items Count**: How many inventory items are used
-   **Total Cost**: Green text showing cost
-   **Actions**: View (eye), Edit (pencil), Delete (trash)

### Add/Edit Page

-   **Code Generator**: Creates unique codes automatically
-   **Code Checker**: Verifies if code exists
-   **Item Rows**: Dynamic - add as many as needed
-   **Real-time Calculation**: Total updates as you add items
-   **Template System**: Uses HTML template for clean item rows

## 🔗 Navigation

-   **Home** → Main dashboard
-   **Inventory** → Manage inventory items (components)
-   **Products** → Manage end products (what you make)
-   **Settings** → System settings

## 📱 Mobile Friendly

The interface is responsive and works on:

-   Desktop computers
-   Tablets
-   Mobile phones

## ⌨️ Keyboard Shortcuts

-   **Tab**: Move between fields
-   **Enter**: Submit form (when focused on submit button)
-   **Escape**: Close modal popups

## 🆘 Need Help?

If you encounter issues:

1. Check the `PRODUCTS_MODULE_GUIDE.md` for detailed instructions
2. Check the `PRODUCTS_IMPLEMENTATION_SUMMARY.md` for technical details
3. Verify you have inventory items created first
4. Check browser console for JavaScript errors

## 🎉 You're Ready!

That's it! You now know how to:

-   ✅ Create products with multiple items
-   ✅ Generate and reuse product codes
-   ✅ Track product costs automatically
-   ✅ View, edit, and delete products

Start creating your first product now! 🚀

---

**Quick Reference Card**

| Action               | How To                            |
| -------------------- | --------------------------------- |
| View all products    | Click "Products" in sidebar       |
| Add new product      | Click "Add New Product" button    |
| Generate code        | Click "Generate" button in form   |
| Check code           | Enter code → Click "Check" button |
| Add item to product  | Click "+ Add Item" button         |
| Remove item          | Click red trash icon on item row  |
| View product details | Click blue eye icon in list       |
| Edit product         | Click blue pencil icon in list    |
| Delete product       | Click red trash icon in list      |

---

**Need to see it in action?** Just navigate to `/products` in your browser!
