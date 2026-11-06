@extends('layouts.master')
@section('title') Stock Management @endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.css') }}">
<style>
    /* ── Stat Cards ─────────────────────────────────────────── */
    .stat-card {
        border-radius: 12px; padding: 18px 20px;
        display: flex; align-items: center; gap: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06); background: #fff;
        transition: transform 0.18s, box-shadow 0.18s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.11); }
    .stat-icon { width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
    .stat-label { font-size:12px;color:#888;margin-bottom:2px;font-weight:500; }
    .stat-value { font-size:20px;font-weight:700;color:#222; }

    /* ── Main Tabs ───────────────────────────────────────────── */
    .mgmt-tabs { display:flex; gap:0; border-bottom:2px solid #dee2e6; margin-bottom:0; }
    .mgmt-tab {
        padding:12px 24px; font-size:13px; font-weight:600;
        border:none; background:none; color:#888; cursor:pointer;
        border-bottom:3px solid transparent; margin-bottom:-2px;
        transition:all 0.18s; display:flex; align-items:center; gap:7px;
    }
    .mgmt-tab:hover { color:#333; }
    .mgmt-tab.active[data-tab="return"]    { color:#fd7e14; border-bottom-color:#fd7e14; }
    .mgmt-tab.active[data-tab="cancelled"] { color:#dc3545; border-bottom-color:#dc3545; }
    .mgmt-tab.active[data-tab="damaged"]   { color:#6f42c1; border-bottom-color:#6f42c1; }
    .mgmt-tab.active[data-tab="expired"]   { color:#0d6efd; border-bottom-color:#0d6efd; }

    /* ── Tab Panels ──────────────────────────────────────────── */
    .tab-panel { display:none; }
    .tab-panel.active { display:block; }

    /* ── Resolution Option Cards ─────────────────────────────── */
    .resolution-opts { display:flex; gap:10px; flex-wrap:wrap; margin:12px 0; }
    .res-opt {
        flex:1; min-width:140px; padding:12px 14px;
        border:2px solid #dee2e6; border-radius:10px;
        cursor:pointer; transition:all 0.18s; background:#fff;
        text-align:center; font-size:13px; font-weight:600; color:#555;
    }
    .res-opt:hover { border-color:#adb5bd; color:#333; }
    .res-opt.selected { border-color:#0d6efd; background:#e8f0fe; color:#0d6efd; }
    .res-opt i { display:block; font-size:22px; margin-bottom:5px; }

    /* ── Resolution Sub-forms ────────────────────────────────── */
    .res-form { display:none; margin-top:14px; padding:14px; background:#f8f9fa; border-radius:10px; border:1px solid #e9ecef; }
    .res-form.active { display:block; }

    /* ── Product Search Dropdown ─────────────────────────────── */
    .product-search-wrap { position:relative; }
    .product-dropdown {
        position:absolute; top:100%; left:0; right:0; z-index:999;
        background:#fff; border:1px solid #dee2e6; border-radius:8px;
        box-shadow:0 4px 16px rgba(0,0,0,0.1); max-height:220px; overflow-y:auto; display:none;
    }
    .product-dropdown.show { display:block; }
    .product-dropdown-item { padding:8px 12px; cursor:pointer; font-size:13px; border-bottom:1px solid #f0f0f0; }
    .product-dropdown-item:hover { background:#f0f4ff; }
    .product-dropdown-item:last-child { border-bottom:none; }

    /* ── Info Alert ──────────────────────────────────────────── */
    .info-note { font-size:12px; color:#6c757d; margin-top:6px; }

    /* GridJS */
    .gridjs-wrapper { border-radius:8px !important; overflow:hidden; }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Home @endslot
    @slot('title') Stock Management @endslot
@endcomponent


<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff3e0;"><i class="bx bx-revision text-warning"></i></div>
            <div><div class="stat-label">Pending Returns</div><div class="stat-value text-warning">{{ $stats['pending_returns'] }}</div></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fdecea;"><i class="bx bx-x-circle text-danger"></i></div>
            <div><div class="stat-label">Cancelled Orders</div><div class="stat-value text-danger">{{ $stats['cancelled_orders'] }}</div></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e9d8fd;"><i class="bx bx-error" style="color:#6f42c1;"></i></div>
            <div><div class="stat-label">Damaged Stock Items</div><div class="stat-value" style="color:#6f42c1;">{{ $stats['damaged_items'] }}</div></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#cfe2ff;"><i class="bx bx-time text-primary"></i></div>
            <div><div class="stat-label">Expired Batches</div><div class="stat-value text-primary">{{ $stats['expired_batches'] }}</div></div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-header py-0 px-0 d-flex justify-content-between align-items-center" style="border-bottom:none;">

    <div class="mgmt-tabs px-3 pt-2" id="mgmtTabs">
        <button class="mgmt-tab active" data-tab="return">
            <i class="bx bx-revision"></i> Return
        </button>

        <button class="mgmt-tab" data-tab="cancelled">
            <i class="bx bx-x-circle"></i> Cancelled
        </button>

        <button class="mgmt-tab" data-tab="damaged">
            <i class="bx bx-error-circle"></i> Damaged
        </button>

        <button class="mgmt-tab" data-tab="expired">
            <i class="bx bx-time"></i> Expired
        </button>
    </div>

    <div class="px-3 pt-2">
        <a href="{{ url('/transfer') }}" class="btn btn-light btn-sm"  style="background: #0c716a; color: #fff;">
            <i class="bx bx-arrow-back"></i> Back
        </a>
    </div>

</div>

    <div class="card-body">


        <div class="tab-panel active" id="panel-return">
            <div class="row mb-3">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Search Order</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="returnOrderSearch" placeholder="Enter Order ID or customer name...">
                        <button class="btn btn-primary" id="btnSearchReturnOrder"><i class="bx bx-search"></i></button>
                    </div>
                </div>
            </div>


            <div id="returnOrdersTable"></div>


            <div id="returnResolveForm" style="display:none;" class="mt-3">
                <div class="card border-warning">
                    <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bx bx-revision me-1"></i>Resolve Return — Order <span id="resolveOrderId"></span></span>
                        <button class="btn btn-sm btn-outline-secondary" id="btnCancelResolve">Cancel</button>
                    </div>
                    <div class="card-body">
                        {{-- Items list --}}
                        <div id="returnItemsList" class="mb-3"></div>

                        <label class="form-label fw-semibold">Resolution Type</label>
                        <div class="resolution-opts" id="returnResolutionOpts">
                            <div class="res-opt" data-res="same" onclick="selectResolution('return','same',this)">
                                <i class="bx bx-package"></i>Same Product
                            </div>
                            <div class="res-opt" data-res="different" onclick="selectResolution('return','different',this)">
                                <i class="bx bx-transfer"></i>Different Product
                            </div>
                            <div class="res-opt" data-res="composite" onclick="selectResolution('return','composite',this)">
                                <i class="bx bx-layer"></i>Composite Price
                            </div>
                            <div class="res-opt" data-res="wallet" onclick="selectResolution('return','wallet',this)">
                                <i class="bx bx-wallet"></i>Wallet Credit
                            </div>
                        </div>


                        <div class="res-form" id="return-form-same">
                            <p class="text-muted info-note mb-2"><i class="bx bx-info-circle"></i> The same product will be dispatched. Stock will be deducted automatically.</p>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Qty to Replace</label>
                                    <input type="number" class="form-control" id="returnSameQty" min="1" placeholder="Qty">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Note</label>
                                    <input type="text" class="form-control" placeholder="Internal note (optional)">
                                </div>
                            </div>
                        </div>


                        <div class="res-form" id="return-form-different">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Select Replacement Product</label>
                                    <div class="product-search-wrap">
                                        <input type="text" class="form-control" id="returnDiffProductSearch" placeholder="Search product..." autocomplete="off">
                                        <div class="product-dropdown" id="returnDiffProductDropdown">
                                            @foreach($products as $p)
                                            <div class="product-dropdown-item" data-id="{{ $p->id }}" data-name="{{ $p->product_name }}">
                                                {{ $p->product_name }} — {{ $p->varient_name ?? '' }}
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <input type="hidden" id="returnDiffProductId">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Qty</label>
                                    <input type="number" class="form-control" id="returnDiffQty" min="1" placeholder="Qty">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Price Diff (₹)</label>
                                    <input type="number" class="form-control" placeholder="0.00" step="0.01">
                                </div>
                            </div>
                        </div>


                        <div class="res-form" id="return-form-composite">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label">Partial Refund Amount (₹)</label>
                                    <input type="number" class="form-control" placeholder="0.00" step="0.01">
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label">Partial Replacement Product</label>
                                    <div class="product-search-wrap">
                                        <input type="text" class="form-control" id="returnCompProductSearch" placeholder="Search product..." autocomplete="off">
                                        <div class="product-dropdown" id="returnCompProductDropdown">
                                            @foreach($products as $p)
                                            <div class="product-dropdown-item" data-id="{{ $p->id }}" data-name="{{ $p->product_name }}">
                                                {{ $p->product_name }} — {{ $p->varient_name ?? '' }}
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Replacement Qty</label>
                                    <input type="number" class="form-control" min="1" placeholder="Qty">
                                </div>
                            </div>
                        </div>


                        <div class="res-form" id="return-form-wallet">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Wallet Credit Amount (₹)</label>
                                    <input type="number" class="form-control" id="returnWalletAmt" placeholder="0.00" step="0.01">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Reason / Note</label>
                                    <input type="text" class="form-control" placeholder="Reason for wallet credit">
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-success" id="btnSubmitReturn">
                                <i class="bx bx-check me-1"></i>Submit Resolution
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="tab-panel" id="panel-cancelled">
            <div class="row mb-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Filter by Status</label>
                    <select class="form-select" id="cancelStatusFilter">
                        <option value="all">All</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="pending">Pending (can cancel)</option>
                        <option value="processing">Processing (can cancel)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Search Order</label>
                    <input type="text" class="form-control" id="cancelOrderSearch" placeholder="Order ID or customer name...">
                </div>
            </div>
            <div id="cancelledOrdersTable"></div>
        </div>


        <div class="tab-panel" id="panel-damaged">
            <div class="row mb-3">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Search Product / Stock</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="damagedStockSearch" placeholder="Product name or SKU...">
                        <button class="btn btn-primary" id="btnSearchDamagedStock"><i class="bx bx-search"></i></button>
                    </div>
                </div>
            </div>
            <div id="damagedStockTable"></div>


            <div id="damagedResolveForm" style="display:none;" class="mt-3">
                <div class="card border-purple" style="border-color:#6f42c1 !important;">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background:rgba(111,66,193,0.08);">
                        <span class="fw-bold"><i class="bx bx-error-circle me-1" style="color:#6f42c1;"></i>Resolve Damaged Stock — <span id="damagedProductName"></span></span>
                        <button class="btn btn-sm btn-outline-secondary" id="btnCancelDamagedResolve">Cancel</button>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Damaged Qty</label>
                                <input type="number" class="form-control" id="damagedQty" min="1" placeholder="Qty">
                                <div class="info-note"><i class="bx bx-info-circle"></i> Will be deducted from available stock</div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Damage Reason</label>
                                <input type="text" class="form-control" id="damagedReason" placeholder="e.g. Transit damage, storage issue">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Incident Date</label>
                                <input type="text" class="form-control flatpickr" id="damagedDate" placeholder="Select date">
                            </div>
                        </div>

                        <label class="form-label fw-semibold">Customer Resolution (if applicable)</label>
                        <div class="resolution-opts">
                            <div class="res-opt" data-res="none" onclick="selectResolution('damaged','none',this)">
                                <i class="bx bx-block"></i>No Replacement
                            </div>
                            <div class="res-opt" data-res="same" onclick="selectResolution('damaged','same',this)">
                                <i class="bx bx-package"></i>Same Product
                            </div>
                            <div class="res-opt" data-res="different" onclick="selectResolution('damaged','different',this)">
                                <i class="bx bx-transfer"></i>Different Product
                            </div>
                            <div class="res-opt" data-res="composite" onclick="selectResolution('damaged','composite',this)">
                                <i class="bx bx-layer"></i>Composite Price
                            </div>
                            <div class="res-opt" data-res="wallet" onclick="selectResolution('damaged','wallet',this)">
                                <i class="bx bx-wallet"></i>Wallet Credit
                            </div>
                        </div>

                        <div class="res-form" id="damaged-form-same">
                            <div class="row g-2">
                                <div class="col-md-4"><label class="form-label">Replace Qty</label><input type="number" class="form-control" min="1" placeholder="Qty"></div>
                                <div class="col-md-8"><label class="form-label">Customer Order ID (if linked)</label><input type="text" class="form-control" placeholder="ORD-XXXX"></div>
                            </div>
                        </div>
                        <div class="res-form" id="damaged-form-different">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Replacement Product</label>
                                    <div class="product-search-wrap">
                                        <input type="text" class="form-control" id="damagedDiffProductSearch" placeholder="Search product..." autocomplete="off">
                                        <div class="product-dropdown" id="damagedDiffProductDropdown">
                                            @foreach($products as $p)
                                            <div class="product-dropdown-item" data-id="{{ $p->id }}" data-name="{{ $p->product_name }}">{{ $p->product_name }} — {{ $p->varient_name ?? '' }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3"><label class="form-label">Qty</label><input type="number" class="form-control" min="1" placeholder="Qty"></div>
                            </div>
                        </div>
                        <div class="res-form" id="damaged-form-composite">
                            <div class="row g-2">
                                <div class="col-md-4"><label class="form-label">Refund Amount (₹)</label><input type="number" class="form-control" placeholder="0.00" step="0.01"></div>
                                <div class="col-md-5">
                                    <label class="form-label">Partial Replacement Product</label>
                                    <div class="product-search-wrap">
                                        <input type="text" class="form-control" placeholder="Search product..." autocomplete="off">
                                        <div class="product-dropdown">
                                            @foreach($products as $p)
                                            <div class="product-dropdown-item" data-id="{{ $p->id }}" data-name="{{ $p->product_name }}">{{ $p->product_name }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3"><label class="form-label">Qty</label><input type="number" class="form-control" min="1" placeholder="Qty"></div>
                            </div>
                        </div>
                        <div class="res-form" id="damaged-form-wallet">
                            <div class="row g-2">
                                <div class="col-md-4"><label class="form-label">Wallet Credit (₹)</label><input type="number" class="form-control" placeholder="0.00" step="0.01"></div>
                                <div class="col-md-8"><label class="form-label">Note</label><input type="text" class="form-control" placeholder="Reason for credit"></div>
                            </div>
                        </div>
                        <div class="res-form active" id="damaged-form-none">
                            <p class="text-muted info-note mb-0"><i class="bx bx-info-circle"></i> Only stock deduction will be logged. No customer resolution will be issued.</p>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-danger" id="btnSubmitDamaged">
                                <i class="bx bx-check me-1"></i>Submit Damaged Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="tab-panel" id="panel-expired">
            <div id="expiredBatchesTable"></div>


            <div id="expiredResolveForm" style="display:none;" class="mt-3">
                <div class="card border-primary">
                    <div class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bx bx-time me-1 text-primary"></i>Resolve Expired Batch — <span id="expiredBatchNo"></span></span>
                        <button class="btn btn-sm btn-outline-secondary" id="btnCancelExpiredResolve">Cancel</button>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Expired Qty to Write-off</label>
                                <input type="number" class="form-control" id="expiredQty" min="1" placeholder="Qty">
                                <div class="info-note"><i class="bx bx-info-circle"></i> Will be deducted from remaining_qty</div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Disposal Method</label>
                                <select class="form-select" id="expiredDisposal">
                                    <option value="discard">Discard / Destroy</option>
                                    <option value="donate">Donate</option>
                                    <option value="return_supplier">Return to Supplier</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Write-off Date</label>
                                <input type="text" class="form-control flatpickr" placeholder="Select date">
                            </div>
                        </div>

                        <label class="form-label fw-semibold">Customer Resolution (if applicable)</label>
                        <div class="resolution-opts">
                            <div class="res-opt" data-res="none" onclick="selectResolution('expired','none',this)">
                                <i class="bx bx-block"></i>No Replacement
                            </div>
                            <div class="res-opt" data-res="same" onclick="selectResolution('expired','same',this)">
                                <i class="bx bx-package"></i>Same Product
                            </div>
                            <div class="res-opt" data-res="different" onclick="selectResolution('expired','different',this)">
                                <i class="bx bx-transfer"></i>Different Product
                            </div>
                            <div class="res-opt" data-res="composite" onclick="selectResolution('expired','composite',this)">
                                <i class="bx bx-layer"></i>Composite Price
                            </div>
                            <div class="res-opt" data-res="wallet" onclick="selectResolution('expired','wallet',this)">
                                <i class="bx bx-wallet"></i>Wallet Credit
                            </div>
                        </div>

                        <div class="res-form active" id="expired-form-none">
                            <p class="text-muted info-note mb-0"><i class="bx bx-info-circle"></i> Batch will be written off. No customer action required.</p>
                        </div>
                        <div class="res-form" id="expired-form-same">
                            <div class="row g-2">
                                <div class="col-md-4"><label class="form-label">Replace Qty</label><input type="number" class="form-control" min="1" placeholder="Qty"></div>
                                <div class="col-md-8"><label class="form-label">Customer Order ID</label><input type="text" class="form-control" placeholder="ORD-XXXX"></div>
                            </div>
                        </div>
                        <div class="res-form" id="expired-form-different">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Replacement Product</label>
                                    <div class="product-search-wrap">
                                        <input type="text" class="form-control" id="expiredDiffProductSearch" placeholder="Search product..." autocomplete="off">
                                        <div class="product-dropdown" id="expiredDiffProductDropdown">
                                            @foreach($products as $p)
                                            <div class="product-dropdown-item" data-id="{{ $p->id }}" data-name="{{ $p->product_name }}">{{ $p->product_name }} — {{ $p->varient_name ?? '' }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3"><label class="form-label">Qty</label><input type="number" class="form-control" min="1" placeholder="Qty"></div>
                            </div>
                        </div>
                        <div class="res-form" id="expired-form-composite">
                            <div class="row g-2">
                                <div class="col-md-4"><label class="form-label">Refund Amount (₹)</label><input type="number" class="form-control" placeholder="0.00" step="0.01"></div>
                                <div class="col-md-5">
                                    <label class="form-label">Partial Replacement</label>
                                    <div class="product-search-wrap">
                                        <input type="text" class="form-control" placeholder="Search product..." autocomplete="off">
                                        <div class="product-dropdown">
                                            @foreach($products as $p)
                                            <div class="product-dropdown-item" data-id="{{ $p->id }}" data-name="{{ $p->product_name }}">{{ $p->product_name }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3"><label class="form-label">Qty</label><input type="number" class="form-control" min="1" placeholder="Qty"></div>
                            </div>
                        </div>
                        <div class="res-form" id="expired-form-wallet">
                            <div class="row g-2">
                                <div class="col-md-4"><label class="form-label">Wallet Credit (₹)</label><input type="number" class="form-control" placeholder="0.00" step="0.01"></div>
                                <div class="col-md-8"><label class="form-label">Note</label><input type="text" class="form-control" placeholder="Reason"></div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-primary" id="btnSubmitExpired">
                                <i class="bx bx-check me-1"></i>Submit Write-off
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /card-body --}}
</div>{{-- /card --}}

{{-- Cancel Order Modal --}}
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-x-circle text-danger me-1"></i>Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cancelOrderId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Order ID</label>
                    <input type="text" class="form-control" id="cancelOrderIdDisplay" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Cancellation Reason</label>
                    <textarea class="form-control" id="cancelReason" rows="3" placeholder="Enter reason for cancellation..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Refund Method</label>
                    <select class="form-select" id="cancelRefundMethod">
                        <option value="razorpay">Refund to Original Payment (Razorpay)</option>
                        <option value="wallet">Add to Customer Wallet</option>
                        <option value="manual">Manual Refund</option>
                        <option value="none">No Refund</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Refund Amount (₹)</label>
                    <input type="number" class="form-control" id="cancelRefundAmount" placeholder="0.00" step="0.01">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-danger" id="btnConfirmCancel">
                    <i class="bx bx-check me-1"></i>Confirm Cancellation
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/app.js') }}"></script>
<script>
    window.returnOrders    = @json($returnOrders);
    window.cancelledOrders = @json($cancelledOrders);
    window.damagedStock    = @json($damagedStock);
    window.expiredBatches  = @json($expiredBatches);
    window.allProducts     = @json($products);
    window.csrfToken       = "{{ csrf_token() }}";
</script>
<script src="{{ URL::asset('assets/js/app/ProductManagementPage.js') }}"></script>
@endsection