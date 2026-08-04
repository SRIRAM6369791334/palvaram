<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title> @yield('title')  </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Yesbe Dashboard" name="description" />
    <meta content="Yesbe" name="author" />
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logoooo.png') }}">
    @include('layouts.head-css')
</head>

@section('body')
    @include('layouts.body')
@show
@yield('content')
@include('layouts.vendor-scripts')
</body>

</html>
