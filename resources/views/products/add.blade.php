@include('partials.auth.header')

<section class="container-fluid">
    <div class="row">
        <div class="col-lg-9">
            <form id="product-form">
                <div class="card mb-3">                
                    <div class="card-body">
                        <h4 class="card-title mb-0" id="form-title">Add New Product</h4>
                        <span class="badge badge-secondary" id="history-indicator" style="display:none;">History loaded</span>
                        <input type="hidden" id="product-id" name="ProductId">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product-code">Product Code <small class="text-muted">(Leave empty to auto-generate)</small></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="product-code" name="ProductCode" placeholder="e.g., PROD-250127-A1B2">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="generate-code-btn">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                                            <button class="btn btn-outline-info" type="button" id="check-code-btn">
                                                <i class="fa fa-search"></i>
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
                    </div>
                </div>

                <div class="card shadow-sm mb-3" id="materials-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-cogs text-primary"></i> Materials Used</span>
                        <button type="button" class="btn btn-sm btn-primary" id="add-material-btn"><i class="fa fa-plus"></i> Add Material</button>
                    </div>
                    <div class="card-body" id="materials-container">
                        <p class="text-muted small mb-0">Add the raw materials required for this product. Unit price and total cost can be adjusted per entry.</p>
                    </div>
                </div>

                <div class="card shadow-sm mb-3" id="inks-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-tint text-info"></i> Ink</span>
                        <button type="button" class="btn btn-sm btn-info" id="add-ink-btn"><i class="fa fa-plus"></i> Add Ink</button>
                    </div>
                    <div class="card-body" id="inks-container">
                        <p class="text-muted small mb-0">Capture ink usage details such as pages yield, cost per page, and total pages printed.</p>
                    </div>
                </div>

                <div class="card shadow-sm mb-3" id="packaging-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-dropbox text-warning"></i> Packaging</span>
                        <button type="button" class="btn btn-sm btn-warning" id="add-packaging-btn"><i class="fa fa-plus"></i> Add Packaging</button>
                    </div>
                    <div class="card-body" id="packaging-container">
                        <p class="text-muted small mb-0">Include boxes, wraps, or other packaging components and their associated costs.</p>
                    </div>
                </div>

                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <strong>Total Cost</strong>
                    <span class="h4 mb-0">₱<span id="total-cost">0.00</span></span>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success" id="submit-btn">
                        <i class="fa fa-save"></i> Save Product
                    </button>
                    <button type="button" class="btn btn-outline-warning" id="clear-all-btn">
                        <i class="fa fa-eraser"></i> Clear All Sections
                    </button>
                    <a href="{{ route('products.list') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
        <div class="col-lg-3">
            <div class="card mb-3">
                
                <div class="card-body">
                    
                    <h5 class="mb-0"><i class="fa fa-history"></i> Product History</h5>
                    
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
                    <h5 class="mb-0"><i class="fa fa-info-circle"></i> Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small text-muted">
                        <li>Use history to pre-fill repeated configurations.</li>
                        <li>Total cost updates automatically when values change.</li>
                        <li>Ensure at least one material, ink, or packaging entry is added.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

@include('products.partials.templates')

@include('partials.auth.footer')
<script src="{{ asset('assets/app/js/products/add.js') }}"></script>

