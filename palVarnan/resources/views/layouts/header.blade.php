@php
    $currentCountryId = session('country_id');
    $currentCountry = $currentCountryId ? \App\Models\Country::find($currentCountryId) : null;
    $headerCategories = \App\Models\Category::where('status', 1)->take(12)->get();
@endphp

<!-- ==================== Mobile Menu Start Here ==================== -->
<div class="mobile-menu scroll-sm d-lg-none d-block">
    <button type="button" class="close-button" aria-label="Close Menu"> <i class="ph ph-x"></i> </button>
    <div class="mobile-menu__inner">
        <a href="/" class="mobile-menu__logo mb-24 d-inline-block">
            <img src="/assets/images/logo/logo.png" alt="Pal Varnan" style="max-height: 48px; width: auto; object-fit: contain;"> 
        </a>
        <div class="mobile-menu__menu">
            <ul class="nav-menu flex-align nav-menu--mobile">
                <li class="nav-menu__item {{ request()->is('/') ? 'activePage' : '' }}">
                    <a href="/" class="nav-menu__link text-heading fw-semibold">Home</a>
                </li>
                <li class="nav-menu__item {{ request()->is('products*') ? 'activePage' : '' }}">
                    <a href="/products" class="nav-menu__link text-heading fw-semibold">Products</a>
                </li>
                <li class="nav-menu__item {{ request()->is('categories*') ? 'activePage' : '' }}">
                    <a href="{{ url('/categories') }}" class="nav-menu__link text-heading fw-semibold">Categories</a>
                </li>
                <li class="nav-menu__item {{ request()->is('about') ? 'activePage' : '' }}">
                    <a href="{{ route('about') }}" class="nav-menu__link text-heading fw-semibold">About Us</a>
                </li>
                <li class="nav-menu__item {{ request()->is('contact') ? 'activePage' : '' }}">
                    <a href="{{ route('contact') }}" class="nav-menu__link text-heading fw-semibold">Contact Us</a>
                </li>
                <li class="nav-menu__item {{ request()->is('cart') ? 'activePage' : '' }}">
                    <a href="{{ url('/cart') }}" class="nav-menu__link text-heading fw-semibold d-flex align-items-center justify-content-between">
                        <span>Shopping Cart</span>
                        <span class="badge bg-main-600 text-white rounded-pill text-xs px-8 py-2">{{ $cartCount }}</span>
                    </a>
                </li>
                @auth
                <li class="nav-menu__item {{ request()->is('wishlist') ? 'activePage' : '' }}">
                    <a href="/wishlist" class="nav-menu__link text-heading fw-semibold d-flex align-items-center justify-content-between">
                        <span>Wishlist</span>
                        <span class="badge bg-main-600 text-white rounded-pill text-xs px-8 py-2">{{ $wishlistCount }}</span>
                    </a>
                </li>
                <li class="nav-menu__item {{ request()->is('myaccount') ? 'activePage' : '' }}">
                    <a href="/myaccount" class="nav-menu__link text-heading fw-semibold">My Profile ({{ Auth::user()->name }})</a>
                </li>
                <li class="nav-menu__item">
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="nav-menu__link text-danger fw-semibold border-0 bg-transparent p-0 text-start w-100">
                            Logout
                        </button>
                    </form>
                </li>
                @else
                <li class="nav-menu__item">
                    <a href="/login" class="nav-menu__link text-heading fw-semibold">Login</a>
                </li>
                <li class="nav-menu__item">
                    <a href="/register" class="nav-menu__link text-heading fw-semibold">Register</a>
                </li>
                @endauth
                <li class="nav-menu__item border-top border-neutral-40 pt-16 mt-16">
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#countryModal" class="nav-menu__link text-heading fw-semibold d-flex align-items-center gap-8">
                        <i class="ph-bold ph-globe text-main-600 text-lg"></i>
                        <span>Change Country / Currency</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- ==================== Mobile Menu End Here ==================== -->

<!-- ======================= Middle Header Start ========================= -->
<header class="header-middle border-bottom border-neutral-40 py-10 py-lg-12 bg-white">
    <div class="container container-lg">
        <nav class="header-inner d-flex align-items-center justify-content-between gap-12 gap-md-16">
            <!-- Logo Start -->
            <div class="logo flex-shrink-0">
                <a href="/" class="link d-inline-block">
                    <img src="/assets/images/logo/logo.png" alt="Pal Varnan" style="max-height: 46px; width: auto; object-fit: contain;"> 
                </a>
            </div>
            <!-- Logo End -->

            <!-- Menu Start (Desktop only) -->
            <div class="header-menu d-lg-block d-none">
                <ul class="nav-menu d-flex align-items-center gap-32 mb-0 ps-0 list-unstyled">
                    <li class="nav-menu__item">
                        <a href="/" class="nav-menu__link text-heading text-15 fw-semibold {{ request()->is('/') ? 'text-main-600 active' : '' }} hover-text-main-600 transition-1 py-8 position-relative">
                            Home
                        </a>
                    </li>
                    <li class="nav-menu__item">
                        <a href="/products" class="nav-menu__link text-heading text-15 fw-semibold {{ request()->is('products*') ? 'text-main-600 active' : '' }} hover-text-main-600 transition-1 py-8 position-relative">
                            Products
                        </a>
                    </li>
                    <li class="nav-menu__item">
                        <a href="{{ url('/categories') }}" class="nav-menu__link text-heading text-15 fw-semibold {{ request()->is('categories*') ? 'text-main-600 active' : '' }} hover-text-main-600 transition-1 py-8 position-relative">
                            Categories
                        </a>
                    </li>
                    <li class="nav-menu__item">
                        <a href="{{ route('about') }}" class="nav-menu__link text-heading text-15 fw-semibold {{ request()->is('about') ? 'text-main-600 active' : '' }} hover-text-main-600 transition-1 py-8 position-relative">
                            About Us
                        </a>
                    </li>
                    <li class="nav-menu__item">
                        <a href="{{ route('contact') }}" class="nav-menu__link text-heading text-15 fw-semibold {{ request()->is('contact') ? 'text-main-600 active' : '' }} hover-text-main-600 transition-1 py-8 position-relative">
                            Contact Us
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Menu End -->
             
            <!-- Middle Header Right start -->
            <div class="header-right d-flex align-items-center gap-8 gap-sm-12 flex-shrink-0">
                <!-- Dropdown Country/Currency Select Start -->
                <a href="javascript:void(0)" 
                   data-bs-toggle="modal" 
                   data-bs-target="#countryModal" 
                   class="d-flex align-items-center gap-6 py-6 px-10 rounded-pill bg-neutral-30 border border-neutral-100 hover-border-main-600 text-neutral-600 hover-text-main-600 text-sm fw-semibold transition-1 text-decoration-none shadow-none"
                   title="Select Country / Currency">
                    @if($currentCountry && strtolower($currentCountry->name) == 'mexico')
                        <img src="/assets/images/flags/mexico.png" alt="Mexico" style="width: 20px; height: 14px; object-fit: cover; border-radius: 2px;" onerror="this.src='https://flagcdn.com/w20/mx.png'">
                        <span class="d-none d-md-inline">Mexico (MXN $)</span>
                    @else
                        <img src="/assets/images/flags/india.png" alt="India" style="width: 20px; height: 14px; object-fit: cover; border-radius: 2px;" onerror="this.src='https://flagcdn.com/w20/in.png'">
                        <span class="d-none d-md-inline">India (INR ₹)</span>
                    @endif
                    <i class="ph ph-caret-down text-xs text-neutral-500"></i>
                </a>
                <!-- Dropdown Select End -->

                <!-- Order Tracking (Desktop / Tablet) -->
                <a href="/myaccount" class="d-none d-md-flex align-items-center gap-6 text-neutral-600 hover-text-main-600 text-sm fw-semibold transition-1 text-decoration-none py-8 px-10 rounded-pill hover-bg-neutral-30">
                    <i class="ph-bold ph-truck text-main-600 text-base"></i>
                    <span>Order Tracking</span>
                </a>

                <!-- Mobile Cart Button (visible below lg) -->
                <a href="{{ url('/cart') }}" class="d-flex d-lg-none align-items-center justify-content-center rounded-circle border border-neutral-100 hover-border-main-600 bg-white transition-1 text-decoration-none position-relative" style="width: 38px; height: 38px;" aria-label="Shopping Cart">
                    <i class="ph-bold ph-shopping-cart-simple text-xl text-main-600"></i>
                    <span class="w-18 h-18 flex-center rounded-circle bg-success-600 text-white text-xs fw-bold position-absolute top-n4 end-n4 js-cart-count-badge" id="cart-count-mobile">
                        {{ $cartCount }}
                    </span>
                </a>

                <!-- Mobile Account Dropdown (visible below lg) -->
                <div class="dropdown d-block d-lg-none">
                    <button class="d-flex align-items-center justify-content-center rounded-circle bg-main-50 border border-main-100 text-main-600 hover-bg-main-600 hover-text-white transition-1"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu" style="width: 38px; height: 38px;">
                        <i class="ph-bold ph-user text-base"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-12 border-0 p-8 mt-8" style="min-width: 180px;">
                        @auth
                        <li>
                            <div class="px-14 py-8 border-bottom border-neutral-40 mb-4">
                                <span class="text-xs text-neutral-400 d-block">Signed in as</span>
                                <strong class="text-sm text-heading text-truncate d-block">{{ Auth::user()->name }}</strong>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item py-8 px-14 rounded-8 d-flex align-items-center gap-8 text-sm fw-medium text-heading hover-bg-main-50 hover-text-main-600" href="/myaccount">
                                <i class="ph ph-user-circle text-base"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-8 px-14 rounded-8 d-flex align-items-center gap-8 text-sm fw-medium text-heading hover-bg-main-50 hover-text-main-600" href="/myaccount">
                                <i class="ph ph-package text-base"></i> Order History
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-8 px-14 rounded-8 d-flex align-items-center gap-8 text-sm fw-medium text-heading hover-bg-main-50 hover-text-main-600" href="/wishlist">
                                <i class="ph ph-heart text-base"></i> Wishlist
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-4 border-neutral-50"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item py-8 px-14 rounded-8 d-flex align-items-center gap-8 text-sm fw-medium text-danger hover-bg-danger-50">
                                    <i class="ph ph-sign-out text-base"></i> Logout
                                </button>
                            </form>
                        </li>
                        @else
                        <li>
                            <a class="dropdown-item py-8 px-14 rounded-8 d-flex align-items-center gap-8 text-sm fw-medium text-heading hover-bg-main-50 hover-text-main-600" href="/login">
                                <i class="ph ph-sign-in text-base"></i> Login
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-8 px-14 rounded-8 d-flex align-items-center gap-8 text-sm fw-medium text-heading hover-bg-main-50 hover-text-main-600" href="/register">
                                <i class="ph ph-user-plus text-base"></i> Register
                            </a>
                        </li>
                        @endauth
                    </ul>
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" class="toggle-mobileMenu d-lg-none text-heading text-2xl d-flex align-items-center justify-content-center border-0 bg-transparent p-0 ms-2" aria-label="Open Menu">
                    <i class="ph ph-list"></i>
                </button>
            </div>
            <!-- Middle Header Right End -->
        </nav>
    </div>
</header>
<!-- ======================= Middle Header End ========================= -->

<!-- ==================== Header Two Start Here (Desktop only) ==================== -->
<header class="header bg-white py-16 border-bottom border-neutral-40 d-none d-lg-block">
    <div class="container container-lg">
        <nav class="header-inner d-flex align-items-center justify-content-between gap-20">
            
            <!-- Category Dropdown Start -->
            <div class="category d-none d-lg-block on-hover-item flex-shrink-0" style="min-width: 220px;">
                <button type="button" class="category__button d-flex align-items-center gap-10 fw-semibold py-14 px-20 bg-white hover-bg-main-50 text-heading rounded-12 w-100 transition-1 border border-neutral-200" style="height: 52px;">
                    <span class="icon text-xl d-flex flex-shrink-0 text-main-600"><i class="ph-bold ph-squares-four"></i></span>
                    <span class="text-sm">All Categories</span>
                    <span class="arrow-icon text-sm d-flex ms-auto text-neutral-500"><i class="ph-bold ph-caret-down"></i></span>
                </button>

                <div class="responsive-dropdown on-hover-dropdown common-dropdown nav-submenu p-0 submenus-submenu-wrapper rounded-12 shadow-lg border border-neutral-100 overflow-hidden" style="min-width: 240px;">
                    <ul class="scroll-sm p-8 m-0 list-unstyled max-h-380 overflow-y-auto">
                        @forelse($headerCategories as $cat)
                            <li>
                                <a href="{{ url('/products?category=' . \Illuminate\Support\Str::slug($cat->category_name)) }}" class="d-flex align-items-center gap-10 py-10 px-14 rounded-8 text-neutral-600 hover-text-main-600 hover-bg-main-50 transition-1 text-sm fw-medium text-decoration-none">
                                    <span class="w-24 h-24 flex-center text-main-600 text-base flex-shrink-0">
                                        <i class="ph-bold ph-tag"></i>
                                    </span>
                                    <span class="text-truncate">{{ $cat->category_name }}</span>
                                </a>
                            </li>
                        @empty
                            <li>
                                <a href="/products" class="d-flex align-items-center gap-10 py-10 px-14 rounded-8 text-neutral-600 hover-text-main-600 hover-bg-main-50 transition-1 text-sm fw-medium text-decoration-none">
                                    <span class="text-main-600 text-base"><i class="ph-bold ph-carrot"></i></span>
                                    <span>Fresh Produce & Grocery</span>
                                </a>
                            </li>
                        @endforelse
                        <li class="border-top border-neutral-50 pt-6 mt-6">
                            <a href="{{ url('/categories') }}" class="d-flex align-items-center justify-content-between py-8 px-14 rounded-8 text-main-600 hover-bg-main-50 text-xs fw-bold text-decoration-none">
                                <span>View All Categories</span>
                                <i class="ph-bold ph-arrow-right"></i>
                            </a>
                        </li>
                        <li>
                            <a href="/products" class="d-flex align-items-center justify-content-between py-8 px-14 rounded-8 text-neutral-600 hover-bg-main-50 text-xs fw-medium text-decoration-none">
                                <span>Browse All Catalog</span>
                                <i class="ph-bold ph-caret-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Category Dropdown End -->

            <!-- Search Start -->
            <form action="/products" method="GET" class="position-relative flex-grow-1 m-0 js-search">
                <div class="position-relative">
                    <input type="text" id="globalSearch" name="search" value="{{ request('search') }}" 
                        class="form-control fw-medium bg-neutral-30 border border-neutral-100 rounded-pill py-14 ps-24 pe-56 text-sm text-heading shadow-none js-search-input" 
                        placeholder="Search products..." 
                        autocomplete="off"
                        style="height: 52px;">
                    <button type="submit"
                        class="position-absolute top-50 translate-middle-y end-0 me-24 text-main-600 hover-text-main-800 text-xl border-0 bg-transparent p-0 flex-center transition-1"
                        title="Search"
                        aria-label="Search">
                        <i class="ph-bold ph-magnifying-glass"></i>
                    </button>
                </div>
                <div id="searchResults"
                    class="js-search-results position-absolute bg-white shadow-lg rounded-12 w-100 mt-6 d-none border border-neutral-100 overflow-hidden"
                    style="z-index: 9999; max-height: 420px; overflow-y: auto;">
                </div>
            </form>
            <!-- Search End -->

            <!-- Header Action Controls Start -->
            <div class="d-flex align-items-center gap-12 flex-shrink-0">
                @auth
                <!-- Wishlist -->
                <a href="/wishlist" class="d-flex align-items-center gap-8 py-10 px-14 rounded-pill border border-neutral-100 hover-border-main-600 bg-white transition-1 text-decoration-none" style="height: 52px;">
                    <span class="text-2xl text-heading d-flex position-relative">
                        <i class="ph-bold ph-heart"></i>
                        <span id="wishlist-count-badge" class="w-18 h-18 flex-center rounded-circle bg-success-600 text-white text-xs position-absolute top-n6 end-n6">
                            {{ $wishlistCount }}
                        </span>
                    </span>
                    <span class="text-sm text-neutral-600 fw-semibold d-none d-xl-inline">Wishlist</span>
                </a>
                @endauth

                <!-- Cart -->
                <a href="{{ url('/cart') }}" class="d-flex align-items-center gap-10 py-10 px-16 rounded-pill border border-neutral-100 hover-border-main-600 bg-white transition-1 text-decoration-none item-hover" style="height: 52px;">
                    <span class="text-2xl text-main-600 d-flex position-relative">
                        <i class="ph-bold ph-shopping-cart-simple"></i>
                        <span id="cart-count" class="w-18 h-18 flex-center rounded-circle bg-success-600 text-white text-xs fw-bold position-absolute top-n6 end-n6 js-cart-count-badge">
                            {{ $cartCount }}
                        </span>
                    </span>
                    <span class="text-sm text-heading fw-semibold">Cart</span>
                </a>

                <!-- Account -->
                @auth
                <div class="dropdown">
                    <button class="d-flex align-items-center gap-8 fw-semibold text-main-600 py-10 px-18 bg-main-50 border border-main-100 rounded-pill hover-bg-main-600 hover-text-white transition-1 text-decoration-none"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu" style="height: 52px;">
                        <i class="ph-bold ph-user text-base"></i>
                        <span class="text-sm max-w-100 text-truncate">{{ Auth::user()->name }}</span>
                        <i class="ph ph-caret-down text-xs"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-12 border-0 p-8 mt-8" style="min-width: 180px;">
                        <li>
                            <a class="dropdown-item py-8 px-14 rounded-8 d-flex align-items-center gap-8 text-sm fw-medium text-heading hover-bg-main-50 hover-text-main-600" href="/myaccount">
                                <i class="ph ph-user-circle text-base"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-8 px-14 rounded-8 d-flex align-items-center gap-8 text-sm fw-medium text-heading hover-bg-main-50 hover-text-main-600" href="/myaccount">
                                <i class="ph ph-package text-base"></i> Order History
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-4 border-neutral-50"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item py-8 px-14 rounded-8 d-flex align-items-center gap-8 text-sm fw-medium text-danger hover-bg-danger-50">
                                    <i class="ph ph-sign-out text-base"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @else
                <div class="dropdown">
                    <button class="d-flex align-items-center gap-8 fw-semibold text-main-600 py-10 px-20 bg-main-50 border border-main-100 rounded-pill hover-bg-main-600 hover-text-white transition-1 text-decoration-none"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu" style="height: 52px;">
                        <i class="ph-bold ph-user text-base"></i>
                        <span class="text-sm">Account</span>
                        <i class="ph ph-caret-down text-xs"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-12 border-0 p-8 mt-8" style="min-width: 160px;">
                        <li>
                            <a class="dropdown-item py-8 px-14 rounded-8 d-flex align-items-center gap-8 text-sm fw-medium text-heading hover-bg-main-50 hover-text-main-600" href="/login">
                                <i class="ph ph-sign-in text-base"></i> Login
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-8 px-14 rounded-8 d-flex align-items-center gap-8 text-sm fw-medium text-heading hover-bg-main-50 hover-text-main-600" href="/register">
                                <i class="ph ph-user-plus text-base"></i> Register
                            </a>
                        </li>
                    </ul>
                </div>
                @endauth
            </div>
            <!-- Header Action Controls End -->

        </nav>
    </div>
</header>
<!-- ==================== Header End Here ==================== -->

<!-- ==================== Mobile Search (visible below lg) ==================== -->
<div class="d-lg-none bg-white border-bottom border-neutral-40 py-10">
    <div class="container container-lg">
        <form action="/products" method="GET" class="position-relative m-0 js-search">
            <div class="position-relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="form-control fw-medium bg-neutral-30 border border-neutral-100 rounded-pill py-12 ps-20 pe-52 text-sm text-heading shadow-none js-search-input"
                    placeholder="Search products..."
                    autocomplete="off">
                <button type="submit"
                    class="position-absolute top-50 translate-middle-y end-0 me-20 text-main-600 hover-text-main-800 text-lg border-0 bg-transparent p-0 flex-center transition-1"
                    aria-label="Search">
                    <i class="ph-bold ph-magnifying-glass"></i>
                </button>
            </div>
            <div class="js-search-results position-absolute bg-white shadow-lg rounded-12 w-100 mt-6 d-none border border-neutral-100 overflow-hidden"
                style="z-index: 9999; max-height: 420px; overflow-y: auto;"></div>
        </form>
    </div>
</div>

@include('layouts.country-modal')

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchForms = document.querySelectorAll(".js-search");

    searchForms.forEach(form => {
        const input = form.querySelector(".js-search-input");
        const resultsBox = form.querySelector(".js-search-results");
        if (!input || !resultsBox) return;

        let timeout = null;

        const doSearch = function () {
            clearTimeout(timeout);
            let query = input.value.trim();

            if (query.length < 2) {
                resultsBox.classList.add("d-none");
                resultsBox.innerHTML = "";
                return;
            }

            timeout = setTimeout(() => {
                fetch(`/search?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        const esc = (str) => String(str ?? '')
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;')
                            .replace(/"/g, '&quot;')
                            .replace(/'/g, '&#39;');

                        resultsBox.innerHTML = '';

                        if (data.length === 0) {
                            resultsBox.innerHTML = `
                                <div class="p-16 text-muted text-center text-sm">
                                    No products found for "${esc(query)}"
                                </div>
                            `;
                        } else {
                            data.forEach(item => {
                                const safeUrl = esc(item.url || '#');
                                const safeImg = esc(item.image || '/assets/images/logo/logo.png');
                                const safeName = esc(item.name || '');
                                const safeType = esc(item.type || 'Product');
                                resultsBox.innerHTML += `
                                    <a href="${safeUrl}" 
                                       class="d-flex align-items-center gap-12 p-12 border-bottom border-neutral-40 text-decoration-none text-heading hover-bg-neutral-30 transition-1">
                                        <img src="${safeImg}" 
                                             width="44"
                                             height="44"
                                             style="object-fit:cover;border-radius:6px;"
                                             onerror="this.onerror=null;this.src='/assets/images/logo/logo.png'">
                                        <div class="overflow-hidden">
                                             <div class="fw-semibold text-sm text-truncate">
                                                 ${safeName}
                                             </div>
                                             <small class="text-main-600 text-xs">
                                                 ${safeType}
                                             </small>
                                        </div>
                                    </a>
                                `;
                            });
                        }

                        resultsBox.classList.remove("d-none");
                    })
                    .catch(() => {
                        resultsBox.classList.add("d-none");
                    });

            }, 300);
        };

        input.addEventListener("input", doSearch);

        document.addEventListener("click", function(e) {
            if (!form.contains(e.target)) {
                resultsBox.classList.add("d-none");
            }
        });
    });
});
</script>