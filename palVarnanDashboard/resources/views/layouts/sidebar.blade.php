<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    {{-- <body data-layout="horizontal" data-sidebar="dark"> --}}
    <!-- LOGO -->
    <div class="navbar-brand-box" style="background:#ffff;">
        <a href="{{ url('/') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/smlo.png') }}" alt="" style="width:55px; border-radius: 16px;">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/logochip.png') }}" alt="" height="22"
                    style="width:40px">
            </span>
        </a>

        <a href="{{ url('/') }}" class="logo logo-light">
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/logoooo.png') }}" alt="" style="width:55px; border-radius: 16px;">
            </span>
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logoooo.png') }}" alt="" height="22"
                    style="width:50px;margin-left: -10px;">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 font-size-16 header-item vertical-menu-btn">
        <i class="fa fa-fw fa-bars"></i>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu" style="background: #0c716a">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">



                
                    <li class="menu-title" data-key="t-menu">Menu</li>
                    <li>
                        <a href="{{ url('/') }}">
                            <i class="bx bx-tachometer icon nav-icon"></i>
                            <span class="menu-item" data-key="t-dashboards">@lang('translation.Dashboard')</span>
                        </a>
                    </li>
                    {{-- <li>
                    <a href="{{ route('users.index') }}">

                        <i class="mdi mdi-18px mdi-account-circle icon nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Dashboard </span>
                    </a>
                </li> --}}


                    @can('Banner Images.index')

                    <!-- <li>
                        <a href="{{ route('bannerImages.index') }}">
                            <i class="mdi mdi-image-area nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Banner Images</span>
                        </a>
                    </li> -->

                    <li>
                        <a href="{{ route('home_promotions.index') }}">
                            <i class="mdi mdi-star-box nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Banners</span>
                        </a>
                    </li>
                    @endcan

                    @if( auth()->user()->can('Roles.index') || auth()->user()->can('Users.index') || auth()->user()->can('Customers.index') )

                    <li>
                        <a href="/userpage">
                            <i class="bx bxs-user icon nav-icon"></i>
                            <span class="menu-item" data-key="t-calendar">Users, Customer, Roles</span>
                        </a>
                    </li>
                    @endif

                    <li>
                        <a href="{{ route('contacts.index') }}">
                            <i class="mdi mdi-email-mark-as-unread icon nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Contact Inquiries</span>
                            @php
                                $inquiryBadge = \App\Models\Contact::count();
                            @endphp
                            @if($inquiryBadge > 0)
                                <span class="badge rounded-pill bg-danger float-end me-2">{{ $inquiryBadge }}</span>
                            @endif
                        </a>
                    </li>



                    @if( auth()->user()->can('Countries.index') || auth()->user()->can('Warehouse.index') || auth()->user()->can('Warehouse Branch.index') )
                    <li>
                        <a href="{{ route('admin.location.index') }}">
                            <i class="bx bx-globe icon nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Locations</span>
                        </a>
                    </li>
                    @endif

                    
                    
                    @if( auth()->user()->can('Categories.index') || auth()->user()->can('Subcategories.index') || auth()->user()->can('Brands.index') || auth()->user()->can('Products.index') || auth()->user()->can('Product Varient.index') )
                   
                    <li>
                        <a href="/product">
                            <i class="bx bxs-basket nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Products</span>
                        </a>
                    </li>

                   <!-- @can('Subcategories.index')
                    <li>
                        <a href="{{ route('subcategories.index') }}">
                            <i class="bx bx-store icon nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Sub Category</span>

                        </a>
                    </li> 
                    @endcan

                    @can('Brands.index')
                    <li>
                        <a href="{{ route('brands.index') }}">
                            <i class="bx bx-award nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Brands</span>
                        </a>
                    </li>
                    @endcan

                    

                    @can('Products.index')
                    <li>
                        <a href="{{ route('products.index') }}">
                            <i class="bx bxs-basket nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Products</span>
                            {{-- <span class="badge rounded-pill bg-danger" data-key="t-hot">@lang('translation.Hot')</span> --}}
                        </a>
                    </li>
                    @endcan

                    
                    <li>
                        <a href="{{ route('productvarient.index') }}">
                            <i class="mdi mdi-timer-sand-empty nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Product Variant</span>

                        </a>
                    </li> -->
                    @endif

                    @if( auth()->user()->can('Gift Categories.index') || auth()->user()->can('Gift Subcategories.index') || auth()->user()->can('Gift Products.index') )
                    <li>
                        <a href="/giftproducts">
                            <i class="bx bx-gift icon nav-icon"></i>
                            <span class="menu-item">Gift Items</span>
                        </a>
                    </li>
                    @endif

                    @if( auth()->user()->can('Stocks.index') || auth()->user()->can('Stock Transfer.index') || auth()->user()->can('Stock Distribution.index') || auth()->user()->can('Stock Tracking.index') || auth()->user()->can('Stock Management.index') )
                    
                    <li>
                        <a href="/transfer">
                            <i class="bx bxs-report nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Product Stock</span>
                        </a>
                    </li>
                 
                    <!-- <li>
                        <a href="/transfer-stock">
                            <i class="bx bxs-report nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Product Stock Transfer</span>
                        </a>
                    </li> -->
                    @endif

                    {{-- <li>
                        <a href="{{ route('todaydeals.index') }}">
                            <i class="bx bx-store icon nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Today Deals</span>

                        </a>
                    </li> --}}

                    {{-- <li>
                        <a href="{{ route('coupons.index') }}">
                            <i class="bx bxs-report nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Coupons</span>
                        </a>
                    </li> --}}
                    {{-- <li>
                    <a href="{{ route('combostock.index') }}">
                        <i class="mdi mdi-clipboard-plus nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Product Combo Stock</span>
                    </a>
                </li> --}}

                    

                    

                    @if( auth()->user()->can('All Orders.index') || auth()->user()->can('New Orders.index') || auth()->user()->can('Package Orders.index') || auth()->user()->can('Dispatched Orders.index') || auth()->user()->can('Out For Delivery Orders.index') ||
                         auth()->user()->can('Delivered Orders.index') || auth()->user()->can('Supplier Details.index') || auth()->user()->can('Purchase Orders.index') || auth()->user()->can('Other Orders.index') )

                    <li>
                        <a href="/orders">
                            <i class="mdi mdi-truck-delivery nav-icon"></i>
                            <span class="menu-item" data-key="t-chat"> Orders</span>
                            <!-- @if ($productOrderBIllingCount)
                                <span class="badge rounded-pill bg-success">{{ $productOrderBIllingCount }}</span>
                            @endif -->

                        </a>
                    </li>

                    
                    <!-- <li>
                        <a href="{{ route('productOrders.index') }}">
                            <i class="mdi mdi-food nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">New Orders</span>
                            @if ($productOrderBIllingCount)
                                <span class="badge rounded-pill bg-success">{{ $productOrderBIllingCount }}</span>
                            @endif

                        </a>
                    </li> -->
                    

                    {{-- <li>
                        <a href="/viewProducts">
                            <i class="mdi mdi-food nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Orders</span>
                        </a>
                    </li> --}}

                   
                    <!-- <li>
                    <a href="{{ route('productpacking.index') }}">
                        <i class="mdi mdi-gift nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Package Orders</span>
                        @if ($productOrderPendingCount)
                            <span class="badge rounded-pill bg-success">{{ $productOrderPendingCount }}</span>
                        @endif

                    </a>
                </li>
                
                    <li>
                    <a href="{{ route('productdispatch.index') }}">
                        <i class="mdi mdi-motorbike nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Dispatched Orders</span>
                        @if ($productOrderDispatchCount)
                            <span class="badge rounded-pill bg-success">{{ $productOrderDispatchCount }}</span>
                        @endif

                    </a>
                </li>
               
                    <li>
                    <a href="{{ route('productdelivery.index') }}">
                        <i class="mdi mdi-truck-delivery nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Out for Delivery Orders</span>
                        @if ($productOrderDeliveryCount)
                            <span class="badge rounded-pill bg-success">{{ $productOrderDeliveryCount }}</span>
                        @endif

                    </a>
                </li> -->
              
                    {{-- <li>
                    <a href="{{ route('productreturn.index') }}">
                        <i class="mdi mdi-truck-delivery-outline nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Product Return</span>
                        @if ($productreturn)
                            <span class="badge rounded-pill bg-success">{{ $productreturn }}</span>
                        @endif

                    </a>
                </li> --}}

                
                    <!-- <li>
                    <a href="{{ route('productcomplete.index') }}">
                        <i class="mdi mdi-dns nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Delivered Order</span>

                    </a>
                </li> -->
                @endif

                    {{-- <li class="menu-title" data-key="t-applications">Refund</li>

                <li>
                    <a href="{{ route('cancelproduct.index') }}">
                        <i class="mdi mdi-bell-ring nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Request Cancel</span>

                        @if ($cancelreq)
                            <span class="badge rounded-pill bg-success">{{ $cancelreq }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('productRefunds.index') }}">
                        <i class="mdi mdi-cash-100 nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Product Refund</span>

                        @if ($productRefundPendingCount)
                            <span class="badge rounded-pill bg-success">{{ $productRefundPendingCount }}</span>
                        @endif
                    </a>
                </li> --}}


                    @if( auth()->user()->can('Product Wise Report.index') || auth()->user()->can('Order Wise Report.index') || auth()->user()->can('Inventory Report.index') || auth()->user()->can('Purchase Report.index') )
                    <li>
                        <a href="/reports">
                            <i class="mdi mdi-autorenew nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Reports</span>

                        </a>
                    </li>
                    @endif

                    
                    <!-- <li class="menu-title" data-key="t-applications">Shipping Charges</li>
                    <li>
                        <a href="/admin/shipping-amount">
                            <i class="mdi mdi-autorenew nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Shipping Charges</span>

                        </a>
                    </li>
                    <li>
                        <a href="/admin/free-ship">
                            <i class="mdi mdi-autorenew nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Free Shipping</span>

                        </a>
                    </li>
                    <li class="menu-title" data-key="t-applications">Coupon Management</li>
                    <li>
                        <a href="/admin/coupons">
                            <i class="mdi mdi-autorenew nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Coupons</span>

                        </a>
                    </li>

                    <li class="menu-title" data-key="t-applications">Scrolling Bar</li>
                    <li>
                        <a href="/scrollingbar">
                            <i class="mdi mdi-autorenew nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Scrolling Message</span>

                        </a>
                    </li> -->

                    {{-- <li>
                        <a href="/cancelrequests">
                            <i class="mdi mdi-close nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Cancellation Requests</span>
                            @if ($cancelreq)
                                <span class="badge rounded-pill bg-success">{{ $cancelreq }}</span>
                            @endif

                        </a>
                    </li>
                    <li>
                        <a href="/returnrequests">
                            <i class="mdi mdi-close nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Return Requests</span>
                            @if ($returnreqcount)
                                <span class="badge rounded-pill bg-success">{{ $returnreqcount }}</span>
                            @endif

                        </a>
                    </li>
                    <li>
                        <a href="{{ route('incomeReports') }}">
                            <i class="bx bxs-report nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Income Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('ordersummery.index') }}">
                            <i class="mdi mdi-autorenew nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Order Summary</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('highselling') }}">
                            <i class="mdi mdi-percent nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">High Selling</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('topcustomer.index') }}">
                            <i class="mdi mdi-plus-circle-outline nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Top Customer</span>
                        </a>
                    </li> --}}

                    <!-- <li class="menu-title" data-key="t-applications">Product Reviews</li> -->
                    {{-- <li>
                    <a href="{{ route('offerImages.index') }}">
                        <i class="bx bxs-report nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Offers Banner</span>
                    </a>
                </li>
                    <li>
                    <a href="{{ route('coupons.index') }}">
                        <i class="bx bxs-report nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">One Time Coupon </span>
                    </a>
                </li> --}}
                <!-- <li>
                    <a href="{{ route('review.index') }}">
                        <i class="mdi mdi-comment-text-multiple nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Reviews</span>
                    </a>
                </li> -->
                <!-- <li class="menu-title" data-key="t-applications">Bulk Order Enquries</li> -->

                <!-- <li>
                        <a href="{{ route('bulkenquiry.index') }}">
                            <i class="mdi mdi-plus-circle-outline nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Bulk Order Enquries</span>
                        </a>
                    </li> -->

                    {{-- Account User Permission --}}
             
                    {{-- Role-based Order Sections for non-admin users --}}

                    @if (Auth::user()->role == 2)
                        <li class="menu-title" data-key="t-applications">Orders</li>
                        <li>
                            <a href="{{ route('productOrders.index') }}">
                                <i class="mdi mdi-food nav-icon"></i>
                                <span class="menu-item" data-key="t-chat">Billing Products</span>
                                @if ($productOrderBIllingCount)
                                    <span class="badge rounded-pill bg-success">{{ $productOrderBIllingCount }}</span>
                                @endif
                            </a>
                        </li>

                    @elseif (Auth::user()->role == 3)
                        <li class="menu-title" data-key="t-applications">Orders</li>
                        <li>
                            <a href="{{ route('productpacking.index') }}">
                                <i class="mdi mdi-food nav-icon"></i>
                                <span class="menu-item" data-key="t-chat">Package Orders</span>
                                @if ($productOrderPendingCount)
                                    <span class="badge rounded-pill bg-success">{{ $productOrderPendingCount }}</span>
                                @endif
                            </a>
                        </li>

                    @elseif (Auth::user()->role == 4)
                        <li class="menu-title" data-key="t-applications">Orders</li>
                        <li>
                            <a href="{{ route('productdispatch.index') }}">
                                <i class="mdi mdi-food nav-icon"></i>
                                <span class="menu-item" data-key="t-chat">Dispatched Orders</span>
                                @if ($productOrderDispatchCount)
                                    <span class="badge rounded-pill bg-success">{{ $productOrderDispatchCount }}</span>
                                @endif
                            </a>
                        </li>

                    @elseif (Auth::user()->role == 5)
                        <li class="menu-title" data-key="t-applications">Orders</li>
                        <li>
                            <a href="{{ route('productdelivery.index') }}">
                                <i class="mdi mdi-food nav-icon"></i>
                                <span class="menu-item" data-key="t-chat">Out of Delivery Orders</span>
                                @if ($productOrderDeliveryCount)
                                    <span class="badge rounded-pill bg-success">{{ $productOrderDeliveryCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('productcomplete.index') }}">
                                <i class="mdi mdi-food nav-icon"></i>
                                <span class="menu-item" data-key="t-chat">Delivered Order</span>
                            </a>
                        </li>
                    @endif
              
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
