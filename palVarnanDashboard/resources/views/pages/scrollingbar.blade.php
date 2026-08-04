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
            Scrolling Bar
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Scrolling Message</h4>
                </div>
          <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('scrollingbar.store') }}">
                @csrf
                @for ($i = 0; $i < 3; $i++)
                    <div class="form-group mb-3">
                        <label>Message {{ $i + 1 }}</label>
                        <input type="text" name="messages[]" class="form-control"
                            value="{{ $bars[$i]->message ?? '' }}" required>
                    </div>
                @endfor
                <button type="submit" class="btn btn-primary">Update Messages</button>
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
