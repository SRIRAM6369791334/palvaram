@extends('layouts.master')
@section('title') Transfer Stock @endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
<style>
.sk-card { background:#fff; border:0.5px solid #e5e5e5; border-radius:16px; overflow:hidden; }
.sk-tab-bar { display:flex; align-items:center; justify-content:space-between; padding:18px 20px 0; border-bottom:0.5px solid #efefef; }
.sk-tabs { display:flex; gap:0; background:#f5f5f5; border-radius:10px; padding:3px; }
.sk-tab { padding:8px 20px; border-radius:8px; font-size:13px; font-weight:500; cursor:pointer; border:none; background:transparent; color:#888; transition:all 0.18s ease; display:flex; align-items:center; gap:7px; white-space:nowrap; }
.sk-tab i { font-size:15px; }
.sk-tab.active { background:#fff; color:#111; border:0.5px solid #e0e0e0; box-shadow:0 1px 4px rgba(0,0,0,0.07); }
.sk-tab:hover:not(.active) { color:#333; }
.sk-pane { display:none; padding:20px; }
.sk-pane.active { display:block; }
.stock-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:100px; font-size:13px; font-weight:600; }
.stock-badge.good    { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; }
.stock-badge.low     { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
.stock-badge.none    { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.stock-badge.unknown { background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb; }

</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Home @endslot
    @slot('title') Transfer Stock @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="sk-card">

            <div class="sk-tab-bar">
                <div class="sk-tabs">
                    <button class="sk-tab active" id="tab-store" onclick="switchTransferTab('store')">
                        <i class="mdi mdi-store"></i> Store → Store
                    </button>
                    <button class="sk-tab" id="tab-warehouse" onclick="switchTransferTab('warehouse')">
                        <i class="mdi mdi-warehouse"></i> Warehouse → Warehouse
                    </button>
                </div>
                <div style="padding-bottom:12px;">
                    <a href="{{ url('/transfer') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#transferStockModal">
                        <i class="mdi mdi-plus me-1"></i> New Transfer
                    </button>
                </div>
            </div>

            <div class="sk-pane active" id="pane-store">
                <div id="store-transfer-table"></div>
            </div>

            <div class="sk-pane" id="pane-warehouse">
                <div id="warehouse-transfer-table"></div>
            </div>

        </div>
    </div>
</div>

<!-- ================= MODAL ================= (unchanged) -->
<div class="modal fade" id="transferStockModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="transferStockForm">
                @csrf
                <input type="hidden" name="from_stock_id" id="transfer_from_stock_id">
                <input type="hidden" id="transfer_available_stock">
                <input type="hidden" id="transfer_variant_id">
                <div class="modal-header">
                    <h5 class="modal-title">Transfer Stock</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Transfer Type</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="transfer_type" id="type_store" value="store" checked>
                                <label class="form-check-label" for="type_store">Store → Store (same warehouse)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="transfer_type" id="type_warehouse" value="warehouse">
                                <label class="form-check-label" for="type_warehouse">Warehouse → Warehouse</label>
                            </div>
                        </div>
                    </div>
                    <div id="section_store_transfer">
                        <h6>Sender</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2"><label>Country</label><select class="form-select select2" id="transfer_country"><option disabled selected>Select Country</option></select></div>
                            <div class="col-md-6 mb-2"><label>Warehouse</label><select class="form-select select2" id="transfer_from_warehouse"><option disabled selected>Select Warehouse</option></select></div>
                            <div class="col-md-6 mb-2"><label>Store</label><select class="form-select select2" id="transfer_from_branch"><option disabled selected>Select Store</option></select></div>
                            <div class="col-md-6 mb-2"><label>Product</label><select class="form-select select2" id="transfer_product"><option disabled selected>Select Product</option></select></div>
                        </div>
                        <p><b>Available Stock:</b> <span id="transfer_current_stock">0</span></p>
                        <hr>
                        <h6>Receiver</h6>
                        <input type="hidden" id="transfer_to_warehouse" name="to_warehouse_id">
                        <div class="row">
                            <div class="col-md-6 mb-2"><label>Store</label><select class="form-select select2" id="transfer_to_branch" name="to_branch_id"><option disabled selected>Select Store</option></select></div>
                            <div class="col-md-6 mb-2"><label>Quantity</label><input type="number" class="form-control" id="transfer_quantity" name="transfer_quantity"></div>
                            <div class="col-md-6 mb-2">
                                <label>Current Stock in Receiver Store</label>
                                <div id="transfer_to_branch_stock">
                                    <span class="badge bg-secondary">Select store first</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="section_warehouse_transfer" style="display:none;">
                        <h6>Source</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2"><label>Country</label><select class="form-select select2" id="wt_country"><option disabled selected>Select Country</option></select></div>
                            <div class="col-md-6 mb-2"><label>Source Warehouse</label><select class="form-select select2" id="wt_from_warehouse"><option disabled selected>Select Warehouse</option></select></div>
                            <div class="col-md-6 mb-2"><label>Product</label><select class="form-select select2" id="wt_product"><option disabled selected>Select Product</option></select></div>
                        </div>
                        <p><b>Available Stock:</b> <span id="wt_current_stock">0</span></p>
                        <input type="hidden" id="wt_available_stock">
                        <input type="hidden" id="wt_variant_id">
                        <hr>
                        <h6>Destination</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2"><label>Destination Warehouse</label><select class="form-select select2" id="wt_to_warehouse"><option disabled selected>Select Warehouse</option></select></div>
                            <div class="col-md-6 mb-2"><label>Quantity</label><input type="number" class="form-control" id="wt_quantity" min="1"></div>
                            <div class="col-md-6 mb-2">
                                <label>Current Stock in Receiver Warehouse</label>
                                <div id="wt_to_warehouse_stock">
                                    <span class="badge bg-secondary">Select warehouse first</span>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success transfer_submit_btn">Transfer Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/app.js') }}"></script>
<script>
    window.transfers          = @json($transfers);
    window.allCountries       = @json($countries);
    window.allWarehouses      = @json($warehouses);
    window.allBranches        = @json($branches);
    window.allStocks          = @json($stocks);
    window.allWarehouseStocks = @json($warehouseStocks);
</script>
<script src="{{ URL::asset('assets/js/app/transferStock.js') }}"></script>
@endsection