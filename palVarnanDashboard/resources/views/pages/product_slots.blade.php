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
                    <h5 class="card-title">Product Order Slots >></h5>
                    <div class="card">
                        <div class="card-header px-3 py-0">
                            Order ID:
                        </div>
                        <div class="card-body px-3 py-1">
                            <h5 class="card-title"> {{ $productSlots[0]->order->order_id }}</h5>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header px-3 py-0">
                            Customer Name:
                        </div>
                        <div class="card-body px-3 py-1">
                            <h5 class="card-title">{{ $productSlots[0]->order->customer->name }}</h5>
                        </div>
                    </div>
                </div>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('productOrders.index') }}">Product Order</a></li>

                        <li class="breadcrumb-item active">Product Slots</li>

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

    <div class="modal fade" id="assignToModal" tabindex="-1" aria-labelledby="assignToModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductSlotsModalLabel">Assign Delivery Person</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="#" method="POST" id="assignDeliveryPartner" novalidate="novalidate">
                        <input type="text" name="order_id" value="" id="orderid_input" hidden="">
                        <div class="row">
                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label class="label-control" for="customer_name_input">Customer Name*</label>
                                    <input type="text" class="form-control " value="" disabled=""
                                        id="customer_name_input">
                                </div>
                            </div>
                            <div class="col-lg-6 ">
                                <div class="form-group">
                                    <label class="label-control" for="customer_area_input">Customer Area*</label>
                                    <input type="text" class="form-control " value="" disabled=""
                                        id="customer_area_input">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 p-0 mt-3">
                            <div class="form-group">
                                <label class="label-control" for="delivery_par">Delivery Partner*</label>
                                <select class="form-control deliverystatus delivery_id_input" id="delivery_par"
                                    name="deliver_id" required="">
                                    <option value="" hidden="">No delivery person found!</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary mt-3 add_submit_btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- viewOrdersModal --}}



    <div class="modal fade" id="viewOrdersModal" tabindex="-1" aria-labelledby="viewOrdersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductSlotsModalLabel">PRODUCT SLOTS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="table_gridjs_modal">


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
        window.productSlots = @json($productSlots);
    </script>
    <script src="{{ URL::asset('assets/js/app/ProductSlotsPage.js') }}"></script>
@endsection
