@include('partials.auth.header')

<section class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto main-container">
            <input type="hidden" id="item-id" value="{{ $item_id }}">
            
            <div class="card card-sub-details area-main-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title">Item Details</h5>
                        <a href="{{ route('inventory.list') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="Brand">Brand <span class="text-danger">*</span></label>
                            <input type="text" class="form-control override-input" placeholder="Enter brand" data-key="Brand">
                        </div>
                        <div class="col-md-6">
                            <label for="ItemName">Item <span class="text-danger">*</span></label>
                            <input type="text" class="form-control override-input" placeholder="Enter item name" data-key="ItemName">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="VariantOne">Variant 1</label>
                            <input type="text" class="form-control override-input" placeholder="Enter first variant" data-key="VariantOne">
                        </div>
                        <div class="col-md-6">
                            <label for="VariantTwo">Variant 2</label>
                            <input type="text" class="form-control override-input" placeholder="Enter second variant" data-key="VariantTwo">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="Size">Size</label>
                            <input type="text" class="form-control override-input" placeholder="Enter size" data-key="Size">
                        </div>
                        <div class="col-md-4">
                            <label for="Microns">Microns</label>
                            <input type="text" class="form-control override-input" placeholder="Enter microns" data-key="Microns">
                        </div>
                        <div class="col-md-4">
                            <label for="Gsm">GSM</label>
                            <input type="text" class="form-control override-input" placeholder="Enter GSM" data-key="Gsm">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="SheetsPerPack">Sheets Per Pack</label>
                            <input 
                                type="number" 
                                min="0"
                                class="form-control override-input text-right" 
                                placeholder="0" 
                                data-key="SheetsPerPack">
                        </div>
                        <div class="col-md-4">
                            <label for="PriceWithoutShippingFee">Price Without Shipping Fee <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                step="0.01"
                                min="0"
                                class="form-control override-input text-right" 
                                placeholder="0.00" 
                                data-key="PriceWithoutShippingFee">
                        </div>
                        <div class="col-md-4">
                            <label for="EstimatedShippingFee">Estimated Shipping Fee</label>
                            <input 
                                type="number" 
                                step="0.01"
                                min="0"
                                class="form-control override-input text-right" 
                                placeholder="0.00" 
                                data-key="EstimatedShippingFee">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="DatePurchased">Date Purchased</label>
                            <input type="date" class="form-control override-input" data-key="DatePurchased">
                        </div>
                        <div class="col-md-8 d-flex flex-column justify-content-end">
                            <small class="text-muted">Provide the purchase date to help track inventory batches and pricing history.</small>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-danger btn-block" data-trigger="clear">
                                <i class="fa fa-trash"></i> Clear
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-success btn-block" data-trigger="save">
                                <i class="fa fa-save"></i> Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@include('partials.auth.footer')
<script src="{{ asset('assets/app/js/inventory/add.js') }}"></script>
