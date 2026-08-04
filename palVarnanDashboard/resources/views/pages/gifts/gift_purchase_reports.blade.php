@extends('layouts.master')

@section('title')
    Gift Purchase Reports
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
@endsection

@section('content')

@component('components.breadcrumb')
    @slot('li_1') Home @endslot
    @slot('title') Gift Purchase Reports @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <ul class="nav nav-tabs card-header-tabs" id="purchaseTab">
                    <li class="nav-item">
                        <button class="nav-link active"
                                data-bs-toggle="tab"
                                data-bs-target="#supplier-panel"
                                data-report="supplier">
                            <i class="ri-user-3-line me-1"></i>
                            Supplier-wise Purchase
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link"
                                data-bs-toggle="tab"
                                data-bs-target="#variance-panel"
                                data-report="variance">
                            <i class="ri-line-chart-line me-1"></i>
                            Cost Variance
                        </button>
                    </li>
                </ul>

                <a href="{{ url('/reports') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                    <i class="mdi mdi-arrow-left"></i> Back
                </a>

            </div>

            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Supplier</label>
                        <select id="filter-supplier" class="form-control">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->supplier_name }}">
                                    {{ $s->supplier_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button id="reset-filter" class="btn btn-danger w-100">Reset</button>
                    </div>
                </div>

                <div class="tab-content">

                    {{-- SUPPLIER --}}
                    <div class="tab-pane fade show active" id="supplier-panel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Supplier</th>
                                        <th>Total Orders</th>
                                        <th>Total Quantity</th>
                                        <th>Total Amount (₹)</th>
                                    </tr>
                                </thead>
                                <tbody id="supplier-body">
                                    @forelse($supplierData as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row->supplier_name }}</td>
                                            <td>{{ $row->total_orders }}</td>
                                            <td>{{ $row->total_qty }}</td>
                                            <td>₹{{ number_format($row->total_amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No Data Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- VARIANCE --}}
                    <div class="tab-pane fade" id="variance-panel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Product</th>
                                        <th>Purchase Price (₹)</th>
                                        <th>Selling Price (₹)</th>
                                        <th>Variance (₹)</th>
                                    </tr>
                                </thead>
                                <tbody id="variance-body">
                                    @forelse($varianceData as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row->product_name }}</td>
                                            <td>₹{{ number_format($row->purchase_price, 2) }}</td>
                                            <td>₹{{ number_format($row->selling_price, 2) }}</td>
                                            <td class="{{ $row->variance > 0 ? 'text-success' : 'text-danger' }}">
                                                ₹{{ number_format($row->variance, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No Data Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function () {

    $('#filter-supplier').on('change', function () {
        applyFilters();
    });

    $('#reset-filter').on('click', function () {
        $('#filter-supplier').val('');
        applyFilters();
    });

    function applyFilters() {
        const params = {
            supplier:    $('#filter-supplier').val(),
            report_type: getActiveTab()
        };

        $('#supplier-body, #variance-body')
            .html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');

        $.ajax({
            url:  '{{ route("gift.purchase.report.filter.data") }}',
            type: 'GET',
            data: params,
            success: function (res) {
                if (params.report_type === 'supplier') {
                    renderSupplier(res.data);
                } else {
                    renderVariance(res.data);
                }
            }
        });
    }

    function getActiveTab() {
        return $('#purchaseTab .nav-link.active').data('report');
    }

    function renderSupplier(rows) {
        let tbody = $('#supplier-body');
        tbody.empty();
        if (!rows.length) {
            tbody.append('<tr><td colspan="5" class="text-center">No Data</td></tr>');
            return;
        }
        rows.forEach((r, i) => {
            tbody.append(`
                <tr>
                    <td>${i + 1}</td>
                    <td>${r.supplier_name}</td>
                    <td>${r.total_orders}</td>
                    <td>${r.total_qty}</td>
                    <td>₹${parseFloat(r.total_amount).toFixed(2)}</td>
                </tr>
            `);
        });
    }

    function renderVariance(rows) {
        let tbody = $('#variance-body');
        tbody.empty();
        if (!rows.length) {
            tbody.append('<tr><td colspan="5" class="text-center">No Data</td></tr>');
            return;
        }
        rows.forEach((r, i) => {
            let color = r.variance > 0 ? 'text-success' : 'text-danger';
            tbody.append(`
                <tr>
                    <td>${i + 1}</td>
                    <td>${r.product_name}</td>
                    <td>₹${parseFloat(r.purchase_price).toFixed(2)}</td>
                    <td>₹${parseFloat(r.selling_price).toFixed(2)}</td>
                    <td class="${color}">₹${parseFloat(r.variance).toFixed(2)}</td>
                </tr>
            `);
        });
    }
});
</script>

<script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/app.js') }}"></script>
@endsection