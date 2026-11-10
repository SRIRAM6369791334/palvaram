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

        .btn-outline-neutral-300:disabled, .btn-outline-neutral-300[disabled] {
            color: #9ca3af !important;
            background-color: #f3f4f6 !important;
            border: 1px solid #e5e7eb !important;
            opacity: 0.8 !important;
            cursor: not-allowed !important;
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

        @yield('main')

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
    <script src="/assets/js/main.js"></script>

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

@auth
<!-- @if(isset($cartCount) && $cartCount > 0) -->
<div id="floating-cart-bar" onclick="window.location.href='/cart'" style="
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 0;
    background: #faf2aa;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 100px;
    padding: 6px 6px 6px 20px;
    min-width: 360px;
    max-width: 500px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.10), 0 1px 4px rgba(0,0,0,0.06);
    cursor: pointer;
    animation: fcbSlideUp 0.35s cubic-bezier(0.34,1.56,0.64,1) both;
">

    {{-- Icon --}}
    <div style="width:36px;height:36px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="ph-fill ph-shopping-cart" style="color:#16a34a;font-size:16px;"></i>
    </div>

    {{-- Label --}}
    <div style="margin-left:10px;flex:1;min-width:0;">
        <div id="fcb-item-label" style="font-size:11px;color:#000;line-height:1.2;">{{ $cartCount }} item{{ $cartCount > 1 ? 's' : '' }} in cart</div>
        <div style="font-size:14px;font-weight:600;color:#111827;line-height:1.3;">Your cart is ready</div>
    </div>

    {{-- Divider --}}
    <div style="width:1px;height:28px;background:rgba(0,0,0,0.08);margin:0 14px;flex-shrink:0;"></div>

    {{-- Subtotal --}}
    <div style="flex-shrink:0;text-align:right;">
        <div style="font-size:11px;color:#000;line-height:1.2;">subtotal</div>
        <div id="fcb-subtotal" style="font-size:14px;font-weight:600;color:#111827;line-height:1.3;">
            {{ $currencySymbol ?? '₹' }}{{ number_format($cartSubtotal, 0) }}
        </div>
    </div>

    {{-- CTA Button --}}
    <div style="display:flex;align-items:center;gap:6px;background:#16a34a;color:#fff;font-size:13px;font-weight:600;border-radius:100px;padding:10px 16px;margin-left:12px;white-space:nowrap;flex-shrink:0;">
        <i class="ph ph-arrow-right" style="font-size:14px;"></i>
        Go to cart
        <span id="fcb-badge" style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:rgba(255,255,255,0.25);border-radius:50%;font-size:11px;font-weight:700;">{{ $cartCount }}</span>
    </div>

</div>

<style>
@keyframes fcbSlideUp {
    from { opacity:0; transform:translateX(-50%) translateY(20px); }
    to   { opacity:1; transform:translateX(-50%) translateY(0); }
}
#floating-cart-bar:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.14), 0 2px 8px rgba(0,0,0,0.08);
    transform: translateX(-50%) translateY(-2px);
    transition: all 0.2s ease;
}
@media (max-width: 480px) {
    #floating-cart-bar {
        min-width: calc(100vw - 32px) !important;
        bottom: 16px !important;
        padding: 6px 6px 6px 14px !important;
    }
}
</style>
@endif
<!-- @endauth -->

<script>
function updateCartCount(delta) {
    const headerEl = document.getElementById('cart-count');
    const bar      = document.getElementById('floating-cart-bar');
    const badge    = document.getElementById('fcb-badge');
    const label    = document.getElementById('fcb-item-label');

    let current = parseInt(headerEl?.textContent || 0) + delta;
    if (headerEl) headerEl.textContent = current;
    if (badge)    badge.textContent = current;
    if (label)    label.textContent = current + (current > 1 ? ' items' : ' item') + ' in cart';

    if (bar) bar.style.display = current <= 0 ? 'none' : 'flex';

    // Refresh subtotal from server
    refreshCartSubtotal();
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