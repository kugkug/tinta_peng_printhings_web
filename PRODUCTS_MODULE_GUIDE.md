# Products Module - User Guide

## Overview

The Products Module allows you to create and manage end products that are composed of multiple items from your inventory. Each product has a unique product code that can be reused for similar products, and tracks the Bill of Materials (BOM) - the list of inventory items required to create the product.

## Features

### 1. **Product Management**

-   Create, view, edit, and delete products
-   Track product costs automatically based on component items
-   View comprehensive product details including all items used

### 2. **Unique Product Codes**

-   **Auto-generation**: System can automatically generate unique product codes (format: `PROD-YYMMDD-XXXX`)
-   **Manual Entry**: Enter custom product codes
-   **Code Reusability**: Use the same product code for identical or similar products created in the future
-   **Code Checking**: Verify if a product code already exists and see what product uses it

### 3. **Bill of Materials (BOM)**

-   Add multiple items from inventory to a single product
-   Specify quantities for each item
-   Automatic cost calculation based on item prices
-   Real-time subtotal and total cost updates

### 4. **Integration with Inventory**

-   Items are pulled directly from your existing inventory
-   Uses item SKU for easy identification
-   Automatically uses item's price per part as unit cost

## How to Use

### Creating a New Product

1. **Navigate to Products**

    - Click on "Products" in the sidebar menu
    - Click the "Add New Product" button

2. **Enter Product Information**

    - **Product Code**:
        - Leave empty to auto-generate, or
        - Enter a custom code, or
        - Click "Generate" button to create a unique code
        - Click "Check" to verify if the code exists
    - **Product Name**: Enter a descriptive name for the product (Required)
    - **Description**: Add optional details about the product

3. **Add Items (Bill of Materials)**

    - Click "Add Item" button to add a row
    - Select an item from the dropdown
    - Enter the quantity needed
    - The unit cost is automatically populated from the item's price
    - Add multiple items as needed
    - Remove items by clicking the trash icon

4. **Review and Save**
    - Check the total cost displayed at the bottom
    - Click "Save Product" to create the product
    - You'll be redirected to the products list

### Editing a Product

1. Go to Products list
2. Click the edit icon (pencil) on the product you want to modify
3. Update the information as needed
4. Add or remove items from the BOM
5. Click "Update Product" to save changes

### Viewing Product Details

1. Go to Products list
2. Click the view icon (eye) on any product
3. A modal will display:
    - Product code and name
    - Description
    - Complete Bill of Materials with quantities and costs
    - Total product cost

### Deleting a Product

1. Go to Products list
2. Click the delete icon (trash) on the product
3. Confirm the deletion
4. The product and its BOM will be permanently removed

## Reusing Product Codes

The system allows you to reuse product codes for identical or similar products. This is useful when:

-   You manufacture the same product multiple times
-   You have product variants with the same base components
-   You want to track product families or categories

**To Reuse a Code:**

1. When creating a new product, enter the existing product code
2. Click "Check" to verify it exists
3. The system will inform you that the code is already in use
4. You can choose to proceed with the same code
5. Both products will share the code but remain separate entries

## Product Code Format

**Auto-generated codes follow this format:**

-   `PROD-250127-A1B2`
    -   `PROD`: Prefix (customizable)
    -   `250127`: Date (YYMMDD format)
    -   `A1B2`: Random 4-character identifier

You can also use completely custom codes like:

-   `SHIRT-001`
-   `WIDGET-V2`
-   `CUSTOM-PRODUCT-123`

## Database Structure

### Products Table

-   `id`: Unique identifier
-   `product_code`: Product code (reusable)
-   `product_name`: Product name
-   `product_description`: Optional description
-   `total_cost`: Auto-calculated total cost
-   `created_at`, `updated_at`: Timestamps

### Product Items Pivot Table

-   Links products to inventory items
-   Stores quantity and unit cost for each item
-   Automatically maintains relationships

## API Endpoints

The Products module provides the following API endpoints:

-   `POST /api/products/list` - Get all products
-   `POST /api/products/get` - Get single product details
-   `POST /api/products/save` - Create or update product
-   `POST /api/products/delete` - Delete product
-   `POST /api/products/generate-code` - Generate unique product code
-   `POST /api/products/check-code` - Check if code exists

## Routes

-   `/products` - List all products
-   `/products/add` - Create new product
-   `/products/edit/{id}` - Edit existing product

## Technical Details

### Models

-   **Product**: Main product model with relationships to items
-   **Item**: Inventory item model (existing)
-   Relationship: Many-to-Many through `product_items` pivot table

### Key Features Implementation

1. **Unique Code Generation**: Uses timestamp and MD5 hash
2. **Cost Calculation**: Automatically sums item quantities × unit costs
3. **Code Reusability**: Unique constraint with manual override option
4. **Real-time Updates**: JavaScript calculates costs as items are added

## Tips and Best Practices

1. **Product Codes**: Use meaningful codes that help identify products (e.g., `SHIRT-RED-M`)
2. **Descriptions**: Add detailed descriptions to help differentiate similar products
3. **BOM Accuracy**: Double-check quantities and items before saving
4. **Regular Updates**: Update product BOMs when component items change
5. **Cost Tracking**: Review total costs regularly to ensure accurate pricing

## Example Use Cases

### Example 1: Custom T-Shirt

-   **Product Code**: `SHIRT-CUSTOM-001`
-   **Items**:
    -   Plain T-shirt (Qty: 1)
    -   Fabric ink - Red (Qty: 0.1 liters)
    -   Printing service (Qty: 1)
-   **Total Cost**: Automatically calculated

### Example 2: Widget Assembly

-   **Product Code**: `WIDGET-V2`
-   **Items**:
    -   Widget base (Qty: 1)
    -   Screws (Qty: 4)
    -   Label (Qty: 1)
    -   Packaging box (Qty: 1)
-   **Total Cost**: Sum of all components

### Example 3: Product Variant (Reusing Code)

-   **Product Code**: `WIDGET-V2` (same as above)
-   Different color variant with same components
-   Tracks separately but uses same code for categorization

## Troubleshooting

**Issue**: Can't save product

-   **Solution**: Ensure you've added at least one item and filled all required fields

**Issue**: Product code already exists warning

-   **Solution**: This is informational - you can still proceed if you want to reuse the code

**Issue**: Items not showing in dropdown

-   **Solution**: Ensure you have items in your inventory first

**Issue**: Total cost is incorrect

-   **Solution**: Check quantities and unit costs for each item

## Future Enhancements

Potential features for future updates:

-   Bulk product creation
-   Product templates
-   Export product BOMs
-   Product categories/tags
-   Cost history tracking
-   Production quantity management

---

**Version**: 1.0  
**Last Updated**: October 27, 2025
