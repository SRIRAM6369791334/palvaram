@extends('layouts.master')
@section('title')
    @lang('translation.Dashboard')
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Pal Varnan
        @endslot
        @slot('title')
            @if (Auth::user()->role == 1)
                Welcome Admin!
            @elseif (Auth::user()->role == 2)
                Welcome Account!
            @elseif (Auth::user()->role == 3)
                Welcome Packing!
            @elseif (Auth::user()->role == 4)
                Welcome Dispatch!
            @elseif (Auth::user()->role == 5)
                Welcome Delivery!
            @endif
        @endslot
    @endcomponent

    <div class="row justify-content-center">
        @if (Auth::user()->role == 1)
            {{-- <div class="col-xl-4">
                <a href="{{ route('customers.index') }}">
                    <div class="card bg-primary">
                        <div class="card-body" style="background: #ce8437">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="bx bxs-user icon nav-icon"></i></h2>
                                    <h3 class="text-white">Customers</h3>
                                    <h3 class="text-white mb-0">{{ $userCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> --}}




            {{-- <div class="col-xl-4">
                <a href="{{ route('productOrders.index') }}">
                    <div class="card bg-primary">
                        <div class="card-body" style="background: #007d44">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="mdi mdi-cart-plus icon nav-icon"></i></h2>

                                    <h3 class="text-white">Order Count</h3>
                                    <h3 class="text-white mb-0">{{ $billing }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> --}}

            {{-- <div class="col-xl-4">
                <a href="{{ route('productpacking.index') }}">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="mdi mdi-clipboard-text-multiple icon nav-icon"></i></h2>
                                    <h3 class="text-white">In Packing</h3>
                                    <h3 class="text-white mb-0">{{ $pending }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> --}}


            {{-- <div class="col-xl-4">
                <a href="{{ route('productdispatch.index') }}">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="mdi mdi-truck-check icon nav-icon"></i></h2>
                                    <h3 class="text-white">Dispatch Order</h3>
                                    <h3 class="text-white mb-0">{{ $dispatch }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> --}}

            {{-- <div class="col-xl-4">
                <a href="{{ route('productdelivery.index') }}">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="mdi mdi-truck-fast-outline icon nav-icon"></i></h2>
                                    <h3 class="text-white">Out For Delivery </h3>
                                    <h3 class="text-white mb-0">{{ $delivery }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> --}}

            {{-- <div class="col-xl-4">
                <a href="{{ route('productcomplete.index') }}">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="mdi mdi-account-multiple-check icon nav-icon"></i></h2>
                                    <h3 class="text-white">Delivered Order </h3>
                                    <h3 class="text-white mb-0">{{ $productOrdercompleteCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> --}}


            {{-- <div class="col-xl-4">
                <a href="{{ route('ordersummery.index') }}">
                    <div class="card bg-primary">
                        <div class="card-body" style="background: #c7173b">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class=" mdi mdi-clipboard-edit icon nav-icon"></i></h2>
                                    <h3 class="text-white">Order summery</h3>
                                    <h3 class="text-white mb-0">{{ $ordersum }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> --}}
            {{-- <div class="col-xl-4">
                <a href="#">
                    <div class="card bg-primary">
                        <div class="card-body" style="background: #ce8437">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class=" mdi mdi-cash-plus nav-icon"></i></h2>
                                    <h3 class="text-white">Total Order Value</h3>
                                    <h3 class="text-white mb-0">{{ $totalAmount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> --}}
            {{-- <div class="col-xl-4">
                <a href="{{ route('productRefunds.index') }}">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="mdi mdi-cash-refund icon nav-icon"></i></h2>
                                    <h3 class="text-white">Product Refund</h3>
                                    <h3 class="text-white mb-0">{{ $productrefund }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> --}}
            <div class="col-xl-3">
                <a href="{{ route('categories.index') }}">
                    <div class="card" style="background: #007d44;border-radius:15px;">
                        <div class="card-body" style="">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="mdi mdi-food  icon nav-icon"></i></h2>
                                    <h3 class="text-white">Category</h3>
                                    <h3 class="text-white mb-0">{{ $categoryCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- <div class="col-xl-4">
                <a href="{{ route('productvarient.index') }}">
                    <div class="card bg-primary">
                        <div class="card-body" style="background: #c7173b">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="bx bxs-basket icon nav-icon"></i></h2>
                                    <h3 class="text-white">Sub category</h3>
                                    <h3 class="text-white mb-0">{{ $subcategoryCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> --}}

            <div class="col-xl-3">
                <a href="{{ route('productvarient.index') }}">
                    <div class="card" style="background: #0899ae;border-radius:15px;">
                        <div class="card-body" style="">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="bx bxs-basket icon nav-icon"></i></h2>
                                    <h3 class="text-white">Product Variant</h3>
                                    <h3 class="text-white mb-0">{{ $productCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>



            <div class="col-xl-3">
                <a href="{{ route('bannerImages.index') }}">
                    <div class="card" style="background: #ce8437;border-radius:15px;">
                        <div class="card-body" style="">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="mdi mdi-image-area icon nav-icon"></i></h2>
                                    <h3 class="text-white">Banner Images</h3>
                                    <h3 class="text-white mb-0">{{ $bannerImagesCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3">
                <a href="{{ route('productOrders.index') }}">
                    <div class="card bg-primary" style="border-radius:15px;">
                        <div class="card-body" >
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="mdi mdi-truck-fast-outline icon nav-icon"></i></h2>
                                    <h3 class="text-white">Order Count</h3>
                                    <h3 class="text-white mb-0">{{ $billing }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3">
                <a href="{{ route('productpacking.index') }}">
                    <div class="card " style="border-radius:15px;background-color:#c7173b;">
                        <div class="card-body" >
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="bx bx-store icon nav-icon"></i></h2>
                                    <h3 class="text-white">In Packing</h3>
                                    <h3 class="text-white mb-0">{{ $pending }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3">
                <a href="{{ route('productdispatch.index') }}">
                    <div class="card bg-primary" style="border-radius:15px;">
                        <div class="card-body" >
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="bx bxs-basket icon nav-icon"></i></h2>
                                    <h3 class="text-white">Dispatch Order</h3>
                                    <h3 class="text-white mb-0">{{ $dispatch }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @elseif (Auth::user()->role == 2)

        @elseif (Auth::user()->role == 3)

        @elseif (Auth::user()->role == 4)

        @elseif (Auth::user()->role == 5)
            <div class="col-xl-4">
                <a href="{{ route('productdelivery.index') }}">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="bx bx-map icon nav-icon"></i></h2>
                                    <h3 class="text-white">Out Of Delivery </h3>
                                    <h3 class="text-white mb-0">{{ $delivery }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-4">
                <a href="{{ route('productcomplete.index') }}">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="bx bx-map icon nav-icon"></i></h2>
                                    <h3 class="text-white">Delivered Order </h3>
                                    <h3 class="text-white mb-0">{{ $productOrdercompleteCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif




    </div>


    {{-- <div class="container-fluid mt-3">
        <h5 class="h5 mb-2">Recent Milk Orders</h5>
        <div id="table-gridjs-milk"></div>
    </div> --}}

    {{-- <div class="container-fluid mt-3">
        <h5 class="h5 mb-2">Recent Product Orders</h5>
        <div id="table-gridjs-product"></div>
    </div> --}}
@endsection
@section('script')
    {{-- <script src="{{ URL::asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/chartjs.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/dashboard.init.js') }}"></script> --}}
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>

    <script>
        window.milkOrders = @json($milkOrders);
        window.productOrders = @json($productOrders);
    </script>
    <script src="{{ URL::asset('assets/js/app/DashboardPage.js') }}"></script>
@endsection
