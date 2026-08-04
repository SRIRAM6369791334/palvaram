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
            Brands
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-transparent border-bottom">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Manage Brands</h5>
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-primary add_btn_top_el"
                                data-bs-toggle="modal" data-bs-target="#addBrandModal">
                                <i class="mdi mdi-plus-circle me-1"></i> Add Brand
                            </button>
                            
                            <a href="{{ url('/product') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Brand Modal -->
    <div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="addBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBrandModalLabel">Add Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addBrandForm" novalidate enctype="multipart/form-data">
                        @csrf
                        @can('Country.selection')
                            <div class="mb-3">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="add_country_id" name="country_id[]" multiple>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">
                                            {{ $country->name }} ({{ $country->currency }})
                                        </option>
                                    @endforeach
                                </select>
                                
                            </div>
                            @endcan
                        <div class="mb-3">
                            <label class="form-label" for="add_brand_name">Brand Name</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="add_brand_name" name="brand_name"
                                placeholder="Enter Brand Name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="add_brand_image">Brand Image</label>
                            <input type="file" class="form-control" id="add_brand_image" name="brand_image"
                                accept="image/*">
                        </div>

                        <div class="mb-3 text-center">
                            <label for="add_brand_image" class="preview-container mx-auto" id="brand-add-preview-container">
                                <div class="flex justify-content-center">
                                    <div class="text-center">
                                        <i class="display-4 col-12 text-muted mdi mdi-cloud-upload"></i>
                                    </div>
                                    <div>
                                        <span class="col-12 text-muted">Upload Image</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary add_submit_btn mt-3" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Brand Modal -->
    <div class="modal fade" id="editBrandModal" tabindex="-1" aria-labelledby="editBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBrandModalLabel">Edit Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editBrandForm" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="edit_brand_id" name="brand_id">
                        <input type="hidden" id="edit_country_id_hidden" name="country_id">
                        @can('Country.selection')
                            <div class="mb-3">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="edit_country_id" name="country_id[]" multiple>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">
                                            {{ $country->name }} ({{ $country->currency }})
                                        </option>
                                    @endforeach
                                </select>
                               
                            </div>
                            @endcan
                        <div class="mb-3">
                            <label class="form-label" for="edit_brand_name">Brand Name*</label>
                            <input type="text" class="form-control" id="edit_brand_name" name="brand_name"
                                placeholder="Enter Brand Name" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-2">Previous Image</div>
                                <div class="edit_show_preview-container mx-auto">
                                    <img src="" alt="current" class="edit_preview_image">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-2">New Image</div>
                                <label for="edit_brand_image" class="edit_preview-container mx-auto">
                                    <div class="flex justify-content-center">
                                        <div class="text-center">
                                            <i class="display-4 col-12 text-muted mdi mdi-cloud-upload"></i>
                                        </div>
                                        <div>
                                            <span class="col-12">Upload Image</span>
                                        </div>
                                    </div>
                                </label>
                                <input type="file" class="form-control d-none" id="edit_brand_image" name="brand_image" accept="image/*">
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
        window.brandsData = @json($brands);
        window.countries = @json($countries);
        window.allowedCountryIds = @json($allowedCountryIds);
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ URL::asset('assets/js/app/BrandsPage.js') }}"></script>
    <script>
        // Add modal select2
        $('#addBrandModal').on('shown.bs.modal', function () {
            if ($('#add_country_id').hasClass("select2-hidden-accessible")) {
                $('#add_country_id').select2('destroy');
            }
            $('#add_country_id').select2({
                placeholder: "Select Countries",
                width: '100%',
                dropdownParent: $('#addBrandModal')
            });
        });

        // Edit modal select2
        $('#editBrandModal').on('shown.bs.modal', function () {
            if ($('#edit_country_id').hasClass("select2-hidden-accessible")) {
                $('#edit_country_id').select2('destroy');
            }
            $('#edit_country_id').select2({
                placeholder: "Select Countries",
                width: '100%',
                dropdownParent: $('#editBrandModal')
            });
        });
    </script>
    
@endsection
