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
            Delivery Person
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
                                            data-bs-toggle="modal" data-bs-target="#addDeliveryPersonModal"><i
                                                class="mdi mdi-plus me-1"></i> Add
                                            Delivery Person</button>
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

    <div class="modal fade" id="addDeliveryPersonModal" tabindex="-1" aria-labelledby="addDeliveryPersonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDeliveryPersonModalLabel">Add Delivery Person</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addDeliveryPersonForm" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_deliveryPersonname">Delivery Person Name*</label>
                                    <input type="text" class="form-control" id="add_deliveryPersonname"
                                        name="deliveryPerson_name" placeholder="DeliveryPerson name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_email">Email*</label>
                                    <input type="email" class="form-control" id="add_email" placeholder="Email"
                                        name="email" required>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="form-label" for="add_phone_number">Phone Number*</label>
                                    <div class="input-group">

                                        <span class="input-group-text" id="inputGroupPrepend">+91</span>
                                        <input type="tel" maxlength="10" pattern="^(?!^91)[6-9]\d{9}$"
                                            class="form-control" id="add_phone_number" name="phone_number"
                                            placeholder="Phone Number" required>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_password">Password*</label>
                                    <div class="input-group">
                                        <input class="form-control" id="add_password" placeholder="Password" name="password"
                                            required type="password">
                                        <span class="input-group-text btn btn-outline-secondary" id="inputGroupAppend"
                                            type="button">
                                            <i class="bx bx-hide icon"></i>
                                            <i class="bx bx-show icon"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary add_submit_btn  mt-3" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editDeliveryPersonModal" tabindex="-1" aria-labelledby="editDeliveryPersonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDeliveryPersonModalLabel">Edit Delivery Person</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editDeliveryPersonForm" novalidate>
                        <input type="hidden" id="edit_deliveryPerson_id" />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_deliveryPersonname">Delivery Person Name*</label>
                                    <input type="text" class="form-control" id="edit_deliveryPersonname"
                                        name="deliveryPerson_name" placeholder="DeliveryPerson name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_email">Email*</label>
                                    <input type="email" class="form-control" id="edit_email" placeholder="Email"
                                        name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="form-label" for="edit_phone_number">Phone Number*</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroupPrepend">+91</span>
                                        <input type="tel" pattern="^(?!^91)[6-9]\d{9}$" class="form-control"
                                            name="phone_number" maxlength="10" id="edit_phone_number"
                                            placeholder="Phone Number" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_password">Change Password*</label>
                                    <div class="input-group">
                                        <input class="form-control" id="edit_password" placeholder="Password"
                                            name="password" required type="password" autocomplete="off">
                                        <span class="input-group-text btn btn-outline-secondary" id="inputGroupAppend"
                                            type="button">
                                            <i v-if="!showPassword" class="bx bx-hide icon"></i>
                                            <i v-if="showPassword" class="bx bx-show icon"></i>

                                        </span>
                                    </div>
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
@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script>
        window.deliveryPersons = @json($deliveryPersons);
    </script>

    <script src="{{ URL::asset('assets/js/app/DeliveryPersonPage.js') }}"></script>
@endsection
