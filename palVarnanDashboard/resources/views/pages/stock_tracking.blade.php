@extends('layouts.master')
@section('title') Stock Tracking @endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.css') }}">
<style>
    /* ── Stat Cards ───────────────────────────────────────────── */
    .stat-card {
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        background: #fff;
        transition: transform 0.18s, box-shadow 0.18s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.11); }
    .stat-icon {
        width: 48px; height: 48px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .stat-label { font-size: 12px; color: #888; margin-bottom: 2px; font-weight: 500; }
    .stat-value { font-size: 20px; font-weight: 700; color: #222; }

    /* ── Hierarchy Breadcrumb Filter ──────────────────────────── */
    .hierarchy-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        padding: 10px 14px;
        background: #f8f9fa;
        border-radius: 10px;
        border: 1px solid #e9ecef;
        margin-bottom: 16px;
    }
    .hierarchy-select {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 13px;
        background: #fff;
        color: #333;
        cursor: pointer;
        min-width: 140px;
    }
    .hierarchy-arrow { color: #adb5bd; font-size: 16px; }

    /* ── Stock Type Tab Pills ─────────────────────────────────── */
    .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
    .filter-tab {
        padding: 6px 16px;
        border-radius: 20px;
        border: 2px solid transparent;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s;
        background: #f4f5f7;
        color: #555;
    }
    .filter-tab.active, .filter-tab:hover { color: #fff; }
    .filter-tab[data-type="all"].active,       .filter-tab[data-type="all"]:hover       { background:#6c757d; border-color:#6c757d; }
    .filter-tab[data-type="available"].active, .filter-tab[data-type="available"]:hover { background:#198754; border-color:#198754; }
    .filter-tab[data-type="reserved"].active,  .filter-tab[data-type="reserved"]:hover  { background:#0d6efd; border-color:#0d6efd; }
    .filter-tab[data-type="damaged"].active,   .filter-tab[data-type="damaged"]:hover   { background:#dc3545; border-color:#dc3545; }
    .filter-tab[data-type="expired"].active,   .filter-tab[data-type="expired"]:hover   { background:#fd7e14; border-color:#fd7e14; }
    .filter-tab[data-type="aging"].active,     .filter-tab[data-type="aging"]:hover     { background:#6f42c1; border-color:#6f42c1; }
    .filter-tab[data-type="dead"].active,      .filter-tab[data-type="dead"]:hover      { background:#212529; border-color:#212529; }

    /* ── Stock Health Mini-bar ────────────────────────────────── */
    .stock-bar-wrap { width: 80px; height: 6px; background:#e9ecef; border-radius:3px; display:inline-block; vertical-align:middle; }
    .stock-bar-fill { height:100%; border-radius:3px; }

    /* ── Aging Badge ─────────────────────────────────────────── */
    .aging-badge {
        display: inline-block; padding: 2px 8px;
        border-radius: 12px; font-size: 11px; font-weight: 600;
    }
    .aging-fresh   { background:#d1e7dd; color:#0a3622; }
    .aging-warning { background:#fff3cd; color:#664d03; }
    .aging-old     { background:#ffe5d0; color:#6a2c00; }
    .aging-dead    { background:#f8d7da; color:#58151c; }

    /* ── GridJS override ─────────────────────────────────────── */
    #table-stock .gridjs-wrapper { border-radius: 8px; overflow: hidden; }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Home @endslot
    @slot('title') Stock Tracking @endslot
@endcomponent

{{-- ── Summary Stat Cards ─────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1e7dd;">
                <i class="bx bx-check-circle text-success"></i>
            </div>
            <div>
                <div class="stat-label">Available</div>
                <div class="stat-value text-success">{{ $stats['available'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:#cfe2ff;">
                <i class="bx bx-lock-alt text-primary"></i>
            </div>
            <div>
                <div class="stat-label">Reserved</div>
                <div class="stat-value text-primary">{{ $stats['reserved'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f8d7da;">
                <i class="bx bx-error-circle text-danger"></i>
            </div>
            <div>
                <div class="stat-label">Damaged</div>
                <div class="stat-value text-danger">{{ $stats['damaged'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:#ffe5d0;">
                <i class="bx bx-time text-warning"></i>
            </div>
            <div>
                <div class="stat-label">Expired</div>
                <div class="stat-value text-warning">{{ $stats['expired'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e9d8fd;">
                <i class="bx bx-hourglass" style="color:#6f42c1;"></i>
            </div>
            <div>
                <div class="stat-label">Aging (>30d)</div>
                <div class="stat-value" style="color:#6f42c1;">{{ $stats['aging'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dee2e6;">
                <i class="bx bx-skull text-dark"></i>
            </div>
            <div>
                <div class="stat-label">Dead Stock</div>
                <div class="stat-value text-dark">{{ $stats['dead'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Main Card ───────────────────────────────────────────────────── --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="mb-0">Stock Tracking</h5>
                <div class="filter-tabs" id="stockFilterTabs">
                    <button class="filter-tab active" data-type="all">All</button>
                    <button class="filter-tab" data-type="available">Available</button>
                    <button class="filter-tab" data-type="reserved">Reserved</button>
                    <button class="filter-tab" data-type="damaged">Damaged</button>
                    <button class="filter-tab" data-type="expired">Expired</button>
                    <button class="filter-tab" data-type="aging">Aging</button>
                    <button class="filter-tab" data-type="dead">Dead</button>
                    <a href="{{ url('/transfer') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            {{-- Hierarchy Drill-down Filters --}}
            <div class="card-body pb-0">
                <div class="hierarchy-bar">
                    <i class="bx bx-filter-alt text-secondary"></i>
                    <select class="hierarchy-select" id="filterCountry">
                        <option value="">All Countries</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <span class="hierarchy-arrow">›</span>
                    <select class="hierarchy-select" id="filterWarehouse">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" data-country="{{ $w->country_id }}">{{ $w->warehouse_name }}</option>
                        @endforeach
                    </select>
                    <span class="hierarchy-arrow">›</span>
                    <select class="hierarchy-select" id="filterBranch">
                        <option value="">All Stores</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" data-warehouse="{{ $b->warehouse_id }}">{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                    <span class="hierarchy-arrow">›</span>
                    <select class="hierarchy-select" id="filterCategory">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->cate_name ?? $cat->category_name ?? $cat->id }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-secondary ms-2" id="btnResetFilters">
                        <i class="bx bx-reset me-1"></i>Reset
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div id="table-stock"></div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="setOfferModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px; border: none; overflow: hidden;">

      <div class="modal-header" style="background: #fff3cd; border-bottom: 1px solid #ffe69c;">
        <h5 class="modal-title d-flex align-items-center gap-2" style="color:#664d03; font-weight:600;">
          <i class="bx bx-tag" style="font-size:20px;"></i> Set Offer Price
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-0">

    
        <div class="px-4 pt-3 pb-2">
          <div class="fw-semibold fs-6" id="offerModalProduct">—</div>
          <div class="text-muted" style="font-size:13px;" id="offerModalVariant">—</div>
        </div>

      
        <div class="px-4">
          <table class="table table-sm mb-0" style="font-size:13px;">
            <tbody>
              <tr>
                <td class="text-muted border-0 ps-0" style="width:45%;">Batch no.</td>
                <td class="fw-medium border-0 text-end pe-0" id="offerModalBatch">—</td>
              </tr>
              <tr>
                <td class="text-muted ps-0">Expiry date</td>
                <td class="fw-medium text-end pe-0" id="offerModalExpiry">—</td>
              </tr>
            </tbody>
          </table>
        </div>

      
        <div class="px-4 py-3 mt-2" style="background:#f8f9fa; border-top:1px solid #eee; border-bottom:1px solid #eee;">
            <div class="row g-2 text-center">
                <div class="col-4">
                    <div class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:.03em;">Purchase price</div>
                    <div class="fw-semibold" style="font-size:15px;" id="offerModalPurchasePrice">—</div>
                </div>
                <div class="col-4" style="border-left:1px solid #e0e0e0;">
                    <div class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:.03em;">MRP price</div>
                    <div class="fw-semibold" style="font-size:15px;" id="offerModalMrpPrice">—</div>
                </div>
                <div class="col-4" style="border-left:1px solid #e0e0e0;">
                    <div class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:.03em;">Selling Price</div>
                    <div class="fw-semibold" style="font-size:15px;" id="offerModalVariantOffer">—</div>
                </div>
            </div>
        </div>

       
        <div class="px-4 pt-3 pb-4">
          <label class="form-label mb-1 fw-medium" style="font-size:13px;">Offer price (₹)</label>
          <div class="input-group input-group-lg">
            <span class="input-group-text">₹</span>
            <input type="number" min="0" step="0.01" class="form-control" id="offerModalPriceInput" placeholder="Enter offer price">
          </div>
          <div class="d-flex justify-content-between mt-2" style="font-size:15px;">
            <span class="text-muted">Discount vs Selling price</span>
            <span class="fw-semibold text-success" id="offerModalDiscountBadge">—</span>
          </div>
          <input type="hidden" id="offerModalBatchId">
          <div class="small text-danger mt-2 d-none" id="offerModalError"></div>
        </div>

      </div>

      <div class="modal-footer" style="border-top:1px solid #eee;">
        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success btn-sm px-3" id="btnSaveOfferModal">
          <i class="bx bx-check me-1"></i>Save offer
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/app.js') }}"></script>
<script>
    window.stockData = @json($stockRows);
</script>
<script src="{{ URL::asset('assets/js/app/StockTrackingPage.js') }}"></script>
@endsection