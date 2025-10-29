@include('partials.auth.header')

<section class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title" id="form-title">Add New Product</h4>
                </div>
                <div class="card-body">
                    <form id="product-form">
                        <input type="hidden" id="product-id" name="ProductId">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product-code">Product Code <small class="text-muted">(Leave empty to auto-generate)</small></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="product-code" name="ProductCode" placeholder="e.g., PROD-250127-A1B2">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="generate-code-btn">
                                                <i class="fa fa-refresh"></i> Generate
                                            </button>
                                            <button class="btn btn-outline-info" type="button" id="check-code-btn">
                                                <i class="fa fa-search"></i> Check
                                            </button>
                                        </div>
                                    </div>
                                    <small id="code-status" class="form-text"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product-name">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="product-name" name="ProductName" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="product-description">Product Description</label>
                            <textarea class="form-control" id="product-description" name="ProductDescription" rows="3"></textarea>
                        </div>

                        <hr>

                        <!-- Load from Existing Product Section -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fa fa-copy"></i> Load from Existing Product (Optional)
                                </h6>
                                <p class="text-muted small mb-3">
                                    Use an existing product as a template. Enter a product code and optionally specify a quantity multiplier for bulk production.
                                </p>
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group mb-2">
                                            <label for="template-product-code" class="small">Product Code to Load</label>
                                            <input type="text" class="form-control form-control-sm" id="template-product-code" placeholder="e.g., PROD-250127-A1B2">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-2">
                                            <label for="quantity-multiplier" class="small">Quantity Multiplier</label>
                                            <input type="number" class="form-control form-control-sm" id="quantity-multiplier" value="1" min="1" step="1">
                                            <small class="text-muted">For bulk: 1 = single, 10 = 10x items</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small d-block">&nbsp;</label>
                                        <button type="button" class="btn btn-info btn-sm" id="load-template-btn">
                                            <i class="fa fa-download"></i> Load Items from Product
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm" id="clear-items-btn">
                                            <i class="fa fa-eraser"></i> Clear All Items
                                        </button>
                                    </div>
                                </div>
                                <div id="template-status" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Bill of Materials (Items)</h5>
                            <button type="button" class="btn btn-primary btn-sm" id="add-item-btn">
                                <i class="fa fa-plus"></i> Add Item
                            </button>
                        </div>

                        <div id="items-container">
                            <!-- Items will be added here dynamically -->
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong>Total Cost: ₱<span id="total-cost">0.00</span></strong>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-success" id="submit-btn">
                                <i class="fa fa-save"></i> Save Product
                            </button>
                            <a href="{{ route('products.list') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Item Row Template -->
<template id="item-row-template">
    <div class="card mb-2 item-row">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group mb-0">
                        <label>Item <span class="text-danger">*</span></label>
                        <select class="form-control item-select" name="Items[][item_id]" required>
                            <option value="">Select an item...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>SKU</label>
                        <input type="text" class="form-control item-sku" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control item-quantity" name="Items[][quantity]" step="0.01" min="0.01" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Unit Cost</label>
                        <input type="number" class="form-control item-unit-cost" name="Items[][unit_cost]" step="0.01" min="0" readonly>
                    </div>
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-block remove-item-btn">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <small class="text-muted">Subtotal: ₱<span class="item-subtotal">0.00</span></small>
                </div>
            </div>
        </div>
    </div>
</template>

@include('partials.auth.footer')
<script src="{{ asset('assets/app/js/products/add.js') }}"></script>

