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
            Combo Product Stock
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

    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModallLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addStockForm" novalidate>
                        @csrf
                        <div class="row">
                        <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="product_quantity">Stock Quantity*</label>
                                    <input type="text" class="form-control" id="stock_quantity"
                                        name="stock_quantity" placeholder="Product Quantity" required>

                                        <input type="hidden" name="productid" id="productid">
                                </div>
                            </div>
                         </div>


                        <div class="text-center">
                            <button class="btn btn-primary edit_submit_btn mt-3" type="submit">Add Quantity</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateProductModal" tabindex="-1" aria-labelledby="updateProductModallLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateProductModalLabel">Reduce Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addStockForm1" novalidate>
                        @csrf
                        <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="product_quantity">Available Stock</label>
                                <input type="text" class="form-control" id="availa_quantity1"
                                    placeholder="Product Quantity" readonly>



                            </div>
                                <div class="mb-3">
                                    <label class="form-label" for="product_quantity">Stock Quantity*</label>
                                    <input type="text" class="form-control" id="stock_quantity1"
                                        name="stock_quantity" placeholder="Product Quantity" required>


                                        <input type="hidden" name="productid" id="productid1">
                                </div>
                            </div>
                         </div>


                        <div class="text-center">
                            <button class="btn btn-primary edit1_submit_btn mt-3" type="submit">Reduce Quantity</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- reduce stock --}}


@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script>
        window.combostocks = @json($combostocks);
    </script>
    <script src="{{ URL::asset('assets/js/app/CompoStockPage.js') }}"></script>
@endsection
