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
            Product Orders
        @endslot
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
    <div class="modal fade" id="assignToModal" tabindex="-1" aria-labelledby="assignToModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductOrdersModalLabel">Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="#" method="POST" id="changestatus" novalidate="novalidate">
                        @csrf
                        <input type="hidden" name="user_id" id="cusid">
                        <input type="hidden" name="phone_number" id="cusnum">
                        <div class="row">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="order_id_input">Order Id*</label>
                                    <input type="text" name="order_id" class="form-control " value=""
                                        id="order_id_input" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="customer_name_input">Customer Name*</label>
                                    <input type="text" name="customer_name" class="form-control " value=""
                                        id="customer_name_input" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="label-control" for="add_status_select">
                                        Select Status*
                                    </label>

                                    <select class="form-select select2" name="select_status" id="add_status_select">
                                        <option value="" selected>Select status</option>

                                        <option value="1">Packing</option>
                                        <option value="unavailable_stock">Unavailable Stock</option>
                                        <option value="cancel">Cancel</option>
                                        <option value="return">Return</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12 d-none" id="unavailable_qty_div">
                                <div class="form-group">
                                    <label class="label-control">
                                        Unavailable Stock Count
                                    </label>

                                    <input type="number"
                                        min="1"
                                        class="form-control"
                                        name="unavailable_count"
                                        id="unavailable_count"
                                        placeholder="Enter unavailable stock count">
                                </div>
                            </div>

                            <div class="col-lg-12 d-none" id="amount_div">
                                <div class="form-group">
                                    <label class="label-control">
                                        Unavailable Stock Amount
                                    </label>

                                    <input type="text"
                                        class="form-control"
                                        name="unavailable_amount"
                                        id="unavailable_amount"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-lg-12 d-none" id="reason_div">
                                <div class="form-group">
                                    <label class="label-control">
                                        Reason
                                    </label>

                                    <textarea class="form-control"
                                            name="reason"
                                            id="reason"
                                            rows="3"></textarea>
                                </div>
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

    {{-- <div class="modal fade" id="assignToModal1" tabindex="-1" aria-labelledby="assignToModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductOrdersModalLabel">Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="pickupstatus" method="POST" id="changestatus2" novalidate="novalidate">
                        @csrf
                        <input type="hidden" name="user_id" id="cusid6">

                        <div class="row">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="order_id_input1">Order Id*</label>
                                    <input type="text" name="order_id" class="form-control " value=""
                                        id="order_id_input1" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="customer_name_input1">Customer Name*</label>
                                    <input type="text" name="customer_name" class="form-control " value=""
                                        id="customer_name_input1" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="label-control" for="add_status_select">
                                        Select Status*
                                    </label>

                                    <select class="form-select select2" name="select_status" id="add_status_select">
                                        <option value="" selected>Select status</option>

                                        <option value="1">Packing</option>
                                        <option value="unavailable_stock">Unavailable Stock</option>
                                        <option value="cancel">Cancel</option>
                                        <option value="return">Return</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12 d-none" id="unavailable_qty_div">
                                <div class="form-group">
                                    <label class="label-control">
                                        Unavailable Stock Count
                                    </label>

                                    <input type="number"
                                        min="1"
                                        class="form-control"
                                        name="unavailable_count"
                                        id="unavailable_count"
                                        placeholder="Enter unavailable stock count">
                                </div>
                            </div>

                            <div class="col-lg-12 d-none" id="amount_div">
                                <div class="form-group">
                                    <label class="label-control">
                                        Unavailable Stock Amount
                                    </label>

                                    <input type="text"
                                        class="form-control"
                                        name="unavailable_amount"
                                        id="unavailable_amount"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-lg-12 d-none" id="reason_div">
                                <div class="form-group">
                                    <label class="label-control">
                                        Reason
                                    </label>

                                    <textarea class="form-control"
                                            name="reason"
                                            id="reason"
                                            rows="3"></textarea>
                                </div>
                            </div>

                        </div>


                        <div class="text-center">
                            <button class="btn btn-primary mt-3 add_submit_btn2" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- product refund --}}
    <div class="modal fade" id="RefundModal" tabindex="-1" aria-labelledby="RefundModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="RefundModalLabel">Product Refund Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="#" method="POST" id="changestatus1" novalidate="novalidate">
                        <input type="hidden" name="user_id" id="cusid1">
                        <div class="row">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="order_id_input">Order Id*</label>
                                    <input type="text" name="order_id" class="form-control " value=""
                                        id="order_id_input1" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="customer_name_input">Customer Name*</label>
                                    <input type="text" name="customer_name" class="form-control " value=""
                                        id="customer_name_input1" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="customer_name_input">Customer Name*</label>
                                    <textarea class="form-control " id="customer_resons_input1" readonly></textarea>
                                </div>
                            </div>

                        </div>


                        <div class="text-center">
                            <button class="btn btn-primary mt-3 reson_submit_btn" type="submit">Submit</button>
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
        window.productOrders = @json($productOrders);
    </script>
    {{-- <script src="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script> --}}

    <script src="{{ URL::asset('assets/js/app/ProductOrdersPage.js') }}"></script>
@endsection
