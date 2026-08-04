@extends('layouts.master')

@section('title') Gift Stock Distribution @endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
@endsection

@section('content')

@component('components.breadcrumb')
    @slot('li_1') Home @endslot
    @slot('title') Gift Stock Distribution @endslot
@endcomponent

<style>
    .dist-card {
        background: #fff;
        border: 1px solid #ebebeb;
        border-radius: 16px;
        padding: 28px 32px;
        margin-bottom: 24px;
    }
    .dist-section-title {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dist-section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #f0f0f0;
    }
    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 600;
    }
    .stock-badge.good     { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .stock-badge.low      { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .stock-badge.none     { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .stock-badge.unknown  { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
    .dist-submit-btn {
        background: #0a0a0f;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 11px 32px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: opacity .2s;
        cursor: pointer;
    }
    .dist-submit-btn:hover   { opacity: 0.85; }
    .dist-submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }
</style>

<div class="row" style=" justify-content: center;">
    
    <div class="col-xl-8">

       
        <div class="dist-card">

            <div class="dist-section-title d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-warehouse" style="font-size:16px;color:#6366f1;"></i>
                    <span>Source — Warehouse</span>
                </div>

                <a href="{{ url('/transfer') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                    <i class="mdi mdi-arrow-left"></i> Back
                </a>

            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country</label>
                    <select id="dist_country_id" class="form-control">
                        <option value="">Select Country</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Warehouse</label>
                    <select id="dist_warehouse_id" class="form-control" disabled>
                        <option value="">Select Warehouse</option>
                    </select>
                </div>
            </div>

            <div class="dist-section-title">
                <i class="mdi mdi-store-outline" style="font-size:16px;color:#10b981;"></i>
                Destination — Store
            </div>

            <div class="row mb-4">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Store</label>
                    <select id="dist_branch_id" class="form-control" disabled>
                        <option value="">Select Store</option>
                    </select>
                </div>

                <!-- <div class="col-md-6">
                    <label class="form-label fw-semibold">Current Store Stock</label>
                    <div class="mt-1" id="store_stock_display">
                        <span class="stock-badge unknown">
                            <i class="mdi mdi-store-outline"></i>
                            Select store
                        </span>
                    </div>
                </div> -->

            </div>

            <div class="dist-section-title">
                <i class="mdi mdi-package-variant" style="font-size:16px;color:#f59e0b;"></i>
                Product
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Product</label>
                    <select id="dist_product_id" class="form-control" disabled>
                        <option value="">Select Product</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->product_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Variant</label>
                    <select id="dist_variant_id" class="form-control" disabled>
                        <option value="">-- No Variant --</option>
                    </select>
                </div>
            </div>

            <div class="row mb-4">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Available Stock in Warehouse
                    </label>

                    <div class="mt-1" id="available_stock_display">
                        <span class="stock-badge unknown">
                            <i class="mdi mdi-help-circle-outline"></i>
                            Select product first
                        </span>
                    </div>
                </div>

                <div class="col-md-6">

                    <div class="d-flex align-items-center justify-content-between mb-1">

                    <label class="form-label fw-semibold mb-0">
                        Quantity to Distribute
                    </label>

                    <div id="store_stock_display">
                        <span class="stock-badge unknown">
                            <i class="mdi mdi-store-outline"></i>
                            Store: 0
                        </span>
                    </div>

                </div>

                    <div class="d-flex align-items-center gap-3">

                        <input type="number" id="dist_quantity" class="form-control" min="1" placeholder="Enter quantity" disabled >                   

                    </div>

                    <small id="qty_error" class="text-danger d-none">
                        Exceeds available stock.
                    </small>

                </div>

            </div>

            <div class="d-flex justify-content-end">
                <button class="dist-submit-btn" id="dist_submit_btn" disabled>
                    <i class="mdi mdi-send-outline me-1"></i> Distribute Stock
                </button>
            </div>

        </div>

    </div>

    <div class="col-xl-5">


        
            <div class="dist-card">
                <div class="dist-section-title">
                    <i class="mdi mdi-history" style="font-size:16px;color:#6366f1;"></i>
                    Recent Distributions
                </div>
                <div id="dist-history-table"></div>
            </div>
       

    </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/app.js') }}"></script>

<script>
    window.distHistory = @json($history);
</script>

<script>

let availableStock = 0;

function renderHistoryGrid() {
    let table = document.getElementById('dist-history-table');
    if (!table) return;
    table.innerHTML = '';

    new gridjs.Grid({
        columns: [
            'Product', 'Warehouse', 'Store', 'Qty', 'Date',
            { name: 'Status', sort: false },
            { name: 'Action', sort: false },
        ],
        pagination: { limit: 8 },
        search: false,
        sort: false,
        data: window.distHistory.map(d => [
            d.product?.product_name ?? '-',
            d.warehouse?.warehouse_name ?? '-',
            d.branch?.branch_name ?? '-',
            d.quantity,
            new Date(d.distributed_at).toLocaleDateString(),
            gridjs.html(distStatusBadge(d.status)),
            gridjs.html(distActionButtons(d)),
        ]),
        style: {
            table: { border: '1px solid #e5e7eb' },
            th: { background: '#f9fafb', fontSize: '11px', textAlign: 'center' },
            td: { textAlign: 'center', fontSize: '13px' }
        }
    }).render(table);
}

function distStatusBadge(status) {
    const map = {
        pending:   'background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;',
        accepted:  'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;',
        cancelled: 'background:#fef2f2;color:#dc2626;border:1px solid #fecaca;',
    };
    const style = map[status] || map.pending;
    return `<span style="padding:4px 12px;border-radius:100px;font-size:12px;font-weight:600;${style}">${status ?? 'pending'}</span>`;
}

function distActionButtons(d) {
    const status = d.status ?? 'pending';
    if (status !== 'pending') return '-';
    return `
        <button onclick="handleDistAction(${d.id}, 'accept')"
            style="padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;
                   background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;
                   cursor:pointer;margin-right:4px;">
            ✓ Accept
        </button>
        <button onclick="handleDistAction(${d.id}, 'cancel')"
            style="padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;
                   background:#fef2f2;color:#dc2626;border:1px solid #fecaca;cursor:pointer;">
            ✗ Cancel
        </button>`;
}

function handleDistAction(id, action) {
    const label = action === 'accept' ? 'Accept' : 'Cancel';
    Swal.fire({
        title: `${label} this distribution?`,
        icon:  action === 'accept' ? 'question' : 'warning',
        showCancelButton:  true,
        confirmButtonText: `Yes, ${label}`,
        cancelButtonText:  'No',
        reverseButtons:    true,
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url:    `/stock-distributions/${id}/${action}`,
            method: 'POST',
            data:   { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                Swal.fire('Done', res.message, 'success').then(() => location.reload());
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Action failed', 'error');
            }
        });
    });
}



$('#dist_country_id').on('change', function () {

    let id = $(this).val();

    $('#dist_warehouse_id').html('<option value="">Select Warehouse</option>');
    $('#dist_branch_id').html('<option value="">Select Store</option>');
    $('#dist_variant_id').html('<option value="">-- No Variant --</option>');

    $('#dist_warehouse_id, #dist_branch_id, #dist_product_id, #dist_variant_id, #dist_quantity')
        .prop('disabled', true);

    $('#dist_product_id').val('');
    $('#dist_quantity').val('');

    resetStockDisplay();

    if (!id) return;

    $.ajax({
        url: "{{ url('/get-warehouses') }}",
        type: "GET",
        data: {
            country_id: id
        },

        success: function (data) {

            console.log('Warehouses:', data);

            $('#dist_warehouse_id').html('<option value="">Select Warehouse</option>');

            if (data.length > 0) {

                data.forEach(function (w) {

                    $('#dist_warehouse_id').append(
                        `<option value="${w.id}">
                            ${w.warehouse_name}
                        </option>`
                    );

                });

                $('#dist_warehouse_id').prop('disabled', false);

            } else {

                $('#dist_warehouse_id').html(
                    '<option value="">No Warehouse Found</option>'
                );

            }
        },

        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
});


$('#dist_warehouse_id').on('change', function () {

    let id = $(this).val();

    $('#dist_branch_id').html('<option value="">Select Store</option>');
    $('#dist_variant_id').html('<option value="">-- No Variant --</option>');

    $('#dist_branch_id, #dist_product_id, #dist_variant_id, #dist_quantity')
        .prop('disabled', true);

    $('#dist_product_id').val('');
    $('#dist_quantity').val('');

    resetStockDisplay();

    if (!id) return;

    $.ajax({
        url: "{{ url('/get-branches') }}",
        type: "GET",
        data: {
            warehouse_id: id
        },

        success: function (data) {

            console.log('Branches:', data);

            $('#dist_branch_id').html('<option value="">Select Store</option>');

            if (data.length > 0) {

                data.forEach(function (b) {

                    $('#dist_branch_id').append(
                        `<option value="${b.id}">
                            ${b.branch_name}
                        </option>`
                    );

                });

                $('#dist_branch_id').prop('disabled', false);
                $('#dist_product_id').prop('disabled', false);

            } else {

                $('#dist_branch_id').html(
                    '<option value="">No Store Found</option>'
                );
            }
        },

        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
});


$('#dist_product_id').on('change', function () {

    let productId = $(this).val();

    $('#dist_variant_id').html('<option value="">-- No Variant --</option>');
    $('#dist_variant_id, #dist_quantity').prop('disabled', true);

    $('#dist_quantity').val('');

    resetStockDisplay();

    if (!productId) return;

    $.ajax({
        url: "{{ url('/get-product-variants') }}",
        type: "GET",
        data: {
            product_id: productId
        },

        success: function (variants) {

            console.log('Variants:', variants);

            if (variants.length > 0) {

                variants.forEach(function (v) {

                    $('#dist_variant_id').append(
                        `<option value="${v.id}">
                            ${v.varient_name ?? v.varient}
                        </option>`
                    );

                });

            }

            $('#dist_variant_id').prop('disabled', false);

            fetchStoreStock();

            if (variants.length === 0) {
                fetchAvailableStock();
                fetchStoreStock();
            }
        },

        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
});


$('#dist_variant_id').on('change', function () {
    fetchAvailableStock();
    fetchStoreStock();
});



function fetchAvailableStock() {

    let productId   = $('#dist_product_id').val();
    let warehouseId = $('#dist_warehouse_id').val();
    let variantId   = $('#dist_variant_id').val();

    if (!productId || !warehouseId) return;

    $('#available_stock_display').html(`
        <span class="stock-badge unknown">
            <i class="mdi mdi-loading mdi-spin"></i>
            Loading...
        </span>
    `);

    $.ajax({
        url: "{{ url('/get-stocks') }}",
        type: "GET",
        data: {
            product_id: productId,
            warehouse_id: warehouseId,
            variant_id: variantId
        },

        success: function (data) {

            availableStock = parseInt(data.available) || 0;

            let badgeClass =
                availableStock > 10 ? 'good' :
                availableStock > 0 ? 'low' :
                'none';

            let icon =
                availableStock > 10 ? 'mdi-check-circle-outline' :
                availableStock > 0 ? 'mdi-alert-outline' :
                'mdi-close-circle-outline';

            $('#available_stock_display').html(`
                <span class="stock-badge ${badgeClass}">
                    <i class="mdi ${icon}"></i>
                    ${availableStock} units available
                </span>
            `);

            if (availableStock > 0) {

                $('#dist_quantity')
                    .prop('disabled', false)
                    .attr('max', availableStock);

                $('#dist_submit_btn').prop('disabled', false);

            } else {

                $('#dist_quantity').prop('disabled', true);
                $('#dist_submit_btn').prop('disabled', true);

            }
        },

        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
}



$('#dist_quantity').on('input', function () {

    let qty = parseInt($(this).val()) || 0;

    if (qty > availableStock) {

        $('#qty_error').removeClass('d-none');
        $('#dist_submit_btn').prop('disabled', true);

    } else {

        $('#qty_error').addClass('d-none');
        $('#dist_submit_btn').prop('disabled', qty <= 0);

    }
});



$('#dist_submit_btn').on('click', function () {

    let qty = parseInt($('#dist_quantity').val()) || 0;

    if (qty <= 0 || qty > availableStock) {

        showDistToast('Please enter valid quantity.', 'error');
        return;

    }

    $(this)
        .prop('disabled', true)
        .html('<i class="mdi mdi-loading mdi-spin me-1"></i> Distributing...');

    $.ajax({

        url: "{{ url('/stock-distribution/store') }}",
        method: "POST",

        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            product_id: $('#dist_product_id').val(),
            variant_id: $('#dist_variant_id').val(),
            warehouse_id: $('#dist_warehouse_id').val(),
            branch_id: $('#dist_branch_id').val(),
            quantity: qty,
        },

        success: function () {

            showDistToast('Stock distributed successfully!', 'success');

            location.reload();
        },

        error: function (xhr) {

            showDistToast(
                xhr.responseJSON?.message || 'Distribution failed',
                'error'
            );

            $('#dist_submit_btn')
                .prop('disabled', false)
                .html('<i class="mdi mdi-send-outline me-1"></i> Distribute Stock');
        }
    });
});


function resetStockDisplay() {

    availableStock = 0;

    $('#available_stock_display').html(`
        <span class="stock-badge unknown">
            <i class="mdi mdi-help-circle-outline"></i>
            Select product first
        </span>
    `);

    $('#store_stock_display').html(`
        <span class="stock-badge unknown">
            <i class="mdi mdi-store-outline"></i>
            Select store
        </span>
    `);

    $('#dist_submit_btn').prop('disabled', true);
}



function showDistToast(message, type = 'success') {

    let existing = document.getElementById('dist-toast');

    if (existing) existing.remove();

    let colors = {
        success: {
            bg: '#f0fdf4',
            border: '#bbf7d0',
            text: '#15803d',
            icon: 'mdi-check-circle-outline'
        },

        error: {
            bg: '#fef2f2',
            border: '#fecaca',
            text: '#dc2626',
            icon: 'mdi-alert-circle-outline'
        }
    };

    let c = colors[type];

    let toast = document.createElement('div');

    toast.id = 'dist-toast';

    toast.style.cssText = `
        position:fixed;
        top:24px;
        right:24px;
        z-index:99999;
        background:${c.bg};
        border:1px solid ${c.border};
        color:${c.text};
        border-radius:12px;
        padding:14px 20px;
        display:flex;
        align-items:center;
        gap:10px;
        font-size:14px;
        font-weight:500;
        box-shadow:0 4px 24px rgba(0,0,0,0.08);
    `;

    toast.innerHTML = `
        <i class="mdi ${c.icon}" style="font-size:20px;"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 4000);
}

function fetchStoreStock() {

    let productId   = $('#dist_product_id').val();
    let warehouseId = $('#dist_warehouse_id').val();
    let branchId    = $('#dist_branch_id').val();
    let variantId   = $('#dist_variant_id').val();

    if (!productId || !warehouseId || !branchId) return;

    $('#store_stock_display').html(`
        <span class="stock-badge unknown">
            <i class="mdi mdi-loading mdi-spin"></i> Loading...
        </span>
    `);

    $.ajax({
        url: "{{ url('/get-store-stocks') }}",
        type: "GET",
        data: {
            product_id: productId,
            warehouse_id: warehouseId,
            branch_id: branchId,
            variant_id: variantId
        },
        success: function (data) {

            let stock = parseInt(data.available) || 0;

            let badgeClass =
                stock > 10 ? 'good' :
                stock > 0 ? 'low' :
                'none';

            let icon =
                stock > 10 ? 'mdi-check-circle-outline' :
                stock > 0 ? 'mdi-alert-outline' :
                'mdi-close-circle-outline';

            $('#store_stock_display').html(`
                <span class="stock-badge ${badgeClass}">
                    <i class="mdi ${icon}"></i>
                    ${stock} units in store
                </span>
            `);
        }
    });
}

$('#dist_branch_id').on('change', function () {
    fetchStoreStock();
});



</script>
@endsection