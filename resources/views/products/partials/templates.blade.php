<template id="material-row-template">
    <div class="card mb-2 shadow-sm material-row" data-row-id="">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold">Material <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm component-item-select" data-component="materials">
                            <option value="">Select an item...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-2">
                        <label class="small text-muted">SKU</label>
                        <input type="text" class="form-control form-control-sm component-sku" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-2">
                        <label class="small">Quantity Used</label>
                        <input type="number" class="form-control form-control-sm material-quantity" min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-2">
                        <label class="small">Unit Price</label>
                        <input type="number" class="form-control form-control-sm material-unit-price" min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn"><i class="fa fa-trash"></i></button>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-4">
                    <small class="text-muted availability-text"></small>
                </div>
                <div class="col-md-4">
                    <label class="small mb-1">Total Cost</label>
                    <input type="number" class="form-control form-control-sm material-total-cost" min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="col-md-4 text-right">
                    <small class="text-muted">Last updated: <span class="row-timestamp">—</span></small>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="ink-row-template">
    <div class="card mb-2 shadow-sm ink-row" data-row-id="">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold">Ink Item <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm component-item-select" data-component="inks">
                            <option value="">Select an item...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-2">
                        <label class="small text-muted">SKU</label>
                        <input type="text" class="form-control form-control-sm component-sku" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-2">
                        <label class="small">Pages Yield</label>
                        <input type="number" class="form-control form-control-sm ink-pages-yield" min="0" step="1" placeholder="0">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-2">
                        <label class="small">Cost per Page</label>
                        <input type="number" class="form-control form-control-sm ink-cost-per-page" min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-2">
                        <label class="small">Total Pages</label>
                        <input type="number" class="form-control form-control-sm ink-total-pages" min="0" step="1" placeholder="0">
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-8">
                    <small class="text-muted">Use pages yield and cost per page to auto-compute total. You can still override it manually.</small>
                </div>
                <div class="col-md-3">
                    <label class="small mb-1">Total Cost</label>
                    <input type="number" class="form-control form-control-sm ink-total-cost" min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="col-md-1 d-flex align-items-end justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="packaging-row-template">
    <div class="card mb-2 shadow-sm packaging-row" data-row-id="">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold">Packaging Item <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm component-item-select" data-component="packaging">
                            <option value="">Select an item...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-2">
                        <label class="small">Quantity Used</label>
                        <input type="number" class="form-control form-control-sm packaging-quantity" min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-2">
                        <label class="small">Total Cost</label>
                        <input type="number" class="form-control form-control-sm packaging-total-cost" min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        </div>
    </div>
</template>

