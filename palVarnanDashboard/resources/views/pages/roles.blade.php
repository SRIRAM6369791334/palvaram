@extends('layouts.master')

@section('title')
    Roles
@endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
@endsection

@section('content')

@component('components.breadcrumb')
    @slot('li_1') Home @endslot
    @slot('title') Role Details @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Roles List</h5>

                    <button type="button"
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#addRoleModal">
                        <i class="mdi mdi-plus"></i> Add Role
                    </button>
                    <a href="{{ url('/userpage') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                </div>

                <div id="table-gridjs"></div>

            </div>
        </div>
    </div>
</div>


<!-- ================= ADD ROLE MODAL ================= -->
<div class="modal fade" id="addRoleModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Add Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="addRoleForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text"
                               class="form-control"
                               name="name"
                               id="add_role_name"
                               placeholder="Enter role name">
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary addrole_submit_btn">
                            Submit
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>


<!-- ================= EDIT ROLE MODAL ================= -->
<div class="modal fade" id="editRoleModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="editRoleForm">

                    <input type="hidden" id="edit_role_id">

                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text"
                               class="form-control"
                               name="name"
                               id="edit_role_name">
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary editrole_submit_btn">
                            Update
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>


<!-- ================= PERMISSION MODAL ================= -->
<div class="modal fade" id="permissionModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 id="permission_role_title">Manage Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="permissionForm">
                    @csrf

                    <input type="hidden" id="permission_role_id">

                    <div class="row" id="permission_container"></div>

                    <div class="text-center mt-3">
                        <button class="btn btn-success permission_submit_btn">
                            Save Permissions
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
    window.roles = @json($roles);
    window.permissions = @json($permissions);
</script>

<script src="{{ URL::asset('assets/js/app/RolesPage.js') }}"></script>

@endsection