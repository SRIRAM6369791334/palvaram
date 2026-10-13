@extends('layouts.master')
@section('title')
    Pal Varnan
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
            Reviews
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
                                    <div>
                                        <button type="button" class="btn btn-success  add_btn_top_el mb-4"
                                            data-bs-toggle="modal" data-bs-target="#addNotificaModal"><i
                                                class="mdi mdi-plus me-1"></i> Add
                                            Reviews</button>
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

    <div class="modal fade" id="addNotificaModal" tabindex="-1" aria-labelledby="addNotificaModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNotificaModalLabel">Add Reviews</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addNotificaForm" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_category_select">Choose Category*</label>
                                    <select class="form-select select2" name="category_id" id="add_category_select">
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->category_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_product_name">Product Name*</label>
                                    <select class="form-select proname product2 select2" name="product_id" id="add_product_name">
                                        <option value="" disabled selected>Select Product.</option>

                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_tittle_name">Name*</label>
                                    <input type="text" class="form-control" id="add_tittle_name"
                                        name="notification_title" placeholder="Name" required>
                                </div>
                            </div>



                            <div class="col-md-6" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label" for="add_content_value">Location</label>
                                    <input type="text" class="form-control" id="add_content_value"
                                        name="notification_content" placeholder="Loaction">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Rating*</label>
                                    <div class="star-rating" style="font-size: 24px;">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="star bx bx-star" data-value="{{ $i }}"
                                                style="cursor:pointer; color: #ccc;"></i>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="star_rating" id="star_rating" required>
                                </div>
                            </div>




                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_product_review">Product
                                        Review*</label>
                                    <textarea type="text" class="form-control" id="add_product_review" name="product_review" placeholder="Product Review"
                                        maxlength="600" required></textarea>
                                </div>
                            </div>
                             {{-- <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="approval_checkbox" name="approval">
                                    <label class="form-check-label" for="approval_checkbox">
                                        Click this to approve this review
                                    </label>
                                </div>
                            </div> --}}

                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="noti_product_image">Notification Image*(600 *
                                        600)</label>
                                    <input type="file" class="form-control "
                                        id="noti_product_image" placeholder="Product Image" name="notification_image" required>
                                </div>
                            </div> --}}





                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary addNoti_submit_btn mt-3" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editNotificationModal" tabindex="-1" aria-labelledby="editNotificationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editNotificationModalLabel">Edit Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editNotificaForm" novalidate>
                        <input type="hidden" id="edit_notifi_id" />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="select_category_select">Choose Category*</label>
                                    <select class="form-select custid select2" name="categoryid" id="edit_category_select">
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->category_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_name">Product Name*</label>
                                    <select class="form-select select2" name="product_id" id="edit_product_name">
                                        <option value="" selected>Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->product_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_title_name">Name*</label>
                                    <input type="text" class="form-control" id="edit_title_name"
                                        name="notification_title" placeholder="Name" required>
                                </div>
                            </div>



                            <div class="col-md-6" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_content_value">Location</label>
                                    <input type="text" class="form-control" id="edit_content_value"
                                        name="notification_content" placeholder="Loaction">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Rating*</label>
                                    <div class="edit-star-rating" style="font-size: 24px;">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="star bx bx-star" data-value="{{ $i }}"
                                                style="cursor:pointer; color: #ccc;"></i>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="star_rating" id="edit_star" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_review">Product
                                        Review*</label>
                                    <textarea type="text" class="form-control" id="edit_product_review" name="product_review"
                                        placeholder="Product Review" required maxlength="200"></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="edit_approval_checkbox" name="approval">
                                    <label class="form-check-label" for="edit_approval_checkbox">
                                        Click this to approve this review
                                    </label>
                                </div>
                            </div>

                            {{-- <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit_product_image">Notification Image*(600 *
                                    600)</label>
                                <input type="file" class="form-control image_el" id="edit1_product_image"
                                    placeholder="Product Image" name="notification_image" required>
                            </div>
                        </div> --}}
                            {{-- <div class="col-md-6 d-flex mb-4">
                            <div class="col-6">
                                <div class="mb-2">Previous Image</div>
                                <label class="edit_show_preview-container">
                                    <img src="" alt="edit_nofti_image" class="edit_preview_image" id="edit_nofti_image"></label>
                            </div>



                        </div> --}}






                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary mt-3 editnoti_submit_btn" type="submit">Update</button>
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
        window.notififils = @json($notififils);
    </script>
    <script src="{{ URL::asset('assets/js/app/NotificationPage.js') }}"></script>
@endsection
