@extends('layouts.master')
@section('title')
    Pal Varnan
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Product Orders summery
        @endslot
    @endcomponent



    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <p style="position: relative;
                top: 68px;
                left: 25px;
                font-weight: bold;">
                    Search:</p>
                <div class="button" style="position: absolute;left: 24%;top: 13%;z-index: 500;">
                    <button class="btn btn-success waves-effect waves-light mb-5" id="exportButton">Export</button>
                    </div>
                <div class="position-relative">
                    <div class="modal-button mt-2" style="width: 618px">
                        <form class="needs-validation" id="ordersummeryForm" novalidate enctype="multipart/form-data">
                            @csrf

                        <div class="row align-items-start">

                            <div class="col-sm">

                                    <label class="form-label" for="select_delivery">Delivery Status*</label>

                                        <select class="form-select select2" name="delivery_status"
                                                                id="select_delivery">
                                                                <option value="" selected>Select Status</option>

                                                                <option value="0">Billing</option>
                                                                <option value="1">Packing</option>
                                                                <option value="2">Dispatched</option>
                                                                <option value="3">Out of Delivery</option>
                                                                <option value="4">Delivered</option>
                                                                <option value="5">Cancel</option>
                                                                <option value="6">Product Return</option>


                                                            </select>


                            </div>

                            <div class="col-sm">
                                <div>
                                    <label class="form-label" for="add_product_description">From Date*</label>
                                    <input type="date" class="form-control cs_from_date fromdate" name="frdate" id="from-date2"
                                    max={{ date('d-m-Y') }}  required>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div>
                                    <label class="form-label" for="add_product_description">To Date*</label>
                                    <input type="date" class="form-control cs_to_date todate" name="todate"
                                    id="to-date2"  required>
                                </div>
                            </div>
                            <div class="col-sm mt-4">
                                <div>
                                    <button type="submit" class="btn btn-success order_submit_btn mb-4"
                                       > Submit</button>
                                </div>
                            </div>


                        </div>
                        </form>
                        <!-- end row -->
                    </div>
                </div>
                <div class="card-body">
                    <div id="table-gridjs" style="position: relative;top:35px"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script>
        window.ordersummerys = @json($ordersummerys);
    </script>
    {{-- <script src="{{ URL::asset('assets/libs/flatpickr/f    latpickr.min.js') }}"></script> --}}

    <script src="{{ URL::asset('assets/js/app/orderSummeryPage.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

    <script>

        $(document).on("click","#exportButton",function() {
    console.log("hai");
    /* Get table data */
    var table = document.getElementById("table-gridjs");
    var sheet = XLSX.utils.table_to_sheet(table);

    /* Create a new Excel workbook */
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, sheet, "Sheet1");

    /* Generate Excel file and trigger download */
    XLSX.writeFile(wb, "data.xlsx");
})

    </script>

@endsection
