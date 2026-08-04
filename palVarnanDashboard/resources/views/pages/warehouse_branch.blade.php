@extends('layouts.master')

@section('title')
Pal Varnan - Ware House Store
@endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Home @endslot
@slot('title') Ware House Store @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">

            <div class="d-flex justify-content-between align-items-center px-3 pt-3">

                    <p class="mb-0 fw-bold">
                        Search:
                    </p>

                    <a href="{{ url('/location') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>

                </div>
            <div class="card-body">

                <div class="position-relative">
                    <div class="modal-button mt-2">
                        <button type="button" class="btn btn-success mb-4"
                            data-bs-toggle="modal" data-bs-target="#addBranchModal">
                            <i class="mdi mdi-plus me-1"></i> Add Store
                        </button>
                    </div>
                </div>

                <div id="table-gridjs"></div>

            </div>
        </div>
    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addBranchModal">
    <div class="modal-dialog modal-dialog-scrollable modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Store</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form id="addBranchForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Country</label>
                            <select class="form-control" id="add_country_id" name="country_id">
                                <option value="">Select Country</option>
                                @foreach($countries as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Warehouse</label>
                            <select class="form-control" id="add_warehouse_id" name="warehouse_id">
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->warehouse_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Store Name</label>
                            <input type="text" class="form-control" id="add_branch_name" name="branch_name">
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

                        <button class="btn btn-primary addbranch_submit_btn" type="submit">
                            Submit
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editBranchModal">
    <div class="modal-dialog modal-dialog-scrollable modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Store</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form id="editBranchForm">
                    <div class="row">

                        <input type="hidden" id="edit_branch_id">

                        <div class="col-md-6 mb-3">
                            <label>Country</label>
                            <select class="form-control" id="edit_country_id">
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Warehouse</label>
                            <select class="form-control" id="edit_warehouse_id">
                                @foreach($warehouses as $w)
                                    <option value="{{ $w->id }}">{{ $w->warehouse_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Store Name</label>
                            <input type="text" class="form-control" id="edit_branch_name">
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

                        <button class="btn btn-primary editbranch_submit_btn" type="submit">
                            Update
                        </button>
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
    window.branches = @json($branches);
    window.csrfToken = "{{ csrf_token() }}";
</script>

<script src="{{ URL::asset('assets/js/app/WareHouseBranchPage.js') }}"></script>
@endsection