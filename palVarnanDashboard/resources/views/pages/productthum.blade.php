@extends('layouts.master')
@section('title')
    Pal Varnan
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <style>
        .modal-button {
            position: static !important;
            text-align: end;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Banner Image
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
                                        <button type="button" class="btn btn-success add_btn_top_el mb-4"
                                            data-bs-toggle="modal" data-bs-target="#addBannerImagesModal"><i
                                                class="mdi mdi-plus me-1"></i> Add
                                            Banner Image</button>
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

    <div class="modal fade" id="addBannerImagesModal" tabindex="-1" aria-labelledby="addBannerImagesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBannerImagesModalLabel">Add Thump Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addthumImagesForm" novalidate>
                        <div class="row">
                            {{-- <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="add_bannerImagesname">BannerImage Name</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_bannerImagesname"
                                        name="bannerImage_name" placeholder="BannerImages name" required>
                                </div>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="add_product_select">Choose Product*</label>
                                    <select class="form-select" name="product_id" id="add_product_select">
                                        <option value="" >Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->product_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="add_bannerImageImage">BannerImage Image</label>
                                    <span class="dark-asterisk">*(600* 300)(Only
                                        png,jpg,jpeg)</span>
                                    <input type="file" class="form-control" id="add_bannerImageImage"
                                        placeholder="BannerImage Image" accept=".png, .jpg, .jpeg" name="product_child_image"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="add_bannerImageImage" class="preview-container">
                                    <div class="flex justify-content-center">
                                        <div class="text-center">
                                            <i class="display-4 col-12 text-muted mdi mdi-cloud-upload"></i>
                                        </div>
                                        <div>
                                            <span class="col-12">Upload Image</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary addvar_submit_btn mt-3" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editthumpImagesModal" tabindex="-1" aria-labelledby="editthumpImagesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editthumpImagesModalLabel">Edit Thump Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editthumImagesForm" novalidate>
                        <input type="hidden" id="edit_thumImages_id" />
                        <div class="row">
                            {{-- <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_bannerImagesname">BannerImage Name*</label>
                                    <input type="text" class="form-control" id="edit_bannerImagesname"
                                        name="bannerImage_name" placeholder="BannerImages name" required>
                                </div>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_select">Choose Product*</label>
                                    <select class="form-select" name="product_id" id="edit_product_select">
                                        <option value="" >Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->product_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_thumImageImage">BannerImage Image*</label>
                                    <input type="file" class="form-control" id="edit_thumImageImage"
                                        placeholder="BannerImageImage" name="product_child_image" accept=".png, .jpg, .jpeg"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-12 d-flex">
                                <div class="col-6">
                                    <div class="mb-2">Previous Image</div>
                                    <label class="edit_show_preview-container">
                                        <img src="" alt="edit_bannerImage_image" class="edit_preview_image"></label>
                                </div>

                                <div class="col-6">
                                    <div class="mb-2">New Image</div>
                                    <label for="edit_bannerImageImage" class="edit_preview-container">
                                        <div class="flex justify-content-center">
                                            <div class="text-center">
                                                <i class="display-4 col-12 text-muted mdi mdi-cloud-upload"></i>
                                            </div>
                                            <div>
                                                <span class="col-12">Upload Image</span>
                                            </div>
                                        </div>
                                    </label>

                                </div>

                            </div>


                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary mt-3 editvar_submit_btn" type="submit">Update</button>
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
        window.productthump = @json($productthump);
        window.csrfToken = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('assets/libs/sortable/sortable.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app/ProductThumPage.js') }}"></script>
@endsection
