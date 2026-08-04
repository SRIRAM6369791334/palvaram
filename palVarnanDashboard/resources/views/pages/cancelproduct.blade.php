@extends('layouts.master')
@section('title')
    Pal Varnan | Cancel Orders
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
            Product Cancel
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
        window.requests = @json($requests);
    </script>
    {{-- <script src="{{ URL::asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script> --}}

    <script src="{{ URL::asset('assets/js/app/CanceltOrdersPage.js') }}"></script>
@endsection
