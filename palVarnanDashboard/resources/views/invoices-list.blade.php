@extends('layouts.master')
@section('title')
    @lang('translation.Invoice_List')
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <link href="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Invoices
        @endslot
        @slot('title')
            Invoice List
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
                                        <button type="button" class="btn btn-success  add_btn_top_el mb-4"
                                            data-bs-toggle="modal" data-bs-target="#addInvoiceModal"><i
                                                class="mdi mdi-plus me-1"></i> Add
                                            Invoice</button>
                                    </div>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="d-flex gap-1">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="datepicker-range">
                                            <span class="input-group-text"><i class="bx bx-calendar-event"></i></span>
                                        </div>
                                        <div class="dropdown">
                                            <a class="btn btn-link text-body shadow-none dropdown-toggle" href="#"
                                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </a>

                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#">Action</a></li>
                                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->
                        </div>
                    </div>
                    <div id="table-invoices-list"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/invoice-list.init.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
@endsection
