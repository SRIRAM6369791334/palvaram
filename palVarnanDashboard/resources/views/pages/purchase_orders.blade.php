@extends('layouts.master')

@section('title')
    Purchase Orders
@endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
@endsection

@section('content')

@component('components.breadcrumb')
    @slot('li_1') Home @endslot
    @slot('title') Purchase Orders @endslot
@endcomponent

<style>
                    .po-tab-link.active {
                        background: #0a0a0f !important;
                        color: #fff !important;
                        border-color: #0a0a0f !important;
                    }
                    .po-tab-link.active .po-tab-count {
                        background: rgba(255,255,255,0.15) !important;
                        color: #fff !important;
                    }
                    .po-tab-link:hover:not(.active) {
                        background: #f9fafb !important;
                        color: #111827 !important;
                    }

                    .po-table td,
                    .po-table th {
                        white-space: nowrap;
                    }


                    .po-table .form-control,
                    .po-table select {
                        width: 100% !important;
                        min-width: 130px;
                        max-width: 130px;
                        box-sizing: border-box;
                    }


                    .po-table td:nth-child(5) .form-control, 
                    .po-table td:nth-child(6) .form-control, 
                    .po-table td:nth-child(7) .form-control,  
                    .po-table td:nth-child(8) .form-control {  
                        min-width: 60px !important;
                        max-width: 60px !important;
                    }

                </style>

<div class="row">
    <div class="col-lg-12">
        <div class="card">

           <div class="d-flex justify-content-between align-items-center px-3 pt-3">

                    <p class="mb-0 fw-bold">
                        Search:
                    </p>

                    <a href="{{ url('/orders') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>

                </div>

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    

                    <button class="btn btn-success"
                        onclick="window.location.href='/products'"
                        style="width:190px">
                        <i class="mdi mdi-plus me-1"></i> Enter new Product?
                    </button>

                    <button class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#createPOModal"
                        style="width:160px">
                        <i class="mdi mdi-plus me-1"></i> Create PO
                    </button>
                </div>

                {{-- ── TAB PILLS ── --}}
                <ul class="nav mb-3" id="poTabs" style="gap:6px;">
                    @php
                        $tabs = [
                            'all'      => ['label' => 'All Orders',  'icon' => 'mdi-format-list-bulleted', 'color' => '#6366f1'],
                            'pending'  => ['label' => 'Pending',     'icon' => 'mdi-clock-outline',        'color' => '#f59e0b'],
                            'ordered'  => ['label' => 'Ordered',     'icon' => 'mdi-truck-delivery-outline','color' => '#3b82f6'],
                            'received' => ['label' => 'Received',    'icon' => 'mdi-check-circle-outline', 'color' => '#10b981'],
                        ];
                        $counts = [
                            'all'      => $orders->count(),
                            'pending'  => $orders->where('status','pending')->count(),
                            'ordered'  => $orders->where('status','ordered')->count(),
                            'received' => $orders->where('status','received')->count(),
                        ];
                    @endphp

                    @foreach($tabs as $key => $tab)
                    <li class="nav-item">
                        <a class="nav-link po-tab-link d-flex align-items-center gap-2 {{ $key === 'all' ? 'active' : '' }}"
                            id="tab-{{ $key }}"
                            data-bs-toggle="tab"
                            href="#panel-{{ $key }}"
                            style="border-radius:10px; padding:8px 18px; font-size:13.5px; font-weight:500;
                                border: 1px solid #e5e7eb; color:#6b7280; background:#fff; transition:all .2s;">
                            <i class="mdi {{ $tab['icon'] }}" style="font-size:16px;"></i>
                            {{ $tab['label'] }}
                            <span class="po-tab-count" style="background:#f3f4f6; color:#6b7280;
                                border-radius:100px; padding:1px 9px; font-size:11px; font-weight:600;">
                                {{ $counts[$key] }}
                            </span>
                        </a>
                    </li>
                    @endforeach
                </ul>

                

                {{-- ── TAB PANELS ── --}}
                <div class="tab-content">
                    @foreach(array_keys($tabs) as $key)
                    <div class="tab-pane fade {{ $key === 'all' ? 'show active' : '' }}" id="panel-{{ $key }}">
                        <div id="po-table-{{ $key }}" class="mt-2"></div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="createPOModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Create Purchase Order</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="createPOForm">
                    @csrf

                    <input type="hidden" name="is_gift" value="0">
                    
                    <div class="row">

                        <div class="col-md-4">
                            <label>Country</label>
                            <select name="country_id" id="country_id" class="form-control select2" required>
                                <option value="">Select Country</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Supplier Name</label>
                            <select name="supplier_id" id="supplier_id" class="form-control select2" required disabled>
                                <option value="">Select Country First</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Warehouse</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-control select2">
                                <option value="">Select Warehouse</option>
                            </select>
                        </div>

                        <!-- <div class="col-md-4">
                            <label>Store</label>
                            <select name="branch_id" id="branch_id" class="form-control select2">
                                <option value="">Select</option>
                            </select>
                        </div> -->

                    </div>

                    <hr>

                    <table class="table po-table">
                        <thead>
                            <tr>
                                <th style="width:220px">Barcode</th>
                                <th>Item Code</th>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>Value</th>
                                <th>Unit</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="po_items_body"></tbody>
                    </table>

                    <button type="button" class="btn btn-sm btn-primary" id="add_po_item">
                        + Add Item
                    </button>

                    <h5 class="mt-3">Total: ₹ <span id="po_grand_total">0</span></h5>

                    <div class="text-center mt-3">
                        <button class="btn btn-success create_po_submit_btn">
                            Submit
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="editPOModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Edit Purchase Order</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="editPOForm">
                    @csrf

                    <input type="hidden" name="is_gift" value="0">
                    <input type="hidden" id="edit_po_id">

                    <div class="row mb-3">

                        <div class="col-md-4">
                            <label>Country</label>
                            <select id="edit_country_id" class="form-control select2">
                                <option value="">Select Country</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Supplier Name</label>
                            <select id="edit_supplier_id" class="form-control select2">
                                <option value="">Select Country First</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Warehouse</label>
                            <select id="edit_warehouse_id" class="form-control select2">
                                <option value="">Select Warehouse</option>
                            </select>
                        </div>

                        <!-- <div class="col-md-4">
                            <label>Store</label>
                            <select id="edit_branch_id" class="form-control select2">
                                <option value="">Select</option>
                            </select>
                        </div> -->


                    <table class="table po-table">
                        <thead>
                            <tr>
                                <th style="width:220px">Barcode</th>
                                <th>Item Code</th>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>Value</th>
                                <th>Unit</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="edit_po_items"></tbody>
                    </table>

                    <h5>Total: ₹ <span id="edit_grand_total">0</span></h5>

                    <div class="text-center mt-3">
                        <button class="btn btn-success update_po_btn">
                            Update
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="batchEntryModal" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Receive Stock — Batch Entry
                    <small class="text-muted fs-6 ms-2">PO: <span id="batch_po_code"></span></small>
                </h5>
                <button class="btn-close" id="cancelBatchBtn" type="button"></button>
            </div>

            <div class="modal-body">
                <form id="batchEntryForm">
                    @csrf
                    <input type="hidden" id="batch_po_id" name="po_id">

                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>PO Qty</th>
                                <th>Batch No</th>
                                <th>Mfg Date</th>
                                <th>Expiry Date</th>
                                <th>Received Qty</th>
                            </tr>
                        </thead>
                        <tbody id="batch_items_body"></tbody>
                    </table>

                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-secondary me-2" id="cancelBatchBtn">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBatchBtn">
                            Confirm Received
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
    window.orders = @json($orders);
    window.allProducts = @json($products);
    window.units = @json($units);
    window.allVariants = @json($variants);
</script>

<script src="{{ URL::asset('assets/js/app/purchaseOrder.js') }}"></script>

<script>
// COUNTRY → SUPPLIER + WAREHOUSE + BARCODE OPTIONS (Create PO modal)
$('#country_id').change(function () {
    let id = $(this).val();

    // Reset & disable supplier until we know the country
    $('#supplier_id').html('<option value="">Select Country First</option>').prop('disabled', true);
    $('#warehouse_id').html('<option value="">Select</option>');

    // Refresh barcode options on any rows already added, since the old
    // selections may belong to a different country now.
    $(".barcode_select").each(function () {
        let $sel = $(this);
        if ($sel.hasClass("select2-hidden-accessible")) {
            $sel.select2('destroy');
        }
        $sel.html(buildBarcodeOptions(id)).val('');
        $sel.trigger('change'); // clears dependent fields in that row
        $sel.select2({ width: '100%', dropdownParent: $("#createPOModal") });
    });

    if (!id) return;

    $.get('/get-suppliers', { country_id: id, is_gift: 0 }, function (suppliers) {
        if (suppliers.length === 0) {
            $('#supplier_id').html('<option value="">No suppliers for this country</option>').prop('disabled', true);
        } else {
            let options = '<option value="">Select Supplier</option>';
            suppliers.forEach(s => {
                options += `<option value="${s.id}">${s.name}</option>`;
            });
            $('#supplier_id').html(options).prop('disabled', false);
        }
    });

    $.get('/get-warehouses', { country_id: id }, function (data) {
        $('#warehouse_id').html('<option value="">Select</option>');
        data.forEach(w => {
            $('#warehouse_id').append(`<option value="${w.id}">${w.warehouse_name}</option>`);
        });
    });
});


// $('#warehouse_id').change(function () {
//     let id = $(this).val();

//     $.get('/get-branches', { warehouse_id: id }, function (data) {
//         $('#branch_id').html('<option value="">Select</option>');
//         data.forEach(b => {
//             $('#branch_id').append(`<option value="${b.id}">${b.branch_name}</option>`);
//         });
//     });
// });
</script>

@endsection