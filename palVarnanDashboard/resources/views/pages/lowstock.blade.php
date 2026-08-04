@extends('layouts.master')
@section('title')
    Pal Varnan
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
            Product Low Stock
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="position-relative">
                        <div class="modal-button mt-2">
                            <div class="row align-items-start">
                                <div class="col-sm">

                                </div>

                            </div>
                            <!-- end row -->
                        </div>
                    </div>
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editstockModal" tabindex="-1" aria-labelledby="editstockModallLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editstockModalLabel">Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addStockLowForm" novalidate>
                        @csrf
                        <div class="row">
                        <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="stock_quantity1">Stock Quantity*</label>
                                    <input type="text" class="form-control" id="stock_quantity1"
                                        name="stock_quantity" placeholder="Product Quantity" required>

                                        <input type="hidden" name="productid" id="productid">
                                </div>
                            </div>
                         </div>


                        <div class="text-center">
                            <button class="btn btn-primary addstock_submit_btn mt-3" type="submit">Add Quantity</button>
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
        window.lowstocks = @json($lowstocks);
    </script>
    <script src="{{ URL::asset('assets/js/app/LowStockPage.js') }}"></script>
@endsection
