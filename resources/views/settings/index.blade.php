@include('partials.auth.header')

<section class="container-fluid">
    <div class="row">
        <div class="col-md-6 main-container">
            
            <div class="card card-sub-details area-main-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h5 class="card-title">Units</h5>
                    </div>
                    
                    <form>
                        <div class="row my-3">
                            <div class="col-md-6">
                                <label for="UnitName">Unit Name</label>
                                <input type="text" class="form-control override-input" placeholder="Unit Name" data-key="unit" data="req">
                                <div class="invalid-feedback animated fadeInDown d-none">Please provide a unit name</div>
                            </div>
                            <div class="col-md-3">
                                <label for="UnitInitial">Unit Initial</label>
                                <input type="text" class="form-control override-input" placeholder="Unit Initial" data-key="initial" data="req">
                                <div class="invalid-feedback animated fadeInDown d-none">Please provide initial</div>
                            </div>

                            <div class="col-md-3">
                                <label for="">&nbsp;</label>
                                <button class="btn btn-md btn-success btn-block " data-trigger="save-units">
                                    <i class="fa fa-save"></i> Save
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Unit</th>
                                        <th>Initial</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="settings-units-tbody"></tbody>
                            </table>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@include('partials.auth.footer')

<script src="{{ asset('assets/app/js/settings/settings.js') }}"></script>
