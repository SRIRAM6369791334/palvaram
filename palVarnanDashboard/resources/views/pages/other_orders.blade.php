@extends('layouts.master')
@section('title')
    Other Orders
@endsection
@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.css') }}">
<style>
    .stat-card {
        border-radius: 12px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        transition: transform 0.18s, box-shadow 0.18s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }
    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .stat-label { font-size: 13px; color: #888; margin-bottom: 2px; }
    .stat-value { font-size: 22px; font-weight: 700; color: #222; }

    .filter-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }
    .filter-tab {
        padding: 7px 18px;
        border-radius: 20px;
        border: 2px solid transparent;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s;
        background: #f4f5f7;
        color: #555;
    }
    .filter-tab.active, .filter-tab:hover { color: #fff; }
    .filter-tab[data-filter="all"].active    { background: #6c757d; border-color: #6c757d; }
    .filter-tab[data-filter="cancelled"].active  { background: #dc3545; border-color: #dc3545; }
    .filter-tab[data-filter="return"].active     { background: #fd7e14; border-color: #fd7e14; }
    .filter-tab[data-filter="unavailable"].active{ background: #0d6efd; border-color: #0d6efd; }

    .filter-tab[data-filter="all"]:hover        { background: #6c757d; }
    .filter-tab[data-filter="cancelled"]:hover  { background: #dc3545; }
    .filter-tab[data-filter="return"]:hover     { background: #fd7e14; }
    .filter-tab[data-filter="unavailable"]:hover{ background: #0d6efd; }

    /* gridjs overrides */
    #table-other-orders .gridjs-wrapper { border-radius: 8px; overflow: hidden; }
</style>
@endsection
@section('content')
@component('components.breadcrumb')
    @slot('li_1') Home @endslot
    @slot('title') Other Orders @endslot
@endcomponent


<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-white">
            <div class="stat-icon" style="background:#fdecea;">
                <i class="bx bx-x-circle text-danger"></i>
            </div>
            <div>
                <div class="stat-label">Cancelled Orders</div>
                <div class="stat-value text-danger">{{ $cancelledCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-white">
            <div class="stat-icon" style="background:#fff3e0;">
                <i class="bx bx-revision text-warning"></i>
            </div>
            <div>
                <div class="stat-label">Returned Orders</div>
                <div class="stat-value text-warning">{{ $returnCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-white">
            <div class="stat-icon" style="background:#e8f0fe;">
                <i class="bx bx-package text-primary"></i>
            </div>
            <div>
                <div class="stat-label">Unavailable Stock Orders</div>
                <div class="stat-value text-primary">{{ $unavailableCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-white">
            <div class="stat-icon" style="background:#fce8e8;">
                <i class="bx bx-rupee text-danger"></i>
            </div>
            <div>
                <div class="stat-label">Total Unavailable Amount</div>
                <div class="stat-value text-danger">₹{{ number_format($totalUnavailableAmount, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="mb-0">Special Orders</h5>
                <div class="filter-tabs" id="filterTabs">
                    <button class="filter-tab active" data-filter="all">All</button>
                    <button class="filter-tab" data-filter="cancelled">Cancelled</button>
                    <button class="filter-tab" data-filter="return">Returned</button>
                    <button class="filter-tab" data-filter="unavailable">Unavailable Stock</button>
                    <a href="{{ url('/orders') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div id="table-other-orders"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/app.js') }}"></script>
<script>
    window.specialOrders = @json($specialOrders);
</script>
<script src="{{ URL::asset('assets/js/app/OtherOrdersPage.js') }}"></script>
@endsection