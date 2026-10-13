@extends('layouts.master')
@section('title')
    Pal Varnan
@endsection

@section('css')
    <link href="{{ URL::asset('assets/libs/choices.js/choices.js.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <style>
        .delivery_assign_truck_icon {

            font-size: 28px;
            padding: 5px;
            background: #003032;
            color: white;
            border-radius: 10px;
        }

        .notification_count_el {
    position: absolute;
    right: -5px;
}
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Areas
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
                                    <div>
                                        <button type="button" class="btn btn-success  add_btn_top_el mb-4"
                                            data-bs-toggle="modal" data-bs-target="#addAreaModal"><i
                                                class="mdi mdi-plus me-1"></i> Add
                                            Area</button>
                                    </div>
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

    <div class="modal fade" id="addAreaModal" tabindex="-1" aria-labelledby="addAreaModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAreaModalLabel">Add Area</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-1 p-4">
                    <form class="needs-validation" id="pincodeSeachForm">
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="mb-3">
                                    <label class="form-label" for="pincode_input">Enter Pincode*</label>
                                    <div class="d-flex">
                                        <input type="tel" class="form-control" id="pincode_input" name="pincode"
                                            placeholder="Enter Pincode" maxlength="6" required>
                                        <button type="submit" class="btn btn-primary pincode_submit">
                                            <i class="bx bx-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <form class="needs-validation" id="addAreaForm" novalidate>
                        <div class="text-center area_loading">
                            <h5 class="h5">Loading Please Wait...</h5>
                        </div>
                        <div class="row area_name_container" style="display:none">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="add_areaname">Area Name*</label>
                                    <select class="form-control select2" name="area_name" data-trigger name="choices-single-default"
                                        id="add_areaname">
                                    </select>

                                </div>
                            </div>

                            <div class="col-md-12 other_area_container">
                                <div class="mb-3">
                                    <label class="form-label" for="other_area_name">Enter Other Area Name*</label>
                                    <input type="text" class="form-control" id="other_area_name" name="other_area_name"
                                        placeholder="Enter Other Area Name" required>
                                </div>
                            </div>

                            <div class="text-center">
                                <button class="btn btn-primary mt-3 add_submit_btn" type="submit">Submit</button>
                            </div>
                        </div>




                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editAreaModal" tabindex="-1" aria-labelledby="editAreaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAreaModalLabel">Edit Area</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editAreaForm" novalidate>
                        <input type="hidden" id="edit_area_id" />
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_pincode_input">Edit Pincode*</label>
                                    <div>
                                        <input type="tel" class="form-control" id="edit_pincode_input"
                                            name="area_pincode" placeholder="Enter Pincode" maxlength="6" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_areaname">Edit Area Name*</label>
                                    <input type="text" class="form-control" id="edit_areaname" name="area_name"
                                        placeholder="Area name" required>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary mt-3 edit_submit_btn" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addDeliveryPersonModal" tabindex="-1" aria-labelledby="addDeliveryPersonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog  modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDeliveryPersonModalLabel">Add Delivery Person</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">



                    <form action="assignDeliveryPerson" class="needs-validation" id="addDeliveryPersonForm"
                        method="POST" novalidate>
                        <input type="hidden" id="edit_area_id" />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="assign_area_id">Area*</label>
                                    <input type="hidden" class="form-control" id="assign_area_id" name="area_id"
                                        placeholder="Area name" readonly required>
                                    <input type="text" class="form-control" id="assign_area_name"
                                        placeholder="Area name" readonly required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="add_delivery_persons_multiple">Delivery
                                        Persons*</label>
                                    <select class="form-control select2" name="delivery_persons[]"
                                        id="add_delivery_persons_multiple" placeholder="This is a placeholder" multiple>
                                        {{-- <option value="">Choose Delivery Person</option>
                                        <option value="1">Choice 1</option>
                                        <option value="2">Choice 2</option>
                                        <option value="3">Choice 3</option>
                                        <option value="4">Choice 4</option> --}}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary mt-3 addDeliverySubmitBtn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="deleteDeliveryPersonModal" tabindex="-1"
        aria-labelledby="deleteDeliveryPersonModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDeliveryPersonModalLabel">Remove Delivery Person</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">



                    <form action="assignDeliveryPerson" class="needs-validation" id="deleteDeliveryPersonForm"
                        method="POST" novalidate>
                        <input type="hidden" id="edit_area_id" />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="delete_assign_area_id">Area*</label>
                                    <input type="hidden" class="form-control" id="delete_assign_area_id" name="area_id"
                                        placeholder="Area name" readonly required>
                                    <input type="text" class="form-control" id="delete_assign_area_name"
                                        placeholder="Area name" readonly required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="delete_delivery_persons_multiple">Delivery
                                        Persons*</label>
                                    <select class="form-control select2" name="delivery_persons[]"
                                        id="delete_delivery_persons_multiple" placeholder="This is a placeholder"
                                        multiple>
                                        {{-- <option value="">Choose Delivery Person</option>
                                        <option value="1">Choice 1</option>
                                        <option value="2">Choice 2</option>
                                        <option value="3">Choice 3</option>
                                        <option value="4">Choice 4</option> --}}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary mt-3 deleteDeliverySubmitBtn" type="submit">Submit</button>
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
    <script src="{{ URL::asset('assets/libs/choices.js/choices.js.min.js') }}"></script>
    <script src="{{ asset('assets/libs/lodash/lodash.min.js') }}"></script>
    <script>
        window.areas = @json($areas);
    </script>
    <script src="{{ URL::asset('assets/js/app/AreasPage.js') }}"></script>
@endsection
