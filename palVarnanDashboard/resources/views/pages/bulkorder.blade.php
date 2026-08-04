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
            Bulk Order Enquries
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <p style="position: relative;
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
                                        <button type="button" class="btn btn-success  add_btn_top_el mb-4"
                                            data-bs-toggle="modal" data-bs-target="#addNotificaModal"><i
                                                class="mdi mdi-plus me-1"></i> Add
                                            Notifications</button>
                                    </div> --}}
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
@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script>
        window.bulkorders = @json($bulkorders);
    </script>
    <script src="{{ URL::asset('assets/js/app/BulkOrderPage.js') }}"></script>
@endsection
