@extends('layouts.master')
@section('title')
    Pal Varnan - Ware House
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <style>
        .modal-button {
            position: static !important;
            text-align: end;
        }
        .gridImage {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Ware House
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center px-3 pt-3">

                   <div></div>

                    <a href="{{ url('/location') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>

                </div>
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
                                            data-bs-toggle="modal" data-bs-target="#addWareHouseModal">
                                            <i class="mdi mdi-plus me-1"></i> Add Ware House
                                        </button>
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

    <!-- Add Promotion Modal -->
    <div class="modal fade" id="addWareHouseModal" tabindex="-1" aria-labelledby="addWareHouseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addWareHouseModalLabel">Add Ware House</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addWareHouseForm" novalidate>
                        <div class="row">
                            @can('Country.selection')
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="add_country_id" name="country_id" required>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">
                                            {{ $country->name }} ({{ $country->currency }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endcan
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="add_warehouse_name">Ware House Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_warehouse_name" name="warehouse_name"  required>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_phone">Phone number</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_phone" name="phone"
                                        placeholder="Phone number" maxlength="50" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_address">Address</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_address" name="address"
                                        placeholder="Address" maxlength="50" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_pincode">Pincode</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_pincode" name="pincode"
                                        placeholder="Pincode " maxlength="50" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_city">City</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_city" name="city"
                                        placeholder="City" maxlength="50" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_state">State</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_state" name="state"
                                        placeholder="State" maxlength="50" required>
                                </div>
                            </div>
                            
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary mt-3 addwarehouse_submit_btn" type="submit">
    Submit
</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Promotion Modal -->
    <div class="modal fade" id="editWareHouseModal" tabindex="-1" aria-labelledby="editWareHouseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editWareHouseModalLabel">Edit Ware House</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editWareHouseForm" novalidate>
                        <input type="hidden" id="edit_promotion_id" name="id">
                        <div class="row">

                            @can('Country.selection')
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="edit_country_id" name="country_id" required>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">
                                            {{ $country->name }} ({{ $country->currency }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endcan
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="edit_warehouse_name">ware House <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_warehouse_name" name="warehouse_name" required>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_phone">Phone number</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="edit_phone" name="phone"
                                        placeholder="Phone number" maxlength="50" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_address">Address</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="edit_address" name="address"
                                        placeholder="Address" maxlength="50" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_pincode">Pincode</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="edit_pincode" name="pincode"
                                        placeholder="Pincode " maxlength="50" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_city">City</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="edit_city" name="city"
                                        placeholder="City" maxlength="50" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_state">State</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="edit_state" name="state"
                                        placeholder="State" maxlength="50" required>
                                </div>
                            </div>
                            
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary mt-3" type="submit">Update</button>
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
        window.warehouse = @json($warehouse);
        window.csrfToken = "{{ csrf_token() }}";
    </script>
    <script src="{{ URL::asset('assets/js/app/WareHousePage.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/libs/sortable/sortable.js') }}"></script>
@endsection
