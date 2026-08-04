@extends('layouts.master')

@section('title')
    Manage Permissions
@endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Home @endslot
    @slot('title') Permissions @endslot
@endcomponent

<div class="container-fluid">

    <!-- Page Title -->
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="mb-0">Manage Permissions - {{ $role->name }}</h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('roles.permissions.update', $role) }}">
                @csrf

                <div class="row">

                    @foreach($permissions as $module => $perms)

                        <div class="col-md-4 mb-4">
                            <div class="border rounded p-3 h-100">

                                <!-- Module Title -->
                                <h5 class="text-primary text-capitalize mb-3">
                                    {{ str_replace('_', ' ', $module) }}
                                </h5>

                                <!-- Select All -->
                                <div class="form-check mb-2">
                                    <input type="checkbox"
                                           class="form-check-input select-all"
                                           data-module="{{ $module }}"
                                           id="select_all_{{ $module }}">

                                    <label class="form-check-label fw-bold"
                                           for="select_all_{{ $module }}">
                                        Select All
                                    </label>
                                </div>

                                <!-- Permissions -->
                                @foreach($perms as $perm)
                                    <div class="form-check mb-1">
                                        <input type="checkbox"
                                               class="form-check-input perm-{{ $module }}"
                                               name="permissions[]"
                                               value="{{ $perm->name }}"
                                               id="perm_{{ $perm->id }}"
                                               {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }}>

                                        <label class="form-check-label" for="perm_{{ $perm->id }}">
                                            {{ ucfirst(str_replace('.', ' ', $perm->name)) }}
                                        </label>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                    @endforeach

                </div>

                <!-- Buttons -->
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        Save Permissions
                    </button>

                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection


@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Select All per module
    document.querySelectorAll('.select-all').forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {

            let module = this.dataset.module;
            let allCheckboxes = document.querySelectorAll('.perm-' + module);

            allCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });

        });

    });

});
</script>

@endsection