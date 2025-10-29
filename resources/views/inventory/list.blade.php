@include('partials.auth.header')

<section class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Low Stock Alert Banner -->
            <div id="low-stock-alert" class="alert alert-warning mb-3" style="display: none;">
                <div class="d-flex align-items-center">
                    <i class="fa fa-exclamation-triangle fa-2x mr-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">Low Stock Alert!</h5>
                        <p class="mb-0">
                            <span id="low-stock-count" class="font-weight-bold">0</span> item(s) have stock levels at or below 
                            <span id="low-stock-threshold" class="font-weight-bold">10</span> units. 
                            Please restock soon to avoid shortages.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            <div id="bulk-actions-bar" class="card mb-3" style="display: none;">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span id="selected-count" class="font-weight-bold">0</span> item(s) selected
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-primary" id="download-selected-pdf">
                                <i class="fa fa-download"></i> Download Barcodes (PDF)
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" id="clear-selection">
                                <i class="fa fa-times"></i> Clear Selection
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="items-table" class="table table-striped table-bordered zero-configuration">
                            <thead>
                                <tr>
                                    <th style="width: 30px;">
                                        <input type="checkbox" id="select-all" title="Select All">
                                    </th>
                                    <th>SKU</th>
                                    <th>Item Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Price/Piece</th>
                                    <th>Parts/Piece</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Barcode Modal -->
<div class="modal fade" id="barcodeModal" tabindex="-1" role="dialog" aria-labelledby="barcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barcodeModalLabel">Item Barcode</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <h6 id="barcode-item-name" class="mb-3"></h6>
                <div id="barcode-display" class="mb-3"></div>
                <p id="barcode-sku" class="text-muted"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
            </div>
        </div>
    </div>
</div>

@include('partials.auth.footer')
<script src="{{ asset('assets/app/js/inventory/list.js') }}"></script>