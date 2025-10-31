@include('partials.auth.header')

<section class="container-fluid">
    <div class="row">
        <div class="col-lg-9">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit Product</h4>
                    <span class="badge badge-secondary" id="history-indicator" style="display:none;">History loaded</span>
                </div>
                <div class="card-body">
                    <form id="product-form">
                        <input type="hidden" id="product-id" name="ProductId" value="{{ $id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product-code">Product Code <small class="text-muted">(Leave empty to auto-generate)</small></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="product-code" name="ProductCode" placeholder="e.g., PROD-250127-A1B2">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="generate-code-btn"><i class="fa fa-refresh"></i></button>
                                            <button class="btn btn-outline-info" type="button" id="check-code-btn"><i class="fa fa-search"></i></button>
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

                        <div class="card shadow-sm mb-3" id="materials-card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <span><i class="fa fa-cogs text-primary"></i> Materials Used</span>
                                <button type="button" class="btn btn-sm btn-primary" id="add-material-btn"><i class="fa fa-plus"></i> Add Material</button>
                            </div>
                            <div class="card-body" id="materials-container">
                                <p class="text-muted small mb-0">Update the raw materials required for this product. Adjust unit price or total cost if needed.</p>
                            </div>
                        </div>

                        <div class="card shadow-sm mb-3" id="inks-card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <span><i class="fa fa-tint text-info"></i> Ink</span>
                                <button type="button" class="btn btn-sm btn-info" id="add-ink-btn"><i class="fa fa-plus"></i> Add Ink</button>
                            </div>
                            <div class="card-body" id="inks-container">
                                <p class="text-muted small mb-0">Review ink usage metrics. Values can be recalculated automatically or overridden.</p>
                            </div>
                        </div>

                        <div class="card shadow-sm mb-3" id="packaging-card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <span><i class="fa fa-dropbox text-warning"></i> Packaging</span>
                                <button type="button" class="btn btn-sm btn-warning" id="add-packaging-btn"><i class="fa fa-plus"></i> Add Packaging</button>
                            </div>
                            <div class="card-body" id="packaging-container">
                                <p class="text-muted small mb-0">Include packaging materials and update their costs.</p>
                            </div>
                        </div>

                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <strong>Total Cost</strong>
                            <span class="h4 mb-0">₱<span id="total-cost">0.00</span></span>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-success" id="submit-btn">
                                <i class="fa fa-save"></i> Update Product
                            </button>
                            <button type="button" class="btn btn-outline-warning" id="clear-all-btn">
                                <i class="fa fa-eraser"></i> Clear All Sections
                            </button>
                            <a href="{{ route('products.list') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-history"></i> Product History</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="template-product-code" class="small text-muted">Load by Product Code</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="template-product-code" placeholder="Enter code">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-info" id="load-template-btn"><i class="fa fa-download"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="history-select" class="small text-muted">Quick History</label>
                        <select class="form-control form-control-sm" id="history-select">
                            <option value="">Select a previous configuration...</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="refresh-history-btn"><i class="fa fa-sync"></i> Refresh</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clear-history-indicator">Clear Flag</button>
                    </div>
                    <hr>
                    <div id="template-status" class="small"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-lightbulb-o"></i> Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small text-muted">
                        <li>Use history to align new products with existing configurations.</li>
                        <li>Review totals after updating item costs.</li>
                        <li>Clear sections before loading another history snapshot.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

@include('products.partials.templates')

@include('partials.auth.footer')
<script src="{{ asset('assets/app/js/products/add.js') }}"></script>

