<header id="page-topbar" class="isvertical-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="index" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('assets/images/smlo.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('assets/images/smlo.png') }}" alt="" height="22"> <span
                            class="logo-txt">@lang('translation.Yesbe')</span>
                    </span>
                </a>

                <a href="index" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('assets/images/smlo.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('assets/images/smlo.png') }}" alt="" height="22"> <span
                            class="logo-txt">@lang('translation.Yesbe')</span>
                    </span>
                </a>

            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        @can('Country.selection')
        <div class="dropdown d-inline-block me-2">
            <button class="btn header-item d-flex align-items-center country-dropdown-btn"
                type="button" data-bs-toggle="dropdown" aria-expanded="false">

                 <span class="ms-1" id="selectedCountryText">
                    {{ session('country_name') ?? $selectedCountry ?? 'Select Country' }}
                </span>
                <i class="mdi mdi-chevron-down ms-1"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end p-2 country-dropdown">
                <li>
                    <a href="javascript:void(0)" 
                    class="dropdown-item country-select"
                    data-id=""
                    data-name="All Countries">
                         All Countries
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                @foreach($countries as $country)
                    <li>
                        <a href="javascript:void(0)" 
                        class="dropdown-item country-select"
                        data-id="{{ $country->id }}"
                        data-name="{{ $country->name }}">
                            {{ $country->name }} ({{ $country->currency }})
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        @endcan

        <div class="d-flex">
            <div class="dropdown d-inline-block">
                <a href="{{ route("lowstock") }}">
                <button type="button" class="btn header-item noti-icon"
                   >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-bell icon-sm">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="noti-dot bg-danger rounded-pill">{{ $stockscount }}</span>
                </button>
            </a>
                {{-- <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="m-0 font-size-15"> Notifications Stock </h5>
                            </div>

                        </div>
                    </div>
                    <div data-simplebar="init" style="max-height: 250px;">
                        <div class="simplebar-wrapper" style="margin: 0px;">
                            <div class="simplebar-height-auto-observer-wrapper">
                                <div class="simplebar-height-auto-observer"></div>
                            </div>
                            <div class="simplebar-mask">
                                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                    <div class="simplebar-content-wrapper"
                                        style="height: auto; overflow: hidden; padding-right: 0px; padding-bottom: 0px;">
                                        <div class="simplebar-content" style="padding: 0px;">
                                            <a href="" class="text-reset notification-item">
                                                <div class="d-flex border-bottom align-items-start bg-light">
                                                    <div class="flex-shrink-0">
                                                        <img src="assets/images/users/avatar-3.jpg"
                                                            class="me-3 rounded-circle avatar-sm" alt="user-pic">
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">Justin Verduzco</h6>
                                                        <div class="text-muted">
                                                            <p class="mb-1 font-size-13">Your task changed an issue from
                                                                "In Progress" to <span
                                                                    class="badge badge-soft-success">Review</span></p>
                                                            <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                                    class="mdi mdi-clock-outline"></i> 1 hours ago</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="" class="text-reset notification-item">
                                                <div class="d-flex border-bottom align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-sm me-3">
                                                            <span
                                                                class="avatar-title bg-primary rounded-circle font-size-16">
                                                                <i class="bx bx-shopping-bag"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">New order has been placed</h6>
                                                        <div class="text-muted">
                                                            <p class="mb-1 font-size-13">Open the order confirmation or
                                                                shipment confirmation.</p>
                                                            <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                                    class="mdi mdi-clock-outline"></i> 5 hours ago</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="" class="text-reset notification-item">
                                                <div class="d-flex border-bottom align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-sm me-3">
                                                            <span
                                                                class="avatar-title bg-soft-success text-success rounded-circle font-size-16">
                                                                <i class="bx bx-cart"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">Your item is shipped</h6>
                                                        <div class="text-muted">
                                                            <p class="mb-1 font-size-13">Here is somthing that you
                                                                might light like to know.</p>
                                                            <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                                    class="mdi mdi-clock-outline"></i> 1 day ago</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>

                                            <a href="" class="text-reset notification-item">
                                                <div class="d-flex border-bottom align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <img src="assets/images/users/avatar-4.jpg"
                                                            class="me-3 rounded-circle avatar-sm" alt="user-pic">
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">Salena Layfield</h6>
                                                        <div class="text-muted">
                                                            <p class="mb-1 font-size-13">Yay ! Everything worked!</p>
                                                            <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                                    class="mdi mdi-clock-outline"></i> 3 days ago</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="simplebar-placeholder" style="width: 0px; height: 0px;"></div>
                        </div>
                        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                            <div class="simplebar-scrollbar"
                                style="transform: translate3d(0px, 0px, 0px); display: none;"></div>
                        </div>
                        <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                            <div class="simplebar-scrollbar"
                                style="transform: translate3d(0px, 0px, 0px); display: none;"></div>
                        </div>
                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-14 btn-block text-decoration-underline fw-bold text-center"
                            href="javascript:void(0)">
                            <span>View All <i class="bx bx-right-arrow-alt"></i></span>
                        </a>
                    </div>
                </div> --}}
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-clock icon-sm">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    @if(($expiringCount ?? 0) > 0)
                        <span class="noti-dot bg-warning rounded-pill">{{ $expiringCount }}</span>
                    @endif
                </button>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="expiring-dropdown">
                    <div class="p-3 border-bottom">
                        <h6 class="m-0 font-size-15">Expiring Soon (no offer set)</h6>
                    </div>
                    <div data-simplebar style="max-height: 280px; overflow-y:auto;">
                        @forelse(($expiringItems ?? []) as $item)
                            <a href="{{ url('/stock-tracking') }}" class="text-reset notification-item d-block px-3 py-2 border-bottom">
                                <div class="fw-semibold">{{ $item->productname }}</div>
                                <div class="small text-warning">
                                    Expires {{ \Carbon\Carbon::parse($item->expiry_date)->diffForHumans() }}
                                </div>
                            </a>
                        @empty
                            <div class="p-3 text-muted small">No items expiring soon.</div>
                        @endforelse
                    </div>
                    @if(($expiringCount ?? 0) > 0)
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link" href="{{ url('/stock-tracking') }}">Set offers now</a>
                    </div>
                    @endif
                </div>
            </div>

            
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-truck icon-sm">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    @if(($overdueDistributionsCount ?? 0) > 0)
                        <span class="noti-dot bg-danger rounded-pill">{{ $overdueDistributionsCount }}</span>
                    @endif
                </button>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="overdue-dropdown">
                    <div class="p-3 border-bottom">
                        <h6 class="m-0 font-size-15">Awaiting Receipt Confirmation</h6>
                    </div>
                    <div data-simplebar style="max-height: 280px; overflow-y:auto;">
                        @forelse(($overdueDistributions ?? []) as $item)
                            @php
                                $daysPending = \Carbon\Carbon::parse($item->distributed_at)->diffInDays(now());
                            @endphp
                            <a href="{{ url('/stock-distribution') }}" class="text-reset notification-item d-block px-3 py-2 border-bottom">
                                <div class="fw-semibold">{{ $item->product_name }}</div>
                                <div class="small text-muted">
                                    {{ $item->warehouse_name ?? '—' }} → {{ $item->branch_name ?? '—' }} • Qty: {{ $item->quantity }}
                                </div>
                                <div class="small text-danger">
                                    Pending {{ $daysPending }}d — not yet confirmed received
                                </div>
                            </a>
                        @empty
                            <div class="p-3 text-muted small">No overdue distributions.</div>
                        @endforelse
                    </div>
                    @if(($overdueDistributionsCount ?? 0) > 0)
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link" href="{{ url('/stock-distribution') }}">Review distributions</a>
                    </div>
                    @endif
                </div>
            </div>


            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item user text-start d-flex align-items-center"
                    id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="{{ asset('/assets/images/logoooo.png') }}" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">Admin</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end pt-0">
                    <a class="dropdown-item " href="javascript:void();"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                            class="bx bx-power-off font-size-16 align-middle me-1"></i> <span
                            key="t-logout">Logout</span></a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on("click", ".country-select", function () {
    let countryId = $(this).data("id");
    let countryName = $(this).data("name");

    $.ajax({
        url: "/set-country",
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            country_id: countryId,
            country_name: countryName
        },
        success: function () {
            location.reload(); // ✅ VERY IMPORTANT
        }
    });
});
</script>

<style>
    
.country-dropdown-btn {
    /* border: 1px solid #ddd; */
    border-radius: 20px;
    padding: 6px 35px;
    height: 40px;
    background: #f5e698 !important;
    transition: 0.3s;
}

.country-dropdown-btn:hover {
    /* background: #c8add0; */
    background: #f5e698;
}

.country-dropdown {
    max-height: 250px;
    overflow-y: auto;
    min-width: 200px;
    border-radius: 10px;
}

.country-dropdown .dropdown-item {
    border-radius: 8px;
    padding: 8px 10px;
}

.country-dropdown .dropdown-item:hover {
    background: #f1f1f1;
}
</style>