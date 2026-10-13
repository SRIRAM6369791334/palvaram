@extends('layouts.master')
@section('title')
    Pal Varnan
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Milk Orders
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {{-- <div class="position-relative">
                        <div class="modal-button mt-2">
                            <div class="row align-items-start">
                                <div class="col-sm">
                                    <div>
                                        <button type="button" class="btn btn-success  add_btn_top_el mb-4"
                                            data-bs-toggle="modal" data-bs-target="#addMilkOrdersModal"><i
                                                class="mdi mdi-plus me-1"></i> Add
                                            MilkOrders</button>
                                    </div>
                                </div>

                            </div>
                            <!-- end row -->
                        </div>
                    </div> --}}
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignToModal" tabindex="-1" aria-labelledby="assignToModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMilkOrdersModalLabel">Assign Delivery Person</h5>
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
                                <select class="form-control deliverystatus delivery_id_input select2" id="delivery_par"
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
                    <h5 class="modal-title" id="editMilkOrdersModalLabel">MILK ORDERS</h5>
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
        window.milkOrders = @json($milkOrders);
    </script>
    <script src="{{ URL::asset('assets/js/app/MilkOrdersPage.js') }}"></script>
@endsection
