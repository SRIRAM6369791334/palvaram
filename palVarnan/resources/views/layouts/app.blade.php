<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Title -->
    <title> Pal Varnan</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="/assets/images/logo/logo.png">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <!-- select 2 -->
    <link rel="stylesheet" href="/assets/css/select2.min.css">
    <!-- Slick -->
    <link rel="stylesheet" href="/assets/css/slick.css">
    <!-- Jquery Ui -->
    <link rel="stylesheet" href="/assets/css/jquery-ui.css">
    <!-- animate -->
    <link rel="stylesheet" href="/assets/css/animate.css">
    <!-- AOS Animation -->
    <link rel="stylesheet" href="/assets/css/aos.css">
    <!-- Main css -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Global High-Contrast Button & SweetAlert2 Styling -->
    <style>
        /* WCAG 2.1 AA Button Contrast Overrides */
        .btn-outline-primary {
            color: #1c799b !important;
            background-color: transparent !important;
            border: 1px solid #1c799b !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:active, .btn-outline-primary:focus {
            color: #ffffff !important;
            background-color: #1c799b !important;
            border-color: #155d77 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(28, 121, 155, 0.25) !important;
        }

        .btn-outline-danger {
            color: #ef4444 !important;
            background-color: transparent !important;
            border: 1px solid #ef4444 !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .btn-outline-danger:hover, .btn-outline-danger:active, .btn-outline-danger:focus {
            color: #ffffff !important;
            background-color: #ef4444 !important;
            border-color: #dc2626 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.25) !important;
        }

        .btn-outline-warning {
            color: #d97706 !important;
            background-color: transparent !important;
            border: 1px solid #f59e0b !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .btn-outline-warning:hover, .btn-outline-warning:active, .btn-outline-warning:focus {
            color: #ffffff !important;
            background-color: #f59e0b !important;
            border-color: #d97706 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.25) !important;
        }

        .btn-outline-success {
            color: #10b981 !important;
            background-color: transparent !important;
            border: 1px solid #10b981 !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .btn-outline-success:hover, .btn-outline-success:active, .btn-outline-success:focus {
            color: #ffffff !important;
            background-color: #10b981 !important;
            border-color: #059669 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.25) !important;
        }

        .btn-outline-secondary, .btn-outline-gray {
            color: #4b5563 !important;
            background-color: transparent !important;
            border: 1px solid #d1d5db !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .btn-outline-secondary:hover, .btn-outline-gray:hover,
        .btn-outline-secondary:active, .btn-outline-gray:active,
        .btn-outline-secondary:focus, .btn-outline-gray:focus {
            color: #111827 !important;
            background-color: #f3f4f6 !important;
            border-color: #9ca3af !important;
        }

        .btn-outline-info {
            color: #0284c7 !important;
            background-color: transparent !important;
            border: 1px solid #0284c7 !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .btn-outline-info:hover, .btn-outline-info:active, .btn-outline-info:focus {
            color: #ffffff !important;
            background-color: #0284c7 !important;
            border-color: #0369a1 !important;
        }

        .btn-outline-dark {
            color: #1f2937 !important;
            background-color: transparent !important;
            border: 1px solid #1f2937 !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .btn-outline-dark:hover, .btn-outline-dark:active, .btn-outline-dark:focus {
            color: #ffffff !important;
            background-color: #1f2937 !important;
            border-color: #111827 !important;
        }

        .btn-outline-neutral-100, .btn-outline-neutral-200 {
            color: #374151 !important;
            background-color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .btn-outline-neutral-100:hover, .btn-outline-neutral-200:hover {
            color: #1c799b !important;
            border-color: #1c799b !important;
            background-color: #f0f7fa !important;
        }

        .btn-outline-neutral-300:disabled, .btn-outline-neutral-300[disabled] {
            color: #9ca3af !important;
            background-color: #f3f4f6 !important;
            border: 1px solid #e5e7eb !important;
            opacity: 0.8 !important;
            cursor: not-allowed !important;
        }

        /* Mobile Header Sticky */
        @media (max-width: 991.98px) {
            .header-middle {
                position: sticky;
                top: 0;
                z-index: 1020;
                background-color: #ffffff !important;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }
        }

        /* =========================================================
           RESPONSIVE UI FIXES — added 2026-11-19
           ========================================================= */

        /* BUG #2: Banner content — remove hardcoded negative margin on mobile */
        .banner-item__content {
            margin-top: -95px; /* desktop default */
        }
        @media (max-width: 575px) {
            .banner-item__content {
                margin-top: 0 !important;
            }
            /* BUG #3: Banner height flexible on mobile */
            .banner-item {
                min-height: 300px;
                height: auto !important;
            }
            .banner-slider__inner {
                flex-direction: column-reverse;
                align-items: center;
                padding: 24px 16px;
            }
        }

        /* =========================================================
           GLOBAL PRODUCT CARD STRETCH & ALIGNMENT
           Fixes squished 123px thumbnail in 350px card bug
           ========================================================= */
        .product-card {
            align-items: stretch !important;
            height: 100% !important;
        }
        .product-card > div,
        .product-card__thumb,
        .product-card__content {
            width: 100% !important;
        }
        .product-card__thumb {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .product-card__thumb img {
            max-width: 100% !important;
            object-fit: contain !important;
        }

        /* Common Tab Pills modern styling across all devices */
        .common-tab .nav-link {
            border-radius: 50rem !important;
            padding: 8px 18px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            background: #f8fafc !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            transition: all 0.2s ease !important;
        }
        .common-tab .nav-link:hover {
            background: #e6f5f4 !important;
            color: #026966 !important;
            border-color: rgba(2, 105, 102, 0.3) !important;
        }
        .common-tab .nav-link.active {
            background: #026966 !important;
            color: #ffffff !important;
            border-color: #026966 !important;
            box-shadow: 0 4px 12px rgba(2, 105, 102, 0.2) !important;
        }

        /* BUG #5: Recommended tabs — horizontal scroll on tablet & mobile instead of overflow */
        @media (max-width: 991px) {
            .section-heading.flex-between {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 14px !important;
            }
            .section-heading .common-tab {
                display: flex !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap !important;
                scrollbar-width: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding-bottom: 8px !important;
                padding-top: 2px !important;
                gap: 8px !important;
            }
            .section-heading .common-tab::-webkit-scrollbar {
                display: none !important;
            }
            .section-heading .common-tab .nav-item {
                flex: 0 0 auto !important;
            }
        }

        /* BUG #7: Body padding so floating cart bar doesn't cover page content */
        @media (max-width: 768px) {
            body:has(#floating-cart-bar) {
                padding-bottom: 90px;
            }
        }

        /* Popular Categories Carousel: Ensure gradient shadows never block clicks or fade on mobile */
        .gradient-shadow::before,
        .gradient-shadow::after {
            pointer-events: none !important;
        }
        @media (max-width: 991px) {
            .gradient-shadow::before,
            .gradient-shadow::after {
                display: none !important;
            }
        }

        /* Sticky footer layout - eliminates any bottom gap without locking window scroll */
        html {
            height: auto !important;
            overflow-x: clip !important;
        }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
            overflow-x: clip !important;
        }
        .site-main {
            flex: 1 0 auto;
            width: 100%;
        }

        /* Safety: Ensure animated elements are always visible and never locked in hidden state */
        .wow {
            visibility: visible !important;
        }
        [data-aos] {
            opacity: 1 !important;
            transform: none !important;
            visibility: visible !important;
        }
        .container-lg {
            max-width: 1320px;
            margin-left: auto !important;
            margin-right: auto !important;
            padding-inline: 24px !important;
        }
        @media (max-width: 575px) {
            .container-lg {
                padding-inline: 16px !important;
            }
        }

        /* Country Modal Selection Cards */
        .country-select-btn {
            border: 2px solid #e5e7eb !important;
            background-color: #ffffff !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .country-select-btn:hover {
            border-color: #1c799b !important;
            background-color: #f0f7fa !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(28, 121, 155, 0.12) !important;
        }
        .country-select-btn.active-country {
            border-color: #1c799b !important;
            background-color: #eef8fb !important;
        }
        .country-select-btn .country-title {
            color: #111827 !important;
            font-weight: 700 !important;
        }

        /* Modern SweetAlert2 UI Enhancement */
        .swal2-popup {
            font-family: inherit !important;
            border-radius: 16px !important;
            padding: 24px !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }
        .swal2-title {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #111827 !important;
        }
        .swal2-html-container {
            font-size: 0.95rem !important;
            color: #4b5563 !important;
        }
        .swal2-confirm {
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 10px 24px !important;
        }
        .swal2-cancel {
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 10px 20px !important;
        }
    </style>

    <!-- Device responsive fixes (loaded last so it can override main.css + layout styles) -->
    <link rel="stylesheet" href="/assets/css/responsive.css?v={{ @filemtime(public_path('assets/css/responsive.css')) }}">

</head>
<body>
    
<!--==================== Preloader Start ====================-->
  <div class="preloader">
    <img src="/assets/images/icon/preloader.gif" alt="">
  </div>
<!--==================== Preloader End ====================-->

<!--==================== Overlay Start ====================-->
<div class="overlay"></div>
<!--==================== Overlay End ====================-->

<!--==================== Sidebar Overlay End ====================-->
<div class="side-overlay"></div>
<!--==================== Sidebar Overlay End ====================-->

<!-- ==================== Scroll to Top End Here ==================== -->
<div class="progress-wrap">
  <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
      <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
  </svg>
</div>
<!-- ==================== Scroll to Top End Here ==================== -->

<!-- ==================== Search Box Start Here ==================== -->
 <form action="#" class="search-box">
  <button type="button" class="search-box__close position-absolute inset-block-start-0 inset-inline-end-0 m-16 w-48 h-48 border border-gray-100 rounded-circle flex-center text-white hover-text-gray-800 hover-bg-white text-2xl transition-1">
    <i class="ph ph-x"></i>
  </button>
  <div class="container">
    <div class="position-relative">
      <input type="text" class="form-control py-16 px-24 text-xl rounded-pill pe-64" placeholder="Search for a product or brand">
      <button type="submit" class="w-48 h-48 bg-main-600 rounded-circle flex-center text-xl text-white position-absolute top-50 translate-middle-y inset-inline-end-0 me-8">
        <i class="ph ph-magnifying-glass"></i>
      </button>
    </div>
  </div>
 </form>
<!-- ==================== Search Box End Here ==================== -->

        @include('layouts.header')

        <main class="flex-grow-1 site-main">
            @yield('main')
        </main>

        @include('layouts.footer')
        
    
    <!-- Jquery js -->
    <script src="/assets/js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle Js -->
    <script src="/assets/js/boostrap.bundle.min.js"></script>
    <!-- Bootstrap Bundle Js -->
    <script src="/assets/js/phosphor-icon.js"></script>
    <!-- Select 2 -->
    <script src="/assets/js/select2.min.js"></script>
    <!-- Slick js -->
    <script src="/assets/js/slick.min.js"></script>
    <!-- count down js -->
    <script src="/assets/js/count-down.js"></script>
    <!-- jquery UI js -->
    <script src="/assets/js/jquery-ui.js"></script>
    <!-- wow js -->
    <script src="/assets/js/wow.min.js"></script>
    <!-- AOS Animation -->
    <script src="/assets/js/aos.js"></script>
    <!-- marque -->
    <script src="/assets/js/marque.min.js"></script>
    <!-- marque -->
    <script src="/assets/js/vanilla-tilt.min.js"></script>
    <!-- Counter -->
    <script src="/assets/js/counter.min.js"></script>
    <!-- main js -->
    <script src="/assets/js/main.js?v={{ @filemtime(public_path('assets/js/main.js')) }}"></script>

<!-- <script>
window.addEventListener('DOMContentLoaded', function () {
   
        window.location.href = "/select-country";
    
});
</script> -->

<!-- Toastr & SweetAlert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.palMainColor = (getComputedStyle(document.documentElement).getPropertyValue('--main-600') || '').trim() || '#1c799b';
</script>

@if(session('success'))

<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        confirmButtonColor: palMainColor,
        confirmButtonText: 'OK'
    });
</script>

@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonColor: palMainColor,
        confirmButtonText: 'OK'
    });
</script>
@endif

@if(session('info'))
<script>
    Swal.fire({
        icon: 'info',
        title: 'Notice',
        text: '{{ session('info') }}',
        confirmButtonColor: palMainColor,
        confirmButtonText: 'OK'
    });
</script>
@endif

@if($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: '{{ $errors->first() }}',
        confirmButtonColor: palMainColor,
        confirmButtonText: 'OK'
    });
</script>
@endif

@if(isset($cartCount) && $cartCount > 0)
<div id="floating-cart-bar" onclick="window.location.href='/cart'" class="floating-cart-bar">

    {{-- Left: Icon & Info --}}
    <div class="fcb-left">
        <div class="fcb-icon">
            <i class="ph-fill ph-shopping-cart"></i>
        </div>
        <div class="fcb-details">
            <span id="fcb-item-label" class="fcb-count">{{ $cartCount }} {{ Str::plural('item', $cartCount) }} in cart</span>
            <div class="fcb-title">
                <span>Subtotal: <strong id="fcb-subtotal" style="color: #047857;">{{ $currencySymbol ?? '₹' }}{{ number_format($cartSubtotal, 0) }}</strong></span>
            </div>
        </div>
    </div>

    {{-- Right: CTA Button --}}
    <div class="fcb-btn">
        <span>Go to cart</span>
        <i class="ph-bold ph-arrow-right"></i>
        <span id="fcb-badge" class="fcb-badge">{{ $cartCount }}</span>
    </div>

</div>

<style>
@keyframes fcbSlideUp {
    from { opacity: 0; transform: translateX(-50%) translateY(20px); }
    to   { opacity: 1; transform: translateX(-50%) translateY(0); }
}
.floating-cart-bar {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    background: #faf2aa;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 100px;
    padding: 7px 8px 7px 16px;
    width: calc(100% - 32px);
    max-width: 440px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.12), 0 1px 4px rgba(0,0,0,0.06);
    cursor: pointer;
    animation: fcbSlideUp 0.35s cubic-bezier(0.34,1.56,0.64,1) both;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    box-sizing: border-box;
}
.floating-cart-bar:hover {
    box-shadow: 0 10px 32px rgba(0,0,0,0.16);
    transform: translateX(-50%) translateY(-2px);
}
.fcb-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    flex: 1 1 auto;
}
.fcb-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #dcfce7;
    color: #16a34a;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
}
.fcb-details {
    min-width: 0;
    flex: 1;
    overflow: hidden;
    line-height: 1.25;
}
.fcb-count {
    font-size: 11px;
    color: #4b5563;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.fcb-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.fcb-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #16a34a;
    color: #ffffff;
    font-size: 13px;
    font-weight: 600;
    border-radius: 100px;
    padding: 8px 14px;
    white-space: nowrap;
    flex-shrink: 0;
}
.fcb-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    background: rgba(255,255,255,0.28);
    border-radius: 50%;
    font-size: 11px;
    font-weight: 700;
}
@media (max-width: 575px) {
    .floating-cart-bar {
        bottom: 16px;
        padding: 6px 6px 6px 12px;
        gap: 8px;
    }
    .fcb-btn {
        padding: 7px 11px;
        font-size: 12px;
    }
}
@media (max-width: 360px) {
    .fcb-count {
        display: none;
    }
    .fcb-title {
        font-size: 12px;
    }
    .fcb-btn {
        padding: 6px 8px;
        font-size: 11px;
    }
}
/* Ensure the progress-wrap (scroll-to-top button) lifts above floating cart bar */
body:has(#floating-cart-bar) .progress-wrap {
    bottom: 84px !important;
}
</style>
@endif

<script>
function updateCartCount(delta) {
    const badges   = document.querySelectorAll('#cart-count, #cart-count-mobile, .js-cart-count-badge, [data-cart-count]');
    const headerEl = document.getElementById('cart-count');
    const mobileEl = document.getElementById('cart-count-mobile');
    const bar      = document.getElementById('floating-cart-bar');
    const badge    = document.getElementById('fcb-badge');
    const label    = document.getElementById('fcb-item-label');

    let current = Math.max(0, parseInt(headerEl?.textContent || mobileEl?.textContent || 0) + delta);
    badges.forEach(el => el.textContent = current);
    if (badge)    badge.textContent = current;
    if (label)    label.textContent = current + (current > 1 ? ' items' : ' item') + ' in cart';

    if (bar) bar.style.display = current <= 0 ? 'none' : 'flex';

    // Refresh subtotal from server
    if (typeof refreshCartSubtotal === 'function') {
        refreshCartSubtotal();
    }

    // Dispatch global event for other components
    window.dispatchEvent(new CustomEvent('cart:updated', { detail: { count: current } }));
}

function refreshCartSubtotal() {
    fetch('/cart/subtotal', {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    })
    .then(r => r.json())
    .then(data => {
        const el = document.getElementById('fcb-subtotal');
        const curr = '{{ $currencySymbol ?? "₹" }}'; if (el) el.textContent = curr + Number(data.subtotal).toLocaleString();
    });
}

// Call this after cart.store to also update subtotal display
function updateFloatingSubtotal(amount) {
    const el = document.getElementById('fcb-subtotal');
    const curr = '{{ $currencySymbol ?? "₹" }}'; if (el) el.textContent = curr + amount.toLocaleString();
}

</script>

    @stack('scripts')

    </body>
</html>