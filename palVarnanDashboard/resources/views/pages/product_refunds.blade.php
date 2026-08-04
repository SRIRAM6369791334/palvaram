@extends('layouts.master')
@section('title')
    Pal Varnan
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <style>
        .modal-content {
            transition: all 1s ease-in;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Product Refunds
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <p style="position: relative;
                top: 33px;
                left: 25px;
                font-weight: bold;">
                    Search:</p>
                <div class="card-body">
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="productRefundModal" tabindex="-1" aria-labelledby="productRefundModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductRefundsModalLabel">PRODUCT REFUNDS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="table_gridjs_modal"></div>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-primary refund_btn" disabled>Refund</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script>
        window.productRefunds = @json($productRefunds);
        window.productRefunds = window.productRefunds.map((item) => {
            item.created_at = new Date(item.created_at).toLocaleDateString("en-IN", {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            }).split(" ").join("-")
            return item;
        })
    </script>
    <script src="{{ URL::asset('assets/js/app/ProductRefundsPage.js') }}"></script>
@endsection
