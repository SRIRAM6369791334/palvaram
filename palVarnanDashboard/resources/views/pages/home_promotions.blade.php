@extends('layouts.master')
@section('title')
    Pal Varnan - Home Promotions
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
            Home Promotions (4 Banners)
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
                                    {{-- <div>
                                        <button type="button" class="btn btn-success add_btn_top_el mb-4"
                                            data-bs-toggle="modal" data-bs-target="#addPromotionModal">
                                            <i class="mdi mdi-plus me-1"></i> Add Promotion Banner
                                        </button>
                                    </div> --}}
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
    <div class="modal fade" id="addPromotionModal" tabindex="-1" aria-labelledby="addPromotionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPromotionModalLabel">Add Home Promotion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addPromotionForm" novalidate>
                        <div class="row">
                            @can('Country.selection')
                            <div class="col-md-12 mb-3">
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
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="add_badge_text">Badge Text <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_badge_text" name="badge_text" placeholder="e.g. Weekend Discount" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="add_main_title">Main Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_main_title" name="main_title" placeholder="e.g. Drink Fresh Corn Juice" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="add_highlight_text">Highlight Text (Span) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_highlight_text" name="highlight_text" placeholder="e.g. Good Taste" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="add_link_url">Link URL <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_link_url" name="link_url" placeholder="e.g. shop" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="add_bg_image">Background Image <span class="text-danger">*</span></label>
                                <span class="dark-asterisk">*(Rec:	597 × 354 px)</span>
                                <input type="file" class="form-control" id="add_bg_image" name="bg_image" accept=".png, .jpg, .jpeg, .webp" required>
                            </div>
                            <div class="col-md-12">
                                <label for="add_bg_image" class="preview-container">
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
                            <button class="btn btn-primary mt-3" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Promotion Modal -->
    <div class="modal fade" id="editPromotionModal" tabindex="-1" aria-labelledby="editPromotionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPromotionModalLabel">Edit Home Promotion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editPromotionForm" novalidate>
                        <input type="hidden" id="edit_promotion_id" name="id">
                        <input type="hidden" id="edit_country_id_hidden" name="country_id">
                        <div class="row">

                            @can('Country.selection')
                            <div class="col-md-12 mb-3">
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
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="edit_badge_text">Badge Text <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_badge_text" name="badge_text" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="edit_main_title">Main Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_main_title" name="main_title" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="edit_highlight_text">Highlight Text <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_highlight_text" name="highlight_text" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="edit_link_url">Link URL <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_link_url" name="link_url" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="edit_bg_image">Background Image</label>
                                <span class="dark-asterisk">*(Rec:	597 × 354 px)</span>
                                <input type="file" class="form-control" id="edit_bg_image" name="bg_image" accept=".png, .jpg, .jpeg, .webp">
                            </div>
                            <div class="col-md-12 d-flex">
                                <div class="col-6">
                                    <div class="mb-2">Previous Image</div>
                                    <label class="edit_show_preview-container">
                                        <img src="" alt="edit_promo_image" id="edit_preview_image" class="edit_preview_image" style="max-width: 100px; border-radius: 5px; display: none;">
                                    </label>
                                </div>
                                <div class="col-6">
                                    <div class="mb-2">New Image</div>
                                    <label for="edit_bg_image" class="edit_preview-container">
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
        window.promotions = @json($promotions);
        window.csrfToken = "{{ csrf_token() }}";
    </script>
    <script src="{{ URL::asset('assets/js/app/HomePromotionsPage.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/libs/sortable/sortable.js') }}"></script>
@endsection
