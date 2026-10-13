@extends('layouts.master')
@section('title')
    Pal Varnan - Gift Sub Categories
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
            Gift Sub Category
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center px-3 pt-3">

                    <p class="mb-0 fw-bold">
                        Search:
                    </p>

                    <a href="{{ url('/giftproducts') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>

                </div>
                <div class="card-body">
                    <div class="position-relative">
                        <div class="modal-button mt-2">
                            <div class="row align-items-start">
                                <div class="col-sm">
                                    <div>
                                        <button type="button" class="btn btn-success add_btn_top_el mb-4"
                                            data-bs-toggle="modal" data-bs-target="#addSubCategoriesModal"><i
                                                class="mdi mdi-plus me-1"></i> Add Gift Sub Category</button>
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

    {{-- ADD MODAL --}}
    <div class="modal fade" id="addSubCategoriesModal" tabindex="-1" aria-labelledby="addCategoriesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoriesModalLabel">Add Gift Sub Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addSubCategoriesForm" novalidate>
                        @csrf
                        <div class="row">
                            @can('Country.selection')
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Country <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="add_country_id" name="country_id" required onchange="loadCategories(this.value, 'add_categoryname')">
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
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="add_categoryname">Gift Category Name</label><span
                                        class="dark-asterisk">*</span>
                                    <select name="category_name" id="add_categoryname" class="form-control select2">
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach ($categories as $ct)
                                            <option value="{{ $ct->id }}">{{ $ct->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sub Category Name</label><span class="dark-asterisk">*</span>

                                    <div id="subCategoryContainer">
                                        <div class="row mb-2 subcategory-row">
                                            <div class="col-10">
                                                <input type="text" class="form-control subcategory-input"
                                                    name="subcategory_name[]" placeholder="Sub Category name" required>
                                            </div>
                                            <div class="col-2 d-flex">
                                                <button type="button" class="btn btn-success addRowBtn">+</button>
                                            </div>
                                        </div>
                                    </div>
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

    {{-- EDIT MODAL --}}
    <div class="modal fade" id="editSubCategoriesModal" tabindex="-1" aria-labelledby="editSubCategoriesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSubCategoriesModalLabel">Edit Gift Sub Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editSubCategoriesForm" novalidate>
                        @csrf
                        <input type="hidden" id="edit_subcategories_id">
                        <div class="row">
                            @can('Country.selection')
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Country <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="edit_country_id" name="country_id" required onchange="loadCategories(this.value, 'edit_categoryname')">
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
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_categoryname">Gift Category Name</label><span
                                        class="dark-asterisk">*</span>
                                    <select name="category_name" id="edit_categoryname" class="form-control select2">
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach ($categories as $ct)
                                            <option value="{{ $ct->id }}">{{ $ct->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="edit_subcategoriesname">Sub Category Name</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="edit_subcategoriesname"
                                        name="subcategory_name" placeholder="Sub Category name" maxlength="50" required>
                                </div>
                            </div>
                            
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary edit_submit_btn mt-3" type="submit">Update</button>
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
        window.subcategories = @json($subcategories);
        window.countries        = @json($countries);       
        window.allCategories    = @json($categories); 
        window.allowedCountryIds = @json($allowedCountryIds);
        window.giftUrls = {
            store: "{{ route('gift_subcategories.store') }}",
            update: "{{ url('updateGiftSubcategories') }}",
            destroy: "{{ url('destroyGiftSubcategories') }}"
        };
    </script>
    <script src="{{ URL::asset('assets/js/app/GiftSubCategoryPage.js') }}"></script>
@endsection
