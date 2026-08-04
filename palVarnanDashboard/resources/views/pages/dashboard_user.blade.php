@extends('layouts.master')
@section('title')
    Pal Varnan
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
            User Details
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center px-3 pt-3">

                    <p class="mb-0 fw-bold">
                        Search:
                    </p>

                    <a href="{{ url('/userpage') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>

                </div>
                    <div class="position-relative">
                        <div class="modal-button mt-2">

                            <div class="row align-items-start">
                                <div class="col-sm">
                                    <div>
                                        <button type="button" class="btn btn-success  add_btn_top_el mb-4"
                                            data-bs-toggle="modal" data-bs-target="#addUserModal"><i
                                                class="mdi mdi-plus me-1"></i> Add
                                            Users</button>
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

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUsertModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="adduserForm" novalidate>
                        @csrf
                        <div class="row">

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Country*</label>
                                    <select class="form-control select2" name="country_id[]" id="add_country_id" multiple>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_user_name">Name*</label>
                                    <input type="text" class="form-control" id="add_user_name" name="name"
                                        placeholder="Name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_email">Email*</label>
                                    <input type="text" class="form-control" id="add_email" name="email"
                                        placeholder="Email" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_phone_number">Phone Number*</label>
                                    <input type="text" class="form-control" id="add_phone_number" name="phone_number"
                                        placeholder="Phone Number" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_role_select">Role*</label>
                                    <select id="add_role_select" name="role" class="form-control">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_status_select">Status*</label>
                                    <select class="form-select" name="status" id="add_status_select">
                                        <option value="" selected>Select Status</option>

                                        <option value="1">Active</option>
                                        <option value="2">InActive</option>



                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_password">Password*</label>
                                    <input type="text" class="form-control" id="add_password" name="password"
                                        placeholder="Password" required>
                                </div>
                            </div>
                        </div>


                        <div class="text-center">
                            <button class="btn btn-primary adduser_submit_btn mt-3" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editUserForm" novalidate>
                        <input type="hidden" id="edit_user_id" />

                        <div class="row">

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Country*</label>
                                    <select class="form-control select2" name="country_id[]" id="edit_country_id" multiple>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_user_name">Name*</label>
                                    <input type="text" class="form-control" id="edit_user_name" name="name"
                                        placeholder="Name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_email">Email*</label>
                                    <input type="text" class="form-control" id="edit_email" name="email"
                                        placeholder="Email" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_phone_number">Phone Number*</label>
                                    <input type="text" class="form-control" id="edit_phone_number"
                                        name="phone_number" placeholder="Phone Number" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_role_select">Role*</label>
                                    <select class="form-select" name="role" id="edit_role_select">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_status_select">Status*</label>
                                    <select class="form-select" name="status" id="edit_status_select">
                                        <option value="" selected>Select Status</option>

                                        <option value="1">Active</option>
                                        <option value="2">InActive</option>



                                    </select>
                                </div>
                            </div>

                        </div>


                        <div class="text-center">
                            <button class="btn btn-primary mt-3 edituser_submit_btn" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit1PasswordModal" tabindex="-1" aria-labelledby="edit1PasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit1PasswordModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="updateUserForm" novalidate>
                        <input type="hidden" id="update_user_id" />

                        <div class="row">

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="update_user_password">Update Password</label>
                                    <input type="text" class="form-control" id="update_user_password" name="password"
                                        placeholder="Password" required>
                                </div>
                            </div>
                        </div>


                        <div class="text-center">
                            <button class="btn btn-primary mt-3 updete_submit_btn" type="submit">Update</button>
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
        window.users = @json($users);
        window.countries = @json($countries);
    </script>
    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ URL::asset('assets/js/app/DashboardUserPage.js') }}"></script>
@endsection
