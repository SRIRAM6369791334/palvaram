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
            Free Shipping
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Free Shipping</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('free-shipping.update') }}" method="POST">
                        @csrf

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled"
                                {{ $freeShipping && $freeShipping->is_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_enabled">Enable Free Shipping for India</label>
                        </div>

                        <div class="mb-3">
                            <label for="minimum_order_amount" class="form-label">Minimum Order Amount (₹)</label>
                            <input type="number" step="0.01" class="form-control" id="minimum_order_amount"
                                name="minimum_order_amount" value="{{ $freeShipping->minimum_order_amount ?? 799 }}"
                                required>
                        </div>

                        <input type="hidden" name="country" value="IN">

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>


            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
@endsection
