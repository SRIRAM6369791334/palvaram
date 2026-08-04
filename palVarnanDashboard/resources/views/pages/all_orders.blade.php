@extends('layouts.master')

@section('title')
    All Orders
@endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.css') }}">

@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Home @endslot
    @slot('title') All Orders @endslot
@endcomponent

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
                <div id="table-gridjs"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/app.js') }}"></script>

<script>
    window.productOrders = @json($productOrders);
</script>

<script src="{{ URL::asset('assets/js/app/AllOrdersPage.js') }}"></script>
@endsection