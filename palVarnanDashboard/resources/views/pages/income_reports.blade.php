@extends('layouts.master')
@section('title')
    Pal Varnan
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <style>
        .modal-content {
            transition: all 1s ease-in;
        }
    </style>

    <link href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.13.4/b-2.3.6/b-html5-2.3.6/b-print-2.3.6/datatables.min.css"
        rel="stylesheet" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Income Report
        @endslot
    @endcomponent

    <form id="income_reports_form">
        <div class="row justify-content-center">
            <div class="col-lg-2">
                <label class="form-label">Select From Date*</label>
                <div class="date-input-container1">
                    <input type="text" name="from_date" class="form-control date_picker_from_date"
                        placeholder="Pick a date">
                </div>
            </div>
            <div class="col-lg-2 date_picker_container">
                <label class="form-label">Select To Date*</label>
                <div class="date-input-container1">
                    <input type="text" name="to_date" class="form-control date_picker_to_date"
                        placeholder="Pick to date">
                </div>
            </div>

            <div class="col-lg-2 product_type_container mt-3">
                <button class="btn btn-primary btn-sm mt-3 report_btn">Get Report</button>
            </div>
        </div>
    </form>



    <div class="container-fluid mt-5 milk_grid_container">
        <h4 class="text-center ">Orders Income Report</h4>
        <div class="loader text-center">
            <img class="img-fluid" src="{{ asset('assets/images/ajax_loader.gif') }}" alt="loading_icon">
        </div>
        <div class="income_reports_container">

        </div>

    </div>


    {{-- <div class="row milk_grid_container mt-3">
        <h4>Milk Orders Report</h4>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div id="table-gridjs-milk"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row product_grid_container mt-3">
        <h4>Product Orders Report</h4>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div id="table-gridjs-product"></div>
                </div>
            </div>
        </div>
    </div> --}}



    <div class="modal fade" id="milkRefundModal" tabindex="-1" aria-labelledby="milkRefundModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMilkRefundsModalLabel">INCOME REPORT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="table_gridjs_modal"></div>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-primary refund_btn" disabled>Refund</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        window.milkProductId = "{{ env('MILK_PRODUCT') }}";
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script
        src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.13.4/b-2.3.6/b-colvis-2.3.6/b-html5-2.3.6/b-print-2.3.6/fh-3.3.2/r-2.4.1/datatables.min.js">
    </script>


    <script src="{{ URL::asset('assets/js/app/IncomeReportPage.js') }}"></script>
@endsection
