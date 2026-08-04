@extends('layouts.master')
@section('title')
    Gift Product Stock
@endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">

<style>
/* ── Reset & Root ── */
*, *::before, *::after { box-sizing: border-box; }

.sk-page {
   
    padding: 0 0 2rem;
}

/* ── Stat Cards ── */
.sk-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 1.5rem;
}
.sk-stat {
    background: #fff;
    border: 0.5px solid #e5e5e5;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    transition: box-shadow 0.2s;
}
.sk-stat:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.07); }
.sk-stat-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    margin-bottom: 8px;
}
.sk-stat-icon.blue  { background: #E6F1FB; color: #185FA5; }
.sk-stat-icon.green { background: #EAF3DE; color: #3B6D11; }
.sk-stat-icon.amber { background: #FAEEDA; color: #854F0B; }
.sk-stat-label { font-size: 12px; color: #888; font-weight: 400; }
.sk-stat-value {
    font-size: 26px;
    font-weight: 600;
    color: #111;
    letter-spacing: -0.5px;
   
    line-height: 1.1;
}

/* ── Card Wrapper ── */
.sk-card {
    background: #fff;
    border: 0.5px solid #e5e5e5;
    border-radius: 16px;
    overflow: hidden;
}

/* ── Tabs ── */
.sk-tab-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 20px 0;
    border-bottom: 0.5px solid #efefef;
}
.sk-tabs {
    display: flex;
    gap: 0;
    background: #f5f5f5;
    border-radius: 10px;
    padding: 3px;
}
.sk-tab {
    padding: 8px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    background: transparent;
    color: #888;
    transition: all 0.18s ease;
    display: flex;
    align-items: center;
    gap: 7px;
    /* font-family: 'Sora', sans-serif; */
    white-space: nowrap;
}
.sk-tab i { font-size: 15px; }
.sk-tab.active {
    background: #fff;
    color: #111;
    border: 0.5px solid #e0e0e0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
}
.sk-tab:hover:not(.active) { color: #333; }

/* ── Toolbar ── */
.sk-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 20px 12px;
}
.sk-search {
    position: relative;
    flex: 1;
    max-width: 300px;
}
.sk-search i {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 15px;
    color: #aaa;
    pointer-events: none;
}
.sk-search input {
    width: 100%;
    padding: 8px 12px 8px 34px;
    border: 0.5px solid #e0e0e0;
    border-radius: 9px;
    font-size: 13px;
    background: #fafafa;
    color: #111;
    /* font-family: 'Sora', sans-serif; */
    outline: none;
    transition: border-color 0.15s, background 0.15s;
}
.sk-search input:focus { border-color: #185FA5; background: #fff; }
.sk-search input::placeholder { color: #bbb; }

.sk-toolbar-right { display: flex; gap: 7px; margin-left: auto; }
.sk-icon-btn {
    width: 34px; height: 34px;
    border: 0.5px solid #e0e0e0;
    border-radius: 9px;
    background: #fafafa;
    color: #666;
    font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all 0.15s;
}
.sk-icon-btn:hover { background: #fff; color: #111; border-color: #ccc; }

/* ── Table ── */
.sk-table-wrap {
    padding: 0 20px 20px;
}
.sk-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    border: 0.5px solid #efefef;
    border-radius: 12px;
    overflow: hidden;
}
.sk-table thead tr {
    background: #f9f9f9;
}
.sk-table th {
    padding: 11px 14px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    color: #999;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    border-bottom: 0.5px solid #efefef;
    white-space: nowrap;
    user-select: none;
}
.sk-table th.right { text-align: right; }
.sk-table th.center { text-align: center; }
.sk-table td {
    padding: 11px 14px;
    border-bottom: 0.5px solid #f3f3f3;
    color: #222;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sk-table tr:last-child td { border-bottom: none; }
.sk-table tbody tr {
    transition: background 0.12s;
}
.sk-table tbody tr:hover td { background: #fafafa; }
.sk-table td.right { text-align: right; }
.sk-table td.center { text-align: center; }

/* ── Badges ── */
.sk-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
    white-space: nowrap;
}
.sk-badge.blue   { background: #E6F1FB; color: #0C447C; }
.sk-badge.green  { background: #EAF3DE; color: #27500A; }
.sk-badge.amber  { background: #FAEEDA; color: #633806; }
.sk-badge.red    { background: #FCEBEB; color: #791F1F; }
.sk-badge.gray   { background: #F1EFE8; color: #444441; }

/* ── Stock numbers ── */
.sk-num {
    /* font-family: 'DM Mono', monospace; */
    font-size: 13px;
    font-weight: 500;
}
.sk-num.ok     { color: #3B6D11; }
.sk-num.low    { color: #854F0B; }
.sk-num.danger { color: #A32D2D; font-weight: 600; }

/* ── Row serial ── */
.sk-sno {
    width: 24px; height: 24px;
    background: #f3f3f3;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 10px;
    color: #aaa;
    /* font-family: 'DM Mono', monospace; */
}

/* ── Product name ── */
.sk-product { font-weight: 500; color: #111; }

/* ── Action buttons ── */
.sk-actions { display: flex; gap: 5px; justify-content: center; }
.sk-btn {
    padding: 5px 11px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 500;
    cursor: pointer;
    border: 0.5px solid;
    /* font-family: 'Sora', sans-serif; */
    transition: all 0.15s;
    display: inline-flex; align-items: center; gap: 4px;
}
.sk-btn i { font-size: 11px; }
.sk-btn-add    { background: #EAF3DE; color: #27500A; border-color: #C0DD97; }
.sk-btn-add:hover { background: #C0DD97; }
.sk-btn-reduce { background: #f5f5f5; color: #555; border-color: #e0e0e0; }
.sk-btn-reduce:hover { background: #ebebeb; color: #111; }

/* ── Pagination ── */
.sk-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px 16px;
    font-size: 12px;
    color: #aaa;
    border-top: 0.5px solid #f3f3f3;
}
.sk-page-btns { display: flex; gap: 4px; }
.sk-page-btn {
    width: 30px; height: 30px;
    border: 0.5px solid #e5e5e5;
    border-radius: 8px;
    background: #fff;
    color: #666;
    font-size: 12px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    /* font-family: 'Sora', sans-serif; */
    transition: all 0.15s;
}
.sk-page-btn.active { background: #185FA5; color: #fff; border-color: #185FA5; }
.sk-page-btn:hover:not(.active) { background: #f5f5f5; color: #111; }
.sk-page-btn:disabled { opacity: 0.4; cursor: default; }

/* ── Tab panes ── */
.sk-pane { display: none; }
.sk-pane.active { display: block; }

/* ── Empty state ── */
.sk-empty {
    text-align: center;
    padding: 48px 20px;
    color: #ccc;
}
.sk-empty i { font-size: 36px; display: block; margin-bottom: 10px; color: #ddd; }
.sk-empty p { font-size: 13px; color: #bbb; }

/* ── Responsive ── */
@media (max-width: 768px) {
    .sk-stats { grid-template-columns: 1fr 1fr; }
    .sk-stat-value { font-size: 20px; }
}
@media (max-width: 500px) {
    .sk-stats { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Home @endslot
    @slot('title') Gift Product Stock @endslot
@endcomponent

<div class="sk-page">

    {{-- ── Stat Cards ── --}}
    <div class="sk-stats">
        <div class="sk-stat">
            <div class="sk-stat-icon blue"><i class="mdi mdi-warehouse"></i></div>
            <span class="sk-stat-label">Total Warehouse Stock</span>
            <span class="sk-stat-value" id="stat-warehouse">—</span>
        </div>
        <div class="sk-stat">
            <div class="sk-stat-icon green"><i class="mdi mdi-store"></i></div>
            <span class="sk-stat-label">Total Store Stock</span>
            <span class="sk-stat-value" id="stat-store">—</span>
        </div>
        <div class="sk-stat">
            <div class="sk-stat-icon amber"><i class="mdi mdi-alert-circle-outline"></i></div>
            <span class="sk-stat-label">Low Stock Items</span>
            <span class="sk-stat-value" id="stat-low">—</span>
        </div>
    </div>

    {{-- ── Main Card ── --}}
    <div class="sk-card">

        <div class="sk-tab-bar d-flex justify-content-between align-items-center">

            <div class="sk-tabs">
                <button class="sk-tab active" id="tab-wh" onclick="switchTab('warehouse')">
                    <i class="mdi mdi-warehouse"></i> Warehouse Stock
                </button>

                <button class="sk-tab" id="tab-st" onclick="switchTab('store')">
                    <i class="mdi mdi-store"></i> Store Stock
                </button>
            </div>

            <div>
                <a href="{{ url('/transfer') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                    <i class="mdi mdi-arrow-left"></i> Back
                </a>
            </div>

        </div>

     
        <div class="sk-pane active" id="pane-warehouse">
            <div class="sk-toolbar">
                <div class="sk-search">
                    <i class="mdi mdi-magnify"></i>
                    <input type="text" id="wh-search" placeholder="Search products, category, warehouse…" oninput="renderWarehouse()">
                </div>
                <!-- <div class="sk-toolbar-right">
                    <button class="sk-icon-btn" title="Filter"><i class="mdi mdi-filter-outline"></i></button>
                    <button class="sk-icon-btn" title="Export"><i class="mdi mdi-download-outline"></i></button>
                </div> -->
            </div>

            <div class="sk-table-wrap">
                <table class="sk-table">
                    <thead>
                        <tr>
                            <th style="width:44px">S.No.</th>
                            <th style="width:90px">Country</th>
                            <th style="width:120px">Warehouse</th>
                            <th style="width:120px">Category</th>
                            <th>Product</th>
                            <th class="right" style="width:100px">Available</th>
                            <th class="right" style="width:80px">Sale</th>
                            <!-- <th class="center" style="width:130px">Actions</th> -->
                        </tr>
                    </thead>
                    <tbody id="wh-tbody">
                        <tr><td colspan="8"><div class="sk-empty"><i class="mdi mdi-package-variant"></i><p>Loading…</p></div></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="sk-pagination">
                <span id="wh-info"></span>
                <div class="sk-page-btns" id="wh-pages"></div>
            </div>
        </div>

      
        <div class="sk-pane" id="pane-store">
            <div class="sk-toolbar">
                <div class="sk-search">
                    <i class="mdi mdi-magnify"></i>
                    <input type="text" id="st-search" placeholder="Search stores, products…" oninput="renderStore()">
                </div>
                <!-- <div class="sk-toolbar-right">
                    <button class="sk-icon-btn" title="Filter"><i class="mdi mdi-filter-outline"></i></button>
                    <button class="sk-icon-btn" title="Export"><i class="mdi mdi-download-outline"></i></button>
                </div> -->
            </div>

            <div class="sk-table-wrap">
                <table class="sk-table">
                    <thead>
                        <tr>
                            <th style="width:44px">S.No.</th>
                            <th style="width:90px">Country</th>
                            <th style="width:120px">Warehouse</th>
                            <th style="width:110px">Store</th>
                            <th style="width:120px">Category</th>
                            <th>Product</th>
                            <th class="right" style="width:100px">Store Stock</th>
                        </tr>
                    </thead>
                    <tbody id="st-tbody">
                        <tr><td colspan="7"><div class="sk-empty"><i class="mdi mdi-store-outline"></i><p>Loading…</p></div></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="sk-pagination">
                <span id="st-info"></span>
                <div class="sk-page-btns" id="st-pages"></div>
            </div>
        </div>

    </div>{{-- /.sk-card --}}
</div>{{-- /.sk-page --}}



<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <div class="modal-header" style="border-bottom:0.5px solid #f0f0f0; padding:20px 24px;">
                <h5 class="modal-title" style=" font-weight:600; font-size:16px;">
                    <i class="mdi mdi-plus-circle-outline me-2" style="color:#3B6D11;"></i>Add Stock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <form id="addStockForm" novalidate>
                    @csrf
                    <input type="hidden" name="productid" id="productid">
                    <div class="mb-3">
                        <label style="font-size:13px; font-weight:500; color:#555; margin-bottom:6px; display:block;">Stock Quantity *</label>
                        <input type="text" class="form-control" id="stock_quantity" name="stock_quantity"
                            placeholder="Enter quantity to add"
                            style="border-radius:10px; border:0.5px solid #e0e0e0;  font-size:15px; padding:10px 14px;">
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn edit_submit_btn" type="submit"
                            style="background:#EAF3DE; color:#27500A; border:0.5px solid #C0DD97; border-radius:10px; padding:10px 32px;  font-weight:500; font-size:14px; width:100%;">
                            <i class="mdi mdi-plus me-1"></i> Add Quantity
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="updateProductModal" tabindex="-1" aria-labelledby="updateProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <div class="modal-header" style="border-bottom:0.5px solid #f0f0f0; padding:20px 24px;">
                <h5 class="modal-title" style=" font-weight:600; font-size:16px;">
                    <i class="mdi mdi-minus-circle-outline me-2" style="color:#854F0B;"></i>Reduce Stock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <form id="addStockForm1" novalidate>
                    @csrf
                    <input type="hidden" name="productid" id="productid1">
                    <div class="mb-3">
                        <label style="font-size:13px; font-weight:500; color:#555; margin-bottom:6px; display:block;">Available Stock</label>
                        <input type="text" class="form-control" id="availa_quantity1" readonly
                            style="border-radius:10px; border:0.5px solid #e0e0e0;  font-size:15px; padding:10px 14px; background:#f9f9f9; color:#888;">
                    </div>
                    <div class="mb-3">
                        <label style="font-size:13px; font-weight:500; color:#555; margin-bottom:6px; display:block;">Quantity to Reduce *</label>
                        <input type="text" class="form-control" id="stock_quantity1" name="stock_quantity"
                            placeholder="Enter quantity to reduce"
                            style="border-radius:10px; border:0.5px solid #e0e0e0;  font-size:15px; padding:10px 14px;">
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn edit1_submit_btn" type="submit"
                            style="background:#FAEEDA; color:#633806; border:0.5px solid #FAC775; border-radius:10px; padding:10px 32px;  font-weight:500; font-size:14px; width:100%;">
                            <i class="mdi mdi-minus me-1"></i> Reduce Quantity
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')


<script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/app.js') }}"></script>

<script>
    window.stocks      = @json($stocks);
    window.storeStocks = @json($storeStocks);
    window.allWarehouses     = @json($warehouses);
    window.allBranches       = @json($warehouseBranches);
    window.allCountries      = @json($countries);
    window.allCategories     = @json($categories);
    window.allSubcategories  = @json($subcategories);
    window.allProducts       = @json($products);
    window.allVariants       = @json($variants);
    window.updateStackUrl    = "{{ route('stacks.update') }}";
</script>
<script src="{{ URL::asset('assets/js/app/ProductStock.js') }}"></script>
@endsection
