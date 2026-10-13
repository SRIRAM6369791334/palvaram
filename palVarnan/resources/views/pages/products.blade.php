@extends('layouts.app')

@section('main')

<style>
/* ==========================================================================
   Pal Varnan — Products Listing
   Theme-token driven (matches the home page palette) · mobile-first · a11y
   ========================================================================== */

:root {
    --pp-radius: 16px;
    --pp-radius-sm: 12px;
    --pp-line: #e9edf2;
    --pp-ink: #0f172a;
    --pp-shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
    --pp-shadow-md: 0 16px 32px -12px hsl(var(--main) / .28), 0 4px 12px -4px rgba(15, 23, 42, .08);
}

/* ============================ Breadcrumb ============================ */
.products-breadcrumb {
    background: linear-gradient(180deg, var(--main-50), #ffffff);
    border-bottom: 1px solid var(--pp-line);
}

/* ============================ Sidebar ============================ */
.shop-sidebar {
    scrollbar-width: thin;
}

.shop-sidebar::-webkit-scrollbar {
    width: 6px;
}

.shop-sidebar::-webkit-scrollbar-thumb {
    background: var(--main-200);
    border-radius: 999px;
}

@media (min-width: 992px) {
    .shop-sidebar {
        position: sticky;
        top: 92px;
    }
}

.shop-sidebar__box {
    background: #ffffff;
    border: 1px solid var(--pp-line);
    border-radius: var(--pp-radius);
    padding: 20px;
    box-shadow: var(--pp-shadow-sm);
}

.sidebar-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding-bottom: 14px;
    margin-bottom: 14px;
    border-bottom: 1px solid var(--pp-line);
}

/* ============================ Unified Filter Rows ============================ */
.filter-item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px 8px 12px;
    border-radius: 10px;
    background: transparent;
    transition: background-color .18s ease, color .18s ease, box-shadow .18s ease;
    gap: 8px;
    position: relative;
}

.filter-item-row:hover {
    background: var(--main-50);
}

.filter-item-row.active {
    background: var(--main-50);
    box-shadow: inset 3px 0 0 0 var(--main-600);
}

.filter-item-link {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #334155;
    font-size: 13.5px;
    font-weight: 500;
    text-decoration: none;
    flex-grow: 1;
    min-width: 0;
    transition: color .15s ease;
}

.filter-item-link span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.filter-item-row:hover .filter-item-link {
    color: var(--main-700);
}

.filter-item-row.active .filter-item-link {
    color: var(--main-700);
    font-weight: 700;
}

.filter-item-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

.filter-count-badge {
    min-width: 26px;
    height: 22px;
    padding: 0 6px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    color: #475569;
    transition: all .15s ease;
}

.filter-item-row.active .filter-count-badge {
    background: var(--main-600);
    color: #ffffff;
}

.filter-item-row:hover .filter-count-badge {
    background: #e2e8f0;
    color: #1e293b;
}

.filter-item-row.active:hover .filter-count-badge {
    background: var(--main-600);
    color: #ffffff;
}

.category-toggle-btn {
    width: 22px;
    height: 22px;
    padding: 0;
    border: none;
    background: transparent;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    cursor: pointer;
    transition: all .15s ease;
    flex-shrink: 0;
}

.category-toggle-btn:hover {
    background: rgba(2, 105, 102, 0.14);
    color: var(--main-700);
}

.category-toggle-btn i {
    font-size: 13px;
    transition: transform .2s ease;
}

.category-toggle-spacer {
    width: 22px;
    height: 22px;
    flex-shrink: 0;
    display: inline-block;
}

.subcategory-list {
    list-style: none;
    margin: 4px 0 6px 18px;
    padding: 2px 0 2px 10px;
    border-left: 2px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.subcategory-link {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12.5px;
    color: #64748b;
    text-decoration: none;
    transition: all .15s ease;
}

.subcategory-link:hover {
    color: var(--main-700);
    background: var(--main-50);
}

.subcategory-link.active {
    color: var(--main-700);
    font-weight: 700;
    background: var(--main-50);
}

.sidebar-fresh-icon {
    width: 34px;
    height: 34px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: var(--main-600);
    color: #fff;
    font-size: 16px;
}

/* Mobile: turn the sidebar into a padded off-canvas drawer */
@media (max-width: 991px) {
    .shop-sidebar {
        width: min(330px, 88vw);
        padding: 16px;
        padding-top: 56px;
        box-shadow: 12px 0 40px -16px rgba(15, 23, 42, .35);
    }

    .shop-sidebar__box {
        padding: 16px;
        margin-bottom: 16px !important;
        box-shadow: none;
    }

    .shop-sidebar-col {
        margin-top: 0 !important;
    }
}

/* ============================ Active Filter Chips ============================ */
.filter-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    background: var(--main-50);
    color: var(--main-700);
    border: 1px solid hsl(var(--main) / .22);
    text-decoration: none;
    transition: background-color .15s ease, color .15s ease, border-color .15s ease;
}

.filter-chip:hover {
    background: var(--main-100);
    color: var(--main-800);
    border-color: hsl(var(--main) / .35);
}

/* ============================ Toolbar ============================ */
.shop-toolbar .toolbar-count strong {
    color: var(--pp-ink);
}

@media (max-width: 991px) {
    .shop-toolbar {
        position: sticky;
        top: 0;
        z-index: 6;
        background: rgba(255, 255, 255, .95);
        backdrop-filter: blur(8px);
    }
}

/* ============================ Product Card ============================ */
.product-card {
    height: 100% !important;
    width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    align-items: stretch !important;
    background: #ffffff;
    border: 1px solid var(--pp-line);
    border-radius: var(--pp-radius);
    padding: 14px;
    position: relative;
    transition: transform .25s cubic-bezier(.16, 1, .3, 1), box-shadow .25s ease, border-color .25s ease;
}

.product-card:focus-within {
    border-color: var(--main-600);
    box-shadow: var(--pp-shadow-md);
    z-index: 30;
}

@media (hover: hover) and (pointer: fine) {
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 28px -8px rgba(2, 105, 102, 0.15), 0 4px 10px -2px rgba(0, 0, 0, 0.04);
        border-color: rgba(2, 105, 102, 0.35);
    }

    .product-card:hover .product-card__thumb img {
        transform: scale(1.06);
    }
}

.product-card__thumb {
    height: 185px;
    width: 100%;
    background: #f8fafc;
    border-radius: var(--pp-radius-sm);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px;
    position: relative;
    margin-bottom: 12px;
}

.product-card__thumb img {
    max-height: 155px;
    max-width: 90% !important;
    object-fit: contain;
    transition: transform .45s cubic-bezier(.16, 1, .3, 1);
}

.product-card__badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 3;
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 700;
    line-height: 1;
    letter-spacing: .02em;
    color: #fff;
    background: #dc2626;
    border-radius: 8px;
    box-shadow: 0 4px 10px -4px rgba(220, 38, 38, .55);
}

.product-card__content {
    width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    align-items: center !important;
    text-align: center !important;
}

.product-card__cat {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--main-600);
    margin-bottom: 0;
}

.product-card__title {
    min-height: 44px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    font-size: 15px;
    line-height: 1.4;
    margin-bottom: 8px;
    text-align: center;
}

.product-card__title a {
    color: var(--pp-ink);
    text-decoration: none;
    transition: color .15s ease;
}

.product-card__title a:hover {
    color: var(--main-600);
}

/* Wishlist Pill Button */
.wishlist-btn-pill {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 3;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid var(--pp-line);
    box-shadow: 0 2px 6px rgba(15, 23, 42, .08);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    padding: 0;
    transition: transform .18s ease, background-color .18s ease, border-color .18s ease;
}

.wishlist-btn-pill:hover {
    background: #fff1f2;
    border-color: #fecdd3;
    transform: scale(1.08);
}

/* Variant Slot and Placeholder */
.variant-slot {
    min-height: 42px;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-bottom: 10px;
    width: 100% !important;
}

.variant-placeholder-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 5px 12px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    margin: 0 auto;
}

.product-card__price {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
    margin-bottom: 10px;
    text-align: center !important;
    width: 100% !important;
}

/* Custom Variant Dropdown */
.variant-dropdown-wrap {
    position: relative;
    width: 100%;
}

.custom-variant-select {
    position: relative;
    user-select: none;
    width: 100%;
}

.variant-selected-display {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    min-height: 40px;
    gap: 8px;
    padding: 7px 12px;
    border: 1.5px solid var(--pp-line);
    border-radius: 10px;
    cursor: pointer;
    background: #f8fafc;
    font-size: 13px;
    transition: border-color .18s ease, background-color .18s ease, box-shadow .18s ease;
}

.variant-selected-display:hover {
    border-color: var(--main-300);
    background: var(--main-50);
}

.custom-variant-select.open .variant-selected-display {
    border-color: var(--main-600);
    background: var(--main-50);
    box-shadow: 0 0 0 3px hsl(var(--main) / .12);
}

.custom-variant-select.open {
    z-index: 40;
}

/* Add To Cart & Quantity Box */
.product-card__cart {
    background: #e6f5f4 !important;
    color: #026966 !important;
    border: 1px solid rgba(2, 105, 102, 0.2) !important;
    font-weight: 600 !important;
    font-size: 13.5px !important;
    padding: 10px 16px !important;
    white-space: nowrap !important;
    border-radius: 50rem !important;
    transition: all 0.2s ease;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    text-decoration: none !important;
    width: 100% !important;
}

.product-card__cart:hover {
    background: #026966 !important;
    color: #ffffff !important;
    border-color: #026966 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(2, 105, 102, 0.25);
}

.qty-box {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    border-radius: 50rem !important;
    background: #e6f5f4 !important;
    border: 1.5px solid #026966 !important;
    padding: 3px 6px !important;
    height: 42px !important;
    transition: all 0.2s ease;
    width: 100% !important;
}

.qty-box .quantity__minus,
.qty-box .quantity__plus {
    width: 32px !important;
    height: 32px !important;
    border-radius: 50% !important;
    border: none !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 16px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.qty-box .quantity__minus {
    background: #ffffff !important;
    color: #026966 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
}

.qty-box .quantity__minus:hover {
    background: #fecdd3 !important;
    color: #dc2626 !important;
}

.qty-box .quantity__plus {
    background: #026966 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(2, 105, 102, 0.25) !important;
}

.qty-box .quantity__plus:hover {
    background: #014e4b !important;
    transform: scale(1.05);
}

.qty-box .quantity__input {
    width: 26px !important;
    border: none !important;
    background: transparent !important;
    text-align: center !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    color: #026966 !important;
    padding: 0 !important;
}

.qty-box-label {
    font-size: 11.5px;
    font-weight: 700;
    color: #026966;
    display: flex;
    align-items: center;
    gap: 4px;
}

.variant-label {
    flex: 1 1 auto;
    min-width: 0;
    font-weight: 600;
    color: #1e293b;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.variant-price-tag {
    flex: 0 0 auto;
    font-weight: 700;
    color: var(--main-700);
    font-size: 12.5px;
    background: var(--main-50);
    padding: 2px 8px;
    border-radius: 6px;
}

.variant-arrow {
    flex: 0 0 auto;
    font-size: 13px;
    color: #64748b;
    transition: transform .2s;
}

.custom-variant-select.open .variant-arrow {
    transform: rotate(180deg);
}

.variant-options-list {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1.5px solid var(--pp-line);
    border-radius: 12px;
    box-shadow: 0 18px 40px -12px rgba(15, 23, 42, .22), 0 4px 10px -4px rgba(15, 23, 42, .08);
    z-index: 1050;
    list-style: none;
    margin: 0;
    padding: 6px;
    max-height: 240px;
    overflow-y: auto;
    width: 100%;
}

.custom-variant-select.open .variant-options-list {
    display: block;
}

.variant-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 9px 10px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    transition: background-color .15s ease, color .15s ease;
}

.variant-option:hover,
.variant-option.selected {
    background: var(--main-50);
    color: var(--main-700);
    font-weight: 600;
}

.variant-option .opt-size {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.variant-option .opt-price {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: baseline;
    gap: 6px;
}

.variant-option .opt-mrp {
    text-decoration: line-through;
    color: #94a3b8;
    font-size: 11px;
}

.variant-option .opt-offer {
    color: var(--main-700);
    font-weight: 700;
}

/* ============================ Focus visibility ============================ */
.filter-item-link:focus-visible,
.category-toggle-btn:focus-visible,
.subcategory-link:focus-visible,
.filter-chip:focus-visible,
.variant-selected-display:focus-visible,
.variant-option:focus-visible,
.wishlist-btn-pill:focus-visible,
.product-card__cart:focus-visible,
.btn:focus-visible {
    outline: 2px solid var(--main-600);
    outline-offset: 2px;
}

/* ============================ Small-screen tuning ============================ */
@media (max-width: 575px) {
    .product-card {
        padding: 10px;
        border-radius: 14px;
    }

    .product-card__thumb {
        padding: 8px;
        border-radius: 10px;
    }

    .product-card__title {
        font-size: .9375rem;
        min-height: 2.5em;
    }

    .variant-selected-display {
        padding: 6px 9px;
        font-size: 12px;
        gap: 6px;
    }

    .product-card__cart {
        font-size: 12.5px;
        padding: 9px 12px;
    }

    .wishlist-btn-pill {
        width: 34px;
        height: 34px;
    }

    .qty-box .quantity__minus,
    .qty-box .quantity__plus {
        width: 34px;
        height: 34px;
    }
}

/* ============================ Reduced motion ============================ */
@media (prefers-reduced-motion: reduce) {
    .product-card,
    .product-card__thumb img,
    .wishlist-btn-pill,
    .variant-arrow,
    .variant-selected-display {
        transition: none !important;
    }

    .product-card:hover {
        transform: none;
    }
}

/* ============================ Custom High-End Pagination ============================ */
.custom-pagination {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 8px;
    padding: 0;
    margin: 0;
    list-style: none;
}

.custom-pagination .page-item {
    display: inline-flex;
}

.custom-pagination .page-link {
    width: 44px;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    color: var(--neutral-700, #334155);
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

.custom-pagination .page-link:hover {
    color: var(--main-600, #026966);
    background: var(--main-50, #e6f5f4);
    border-color: rgba(2, 105, 102, 0.35);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(2, 105, 102, 0.12);
}

.custom-pagination .page-item.active .page-link {
    background: var(--main-600, #026966) !important;
    border-color: var(--main-600, #026966) !important;
    color: #ffffff !important;
    font-weight: 700;
    box-shadow: 0 4px 14px -2px rgba(2, 105, 102, 0.4) !important;
    transform: translateY(-1px);
}

.custom-pagination .page-item.disabled .page-link {
    background: #f8fafc;
    border-color: #f1f5f9;
    color: #cbd5e1;
    cursor: not-allowed;
    pointer-events: none;
    box-shadow: none;
    transform: none;
}

.custom-pagination .page-link.dots {
    border: none;
    background: transparent;
    cursor: default;
    box-shadow: none;
    width: auto;
    padding: 0 6px;
    color: #94a3b8;
}

@media (max-width: 575px) {
    .custom-pagination .page-link {
        width: 38px;
        height: 38px;
        font-size: 13px;
        border-radius: 10px;
    }
}
</style>

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb products-breadcrumb mb-0 py-24 bg-main-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <div>
                <h6 class="mb-2 text-heading fw-bold">All Products</h6>
                <span class="text-neutral-500 text-xs">Fresh groceries, farm staples &amp; export specialties</span>
            </div>
            <ul class="flex-align gap-8 flex-wrap list-unstyled mb-0">
                <li class="text-sm">
                    <a href="{{ url('/') }}" class="text-gray-900 flex-align gap-8 hover-text-main-600 text-decoration-none">
                        <i class="ph ph-house"></i>
                        Home
                    </a>
                </li>
                <li class="flex-align text-neutral-400">
                    <i class="ph ph-caret-right"></i>
                </li>
                <li class="text-sm text-main-600 fw-semibold"> Products </li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

<!-- =============================== Shop Section Start ======================================== -->
<section class="shop py-60">
    <div class="container container-lg">
        <div class="row gy-4">

            <!-- Sidebar Start -->
            <div class="col-lg-3 shop-sidebar-col">
                <div class="shop-sidebar">

                    <!-- Mobile Drawer Close Button -->
                    <button type="button"
                        class="shop-sidebar__close d-lg-none d-flex w-36 h-36 flex-center border border-neutral-200 rounded-circle hover-bg-main-600 position-absolute inset-inline-end-0 me-12 mt-8 hover-text-white transition-1"
                        aria-label="Close filters">
                        <i class="ph-bold ph-x text-lg"></i>
                    </button>

                    <!-- CATEGORY BOX -->
                    <div class="shop-sidebar__box mb-24">
                        <div class="sidebar-heading">
                            <h6 class="text-heading fw-bold text-base mb-0 d-flex align-items-center gap-8">
                                <i class="ph-bold ph-squares-four text-main-600 text-lg"></i>
                                Product Categories
                            </h6>
                            @if(request('category') || request('subcategory'))
                                <a href="{{ url('/products') }}?{{ http_build_query(request()->except(['category', 'subcategory', 'page'])) }}" 
                                   class="text-xs text-neutral-500 hover-text-main-600 text-decoration-none">Reset</a>
                            @endif
                        </div>

                        <div class="category-sidebar d-flex flex-column gap-6">

                            <!-- All Categories Option -->
                            <div class="filter-item-row {{ !request('category') && !request('subcategory') ? 'active' : '' }}">
                                <a href="{{ url('/products') }}?{{ http_build_query(request()->except(['category', 'subcategory', 'page'])) }}"
                                   class="filter-item-link">
                                    <i class="ph-bold ph-squares-four text-sm text-main-600"></i>
                                    <span>All Products</span>
                                </a>
                                <div class="filter-item-meta">
                                    <span class="filter-count-badge">
                                        {{ $products->total() }}
                                    </span>
                                    <span class="category-toggle-spacer" aria-hidden="true"></span>
                                </div>
                            </div>

                            @foreach($categories as $category)
                                @php
                                    $subs = $subcategories->where('category_name', $category->id);
                                    $isCatActive = request('category') == $category->id;
                                    $hasActiveSub = $subs->contains('id', request('subcategory'));
                                    $isOpen = $isCatActive || $hasActiveSub;
                                @endphp

                                <div class="category-item">
                                    <div class="filter-item-row {{ $isCatActive ? 'active' : '' }}">
                                        <a href="/products?category={{ $category->id }}{{ request('sort') ? '&sort=' . request('sort') : '' }}"
                                           class="filter-item-link">
                                            <i class="ph-bold ph-tag text-xs {{ $isCatActive ? 'text-main-600' : 'text-neutral-400' }}"></i>
                                            <span>{{ $category->category_name }}</span>
                                        </a>

                                        <div class="filter-item-meta">
                                            <span class="filter-count-badge">
                                                {{ $category->products_count ?? 0 }}
                                            </span>

                                            @if($subs->count() > 0)
                                                <button type="button" class="category-toggle-btn"
                                                    data-target="cat{{ $category->id }}"
                                                    aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                                                    aria-label="Toggle {{ $category->category_name }} subcategories">
                                                    <i class="ph-bold {{ $isOpen ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                                                </button>
                                            @else
                                                <span class="category-toggle-spacer" aria-hidden="true"></span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($subs->count() > 0)
                                        <ul id="cat{{ $category->id }}"
                                            class="subcategory-list {{ $isOpen ? '' : 'd-none' }}">
                                            @foreach($subs as $sub)
                                                @php $isSubActive = request('subcategory') == $sub->id; @endphp
                                                <li>
                                                    <a href="/products?category={{ $category->id }}&subcategory={{ $sub->id }}{{ request('sort') ? '&sort=' . request('sort') : '' }}"
                                                       class="subcategory-link {{ $isSubActive ? 'active' : '' }}">
                                                        <i class="ph-bold ph-caret-right text-xs {{ $isSubActive ? 'text-main-600' : 'text-neutral-400' }}"></i>
                                                        <span>{{ $sub->subcategory_name }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <!-- BRANDS BOX -->
                    <div class="shop-sidebar__box mb-24">
                        <div class="sidebar-heading">
                            <h6 class="text-heading fw-bold text-base mb-0 d-flex align-items-center gap-8">
                                <i class="ph-bold ph-shield-check text-main-600 text-lg"></i>
                                Brands
                            </h6>
                            @if(request('brand'))
                                <a href="{{ url('/products') }}?{{ http_build_query(request()->except(['brand', 'page'])) }}" 
                                   class="text-xs text-neutral-500 hover-text-main-600 text-decoration-none">Reset</a>
                            @endif
                        </div>

                        <div class="category-sidebar d-flex flex-column gap-6">
                            @foreach($brands as $brand)
                                @php $isBrandActive = request('brand') == $brand->id; @endphp
                                <div class="filter-item-row {{ $isBrandActive ? 'active' : '' }}">
                                    <a href="{{ $isBrandActive ? url('/products') . '?' . http_build_query(request()->except(['brand', 'page'])) : url('/products') . '?' . http_build_query(array_merge(request()->except('page'), ['brand' => $brand->id])) }}"
                                       class="filter-item-link">
                                        <i class="ph{{ $isBrandActive ? '-fill ph-check-circle text-main-600' : ' ph-circle text-neutral-300' }}"></i>
                                        <span>{{ $brand->brand_name }}</span>
                                    </a>
                                    <div class="filter-item-meta">
                                        <span class="filter-count-badge">
                                            {{ $brand->products_count ?? 0 }}
                                        </span>
                                        <span class="category-toggle-spacer" aria-hidden="true"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- SUPPORT / FRESHNESS CARD -->
                    <div class="p-20 rounded-20 bg-main-50 border border-main-200">
                        <div class="d-flex align-items-center gap-10 mb-8">
                            <span class="sidebar-fresh-icon"><i class="ph-bold ph-plant"></i></span>
                            <h6 class="text-heading fw-bold text-sm mb-0">100% Farm Fresh</h6>
                        </div>
                        <p class="text-neutral-600 text-xs mb-14 line-height-1-6">
                            Direct sourcing from Kanyakumari farmers and trusted organic growers straight to your kitchen.
                        </p>
                        <a href="tel:+918778509639" class="text-main-600 text-xs fw-bold d-inline-flex align-items-center gap-6 text-decoration-none">
                            <i class="ph-bold ph-phone-call"></i>
                            <span>Helpline: +91 8778509639</span>
                        </a>
                    </div>

                    <!-- Hidden Price Slider Elements to maintain main.js compatibility -->
                    <div style="display: none;">
                        <div id="slider-range"></div>
                        <input type="text" id="amount" readonly>
                    </div>

                </div>
            </div>
            <!-- Sidebar End -->

            <!-- Content Start -->
            <div class="col-lg-9">

                <!-- Top Filter / Results Bar -->
                <div class="shop-toolbar bg-white border border-neutral-100 rounded-16 p-16 p-sm-20 mb-32 d-flex align-items-center justify-content-between flex-wrap gap-16 shadow-xs">
                    <div class="d-flex align-items-center flex-wrap gap-12">
                        <span class="toolbar-count text-neutral-600 text-sm">
                            Showing <strong class="text-heading">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</strong> of <strong class="text-heading">{{ $products->total() }}</strong> products
                        </span>

                        <!-- Active Filter Pills -->
                        @php
                            $activeCat = request('category') ? $categories->firstWhere('id', request('category')) : null;
                            $activeSub = request('subcategory') ? $subcategories->firstWhere('id', request('subcategory')) : null;
                            $activeBrand = request('brand') ? $brands->firstWhere('id', request('brand')) : null;
                        @endphp

                        @if($activeCat || $activeSub || $activeBrand)
                            <div class="d-flex align-items-center flex-wrap gap-8 ps-sm-12 border-start-sm border-neutral-200">
                                @if($activeCat)
                                    <a href="{{ url('/products') }}?{{ http_build_query(request()->except(['category', 'subcategory', 'page'])) }}" 
                                       class="filter-chip" title="Clear Category filter">
                                        <span>{{ $activeCat->category_name }}</span>
                                        <i class="ph-bold ph-x text-xs"></i>
                                    </a>
                                @endif

                                @if($activeSub)
                                    <a href="{{ url('/products') }}?{{ http_build_query(request()->except(['subcategory', 'page'])) }}" 
                                       class="filter-chip" title="Clear Subcategory filter">
                                        <span>{{ $activeSub->subcategory_name }}</span>
                                        <i class="ph-bold ph-x text-xs"></i>
                                    </a>
                                @endif

                                @if($activeBrand)
                                    <a href="{{ url('/products') }}?{{ http_build_query(request()->except(['brand', 'page'])) }}" 
                                       class="filter-chip" title="Clear Brand filter">
                                        <span>{{ $activeBrand->brand_name }}</span>
                                        <i class="ph-bold ph-x text-xs"></i>
                                    </a>
                                @endif

                                <a href="{{ url('/products') }}" class="text-xs text-danger-600 hover-text-danger-800 fw-semibold text-decoration-none">
                                    Clear All
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Right Actions: Sort by, Show Count Selector & Mobile Filter -->
                    <div class="d-flex align-items-center flex-wrap gap-12 ms-auto">
                        <!-- Sort by Dropdown -->
                        <div class="d-flex align-items-center gap-6">
                            <label for="sortSelect" class="text-xs text-neutral-500 fw-semibold d-none d-sm-inline mb-0 flex-shrink-0">Sort by:</label>
                            <select id="sortSelect"
                                    class="form-select form-select-sm rounded-10 text-xs fw-semibold border-neutral-200 py-6 px-12"
                                    style="cursor: pointer; min-width: 135px; background-color: #fff;"
                                    onchange="window.location.href=this.value"
                                    aria-label="Sort products">
                                <option value="{{ url('/products') }}?{{ http_build_query(array_merge(request()->except(['sort', 'page']), ['sort' => 'latest'])) }}" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Latest Arrivals</option>
                                <option value="{{ url('/products') }}?{{ http_build_query(array_merge(request()->except(['sort', 'page']), ['sort' => 'price_low_high'])) }}" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="{{ url('/products') }}?{{ http_build_query(array_merge(request()->except(['sort', 'page']), ['sort' => 'price_high_low'])) }}" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="{{ url('/products') }}?{{ http_build_query(array_merge(request()->except(['sort', 'page']), ['sort' => 'name_asc'])) }}" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                <option value="{{ url('/products') }}?{{ http_build_query(array_merge(request()->except(['sort', 'page']), ['sort' => 'name_desc'])) }}" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                            </select>
                        </div>

                        <!-- Show count -->
                        <div class="d-none d-md-flex align-items-center gap-6 text-xs text-neutral-500">
                            <span class="fw-medium">Show:</span>
                            <div class="d-flex align-items-center gap-4">
                                @foreach([6, 12, 24] as $cnt)
                                    @php $isActivePerPage = request('per_page', 6) == $cnt; @endphp
                                    <a href="{{ url('/products') }}?{{ http_build_query(array_merge(request()->except(['per_page', 'page']), ['per_page' => $cnt])) }}"
                                       class="px-10 py-4 rounded-8 fw-bold text-xs text-decoration-none transition-1 {{ $isActivePerPage ? 'bg-main-600 text-white shadow-xs' : 'bg-neutral-100 text-neutral-600 hover-bg-main-50 hover-text-main-600' }}">
                                        {{ $cnt }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Mobile Filter Drawer Trigger -->
                        <button type="button" class="btn btn-outline-main d-lg-none d-flex align-items-center gap-8 py-8 px-14 rounded-10 text-sm sidebar-btn">
                            <i class="ph-bold ph-funnel"></i>
                            <span>Filter</span>
                        </button>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row g-4">

                    @forelse($products as $product)

                        <div class="col-6 col-md-4">

                            <div class="product-card h-100 position-relative transition-2">

                                @php
                                    $variant = $product->variants->first();
                                    $variantsWithUnit = $product->variants->filter(fn($v) => !empty($v->value) && !empty($v->unit));
                                    $discountPct = ($variant && $variant->mrp_price && $variant->offer_price && $variant->mrp_price > $variant->offer_price)
                                        ? round((($variant->mrp_price - $variant->offer_price) / $variant->mrp_price) * 100)
                                        : 0;
                                @endphp

                                <span class="product-card__badge {{ $discountPct > 0 ? '' : 'd-none' }}" data-discount-badge>
                                    <i class="ph-bold ph-lightning text-xs me-1"></i>{{ $discountPct > 0 ? $discountPct . '% OFF' : '' }}
                                </span>

                                <!-- Wishlist Button -->
                                @auth
                                    <button type="button"
                                        class="wishlist-btn wishlist-btn-pill"
                                        data-product-id="{{ $product->id }}"
                                        data-variant-id="{{ $variant->id ?? '' }}"
                                        aria-label="Toggle wishlist">
                                        <i class="ph{{ in_array($product->id, $wishlistProductIds ?? []) ? '-fill' : '' }} ph-heart text-xl {{ in_array($product->id, $wishlistProductIds ?? []) ? 'text-danger-600' : 'text-neutral-400' }}"></i>
                                    </button>
                                @else
                                    <button type="button"
                                        class="wishlist-btn-pill"
                                        onclick="showLoginAlert()"
                                        aria-label="Login to add to wishlist">
                                        <i class="ph ph-heart text-xl text-neutral-400"></i>
                                    </button>
                                @endauth

                                <!-- Product Image Thumbnail -->
                                <a href="/product/{{ $product->slug }}" class="product-card__thumb text-decoration-none">
                                    <img src="{{ env('MAIN_URL') }}images/{{ $product->product_image }}"
                                        alt="{{ $product->product_name }}"
                                        onerror="this.src='/assets/images/thumbs/product-two-img1.png';">
                                </a>

                                <!-- Card Content -->
                                <div class="product-card__content mt-12 flex-grow-1 d-flex flex-column justify-content-between w-100">

                                    <div class="w-100 text-center">
                                        <div class="d-flex align-items-center justify-content-center flex-wrap gap-8 mb-6 text-center w-100">
                                            <span class="product-card__cat mb-0">
                                                {{ $product->cate_name }}
                                                @if($product->subcate_name)
                                                    <span class="text-neutral-400">/</span> {{ $product->subcate_name }}
                                                @endif
                                            </span>
                                            <span class="text-neutral-300" style="font-size: 10px;">•</span>
                                            <div class="d-inline-flex align-items-center gap-4 text-xs">
                                                <i class="ph-fill ph-star text-warning-600" style="font-size: 13px;"></i>
                                                <span class="fw-bold text-heading" style="font-size: 12px;">4.9</span>
                                                <span class="text-neutral-400" style="font-size: 11px;">(18)</span>
                                            </div>
                                        </div>

                                        <h6 class="product-card__title title text-base fw-semibold mb-8 text-line-2 text-center w-100">
                                            <a href="/product/{{ $product->slug }}" class="text-heading hover-text-main-600 text-decoration-none transition-1">
                                                {{ $product->product_name }}
                                            </a>
                                        </h6>

                                        <!-- Custom Variant Dropdown / Slot -->
                                        <div class="variant-slot w-100">
                                            @if($variantsWithUnit->count() > 0)
                                                <div class="variant-dropdown-wrap w-100">
                                                    <div class="custom-variant-select" data-product-id="{{ $product->id }}">
                                                        <div class="variant-selected-display" role="button" tabindex="0"
                                                            aria-haspopup="listbox" aria-expanded="false" aria-label="Select variant">
                                                            @php $first = $variantsWithUnit->first(); @endphp
                                                            <span class="variant-label">{{ $first->value }}{{ $first->unit->short_name ?? '' }}</span>
                                                            <span class="variant-price-tag">₹{{ $first->offer_price }}</span>
                                                            <i class="ph ph-caret-down variant-arrow" aria-hidden="true"></i>
                                                        </div>
                                                        <ul class="variant-options-list" role="listbox" aria-label="Available variants">
                                                            @foreach($variantsWithUnit as $v)
                                                                <li class="variant-option {{ $loop->first ? 'selected' : '' }}"
                                                                    role="option" tabindex="0"
                                                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                                                    data-variant-id="{{ $v->id }}"
                                                                    data-offer="{{ $v->offer_price }}"
                                                                    data-mrp="{{ $v->mrp_price }}">
                                                                    <span class="opt-size">{{ $v->value }}{{ $v->unit->short_name ?? '' }}</span>
                                                                    <span class="opt-price">
                                                                        @if($v->mrp_price)
                                                                            <span class="opt-mrp">₹{{ $v->mrp_price }}</span>
                                                                        @endif
                                                                        <span class="opt-offer">₹{{ $v->offer_price }}</span>
                                                                    </span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="variant-placeholder-pill">
                                                    <i class="ph-bold ph-package text-main-600"></i>
                                                    <span>Standard Pack</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="w-100 text-center">
                                        <!-- Price Section -->
                                        <div class="product-card__price d-flex align-items-center justify-content-center flex-wrap gap-8 my-10 text-center w-100">
                                            <div class="d-flex align-items-baseline justify-content-center gap-8">
                                                <span class="text-heading text-lg fw-bold">
                                                    ₹{{ $variant->offer_price ?? 0 }}
                                                </span>
                                                @if($variant && $variant->mrp_price && $variant->mrp_price > $variant->offer_price)
                                                    <span class="text-neutral-400 text-sm fw-medium text-decoration-line-through">
                                                        ₹{{ $variant->mrp_price }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($variant && $variant->mrp_price && $variant->mrp_price > $variant->offer_price)
                                                <span class="badge bg-success-50 text-success-700 text-xs px-6 py-2 rounded-6 fw-bold">
                                                    Save ₹{{ $variant->mrp_price - $variant->offer_price }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Cart Control Button -->
                                        @php
                                            $inCart   = $variant ? in_array($variant->id, $cartProductIds) : false;
                                            $cartItem = $variant ? $cartItems->where('product_varient_id', $variant->id)->first() : null;
                                        @endphp

                                        <div class="cart-control mt-8" data-product-id="{{ $product->id }}" data-variant-id="{{ $variant->id ?? '' }}">
                                            @if($inCart)
                                                <div class="qty-box">
                                                    <button type="button" class="quantity__minus">−</button>
                                                    <div class="d-flex align-items-center gap-4">
                                                        <span class="qty-box-label">In Cart:</span>
                                                        <input type="number" class="quantity__input" value="{{ $cartItem->product_quantity ?? 1 }}" min="1" readonly>
                                                    </div>
                                                    <button type="button" class="quantity__plus">+</button>
                                                </div>
                                            @else
                                                <button type="button"
                                                    class="add-to-cart-btn product-card__cart btn py-10 px-16 rounded-pill flex-align gap-6 w-100 justify-content-center text-sm fw-semibold">
                                                    <span>Add To Cart</span> <i class="ph-bold ph-shopping-cart-simple text-base"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="col-12">
                            <div class="empty-state text-center py-60 px-24 bg-white border border-neutral-100 rounded-24 shadow-xs">
                                <div class="w-80 h-80 rounded-circle bg-main-50 text-main-600 flex-center mx-auto mb-20 text-36">
                                    <i class="ph-bold ph-package"></i>
                                </div>
                                <h5 class="text-heading fw-bold mb-8">No Products Found</h5>
                                <p class="text-neutral-500 text-sm mb-24 max-w-440 mx-auto">
                                    We couldn't find any products matching your selected category or brand filters. Try clearing your filters to explore our full grocery catalog.
                                </p>
                                <a href="{{ url('/products') }}" class="btn btn-main py-10 px-24 rounded-12 fw-semibold d-inline-flex align-items-center gap-8 text-decoration-none">
                                    <i class="ph-bold ph-arrow-counter-clockwise"></i>
                                    <span>Reset All Filters</span>
                                </a>
                            </div>
                        </div>

                    @endforelse

                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="mt-48 mb-20 d-flex justify-content-center">
                        {{ $products->withQueryString()->links('partials.pagination') }}
                    </div>
                @endif

            </div>
            <!-- Content End -->

        </div>
    </div>
</section>
<!-- =============================== Shop Section End ======================================== -->

<!-- ========================= Trust Shipping Badges Start ========================= -->
<section class="shipping py-40 mb-40">
    <div class="container container-lg">
        <div class="row g-4">
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="300">
                <div class="shipping-item d-flex align-items-center gap-16 p-24 rounded-16 bg-main-50 hover-bg-main-100 transition-2">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-28 flex-shrink-0">
                        <i class="ph-fill ph-car-profile"></i>
                    </span>
                    <div>
                        <h6 class="mb-4 text-heading fw-bold text-sm">Reliable Delivery</h6>
                        <span class="text-neutral-500 text-xs">Safe and timely doorstep shipping</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="500">
                <div class="shipping-item d-flex align-items-center gap-16 p-24 rounded-16 bg-main-50 hover-bg-main-100 transition-2">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-28 flex-shrink-0">
                        <i class="ph-fill ph-hand-coins"></i>
                    </span>
                    <div>
                        <h6 class="mb-4 text-heading fw-bold text-sm">Best Value Pricing</h6>
                        <span class="text-neutral-500 text-xs">Direct farm savings for you</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="700">
                <div class="shipping-item d-flex align-items-center gap-16 p-24 rounded-16 bg-main-50 hover-bg-main-100 transition-2">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-28 flex-shrink-0">
                        <i class="ph-fill ph-credit-card"></i>
                    </span>
                    <div>
                        <h6 class="mb-4 text-heading fw-bold text-sm">100% Secure Payment</h6>
                        <span class="text-neutral-500 text-xs">Encrypted transactions guaranteed</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="900">
                <div class="shipping-item d-flex align-items-center gap-16 p-24 rounded-16 bg-main-50 hover-bg-main-100 transition-2">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-28 flex-shrink-0">
                        <i class="ph-fill ph-chats"></i>
                    </span>
                    <div>
                        <h6 class="mb-4 text-heading fw-bold text-sm">Friendly Support</h6>
                        <span class="text-neutral-500 text-xs">Dedicated helpline +91 8778509639</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= Trust Shipping Badges End ========================= -->

<script>
// Theme primary color (keeps toasts/alerts in sync with the site palette)
const palBrandColor = (getComputedStyle(document.documentElement).getPropertyValue('--main-600') || '').trim() || '#1C799B';

// Category accordion toggle — click + keyboard
function toggleCategoryAccordion(btn) {
    const targetId = btn.getAttribute("data-target");
    if (!targetId) return;
    const el = document.getElementById(targetId);
    if (!el) return;

    el.classList.toggle("d-none");
    const isOpen = !el.classList.contains("d-none");
    btn.setAttribute("aria-expanded", isOpen ? "true" : "false");

    const icon = btn.querySelector("i");
    if (icon) {
        if (isOpen) {
            icon.classList.remove("ph-caret-down");
            icon.classList.add("ph-caret-up");
        } else {
            icon.classList.remove("ph-caret-up");
            icon.classList.add("ph-caret-down");
        }
    }
}

document.addEventListener("click", function (e) {
    const btn = e.target.closest(".category-toggle-btn");
    if (btn) {
        e.preventDefault();
        e.stopPropagation();
        toggleCategoryAccordion(btn);
    }
});

document.addEventListener("keydown", function (e) {
    if (e.key !== "Enter" && e.key !== " ") return;
    const btn = e.target.closest(".category-toggle-btn");
    if (!btn) return;
    e.preventDefault();
    toggleCategoryAccordion(btn);
});

// Guest alert
function showLoginAlert() {
    Swal.fire({
        icon: 'warning',
        title: 'Login Required',
        text: 'Please login to manage your wishlist and cart items',
        confirmButtonColor: palBrandColor,
        confirmButtonText: 'Login Now'
    }).then((result) => {
        if (result.isConfirmed) window.location.href = "{{ route('login') }}";
    });
}

// Quantity Box Render
function showQtyBox(controlDiv, qty) {
    controlDiv.innerHTML = `
        <div class="qty-box">
            <button type="button" class="quantity__minus">−</button>
            <div class="d-flex align-items-center gap-4">
                <span class="qty-box-label">In Cart:</span>
                <input type="number" class="quantity__input" value="${qty}" min="1" readonly>
            </div>
            <button type="button" class="quantity__plus">+</button>
        </div>`;
    attachQtyEvents(controlDiv);
}

function attachQtyEvents(controlDiv) {
    const variantId = controlDiv.dataset.variantId;
    const input = controlDiv.querySelector('.quantity__input');
    if (!input) return;

    const plusBtn = controlDiv.querySelector('.quantity__plus');
    const minusBtn = controlDiv.querySelector('.quantity__minus');

    if (plusBtn) {
        plusBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const newQty = parseInt(input.value) + 1;
            syncCart(variantId, newQty, input);
        });
    }

    if (minusBtn) {
        minusBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const currentQty = parseInt(input.value);

            if (currentQty <= 1) {
                fetch("{{ route('cart.ajaxRemove') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ product_varient_id: variantId })
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        controlDiv.innerHTML = `
                            <button type="button"
                                class="add-to-cart-btn product-card__cart btn py-10 px-16 rounded-pill flex-align gap-6 w-100 justify-content-center text-sm fw-semibold">
                                <span>Add To Cart</span> <i class="ph-bold ph-shopping-cart-simple text-base"></i>
                            </button>`;
                        attachAddToCartEvent(controlDiv);
                    }
                });
            } else {
                syncCart(variantId, currentQty - 1, input);
            }
        });
    }
}

function syncCart(variantId, quantity, input) {
    fetch("{{ route('cart.ajaxUpdate') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_varient_id: variantId, quantity: quantity })
    }).then(r => r.json()).then(data => {
        if (data.success) input.value = quantity;
    });
}

function attachAddToCartEvent(controlDiv) {
    const btn = controlDiv.querySelector('.add-to-cart-btn');
    if (!btn) return;

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const productId = controlDiv.dataset.productId;
        const variantId = controlDiv.dataset.variantId;

        fetch("{{ route('cart.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: productId, product_varient_id: variantId, quantity: 1 })
        }).then(r => r.json()).then(data => {
            showQtyBox(controlDiv, 1);
            Swal.fire({
                icon: 'success', title: data.message || 'Added to cart successfully!',
                toast: true, position: 'top-end',
                showConfirmButton: false, timer: 2000, timerProgressBar: true
            });
        });
    });
}

// Initialize Cart Controls on page load
document.querySelectorAll('.cart-control').forEach(controlDiv => {
    if (controlDiv.querySelector('.qty-box')) {
        attachQtyEvents(controlDiv);
    } else {
        attachAddToCartEvent(controlDiv);
    }
});

// Custom Variant Select Dropdown Logic
function closeAllVariantSelects(except) {
    document.querySelectorAll('.custom-variant-select.open').forEach(function (s) {
        if (s === except) return;
        s.classList.remove('open');
        const d = s.querySelector('.variant-selected-display');
        if (d) d.setAttribute('aria-expanded', 'false');
    });
}

function refreshDiscountBadge(cardRoot, offerPrice, mrpPrice) {
    if (!cardRoot) return;
    const badge = cardRoot.querySelector('[data-discount-badge]');
    if (!badge) return;
    const offer = Number(offerPrice);
    const mrp = Number(mrpPrice);
    if (mrp && mrp > offer) {
        const pct = Math.round(((mrp - offer) / mrp) * 100);
        badge.innerHTML = `<i class="ph-bold ph-lightning text-xs me-1"></i>${pct}% OFF`;
        badge.classList.remove('d-none');
    } else {
        badge.innerHTML = '';
        badge.classList.add('d-none');
    }
}

function applyVariant(select, option, options) {
    const variantId  = option.dataset.variantId;
    const offerPrice = option.dataset.offer;
    const mrpPrice   = option.dataset.mrp;
    const sizeText   = option.querySelector('.opt-size').textContent;

    // Update selected display
    select.querySelector('.variant-label').textContent = sizeText;
    select.querySelector('.variant-price-tag').textContent = '₹' + offerPrice;

    // Mark selected
    options.forEach(function (o) {
        o.classList.toggle('selected', o === option);
        o.setAttribute('aria-selected', o === option ? 'true' : 'false');
    });

    // Close dropdown
    select.classList.remove('open');
    const displayEl = select.querySelector('.variant-selected-display');
    if (displayEl) displayEl.setAttribute('aria-expanded', 'false');

    // Update price in card
    const card = select.closest('.product-card__content');
    if (card) {
        const priceBox = card.querySelector('.product-card__price');
        if (priceBox) {
            const hasDiscount = mrpPrice && Number(mrpPrice) > Number(offerPrice);
            const savings = hasDiscount ? (Number(mrpPrice) - Number(offerPrice)) : 0;
            priceBox.innerHTML = `
                <div class="d-flex align-items-baseline justify-content-center gap-8">
                    <span class="text-heading text-lg fw-bold">₹${offerPrice}</span>
                    ${hasDiscount ? `<span class="text-neutral-400 text-sm fw-medium text-decoration-line-through">₹${mrpPrice}</span>` : ''}
                </div>
                ${hasDiscount ? `<span class="badge bg-success-50 text-success-700 text-xs px-6 py-2 rounded-6 fw-bold">Save ₹${savings}</span>` : ''}
            `;
        }

        const control = card.querySelector('.cart-control');
        if (control) {
            control.dataset.variantId = variantId;
            if (control.querySelector('.qty-box')) {
                control.innerHTML = `
                    <button type="button"
                        class="add-to-cart-btn product-card__cart btn py-10 px-16 rounded-pill flex-align gap-6 w-100 justify-content-center text-sm fw-semibold">
                        <span>Add To Cart</span> <i class="ph-bold ph-shopping-cart-simple text-base"></i>
                    </button>`;
                attachAddToCartEvent(control);
            }
        }
    }

    const cardRoot = select.closest('.product-card');
    refreshDiscountBadge(cardRoot, offerPrice, mrpPrice);

    if (cardRoot) {
        const wishlistBtn = cardRoot.querySelector('.wishlist-btn');
        if (wishlistBtn) {
            wishlistBtn.dataset.variantId = variantId;
            const wIcon = wishlistBtn.querySelector('i');
            if (wIcon) {
                wIcon.className = 'ph ph-heart text-xl text-neutral-400';
                fetch(`/wishlist/check?product_id=${wishlistBtn.dataset.productId}&variant_id=${variantId}`, {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(r => r.json())
                .then(data => {
                    wIcon.className = data.in_wishlist
                        ? 'ph-fill ph-heart text-xl text-danger-600'
                        : 'ph ph-heart text-xl text-neutral-400';
                })
                .catch(function () {});
            }
        }
    }
}

document.querySelectorAll('.custom-variant-select').forEach(function (select) {
    const display = select.querySelector('.variant-selected-display');
    const options = Array.from(select.querySelectorAll('.variant-option'));
    if (!display) return;

    const setOpen = function (open) {
        if (open) closeAllVariantSelects(select);
        select.classList.toggle('open', open);
        display.setAttribute('aria-expanded', open ? 'true' : 'false');
    };

    // Toggle open/close — pointer
    display.addEventListener('click', function (e) {
        e.stopPropagation();
        setOpen(!select.classList.contains('open'));
    });

    // Toggle open/close — keyboard
    display.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            setOpen(!select.classList.contains('open'));
        } else if (e.key === 'Escape') {
            setOpen(false);
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            setOpen(true);
            if (options[0]) options[0].focus();
        }
    });

    // Option select — pointer + keyboard
    options.forEach(function (option) {
        option.addEventListener('click', function () {
            applyVariant(select, option, options);
        });
        option.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                applyVariant(select, option, options);
                display.focus();
            } else if (e.key === 'Escape') {
                setOpen(false);
                display.focus();
            }
        });
    });
});

// Close open dropdowns on exterior click
document.addEventListener('click', function () {
    closeAllVariantSelects();
});

// Wishlist Toggle Logic
document.querySelectorAll('.wishlist-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const productId = this.dataset.productId;
        const variantId = this.dataset.variantId;
        const icon      = this.querySelector('i');
        const self      = this;

        if (!icon) return;

        // If already wishlisted (filled heart) -> remove directly
        if (icon.classList.contains('ph-fill')) {
            toggleWishlist(self, productId, variantId, icon);
            return;
        }

        // Check if another variant of same product is already in wishlist
        fetch(`/wishlist/check-product?product_id=${productId}&exclude_variant_id=${variantId}`, {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.has_other_variant) {
                Swal.fire({
                    icon: 'question',
                    title: 'Already in Wishlist',
                    html: `You already have <strong>${data.existing_variant}</strong> in your wishlist.<br>Do you want to add <strong>${data.new_variant}</strong> as well?`,
                    showCancelButton: true,
                    confirmButtonColor: palBrandColor,
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Add it!',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) {
                        toggleWishlist(self, productId, variantId, icon);
                    }
                });
            } else {
                toggleWishlist(self, productId, variantId, icon);
            }
        });
    });
});

function toggleWishlist(btn, productId, variantId, icon) {
    fetch("{{ route('wishlist.toggle') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ product_id: productId, variant_id: variantId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'added') {
            icon.classList.remove('ph-heart', 'text-neutral-400');
            icon.classList.add('ph-fill', 'ph-heart', 'text-danger-600');
        } else {
            icon.classList.remove('ph-fill', 'text-danger-600');
            icon.classList.add('ph-heart', 'text-neutral-400');
        }

        Swal.fire({
            icon: data.status === 'added' ? 'success' : 'info',
            title: data.status === 'added' ? 'Added to Wishlist!' : 'Removed from Wishlist',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1800,
            timerProgressBar: true
        });
    });
}
</script>

@endsection