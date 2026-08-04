@extends('layouts.master')
@section('title')
    Gift Supplier Details
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Home @endslot
        @slot('title') Gift Supplier Details @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center px-3 pt-3">
                    <p class="mb-0 fw-bold">Search:</p>
                    <a href="{{ url('/giftproducts') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <div class="modal-button mt-2 mb-4">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                            <i class="mdi mdi-plus me-1"></i> Add Gift Supplier
                        </button>
                    </div>
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Gift Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addSupplierForm" novalidate>
                        @csrf
                        <div class="row">
                            @can('Country.selection')
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Country <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="add_country_id" name="country_id">
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }} ({{ $country->currency }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endcan
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="add_suppliername" name="name" placeholder="Supplier name" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Shop Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="add_shop" name="shop" placeholder="Shop name" maxlength="150">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="add_location" name="location" placeholder="Location" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="add_phone" name="phone" placeholder="Phone number" maxlength="50">
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Gift Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editSupplierForm" novalidate>
                        @csrf
                        <input type="hidden" id="edit_supplier_id" />
                        <div class="row">
                            @can('Country.selection')
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Country <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="edit_country_id" name="country_id">
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }} ({{ $country->currency }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endcan
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_suppliername" name="name" placeholder="Supplier name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Shop Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_shop" name="shop" placeholder="Shop name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_location" name="location" placeholder="Location">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_phone" name="phone" placeholder="Phone number">
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
        window.giftSupplierUrls = {
            store:   "{{ route('gift_suppliers.store') }}",
            update:  "{{ url('updateGiftSupplier') }}",
            destroy: "{{ url('destroyGiftSupplier') }}",
        };
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ URL::asset('assets/js/app/GiftSupplierDetail.js') }}"></script>
    <script>
        $('#addSupplierModal').on('shown.bs.modal', function () {
            if ($('#add_country_id').hasClass("select2-hidden-accessible")) $('#add_country_id').select2('destroy');
            $('#add_country_id').select2({ placeholder: "Select Country", width: '100%', dropdownParent: $('#addSupplierModal') });
        });
        $('#editSupplierModal').on('shown.bs.modal', function () {
            if ($('#edit_country_id').hasClass("select2-hidden-accessible")) $('#edit_country_id').select2('destroy');
            $('#edit_country_id').select2({ placeholder: "Select Country", width: '100%', dropdownParent: $('#editSupplierModal') });
        });
    </script>
@endsection