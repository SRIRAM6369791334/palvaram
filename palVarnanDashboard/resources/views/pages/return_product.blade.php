
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
            Return Products
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
    <div class="modal fade" id="ReturnModal" tabindex="-1" aria-labelledby="ReturnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ReturnModalLabel">Return Products </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="#" method="POST" id="changestatus6" novalidate="novalidate">
                        <input type="hidden" name="user_id" id="cusiddelive1">
                        <input type="hidden" name="phone_number" id="numcus1">
                        <div class="row">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="order_id_input2">Order Id*</label>
                                    <input type="text" name="order_id" class="form-control " value=""
                                        id="order_id_input6" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="customer_name_input2">Customer Name*</label>
                                    <input type="text" name="customer_name" class="form-control " value=""
                                        id="customer_name_input6" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="add_status_select">Select Status*</label>
                                    <select class="form-select" name="select_status"
                                    id="add_status_select6">
                                    <option value="">Select Status</option>
                                    <option value="3">Out Of Delivery</option>
                                    <option value="4">Delivered</option>

                                </select>
                                </div>
                            </div>

                        </div>


                        <div class="text-center">
                            <button class="btn btn-primary mt-3 return_submit_btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- cod model --}}

    <div class="modal fade" id="collectedModal" tabindex="-1" aria-labelledby="collectedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="collectedModalLabel">Return Products </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="#" method="POST" id="collectstatus61" novalidate="novalidate">
                        <input type="hidden" name="user_id" id="cusiddelive16">
                        <input type="hidden" name="phone_number" id="numcus16">
                        <div class="row">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="order_id_input2">Order Id*</label>
                                    <input type="text" name="order_id" class="form-control " value=""
                                        id="order_id_input61" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="customer_name_input2">Customer Name*</label>
                                    <input type="text" name="customer_name" class="form-control " value=""
                                        id="customer_name_input61" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12" id="codamt1">
                                <div class="form-group">
                                    <label class="label-control" for="customer_name_input2">COD Amount*</label>
                                    <input type="text" name="total_amount" class="form-control " value=""
                                        id="cod_input61" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="add_status_select">Select Status*</label>
                                    <select class="form-select" name="select_status"
                                    id="add_status_select61">
                                    <option value="">Select Status</option>
                                    <option value="3">Out Of Delivery</option>
                                    <option value="4">Delivered</option>

                                </select>
                                </div>
                            </div>

                        </div>


                        <div class="text-center">
                            <button class="btn btn-primary mt-3 collect_submit_btn" type="submit">Submit</button>
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
        window.requests = @json($requests);
    </script>
    {{-- <script src="{{ URL::asset('assets/libs/flatpickr/f    latpickr.min.js') }}"></script> --}}

    <script src="{{ URL::asset('assets/js/app/ProductreturnPage.js') }}"></script>
@endsection
