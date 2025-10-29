# Product Template Feature - Load from Existing Products

## 🎯 Overview

The **Load from Existing Product** feature allows you to quickly create new products by using existing products as templates. This is perfect for:

-   Creating multiple instances of the same product
-   Bulk production planning
-   Using similar products as a starting point
-   Rapid product creation with consistent Bill of Materials

## ✨ Key Features

### 1. **Template Loading**

-   Load any existing product's Bill of Materials
-   Automatically populates all items and their quantities
-   Preserves unit costs from the template

### 2. **Quantity Multiplier**

-   Scale production with a simple multiplier
-   Create bulk orders easily (e.g., 10x, 50x, 100x)
-   Perfect for production planning

### 3. **Easy Modifications**

-   Loaded items can be edited after loading
-   Add more items if needed
-   Remove items that aren't required
-   Adjust quantities for specific needs

### 4. **Inventory Integration**

-   Still checks inventory availability
-   Shows real-time stock warnings
-   Prevents creation if insufficient inventory
-   Automatically deducts from inventory when saved

## 🎨 User Interface

### Location

The "Load from Existing Product" section appears on both:

-   **Add Product** page (`/products/add`)
-   **Edit Product** page (`/products/edit/{id}`)

### Interface Elements

**Section Layout:**

```
┌─────────────────────────────────────────────────────────┐
│ 📋 Load from Existing Product (Optional)               │
│                                                         │
│ Use an existing product as a template...               │
│                                                         │
│ [Product Code]  [Multiplier]  [Load] [Clear]          │
│                                                         │
│ Status: ✅ Loaded Product with 3 items (x5)           │
└─────────────────────────────────────────────────────────┘
```

**Input Fields:**

1. **Product Code to Load** - Enter the code of the product you want to use
2. **Quantity Multiplier** - Scale the quantities (default: 1)
3. **Load Items from Product** button - Fetches and loads the items
4. **Clear All Items** button - Removes all items from the form

## 📖 How to Use

### Basic Usage (Single Product)

**Step 1: Enter Product Code**

```
Product Code to Load: SHIRT-CUSTOM-001
Quantity Multiplier: 1
```

**Step 2: Click "Load Items from Product"**

**Result:**

-   All items from SHIRT-CUSTOM-001 are loaded
-   Quantities remain as in the template
-   Items appear in the Bill of Materials section

### Bulk Production (With Multiplier)

**Step 1: Enter Product Code and Multiplier**

```
Product Code to Load: SHIRT-CUSTOM-001
Quantity Multiplier: 10
```

**Step 2: Click "Load Items from Product"**

**Result:**

-   All items are loaded with quantities multiplied by 10
-   Perfect for creating a bulk production order

**Example:**

```
Original Product (SHIRT-CUSTOM-001):
- T-Shirt: 1 unit
- Red Ink: 0.5 units
- Label: 1 unit

After loading with multiplier 10:
- T-Shirt: 10 units
- Red Ink: 5 units
- Label: 10 units
```

## 💡 Use Cases

### Use Case 1: Repeat Orders

**Scenario:** Customer orders the same custom t-shirt every month

**Solution:**

1. Create first product: `SHIRT-CUSTOMER-A`
2. Next month, use "Load from Existing Product"
3. Enter code: `SHIRT-CUSTOMER-A`
4. Multiplier: 1 (or adjust for quantity)
5. Save with new product name or same code

### Use Case 2: Bulk Production

**Scenario:** Need to produce 50 units of a product

**Solution:**

1. Have a base product: `WIDGET-STANDARD`
2. Load from: `WIDGET-STANDARD`
3. Set multiplier: 50
4. System calculates: All items × 50
5. Check inventory availability
6. Save as bulk production order

### Use Case 3: Product Variants

**Scenario:** Create similar product with slight changes

**Solution:**

1. Load from: `SHIRT-RED`
2. Multiplier: 1
3. Items are loaded
4. Manually change: Red Ink → Blue Ink
5. Save as: `SHIRT-BLUE`

### Use Case 4: Production Planning

**Scenario:** Plan materials needed for weekly production

**Solution:**

1. Load from: `PRODUCT-A`
2. Set multiplier: 100 (weekly quantity)
3. Check if inventory is sufficient
4. If not, order more materials
5. Create production batch when ready

## 🔄 Workflow Examples

### Example 1: Simple Template Usage

**Starting Point:**

```
Existing Product: CUSTOM-WIDGET-001
Items:
- Plastic Shell: 2 units
- Screw Set: 1 unit
- Label: 1 unit
Total Cost: ₱150.00
```

**Action:**

```
1. Click "Add New Product"
2. Enter Product Code: CUSTOM-WIDGET-001
3. Multiplier: 1
4. Click "Load Items from Product"
```

**Result:**

```
New Product Form populated with:
- Plastic Shell: 2 units
- Screw Set: 1 unit
- Label: 1 unit

✅ You can now:
- Change product name
- Modify quantities
- Add/remove items
- Save as new product
```

### Example 2: Bulk Production with Multiplier

**Starting Point:**

```
Existing Product: T-SHIRT-PLAIN
Items:
- T-Shirt Blank: 1 unit @ ₱100
- Packaging: 1 unit @ ₱10
Total Cost: ₱110.00
```

**Action:**

```
1. Click "Add New Product"
2. Enter:
   - Product Name: "Bulk Order - Plain T-Shirts (25 units)"
   - Template Code: T-SHIRT-PLAIN
   - Multiplier: 25
3. Click "Load Items from Product"
```

**Result:**

```
Items loaded:
- T-Shirt Blank: 25 units @ ₱100 = ₱2,500.00
- Packaging: 25 units @ ₱10 = ₱250.00
Total Cost: ₱2,750.00

System checks inventory:
✅ T-Shirt Blank: 100 available (need 25) ✓
✅ Packaging: 50 available (need 25) ✓

Ready to save!
```

### Example 3: Modified Template

**Starting Point:**

```
Existing Product: SHIRT-RED
Items:
- White T-Shirt: 1 unit
- Red Ink: 0.2 units
- Print Service: 1 unit
```

**Action:**

```
1. Load from: SHIRT-RED (multiplier: 1)
2. Items are loaded
3. Manually change:
   - Remove: Red Ink
   - Add: Blue Ink (0.2 units)
4. Save as: SHIRT-BLUE
```

**Result:**

```
New Product: SHIRT-BLUE
Items:
- White T-Shirt: 1 unit
- Blue Ink: 0.2 units
- Print Service: 1 unit

✅ Quick variant creation!
```

## ⚠️ Important Notes

### Inventory Checks Still Apply

**Even when loading from a template:**

-   ✅ System checks inventory availability
-   ✅ Shows warnings for insufficient stock
-   ✅ Prevents saving if inventory is too low
-   ✅ Automatically deducts inventory when saved

**Example:**

```
Load Product with multiplier 100
Item needs: 500 units
Available: 300 units

Result: ⚠️ Cannot save - insufficient inventory!
```

### Clearing Items

**"Clear All Items" button:**

-   Removes all items from the Bill of Materials
-   Requires confirmation if items exist
-   Useful for starting fresh
-   Doesn't affect the template product

### Template Product Remains Unchanged

**Important:**

-   Loading a template does NOT modify the original product
-   Template product is read-only in this operation
-   You're creating a NEW product with similar items
-   Original product's inventory is not affected until you save the new product

## 🎓 Best Practices

### 1. Use Meaningful Product Codes

**Good:**

```
SHIRT-CUSTOM-RED
WIDGET-STANDARD-V2
PRODUCT-BULK-001
```

**Why:** Easy to remember and load as templates

### 2. Create Base Templates

**Strategy:**

-   Create "master" products with `.00` suffix
-   Example: `WIDGET-STANDARD.00`
-   Use as templates for actual orders
-   Never modify the master, always create new products from it

### 3. Use Multipliers for Bulk Orders

**Instead of:**

-   Manually entering large quantities
-   Calculating each item × quantity

**Do:**

-   Load template with multiplier
-   System calculates automatically
-   Faster and error-free

### 4. Verify Loaded Items

**After loading:**

-   ✅ Review all items
-   ✅ Check quantities
-   ✅ Verify inventory availability
-   ✅ Adjust if needed before saving

### 5. Document Template Products

**Keep a list:**

```
SHIRT-CUSTOM-RED → Red custom t-shirt template
WIDGET-A → Standard widget configuration
PACKAGE-BASIC → Basic packaging template
```

## 🚫 Common Mistakes to Avoid

### Mistake 1: Forgetting to Change Product Name

**Problem:**

-   Load template
-   Keep same product name
-   Confusing when viewing products

**Solution:**

-   Always update product name after loading
-   Make it descriptive and unique

### Mistake 2: Not Checking Inventory Before Bulk Orders

**Problem:**

-   Load with multiplier 100
-   Inventory only has 50 units
-   Cannot save

**Solution:**

-   Check inventory first
-   Or use lower multiplier
-   Restock if needed

### Mistake 3: Modifying Wrong Product

**Problem:**

-   Want to edit template
-   Accidentally create new product

**Solution:**

-   To edit template: Use "Edit" from products list
-   To create from template: Use "Load from Existing Product"

## 🔧 Technical Details

### How It Works

**Backend Process:**

1. User enters product code
2. System calls `/api/products/check-code`
3. If exists, calls `/api/products/get` for full details
4. Returns product with all items and quantities

**Frontend Process:**

1. Receives product data
2. Clears existing items in form
3. For each item in template:
    - Creates new item row
    - Sets quantity × multiplier
    - Preserves unit cost
    - Calculates subtotal
4. Updates total cost
5. Shows success message

**Inventory Integration:**

-   Template loading does NOT deduct inventory
-   Inventory is only deducted when you save the new product
-   All inventory checks apply normally

### API Endpoints Used

**1. Check Product Code:**

```
POST /api/products/check-code
Body: { ProductCode: "SHIRT-001" }
Response: { exists: true, product: {...} }
```

**2. Get Product Details:**

```
POST /api/products/get
Body: { ProductId: 123 }
Response: { product: {...}, items: [...] }
```

### Data Flow

```
User Input
    ↓
[Product Code] → Check if exists
    ↓
[Product ID] → Load full details
    ↓
[Items Array] → Multiply quantities
    ↓
[UI Update] → Populate form
    ↓
[User Review] → Modify if needed
    ↓
[Save Product] → Create new + deduct inventory
```

## 📊 Example Scenarios

### Scenario A: Monthly Production

**Setup:**

```
Template: PRODUCT-MONTHLY-BASE
Items per unit: 5 different components
Monthly volume: 200 units
```

**Process:**

```
1. Start of month: Load PRODUCT-MONTHLY-BASE
2. Set multiplier: 200
3. System calculates total materials needed
4. Check inventory: All available ✓
5. Save as: PRODUCTION-BATCH-JAN-2025
6. Inventory automatically deducted
7. Production team uses the order
```

### Scenario B: Customer Repeat Order

**Setup:**

```
Customer: ABC Corp
Original order: ORDER-ABC-001 (from 3 months ago)
New order: Same specifications
```

**Process:**

```
1. Load from: ORDER-ABC-001
2. Multiplier: 1 (same quantity)
3. Review items (prices may have changed)
4. Update product name: ORDER-ABC-002
5. Save new order
6. Invoice customer with new order ID
```

### Scenario C: Product Line Creation

**Setup:**

```
Base Product: WIDGET-BASE
Want to create: Color variants (Red, Blue, Green)
```

**Process:**

```
1. Create WIDGET-BASE once (master template)
2. For Red:
   - Load WIDGET-BASE
   - Change: Paint Color → Red
   - Save as: WIDGET-RED
3. For Blue:
   - Load WIDGET-BASE
   - Change: Paint Color → Blue
   - Save as: WIDGET-BLUE
4. Repeat for all variants
```

## 🎉 Benefits

1. **Time Saving**

    - No manual item entry
    - Quick bulk order creation
    - Consistent product creation

2. **Accuracy**

    - No calculation errors
    - Consistent specifications
    - Automated quantity scaling

3. **Flexibility**

    - Start from template
    - Modify as needed
    - Create variants easily

4. **Production Planning**

    - Easy bulk material calculation
    - Quick inventory checks
    - Efficient resource planning

5. **Consistency**
    - Standardized products
    - Reliable BOMs
    - Quality control

## 🆘 Troubleshooting

### Problem: Product code not found

**Solutions:**

-   ✅ Check spelling of product code
-   ✅ Verify product exists in products list
-   ✅ Try copying code from products list

### Problem: Items not loading

**Solutions:**

-   ✅ Refresh the page
-   ✅ Check browser console for errors
-   ✅ Verify product has items in its BOM

### Problem: Multiplier not working

**Solutions:**

-   ✅ Ensure multiplier is a positive number
-   ✅ Check that multiplier field has a value
-   ✅ Try with multiplier = 1 first

### Problem: Insufficient inventory after loading

**Solutions:**

-   ✅ Reduce multiplier value
-   ✅ Add more inventory first
-   ✅ Modify item quantities in the form

## 📚 Related Features

-   **Product Code Generation** - Auto-generate unique codes
-   **Product Code Checking** - Verify code availability
-   **Inventory Management** - Automatic deductions
-   **Bill of Materials** - Item management in products

## 🎯 Quick Reference

| Action               | Steps                                          |
| -------------------- | ---------------------------------------------- |
| Load template (1x)   | Enter code → Click "Load"                      |
| Load for bulk (10x)  | Enter code → Set multiplier: 10 → Click "Load" |
| Clear loaded items   | Click "Clear All Items" → Confirm              |
| Modify after loading | Edit any item row as normal                    |
| Check inventory      | Watch for warnings after changing quantities   |

---

**Feature Status:** ✅ Active and Production-Ready  
**Version:** 1.0  
**Last Updated:** October 27, 2025
