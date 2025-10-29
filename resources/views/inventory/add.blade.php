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
                        <div class="col-md-12">
                            <label for="ItemName">Item Name / Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control override-input" placeholder="Enter item name" data-key="ItemName">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="ItemDescription">Description</label>
                            <textarea class="form-control override-input" rows="3" placeholder="Enter item description (optional)" data-key="ItemDescription"></textarea>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ItemPrice">Bundle Price <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                step="0.01"
                                class="form-control override-input text-right" 
                                placeholder="0.00" 
                                data-key="ItemPrice">
                        </div>
                        <div class="col-md-6">
                            <label for="ItemQuantity">Packs Per Bundle <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                class="form-control override-input text-right" 
                                placeholder="0" 
                                data-key="ItemQuantity">
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ItemPricePerPiece">Price Per Pack <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                step="0.01"
                                class="form-control override-input text-right" 
                                placeholder="0.00" 
                                data-key="ItemPricePerPiece">
                        </div>
                        <div class="col-md-6">
                            <label for="ItemPartsPerPiece">Parts Per Piece <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                class="form-control override-input text-right" 
                                placeholder="0" 
                                data-key="ItemPartsPerPiece">
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ItemPricePerPart">Price Per Part <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                step="0.01"
                                class="form-control override-input text-right" 
                                placeholder="0.00" 
                                data-key="ItemPricePerPart">
                        </div>
                        <div class="col-md-6">
                            <label for="ItemPricePerPartOfPiece">Price Per Part of Piece <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                step="0.01"
                                class="form-control override-input text-right" 
                                placeholder="0.00" 
                                data-key="ItemPricePerPartOfPiece">
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
