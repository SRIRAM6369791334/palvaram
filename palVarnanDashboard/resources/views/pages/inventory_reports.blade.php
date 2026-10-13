@extends('layouts.master')
@section('title')
    Inventory Reports
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Inventory Reports
        @endslot
    @endcomponent

    {{-- STAT CARDS --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1 fw-semibold" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px;">Total Products</p>
                        <h4 class="mb-0 fw-bold text-primary">{{ $totalProducts ?? 0 }}</h4>
                        <small class="text-muted">Across all variants</small>
                    </div>
                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="ri-box-3-line text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1 fw-semibold" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px;">Total Stock Units</p>
                        <h4 class="mb-0 fw-bold text-success">{{ $totalStock ?? 0 }}</h4>
                        <small class="text-muted">Available stock</small>
                    </div>
                    <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="ri-stack-line text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1 fw-semibold" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px;">Low Stock Items</p>
                        <h4 class="mb-0 fw-bold text-warning">{{ $lowStockCount ?? 0 }}</h4>
                        <small class="text-muted">Below threshold</small>
                    </div>
                    <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="ri-error-warning-line text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1 fw-semibold" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px;">Dead Stock Items</p>
                        <h4 class="mb-0 fw-bold text-danger">{{ $deadStockCount ?? 0 }}</h4>
                        <small class="text-muted">0 sales, stagnant</small>
                    </div>
                    <div class="avatar-sm bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="ri-archive-line text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <ul class="nav nav-tabs card-header-tabs" id="inventoryTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="stock-tab" data-bs-toggle="tab"
                                    data-bs-target="#stock-panel" type="button" role="tab"
                                    data-report="stock_on_hand">
                                    <i class="ri-box-3-line me-1"></i> Stock on Hand
                                    <span class="badge bg-primary ms-1" id="badge-stock">{{ count($stockOnHand ?? []) }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="low-tab" data-bs-toggle="tab"
                                    data-bs-target="#low-panel" type="button" role="tab"
                                    data-report="low_stock">
                                    <i class="ri-error-warning-line me-1"></i> Low Stock
                                    <span class="badge bg-warning ms-1" id="badge-low">{{ count($lowStock ?? []) }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="dead-tab" data-bs-toggle="tab"
                                    data-bs-target="#dead-panel" type="button" role="tab"
                                    data-report="dead_stock">
                                    <i class="ri-archive-line me-1"></i> Dead Stock
                                    <span class="badge bg-secondary ms-1" id="badge-dead">{{ count($deadStock ?? []) }}</span>
                                </button>
                            </li>
                        </ul>

                        {{-- Export Buttons --}}
                        <div class="d-flex gap-2">
                            <a href="{{ url('/reports') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                            <button id="export-excel" class="btn btn-success btn-sm">
                                <i class="ri-file-excel-line me-1"></i> Export Excel
                            </button>
                            <button id="export-pdf" class="btn btn-danger btn-sm">
                                <i class="ri-file-pdf-line me-1"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    {{-- FILTERS --}}
                    <div class="row mb-3 g-2">
                        <!-- <div class="col-md-3">
                            <label class="form-label">Country</label>
                            <select id="filter-country" class="form-control select2">
                                <option value="">All Countries</option>
                                @foreach($countries ?? [] as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div> -->
                        <div class="col-md-3">
                            <label class="form-label">Warehouse</label>
                            <select id="filter-warehouse" class="form-control select2">
                                <option value="">All Warehouses</option>
                                @foreach($warehouses ?? [] as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Branch</label>
                            <select id="filter-branch" class="form-control select2">
                                <option value="">All Branches</option>
                                @foreach($branches ?? [] as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Category</label>
                            <select id="filter-category" class="form-control select2">
                                <option value="">All Categories</option>
                                @foreach($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button id="reset-filter" class="btn btn-danger w-100">
                                Reset
                            </button>
                        </div>
                    </div>

                 
                    <div class="tab-content" id="inventoryTabContent">

                        
                        <div class="tab-pane fade show active" id="stock-panel" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0" id="stock-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Product Name</th>
                                            <th>Variant</th>
                                            <th>SKU</th>
                                            <th>Category</th>
                                            <th>Country</th>
                                            <th>Warehouse</th>
                                            <th>Branch</th>
                                            <th>Overall Stock</th>
                                            <th>Available Stock</th>
                                            <th>Sold Stock</th>
                                            <th>MRP (₹)</th>
                                            <th>Last Updated</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="stock-body">
                                        @forelse($stockOnHand ?? [] as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->productname ?? '-' }}</td>
                                                <td>{{ $item->varient_name ?? '-' }}</td>
                                                <td><code>{{ $item->sku ?? '-' }}</code></td>
                                                <td>{{ $item->cate_name ?? '-' }}</td>
                                                <td>{{ $item->country_name ?? '-' }}</td>
                                                <td>{{ $item->warehouse_name ?? '-' }}</td>
                                                <td>{{ $item->branch_name ?? '-' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <!-- <div class="progress flex-grow-1" style="height:6px; min-width:60px;">
                                                            @php
                                                                $pct = $item->overallstock > 0 ? min(100, round($item->availablestock / $item->overallstock * 100)) : 0;
                                                                $barColor = $pct > 50 ? 'bg-success' : ($pct > 20 ? 'bg-warning' : 'bg-danger');
                                                            @endphp
                                                            <div class="progress-bar {{ $barColor }}" style="width:{{ $pct }}%"></div>
                                                        </div> -->
                                                        <span>{{ $item->overallstock }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ $item->availablestock }}</td>
                                                <td>{{ $item->salestock }}</td>
                                                <td>₹{{ number_format($item->mrp_price ?? 0, 2) }}</td>
                                                <td>{{ $item->last_stockupdate_date ?? '-' }}</td>
                                                <td>
                                                    @if(($item->availablestock ?? 0) > 50)
                                                        <span class="badge bg-success">Healthy</span>
                                                    @elseif(($item->availablestock ?? 0) > 10)
                                                        <span class="badge bg-warning text-dark">Moderate</span>
                                                    @else
                                                        <span class="badge bg-danger">Critical</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="14" class="text-center text-muted py-4">No Data Found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        
                        <div class="tab-pane fade" id="low-panel" role="tabpanel">
                            <div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-3" role="alert" style="font-size:13px;">
                                <i class="ri-error-warning-line fs-5"></i>
                                Items where available stock is at or below the low stock threshold.
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0" id="low-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Product Name</th>
                                            <th>Variant</th>
                                            <th>SKU</th>
                                            <th>Category</th>
                                            <th>Country</th>
                                            <th>Branch</th>
                                            <th>Available Stock</th>
                                            <th>Threshold</th>
                                            <th>MRP (₹)</th>
                                            <th>Last Updated</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="low-body">
                                        @forelse($lowStock ?? [] as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->productname ?? '-' }}</td>
                                                <td>{{ $item->varient_name ?? '-' }}</td>
                                                <td><code>{{ $item->sku ?? '-' }}</code></td>
                                                <td>{{ $item->cate_name ?? '-' }}</td>
                                                <td>{{ $item->country_name ?? '-' }}</td>
                                                <td>{{ $item->branch_name ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-danger fs-6 px-2">{{ $item->availablestock }}</span>
                                                </td>
                                                <td>{{ $item->low_stocks ?? '-' }}</td>
                                                <td>₹{{ number_format($item->mrp_price ?? 0, 2) }}</td>
                                                <td>{{ $item->last_stockupdate_date ?? '-' }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary reorder-btn"
                                                        data-id="{{ $item->id }}" title="Reorder">
                                                        <i class="ri-refresh-line"></i> Reorder
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center text-muted py-4">No Low Stock Items Found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        
                        <div class="tab-pane fade" id="dead-panel" role="tabpanel">
                            <div class="alert alert-secondary d-flex align-items-center gap-2 py-2 mb-3" role="alert" style="font-size:13px;">
                                <i class="ri-archive-line fs-5"></i>
                                Items with zero sales and no stock movement for over 30 days.
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0" id="dead-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Product Name</th>
                                            <th>Variant</th>
                                            <th>SKU</th>
                                            <th>Category</th>
                                            <th>Country</th>
                                            <th>Branch</th>
                                            <th>Overall Stock</th>
                                            <th>Sold Stock</th>
                                            <th>MRP (₹)</th>
                                            <th>Last Updated</th>
                                            <th>Days Stagnant</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dead-body">
                                        @forelse($deadStock ?? [] as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->productname ?? '-' }}</td>
                                                <td>{{ $item->varient_name ?? '-' }}</td>
                                                <td><code>{{ $item->sku ?? '-' }}</code></td>
                                                <td>{{ $item->cate_name ?? '-' }}</td>
                                                <td>{{ $item->country_name ?? '-' }}</td>
                                                <td>{{ $item->branch_name ?? '-' }}</td>
                                                <td>{{ $item->overallstock }}</td>
                                                <td><span class="text-muted">{{ $item->salestock }}</span></td>
                                                <td>₹{{ number_format($item->mrp_price ?? 0, 2) }}</td>
                                                <td>{{ $item->last_stockupdate_date ?? '-' }}</td>
                                                <td>
                                                    @php
                                                        $days = $item->last_stockupdate_date
                                                            ? now()->diffInDays(\Carbon\Carbon::parse($item->last_stockupdate_date))
                                                            : '-';
                                                    @endphp
                                                    <span class="badge bg-secondary">{{ $days }} days</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center text-muted py-4">No Dead Stock Items Found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>{{-- end tab-content --}}
                </div>{{-- end card-body --}}
            </div>{{-- end card --}}
        </div>
    </div>

@endsection

@section('script')
<script>
$(document).ready(function () {

$('#filter-country, #filter-warehouse, #filter-branch, #filter-category').on('change', function () {
    applyFilters();
});

function applyFilters() {

    $('#stock-body, #low-body, #dead-body')
        .html('<tr><td colspan="14" class="text-center">Loading...</td></tr>');
        
    const params = {
        country_id:   $('#filter-country').val(),
        warehouse_id: $('#filter-warehouse').val(),
        branch_id:    $('#filter-branch').val(),
        category_id:  $('#filter-category').val(),
        report_type:  getActiveTab()
    };

    $.ajax({
        url: '{{ route("inventory.report.filter.data") }}', // create this route
        type: 'GET',
        data: params,
        success: function (res) {

            if (params.report_type === 'stock_on_hand') {
                renderStock(res.data);
            } 
            else if (params.report_type === 'low_stock') {
                renderLow(res.data);
            } 
            else {
                renderDead(res.data);
            }

            // Update badge counts
            $('#badge-stock').text(res.stock_count || 0);
            $('#badge-low').text(res.low_count || 0);
            $('#badge-dead').text(res.dead_count || 0);
        }
    });
}


// ── RESET FILTER ──
$('#reset-filter').on('click', function () {

    $('#filter-country').val('');
    $('#filter-warehouse').html('<option value="">All Warehouses</option>');
    $('#filter-branch').html('<option value="">All Branches</option>');
    $('#filter-category').html('<option value="">All Categories</option>');

    applyFilters(); // reload full data
});


$('#filter-country').on('change', function () {
    const countryId = $(this).val();

    // Reset dependent dropdowns
    resetSelect('#filter-warehouse', 'All Warehouses');
    resetSelect('#filter-branch',    'All Branches');
    resetSelect('#filter-category',  'All Categories');

    if (!countryId) return;

    $.get('{{ route("inventory.report.filter.warehouses") }}', { country_id: countryId }, function (data) {
        populateSelect('#filter-warehouse', data, 'id', 'warehouse_name', 'All Warehouses');
    });

    $.get('{{ route("inventory.report.filter.categories") }}', { country_id: countryId }, function (data) {
        populateSelect('#filter-category', data, 'id', 'category_name', 'All Categories');
    });
});

// Warehouse → Branches
$('#filter-warehouse').on('change', function () {
    const warehouseId = $(this).val();

    resetSelect('#filter-branch', 'All Branches');

    if (!warehouseId) return;

    $.get('{{ route("inventory.report.filter.branches") }}', { warehouse_id: warehouseId }, function (data) {
        populateSelect('#filter-branch', data, 'id', 'branch_name', 'All Branches');
    });
});

// Helper: reset a select to just the default option
function resetSelect(selector, placeholder) {
    $(selector).html(`<option value="">${placeholder}</option>`);
}

// Helper: populate a select from an array of objects
function populateSelect(selector, items, valKey, textKey, placeholder) {
    const $el = $(selector);
    $el.html(`<option value="">${placeholder}</option>`);
    items.forEach(function (item) {
        $el.append(`<option value="${item[valKey]}">${item[textKey]}</option>`);
    });
}

    // ── RENDER STOCK ON HAND ──
    function renderStock(rows) {
        const tbody = $('#stock-body');
        tbody.empty();
        if (!rows.length) {
            tbody.append('<tr><td colspan="14" class="text-center text-muted py-4">No Data Found</td></tr>');
            return;
        }
        rows.forEach(function (item, i) {
            const pct       = item.overallstock > 0 ? Math.min(100, Math.round(item.availablestock / item.overallstock * 100)) : 0;
            const barColor  = pct > 50 ? 'bg-success' : pct > 20 ? 'bg-warning' : 'bg-danger';
            const badgeCls  = item.availablestock > 50 ? 'bg-success' : item.availablestock > 10 ? 'bg-warning text-dark' : 'bg-danger';
            const badgeTxt  = item.availablestock > 50 ? 'Healthy' : item.availablestock > 10 ? 'Moderate' : 'Critical';
            tbody.append(`
                <tr>
                    <td>${i + 1}</td>
                    <td>${item.productname || '-'}</td>
                    <td>${item.varient_name || '-'}</td>
                    <td><code>${item.sku || '-'}</code></td>
                    <td>${item.cate_name || '-'}</td>
                    <td>${item.country_name || '-'}</td>
                    <td>${item.warehouse_name || '-'}</td>
                    <td>${item.branch_name || '-'}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:6px;min-width:60px;">
                                <div class="progress-bar ${barColor}" style="width:${pct}%"></div>
                            </div>
                            <span>${item.overallstock}</span>
                        </div>
                    </td>
                    <td>${item.availablestock}</td>
                    <td>${item.salestock}</td>
                    <td>₹${parseFloat(item.mrp_price || 0).toFixed(2)}</td>
                    <td>${item.last_stockupdate_date || '-'}</td>
                    <td><span class="badge ${badgeCls}">${badgeTxt}</span></td>
                </tr>`);
        });
    }

    // ── RENDER LOW STOCK ──
    function renderLow(rows) {
        const tbody = $('#low-body');
        tbody.empty();
        if (!rows.length) {
            tbody.append('<tr><td colspan="12" class="text-center text-muted py-4">No Low Stock Items Found</td></tr>');
            return;
        }
        rows.forEach(function (item, i) {
            tbody.append(`
                <tr>
                    <td>${i + 1}</td>
                    <td>${item.productname || '-'}</td>
                    <td>${item.varient_name || '-'}</td>
                    <td><code>${item.sku || '-'}</code></td>
                    <td>${item.cate_name || '-'}</td>
                    <td>${item.country_name || '-'}</td>
                    <td>${item.branch_name || '-'}</td>
                    <td><span class="badge bg-danger fs-6 px-2">${item.availablestock}</span></td>
                    <td>${item.low_stocks || '-'}</td>
                    <td>₹${parseFloat(item.mrp_price || 0).toFixed(2)}</td>
                    <td>${item.last_stockupdate_date || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary reorder-btn" data-id="${item.id}">
                            <i class="ri-refresh-line"></i> Reorder
                        </button>
                    </td>
                </tr>`);
        });
    }

    // ── RENDER DEAD STOCK ──
    function renderDead(rows) {
        const tbody = $('#dead-body');
        tbody.empty();
        if (!rows.length) {
            tbody.append('<tr><td colspan="12" class="text-center text-muted py-4">No Dead Stock Items Found</td></tr>');
            return;
        }
        rows.forEach(function (item, i) {
            const days = item.days_stagnant || '-';
            tbody.append(`
                <tr>
                    <td>${i + 1}</td>
                    <td>${item.productname || '-'}</td>
                    <td>${item.varient_name || '-'}</td>
                    <td><code>${item.sku || '-'}</code></td>
                    <td>${item.cate_name || '-'}</td>
                    <td>${item.country_name || '-'}</td>
                    <td>${item.branch_name || '-'}</td>
                    <td>${item.overallstock}</td>
                    <td><span class="text-muted">${item.salestock}</span></td>
                    <td>₹${parseFloat(item.mrp_price || 0).toFixed(2)}</td>
                    <td>${item.last_stockupdate_date || '-'}</td>
                    <td><span class="badge bg-secondary">${days} days</span></td>
                </tr>`);
        });
    }

    // ── EXPORT EXCEL ──
    $('#export-excel').on('click', function () {
        const params = buildParams();
        const tab    = getActiveTab();
        window.location.href = '{{ route("inventory.report.export.excel") }}' + '?' + params + '&report_type=' + tab;
    });

    // ── EXPORT PDF ──
    $('#export-pdf').on('click', function () {
        const params = buildParams();
        const tab    = getActiveTab();
        window.location.href = '{{ route("inventory.report.export.pdf") }}' + '?' + params + '&report_type=' + tab;
    });

    function buildParams() {
        return $.param({
            country_id:   $('#filter-country').val(),
            warehouse_id: $('#filter-warehouse').val(),
            branch_id:    $('#filter-branch').val(),
            category_id:  $('#filter-category').val(),
        });
    }

    function getActiveTab() {
        return $('#inventoryTab .nav-link.active').data('report') || 'stock_on_hand';
    }

    // ── REORDER BUTTON ──
    $(document).on('click', '.reorder-btn', function () {
        const id = $(this).data('id');
        // Hook into your reorder route here
        alert('Reorder triggered for stock ID: ' + id);
    });

});
</script>

<script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/app.js') }}"></script>
@endsection