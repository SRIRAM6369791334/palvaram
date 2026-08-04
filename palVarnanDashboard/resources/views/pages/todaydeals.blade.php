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
            Today's Deals
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
                                            data-bs-toggle="modal" data-bs-target="#addtoadyDealsModal"><i
                                                class="mdi mdi-plus me-1"></i> Add
                                            Deals</button>
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

    {{-- ADD DEALS MODAL --}}

    <div class="modal fade" id="addtoadyDealsModal" tabindex="-1" aria-labelledby="addCategoriesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoriesModalLabel">Add Deal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addDealsForm" novalidate>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="add_productname">Product Name</label><span
                                        class="dark-asterisk">*</span>
                                    <select name="add_productname" id="add_productname" class="form-control">
                                        <option value="" disabled selected>Select Product</option>
                                        @if ($prods)
                                            @foreach ($prods as $ct)
                                                <option value="{{ $ct->id }}">{{ $ct->varient_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="add_offervalue">Offer Value %</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_offervalue" name="add_offervalue"
                                        placeholder="Offer Value In Percentage" maxlength="50" required>
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

    {{-- EDIT DEALS MODAL --}}

    <div class="modal fade" id="editFormModal" tabindex="-1" aria-labelledby="addCategoriesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoriesModalLabel">Edit Deal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editDealsForm" novalidate>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <input type="hidden" id="edit_product_id" />
                                    <input type="hidden" id="edit_deals_id" />
                                    <label class="form-label" for="edit_productname">Product Name</label><span
                                        class="dark-asterisk">*</span>
                                    <select name="edit_productname" id="edit_productname" class="form-control">
                                        <option value="" disabled selected>Select Product</option>
                                        @if ($prods)
                                            @foreach ($prods as $ct)
                                                <option value="{{ $ct->id }}">{{ $ct->varient_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="edit_offervalue">Offer Value %</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="edit_offervalue" name="edit_offervalue"
                                        placeholder="Enter Offer Value" maxlength="50" required>
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

@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>

    <script>
        window.subcate = @json($deals);
    </script>

    <script src="{{ URL::asset('assets/js/app/TodayDealsPage.js') }}"></script>
@endsection
