@extends('layouts.master')
@section('title')
    Pal Varnan
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between pb-0">
                <div class="d-flex gap-4 align-items-center">
                    <h5 class="card-title">Product Order Summery >></h5>
                    <div class="card">
                        <div class="card-header px-3 py-0">
                            Order ID:
                        </div>
                        <div class="card-body px-3 py-1">
                            <h5 class="card-title"> {{ $productSlotss[0]->order->order_id }}</h5>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header px-3 py-0">
                            Customer Name:
                        </div>
                        <div class="card-body px-3 py-1">
                            <h5 class="card-title">{{ $productSlotss[0]->order->customer->name }}</h5>
                        </div>
                    </div>
                </div>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('productOrders.index') }}">Product Order Summery</a></li>

                        <li class="breadcrumb-item active">Product Orders</li>

                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <p style="position: relative;
                top: 20px;
                left: 25px;
                font-weight: bold;">
                    Search:</p>
                <div class="card-body pt-0">
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>


    {{-- viewOrdersModal --}}




@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script>
        window.productSlotss = @json($productSlotss);
    </script>
    <script src="{{ URL::asset('assets/js/app/orderslotPage.js') }}"></script>
@endsection
