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
            One Time Customer Coupon
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
                                            data-bs-toggle="modal" data-bs-target="#addProductModal"><i
                                                class="mdi mdi-plus me-1"></i> Add
                                            Coupons</button>
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

    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add Coupon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addCouponForm" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_coupon_name">Coupon Code Name*</label>
                                    <input type="text" class="form-control" id="add_coupon_name" name="codename"
                                        placeholder="Counpon Code name" required>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_mini_amt">Minimum amount of purchase*</label>
                                    <input type="text" class="form-control" id="add_mini_amt" name="mini_amt"
                                        placeholder="Minimum Amount" oninput="event.target.value = event.target.value.replace(/[^0-9]/g, '')" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_discount_select">Discount Unit*</label>
                                    <select class="form-select" name="discounttype" id="add_discount_select">
                                        <option value="">Select Discount</option>

                                        <option value="1">Rs</option>
                                        <option value="2">%</option>


                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_discount_value">Discount Value*</label>
                                    <input type="text" class="form-control" id="add_discount_value" name="discount"
                                        placeholder="Discount value" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_start_date">Start Date*</label>
                                    <input type="date" class="form-control" id="add_start_date" name="start_date"
                                        placeholder="Date" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_end_date">End Date*</label>
                                    <input type="date" class="form-control" id="add_end_date" name="end_date"
                                        placeholder="date" required>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary add_submit_btn2 mt-3" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editcouponModal" tabindex="-1" aria-labelledby="editcouponModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editcouponModalLabel">Edit Coupons</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editCouponForm" novalidate>
                        <input type="hidden" id="edit_coupon_id" />
                        <div class="row">

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_coupon_name">Coupon Code Name*</label>
                                    <input type="text" class="form-control" id="edit_coupon_name" name="codename"
                                        placeholder="Counpon Code name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_mini_amt">Minimum amount of purchase*</label>
                                    <input type="text" class="form-control" id="edit_mini_amt" name="mini_amt"
                                        placeholder="Minimum Amount" oninput="event.target.value = event.target.value.replace(/[^0-9]/g, '')" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_discount_select">Discount Unit*</label>
                                    <select class="form-select" name="discounttype" id="edit_discount_select">
                                        <option value="">Select Discount</option>

                                        <<option value="1">Rs</option>
                                        <option value="2">%</option>


                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_discount_value">Discount Value*</label>
                                    <input type="text" class="form-control" id="edit_discount_value" name="discount"
                                        placeholder="Discount value" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_start_date">Start Date*</label>
                                    <input type="date" class="form-control" id="edit_start_date" name="start_date"
                                        placeholder="Date" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_end_date">End Date*</label>
                                    <input type="date" class="form-control" id="edit_end_date" name="end_date"
                                        placeholder="date" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary mt-3 edit_submit_btn1" type="submit">Update</button>
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
        window.coupons = @json($coupons);
    </script>
    <script src="{{ URL::asset('assets/js/app/CouponsPage.js') }}"></script>
@endsection
