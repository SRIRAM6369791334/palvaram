@extends('layouts.master')
@section('title')
    Supplier Details
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Supplier Details
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <p
                    style="position: relative;
                top: 33px;
                left: 25px;
                font-weight: bold;">
                    Search:</p>
                <div class="card-body">
                    <div class="position-relative">
                        <div class="modal-button mt-2">
                            <div class="row align-items-start">
                                <div class="col-sm">
                                    <div>
                                        <button type="button" class="btn btn-success add_btn_top_el mb-4"
                                            data-bs-toggle="modal" data-bs-target="#addSupplierModal"><i
                                                class="mdi mdi-plus me-1"></i> Add
                                            Supplier Detail</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSupplierModalLabel">Add Supplier Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addSupplierForm" novalidate>
                        @csrf
                        <div class="row">
                            @can('Country.selection')
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Country <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="add_country_id" name="country_id">
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">
                                                {{ $country->name }} ({{ $country->currency }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endcan
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_suppliername">Supplier Name</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_suppliername" name="name"
                                        placeholder="Supplier name" maxlength="50" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_shop">Shop Name</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_shop" name="shop"
                                        placeholder="Shop name" maxlength="150" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_location">Location</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_location" name="location"
                                        placeholder="Location" maxlength="50" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_phone">Phone number</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_phone" name="phone"
                                        placeholder="Phone number" maxlength="50" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary add_submit_btn mt-3" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSupplierModalLabel">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editSupplierForm" novalidate>
                        @csrf
                        <input type="hidden" id="edit_supplier_id" />
                        <input type="hidden" id="edit_country_id_hidden" name="country_id">
                        <div class="row">
                            @can('Country.selection')
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Country <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="edit_country_id" name="country_id">
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">
                                                {{ $country->name }} ({{ $country->currency }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endcan
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_suppliername">Supplier Name*</label>
                                    <input type="text" class="form-control" id="edit_suppliername"
                                        name="name" placeholder="Supplier name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_shop">Shop name*</label>
                                    <input type="text" class="form-control" id="edit_shop"
                                        name="shop" placeholder="Shop name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_location">Location*</label>
                                    <input type="text" class="form-control" id="edit_location"
                                        name="location" placeholder="Location" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_phone">Phone number*</label>
                                    <input type="text" class="form-control" id="edit_phone"
                                        name="phone" placeholder="Phone number" required>
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
        window.suppliers = @json($suppliers);
        window.countries = @json($countries);
        window.allowedCountryIds = @json($allowedCountryIds);
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ URL::asset('assets/js/app/SupplierDetail.js') }}"></script>
    <script>
        // Add modal select2
        $('#addSupplierModal').on('shown.bs.modal', function () {
            if ($('#add_country_id').hasClass("select2-hidden-accessible")) {
                $('#add_country_id').select2('destroy');
            }
            $('#add_country_id').select2({
                placeholder: "Select Countries",
                width: '100%',
                dropdownParent: $('#addSupplierModal')
            });
        });

        // Edit modal select2
        $('#editSupplierModal').on('shown.bs.modal', function () {
            if ($('#edit_country_id').hasClass("select2-hidden-accessible")) {
                $('#edit_country_id').select2('destroy');
            }
            $('#edit_country_id').select2({
                placeholder: "Select Countries",
                width: '100%',
                dropdownParent: $('#editSupplierModal')
            });
        });
    </script>
@endsection