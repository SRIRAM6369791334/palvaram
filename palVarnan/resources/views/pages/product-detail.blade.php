@extends('layouts.app')
@section('main')

<style>
/* ===================================================
   Product Details Custom Enhancements & MarketPro Alignment
   =================================================== */
.product-details {
    background-color: #ffffff;
}

/* Override default oversized clamp padding on thumb slider */
.product-details__thumb-slider {
    padding: 24px !important;
}

.product-details__thumb {
    min-height: 380px;
    height: 380px;
    display: flex !important;
    align-items: center;
    justify-content: center;
    background-color: #ffffff;
    overflow: hidden;
}

.product-details__thumb img {
    max-height: 340px;
    max-width: 100%;
    width: auto;
    object-fit: contain;
    transition: transform 0.35s ease;
}

.product-details__thumb:hover img {
    transform: scale(1.06);
}

.product-main-slider-wrap {
    background: #ffffff;
    border: 1px solid var(--gray-100, #e2e8f0);
    border-radius: 16px;
    position: relative;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.product-thumb-item {
    width: 84px;
    height: 84px;
    border-radius: 12px;
    border: 1.5px solid var(--gray-100, #e2e8f0);
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    margin: 0 4px;
}

.product-thumb-item img {
    max-height: 68px;
    max-width: 68px;
    object-fit: contain;
}

.product-thumb-item:hover,
.product-thumb-item.active,
.product-details__images-slider .slick-current .product-thumb-item {
    border-color: var(--main-600, #1c799b) !important;
    box-shadow: 0 0 0 2px rgba(28, 121, 155, 0.2);
}

/* Content details */
.product-details__content h4 {
    font-size: 26px;
    line-height: 1.3;
}

.product-meta-item {
    font-size: 13.5px;
    color: var(--gray-600, #64748b);
}

.variant-pill {
    cursor: pointer;
    transition: all 0.2s ease;
}

.variant-pill:hover {
    border-color: var(--main-600, #1c799b);
}

.variant-pill.active {
    background-color: var(--main-600, #1c799b) !important;
    border-color: var(--main-600, #1c799b) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 10px rgba(28, 121, 155, 0.25);
}

/* Sidebar styling */
.product-details__sidebar {
    background-color: #ffffff;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
}

.product-details__sidebar-item {
    padding: 18px 20px;
    background-color: #f8fafc;
    border-bottom: 1px solid var(--gray-100, #e2e8f0);
    display: flex;
    align-items: flex-start;
    gap: 16px;
    transition: background-color 0.2s ease;
}

.product-details__sidebar-item:last-child {
    border-bottom: none;
}

.product-details__sidebar-item:hover {
    background-color: #f1f5f9;
}

.product-details__sidebar-icon {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background-color: #ffffff;
    color: var(--main-600, #1c799b);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

/* Tabs */
.product-dContent {
    background: #ffffff;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.02);
}

.product-dContent__header {
    background-color: #ffffff;
    padding: 16px 32px;
}

.product-dContent__box {
    padding: 36px 32px;
}

/* Related products card & dropdown styling */
.new-arrival .product-card {
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    align-items: stretch !important;
    width: 100% !important;
    box-sizing: border-box !important;
    background: #ffffff;
    transition: all 0.25s ease;
    overflow: visible !important;
}

.new-arrival .product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
}

.new-arrival .product-card__content {
    width: 100% !important;
    max-width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    flex-grow: 1 !important;
    box-sizing: border-box !important;
}

.variant-dropdown-wrap,
.custom-variant-select {
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    position: relative;
}

.variant-dropdown-wrap {
    min-height: 38px;
}

.custom-variant-select {
    user-select: none;
}

.variant-selected-display {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    min-height: 36px !important;
    gap: 6px;
    padding: 5px 8px !important;
    border: 1px solid var(--gray-200, #e2e8f0);
    border-radius: 8px;
    cursor: pointer;
    background: #f8fafc;
    font-size: 12px !important;
    transition: all 0.18s ease;
}

.variant-selected-display:hover {
    border-color: var(--main-600, #1c799b);
    background: #f0fdfa;
}

.custom-variant-select.open .variant-selected-display {
    border-color: var(--main-600, #1c799b);
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(28, 121, 155, 0.12);
}

.custom-variant-select.open {
    z-index: 40;
}

.custom-variant-select.open .variant-arrow {
    transform: rotate(180deg);
}

.variant-label {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    font-weight: 600 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    text-align: left !important;
}

.variant-price-tag {
    flex: 0 0 auto !important;
    font-size: 11.5px !important;
    padding: 1px 4px !important;
    white-space: nowrap !important;
}

.variant-arrow {
    flex: 0 0 auto !important;
    font-size: 13px !important;
    transition: transform 0.2s ease;
}

.variant-options-list {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid var(--gray-200, #e2e8f0);
    border-radius: 10px;
    box-shadow: 0 14px 28px rgba(15, 23, 42, 0.15);
    z-index: 1050;
    list-style: none;
    margin: 0;
    padding: 4px;
    max-height: 200px;
    overflow-y: auto;
}

.custom-variant-select.open .variant-options-list {
    display: block;
}

.variant-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 7px 9px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    transition: background-color 0.15s ease;
}

.variant-option:hover,
.variant-option.selected {
    background: #f0fdfa;
    color: var(--main-600, #1c799b);
    font-weight: 600;
}

.new-arrival .product-card .title {
    min-height: 38px !important;
    max-height: 38px !important;
    font-size: 13.5px !important;
    line-height: 1.4 !important;
    margin-bottom: 6px !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    overflow: hidden !important;
}

.product-card-category {
    font-size: 11.5px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: block !important;
    height: 18px !important;
    line-height: 18px !important;
}

.product-card__thumb {
    min-height: 180px;
    background-color: #f8fafc;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    width: 100%;
}

.product-card__thumb img {
    transition: transform 0.3s ease;
}

.product-card:hover .product-card__thumb img {
    transform: scale(1.06);
}

.cart-control,
.product-card__cart,
.new-arrival .qty-box {
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
}

.product-card__cart {
    background: #f1f5f9 !important;
    color: #0f172a !important;
    border: 1px solid #e2e8f0 !important;
    font-weight: 600 !important;
    font-size: 12.5px !important;
    padding: 8px 10px !important;
    border-radius: 50rem !important;
    white-space: nowrap !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    transition: all 0.2s ease;
}

.product-card__cart:hover {
    background: var(--main-600, #1c799b) !important;
    color: #ffffff !important;
    border-color: var(--main-600, #1c799b) !important;
    box-shadow: 0 4px 10px rgba(28, 121, 155, 0.25);
}

/* Slick Arrows */
.slick-arrow.flex-center {
    width: 40px;
    height: 40px;
    cursor: pointer;
    transition: all 0.2s ease;
}
</style>

@php
    $variantsWithUnit = $product->variants->filter(fn($v) => !empty($v->value) && !empty($v->unit));
    $firstVariant = $variantsWithUnit->first() ?? $product->variants->first();
    
    // Calculate initial savings percentage
    $discountPercent = 0;
    if ($firstVariant && $firstVariant->mrp_price && $firstVariant->mrp_price > $firstVariant->offer_price) {
        $discountPercent = round((($firstVariant->mrp_price - $firstVariant->offer_price) / $firstVariant->mrp_price) * 100);
    }

    // Build image collection
    $galleryImages = collect();
    if (!empty($product->product_image)) {
        $galleryImages->push($product->product_image);
    }
    if ($product->childImages && $product->childImages->count() > 0) {
        foreach ($product->childImages as $ci) {
            if (!empty($ci->product_child_image) && !$galleryImages->contains($ci->product_child_image)) {
                $galleryImages->push($ci->product_child_image);
            }
        }
    }
    foreach ($product->variants as $pv) {
        if (!empty($pv->varient_img) && !$galleryImages->contains($pv->varient_img)) {
            $galleryImages->push($pv->varient_img);
        }
    }
    if ($galleryImages->isEmpty()) {
        $galleryImages->push('product-details-thumb1.png');
    }
@endphp

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-24 bg-color-one border-bottom border-gray-100">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0 text-heading fw-bold">{{ $product->product_name }}</h6>
            <ul class="flex-align gap-8 flex-wrap">
                <li class="text-sm">
                    <a href="/" class="text-gray-900 flex-align gap-6 hover-text-main-600 transition-1">
                        <i class="ph ph-house text-md"></i> Home
                    </a>
                </li>
                <li class="flex-align text-gray-400 text-xs"><i class="ph ph-caret-right"></i></li>
                <li class="text-sm">
                    <a href="/products?category={{ $product->category_id }}" class="text-gray-900 hover-text-main-600 transition-1">
                        {{ $product->category->category_name ?? $product->cate_name ?? 'Products' }}
                    </a>
                </li>
                @if($product->subcate_name)
                    <li class="flex-align text-gray-400 text-xs"><i class="ph ph-caret-right"></i></li>
                    <li class="text-sm">
                        <a href="/products?category={{ $product->category_id }}&subcategory={{ $product->subcategory_id }}" class="text-gray-900 hover-text-main-600 transition-1">
                            {{ $product->subcate_name }}
                        </a>
                    </li>
                @endif
                <li class="flex-align text-gray-400 text-xs"><i class="ph ph-caret-right"></i></li>
                <li class="text-sm text-main-600 fw-semibold">{{ $product->product_name }}</li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

<!-- ========================= Product Details Start =============================== -->
<section class="product-details py-60">
    <div class="container container-lg">
        <div class="row gy-4">
            
            <!-- Left 9 Columns: Gallery + Content -->
            <div class="col-lg-9">
                <div class="row gy-4">

                    <!-- Product Image Gallery (col-xl-6) -->
                    <div class="col-xl-6">
                        <div class="product-details__left">
                            <div class="product-main-slider-wrap">
                                <!-- Discount Badge -->
                                <span class="badge bg-danger-600 text-white rounded-pill px-12 py-6 text-xs fw-bold position-absolute {{ $discountPercent > 0 ? '' : 'd-none' }}"
                                      style="top: 16px; left: 16px; z-index: 5;"
                                      id="detail-img-badge">
                                    {{ $discountPercent }}% OFF
                                </span>

                                <!-- Quick In-Stock Tag -->
                                <span class="badge bg-success-50 text-success-600 border border-success-200 rounded-pill px-10 py-4 text-xs fw-semibold position-absolute flex-align gap-4"
                                      style="top: 16px; right: 16px; z-index: 5;">
                                    <i class="ph-fill ph-check-circle"></i> In Stock
                                </span>

                                <!-- Main Image Slider -->
                                <div class="product-details__thumb-slider">
                                    @foreach($galleryImages as $img)
                                        <div>
                                            <div class="product-details__thumb flex-center">
                                                <img src="{{ Str::startsWith($img, 'assets/') ? '/' . $img : (Str::startsWith($img, 'http') ? $img : env('MAIN_URL') . 'images/' . $img) }}"
                                                     alt="{{ $product->product_name }}"
                                                     id="main-preview-img-{{ $loop->index }}"
                                                     class="main-zoomable-img"
                                                     onerror="this.src='/assets/images/thumbs/product-details-thumb1.png'">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Thumbnail Navigation Slider -->
                            <div class="mt-20">
                                <div class="product-details__images-slider">
                                    @foreach($galleryImages as $img)
                                        <div>
                                            <div class="product-thumb-item {{ $loop->first ? 'active' : '' }}" data-slide-index="{{ $loop->index }}">
                                                <img src="{{ Str::startsWith($img, 'assets/') ? '/' . $img : (Str::startsWith($img, 'http') ? $img : env('MAIN_URL') . 'images/' . $img) }}"
                                                     alt="Thumbnail {{ $loop->iteration }}"
                                                     onerror="this.src='/assets/images/thumbs/product-details-img1.png'">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Details Content (col-xl-6) -->
                    <div class="col-xl-6">
                        <div class="product-details__content ps-xl-16">
                            
                            <!-- Title -->
                            <h4 class="mb-10 text-heading fw-bold">{{ $product->product_name }}</h4>

                            <!-- Ratings, SKU & Stock Meta -->
                            <div class="flex-align flex-wrap gap-12 mb-16">
                                <div class="flex-align gap-8">
                                    <div class="flex-align gap-4">
                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                    </div>
                                    <span class="text-xs fw-semibold text-neutral-800">4.8</span>
                                    <span class="text-xs text-gray-500">(128 reviews)</span>
                                </div>
                                <span class="text-gray-300">|</span>
                                <span class="text-xs text-gray-700">
                                    <span class="text-gray-400">SKU:</span> 
                                    <strong class="text-heading" id="detail-sku">{{ $firstVariant->sku ?? 'PV-' . $product->id }}</strong>
                                </span>
                                <span class="text-gray-300">|</span>
                                <span class="text-xs text-success-600 fw-semibold flex-align gap-4">
                                    <i class="ph-fill ph-shield-check text-sm"></i> Verified Authentic
                                </span>
                            </div>

                            <!-- Category & Brand Info -->
                            <div class="flex-align gap-16 flex-wrap mb-16 text-sm">
                                @if($product->cate_name || ($product->category && $product->category->category_name))
                                    <span class="text-gray-500">
                                        Category: <a href="/products?category={{ $product->category_id }}" class="text-main-600 fw-medium hover-text-decoration-underline">{{ $product->cate_name ?? $product->category->category_name }}</a>
                                    </span>
                                @endif
                                @if($product->brand_name || ($product->brand && $product->brand->brand_name))
                                    <span class="text-gray-500">
                                        Brand: <strong class="text-heading fw-semibold">{{ $product->brand->brand_name ?? $product->brand_name }}</strong>
                                    </span>
                                @endif
                            </div>

                            <span class="mt-16 pt-16 border-top border-gray-100 d-block"></span>

                            <!-- Short Description -->
                            @if($product->product_description)
                                <p class="text-gray-700 text-sm leading-relaxed mb-20">{{ Str::limit($product->product_description, 220) }}</p>
                            @endif

                            <!-- Price Display + WhatsApp Button -->
                            <div class="mt-16 mb-20 flex-align flex-wrap justify-content-between gap-16">
                                <div class="flex-align gap-12">
                                    <h3 class="mb-0 text-heading fw-bold" id="detail-offer-price">
                                        ₹{{ $firstVariant->offer_price ?? 0 }}
                                    </h3>
                                    @if($firstVariant && $firstVariant->mrp_price)
                                        <span class="text-md text-gray-400 text-decoration-line-through fw-medium" id="detail-mrp-price">
                                            ₹{{ $firstVariant->mrp_price }}
                                        </span>
                                    @endif
                                    <span class="badge bg-danger-50 text-danger-600 border border-danger-200 rounded-pill px-8 py-4 text-xs fw-bold {{ $discountPercent > 0 ? '' : 'd-none' }}" id="detail-discount-badge">
                                        Save {{ $discountPercent }}%
                                    </span>
                                </div>
                                
                                <a href="https://api.whatsapp.com/send?text=Hello,%20I%20am%20interested%20in%20ordering%20{{ urlencode($product->product_name) }}%20(₹{{ $firstVariant->offer_price ?? 0 }})%20from%20Pal%20Varnan:%20{{ urlencode(url()->current()) }}"
                                   target="_blank"
                                   class="btn btn-outline-main rounded-pill flex-align gap-8 py-8 px-18 text-xs fw-semibold hover-bg-main-600 hover-text-white transition-1">
                                    <i class="ph-fill ph-whatsapp-logo text-base text-success-600"></i> Order on WhatsApp
                                </a>
                            </div>

                            <!-- Special Offer Countdown Box (MarketPro signature) -->
                            <div class="flex-align flex-wrap gap-12 bg-color-one rounded-12 py-12 px-20 mb-20 border border-gray-100">
                                <div class="flex-align gap-8">
                                    <span class="w-24 h-24 bg-main-600 text-white rounded-circle flex-center text-xs"><i class="ph-fill ph-tag"></i></span>
                                    <span class="text-main-600 text-xs fw-semibold">Special Offer:</span>
                                </div>
                                <div class="countdown" id="countdown11">
                                    <ul class="countdown-list flex-align flex-wrap gap-4 mb-0 ps-0">
                                        <li class="countdown-list__item text-heading flex-align gap-4 text-xs fw-semibold px-8 py-2 rounded-4 border border-main-600 bg-white shadow-xs"><span class="days">2</span>d</li>
                                        <li class="countdown-list__item text-heading flex-align gap-4 text-xs fw-semibold px-8 py-2 rounded-4 border border-main-600 bg-white shadow-xs"><span class="hours">18</span>h</li>
                                        <li class="countdown-list__item text-heading flex-align gap-4 text-xs fw-semibold px-8 py-2 rounded-4 border border-main-600 bg-white shadow-xs"><span class="minutes">42</span>m</li>
                                        <li class="countdown-list__item text-heading flex-align gap-4 text-xs fw-semibold px-8 py-2 rounded-4 border border-main-600 bg-white shadow-xs"><span class="seconds">15</span>s</li>
                                    </ul>
                                </div>
                                <span class="text-gray-700 text-xs">Remains until offer ends</span>
                            </div>

                            <!-- Fast Selling / Stock Progress Bar -->
                            <div class="mb-24">
                                <div class="flex-align gap-8 mb-8">
                                    <span class="w-24 h-24 bg-warning-50 flex-center rounded-circle text-warning-600 text-sm"><i class="ph-fill ph-lightning"></i></span>
                                    <span class="text-xs fw-semibold text-gray-900">Fast Selling! Limited Stock Available</span>
                                </div>
                                <div class="progress w-100 bg-gray-100 rounded-pill h-6" role="progressbar" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-warning-600 rounded-pill" style="width: 78%"></div>
                                </div>
                            </div>

                            <!-- Size / Weight Variant Selector -->
                            @if($variantsWithUnit->count() > 0)
                            <div class="mb-24">
                                <span class="text-gray-900 d-block mb-10 fw-semibold text-sm">Select Size / Weight:</span>
                                <div class="flex-align flex-wrap gap-8" id="variant-selector">
                                    @foreach($variantsWithUnit as $v)
                                        @php
                                            $vDiscount = ($v->mrp_price && $v->mrp_price > $v->offer_price)
                                                ? round((($v->mrp_price - $v->offer_price) / $v->mrp_price) * 100)
                                                : 0;
                                        @endphp
                                        <button type="button"
                                            class="variant-pill border rounded-pill px-16 py-8 text-sm fw-medium transition-1
                                                {{ $loop->first ? 'active bg-main-600 text-white border-main-600 shadow-sm' : 'bg-white text-gray-700 border-gray-200 hover-border-main-600 hover-bg-main-50' }}"
                                            data-variant-id="{{ $v->id }}"
                                            data-offer="{{ $v->offer_price }}"
                                            data-mrp="{{ $v->mrp_price }}"
                                            data-discount="{{ $vDiscount }}"
                                            data-sku="{{ $v->sku ?? 'PV-' . $product->id }}"
                                            data-img="{{ $v->varient_img ? (Str::startsWith($v->varient_img, 'http') ? $v->varient_img : env('MAIN_URL') . 'images/' . $v->varient_img) : '' }}">
                                            <span>{{ $v->value }}{{ $v->unit->short_name ?? '' }}</span>
                                            <span class="text-xs opacity-75 ms-4">₹{{ $v->offer_price }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Quantity Stepper, Add to Cart & Wishlist -->
                            <span class="text-gray-900 d-block mb-8 text-sm fw-medium">Quantity:</span>
                            <div class="flex-between gap-16 flex-wrap mb-24">
                                <div class="flex-align flex-wrap gap-16">
                                    @php
                                        $inCart = in_array($firstVariant->id ?? 0, $cartProductIds);
                                        $cartItem = $cartItems->where('product_varient_id', $firstVariant->id ?? 0)->first();
                                    @endphp

                                    <div class="detail-cart-control"
                                        data-product-id="{{ $product->id }}"
                                        data-variant-id="{{ $firstVariant->id ?? '' }}">

                                        @if($inCart)
                                            <div class="qty-box border border-gray-200 rounded-pill py-8 px-16 flex-align bg-white shadow-xs">
                                                <button type="button" class="quantity__minus p-4 text-gray-700 hover-text-main-600 flex-center border-0 bg-transparent"><i class="ph ph-minus"></i></button>
                                                <input type="number" class="quantity__input border-0 text-center w-32 fw-semibold" value="{{ $cartItem->product_quantity ?? 1 }}" min="1" readonly>
                                                <button type="button" class="quantity__plus p-4 text-gray-700 hover-text-main-600 flex-center border-0 bg-transparent"><i class="ph ph-plus"></i></button>
                                            </div>
                                        @else
                                            <button type="button"
                                                class="add-to-cart-btn btn btn-main rounded-pill flex-align d-inline-flex gap-8 px-44 py-12 fw-semibold shadow-sm transition-1">
                                                <i class="ph ph-shopping-cart text-lg"></i> Add To Cart
                                            </button>
                                        @endif

                                    </div>
                                </div>

                                <div class="flex-align gap-12">
                                    @auth
                                    <button type="button"
                                        class="wishlist-btn w-48 h-48 bg-main-50 text-main-600 text-xl hover-bg-main-600 hover-text-white flex-center rounded-circle border-0 transition-1"
                                        title="Save to Wishlist"
                                        data-product-id="{{ $product->id }}"
                                        data-variant-id="{{ $firstVariant->id ?? '' }}">
                                        <i class="{{ $wishlistVariantIds->contains($firstVariant->id ?? 0) ? 'ph-fill text-danger-600' : 'ph' }} ph-heart"></i>
                                    </button>
                                    @else
                                    <button type="button"
                                        onclick="showLoginAlert()"
                                        class="w-48 h-48 bg-main-50 text-main-600 text-xl hover-bg-main-600 hover-text-white flex-center rounded-circle border-0 transition-1"
                                        title="Login to save to Wishlist">
                                        <i class="ph ph-heart"></i>
                                    </button>
                                    @endauth

                                    <button type="button"
                                        class="share-btn w-48 h-48 bg-gray-50 text-gray-700 text-xl hover-bg-main-600 hover-text-white flex-center rounded-circle border-0 transition-1"
                                        title="Share this product"
                                        onclick="copyProductLink()">
                                        <i class="ph ph-share-network"></i>
                                    </button>
                                </div>
                            </div>

                            <span class="mt-24 pt-24 text-gray-700 border-top border-gray-100 d-block"></span>

                            <!-- Highlights & Guarantees Box (Marketpro style) -->
                            <div class="flex-between gap-16 p-12 border border-main-two-600 border-dashed rounded-8 mb-16 bg-main-two-50">
                                <div class="flex-align gap-12">
                                    <span class="w-20 h-20 flex-center bg-main-two-600 text-white text-xs rounded-circle">
                                        <i class="ph-bold ph-check"></i>
                                    </span>
                                    <span class="text-gray-900 fw-medium text-xs">Direct Farm Sourcing & Best Quality Assured</span>
                                </div>
                                <a href="/products" class="text-xs fw-semibold text-main-two-600 text-decoration-underline hover-text-main-two-700">Explore Store</a>
                            </div>

                            <ul class="list-inside ms-4 ps-0 mb-0">
                                <li class="text-gray-700 text-xs mb-8 flex-align gap-8">
                                    <i class="ph-fill ph-check-circle text-main-600 text-base"></i> 100% Genuine product with rigorous quality checks
                                </li>
                                <li class="text-gray-700 text-xs mb-0 flex-align gap-8">
                                    <i class="ph-fill ph-check-circle text-main-600 text-base"></i> Safe, contactless & temperature-monitored packaging
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 3 Columns: Official MarketPro Trust & Delivery Sidebar -->
            <div class="col-lg-3">
                <div class="product-details__sidebar border border-gray-100 rounded-16 overflow-hidden">
                    
                    <!-- Store info badge -->
                    <div class="p-20 bg-white border-bottom border-gray-100">
                        <div class="flex-between bg-main-600 rounded-pill p-8">
                            <div class="flex-align gap-8 ps-6">
                                <span class="w-36 h-36 bg-white text-main-600 rounded-circle flex-center text-lg"><i class="ph ph-storefront"></i></span>
                                <span class="text-white fw-medium text-sm">by Pal Varnan</span>
                            </div>
                            <a href="/products" class="btn btn-white rounded-pill text-uppercase text-xs fw-bold px-12 py-8">View Store</a>
                        </div>
                    </div>

                    <!-- Fast Delivery -->
                    <div class="product-details__sidebar-item">
                        <span class="product-details__sidebar-icon">
                            <i class="ph-fill ph-truck"></i>
                        </span>
                        <div>
                            <h6 class="text-sm mb-4 text-heading fw-semibold">Fast Delivery</h6>
                            <p class="text-gray-600 text-xs mb-0 leading-normal">Lightning-fast shipping, guaranteed to your doorstep.</p>
                        </div>
                    </div>

                    <!-- Easy Returns -->
                    <div class="product-details__sidebar-item">
                        <span class="product-details__sidebar-icon">
                            <i class="ph-fill ph-arrow-u-up-left"></i>
                        </span>
                        <div>
                            <h6 class="text-sm mb-4 text-heading fw-semibold">Easy Returns & Support</h6>
                            <p class="text-gray-600 text-xs mb-0 leading-normal">Shop risk-free with friendly customer care resolution.</p>
                        </div>
                    </div>

                    <!-- Quality Assurance -->
                    <div class="product-details__sidebar-item">
                        <span class="product-details__sidebar-icon">
                            <i class="ph-fill ph-seal-check"></i>
                        </span>
                        <div>
                            <h6 class="text-sm mb-4 text-heading fw-semibold">100% Quality Guaranteed</h6>
                            <p class="text-gray-600 text-xs mb-0 leading-normal">Directly tested, farm-fresh and certified authentic.</p>
                        </div>
                    </div>

                    <!-- Secure Payment -->
                    <div class="product-details__sidebar-item">
                        <span class="product-details__sidebar-icon">
                            <i class="ph-fill ph-credit-card"></i>
                        </span>
                        <div>
                            <h6 class="text-sm mb-4 text-heading fw-semibold">Secure Payment</h6>
                            <p class="text-gray-600 text-xs mb-0 leading-normal">UPI, NetBanking, Credit/Debit cards & Cash on Delivery.</p>
                        </div>
                    </div>

                    <!-- Packaging -->
                    <div class="product-details__sidebar-item">
                        <span class="product-details__sidebar-icon">
                            <i class="ph-fill ph-package"></i>
                        </span>
                        <div>
                            <h6 class="text-sm mb-4 text-heading fw-semibold">Hygienic Packaging</h6>
                            <p class="text-gray-600 text-xs mb-0 leading-normal">Sealed food-grade packaging to keep aroma & purity intact.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- ========================= Tabbed Information Section =============================== -->
        <div class="pt-60">
            <div class="product-dContent border border-gray-100 rounded-24 overflow-hidden">
                <div class="product-dContent__header border-bottom border-gray-100 flex-between flex-wrap gap-16">
                    <ul class="nav common-tab nav-pills mb-0" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-description-tab" data-bs-toggle="pill" data-bs-target="#pills-description" type="button" role="tab" aria-controls="pills-description" aria-selected="true">Description</button>
                        </li>
                        @if($product->product_specification)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-specification-tab" data-bs-toggle="pill" data-bs-target="#pills-specification" type="button" role="tab" aria-controls="pills-specification" aria-selected="false">Specifications</button>
                        </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-reviews-tab" data-bs-toggle="pill" data-bs-target="#pills-reviews" type="button" role="tab" aria-controls="pills-reviews" aria-selected="false">Reviews (4.8)</button>
                        </li>
                    </ul>
                    <div class="btn bg-color-one rounded-16 flex-align gap-8 text-main-600 text-sm fw-semibold cursor-default">
                        <img src="/assets/images/icon/satisfaction-icon.png" alt="" onerror="this.style.display='none'">
                        <i class="ph-fill ph-shield-check text-xl text-main-600"></i>
                        100% Satisfaction Guaranteed
                    </div>
                </div>

                <div class="product-dContent__box">
                    <div class="tab-content" id="pills-tabContent">
                        
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="pills-description" role="tabpanel" aria-labelledby="pills-description-tab">
                            <div class="mb-32">
                                <h6 class="mb-16 text-heading fw-bold">Product Description</h6>
                                <p class="text-gray-700 leading-relaxed">
                                    {{ $product->product_description ?? 'Experience the rich taste and unmatched freshness of ' . $product->product_name . '. Crafted to perfection with top-quality ingredients, ensuring purity and superior nourishment for you and your family.' }}
                                </p>
                            </div>
                            
                            <div class="mb-32">
                                <h6 class="mb-16 text-heading fw-bold">Key Highlights & Benefits</h6>
                                <ul class="list-inside ms-4 ps-0 mb-0">
                                    <li class="text-gray-700 mb-12 flex-align gap-12">
                                        <span class="w-20 h-20 bg-main-50 text-main-600 text-xs flex-center rounded-circle flex-shrink-0"><i class="ph ph-check"></i></span>
                                        <span>100% natural and unadulterated source ingredients</span>
                                    </li>
                                    <li class="text-gray-700 mb-12 flex-align gap-12">
                                        <span class="w-20 h-20 bg-main-50 text-main-600 text-xs flex-center rounded-circle flex-shrink-0"><i class="ph ph-check"></i></span>
                                        <span>Carefully processed under stringent food-safety and hygienic standards</span>
                                    </li>
                                    <li class="text-gray-700 mb-12 flex-align gap-12">
                                        <span class="w-20 h-20 bg-main-50 text-main-600 text-xs flex-center rounded-circle flex-shrink-0"><i class="ph ph-check"></i></span>
                                        <span>Retains authentic natural flavor, aroma, and essential nutritional profile</span>
                                    </li>
                                    <li class="text-gray-700 mb-12 flex-align gap-12">
                                        <span class="w-20 h-20 bg-main-50 text-main-600 text-xs flex-center rounded-circle flex-shrink-0"><i class="ph ph-check"></i></span>
                                        <span>Zero artificial colors, synthetic flavors or harmful chemical additives</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="mb-0">
                                <h6 class="mb-16 text-heading fw-bold">Storage & Usage Instructions</h6>
                                <p class="text-gray-700 leading-relaxed mb-0">
                                    Store in a clean, cool, and dry place away from direct sunlight. After opening, keep tightly sealed in an airtight food-grade container to preserve peak freshness and taste.
                                </p>
                            </div>
                        </div>

                        <!-- Specifications Tab -->
                        @if($product->product_specification)
                        <div class="tab-pane fade" id="pills-specification" role="tabpanel" aria-labelledby="pills-specification-tab">
                            <div class="mb-24">
                                <h6 class="mb-20 text-heading fw-bold">Product Specifications</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered border-gray-100 rounded-8 overflow-hidden mb-0">
                                        <tbody>
                                            <tr>
                                                <th class="bg-gray-50 text-heading fw-semibold py-12 px-20" style="width: 28%;">Product Name</th>
                                                <td class="text-gray-700 py-12 px-20">{{ $product->product_name }}</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-gray-50 text-heading fw-semibold py-12 px-20">Category</th>
                                                <td class="text-gray-700 py-12 px-20">{{ $product->cate_name ?? $product->category->category_name ?? 'General' }}</td>
                                            </tr>
                                            @if($product->subcate_name)
                                            <tr>
                                                <th class="bg-gray-50 text-heading fw-semibold py-12 px-20">Sub Category</th>
                                                <td class="text-gray-700 py-12 px-20">{{ $product->subcate_name }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th class="bg-gray-50 text-heading fw-semibold py-12 px-20">Brand</th>
                                                <td class="text-gray-700 py-12 px-20">{{ $product->brand->brand_name ?? $product->brand_name ?? 'Pal Varnan' }}</td>
                                            </tr>
                                            @if($firstVariant)
                                            <tr>
                                                <th class="bg-gray-50 text-heading fw-semibold py-12 px-20">Available Size / Unit</th>
                                                <td class="text-gray-700 py-12 px-20">{{ $firstVariant->value }}{{ $firstVariant->unit->short_name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="bg-gray-50 text-heading fw-semibold py-12 px-20">SKU Code</th>
                                                <td class="text-gray-700 py-12 px-20">{{ $firstVariant->sku ?? 'PV-' . $product->id }}</td>
                                            </tr>
                                            @if($firstVariant->product_gst)
                                            <tr>
                                                <th class="bg-gray-50 text-heading fw-semibold py-12 px-20">Applicable GST</th>
                                                <td class="text-gray-700 py-12 px-20">{{ $firstVariant->product_gst }}%</td>
                                            </tr>
                                            @endif
                                            @endif
                                            <tr>
                                                <th class="bg-gray-50 text-heading fw-semibold py-12 px-20">Specification Details</th>
                                                <td class="text-gray-700 py-12 px-20">{{ $product->product_specification }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Reviews Tab (MarketPro exact structure) -->
                        <div class="tab-pane fade" id="pills-reviews" role="tabpanel" aria-labelledby="pills-reviews-tab">
                            <div class="row g-4">
                                <!-- Customer reviews list + review form -->
                                <div class="col-lg-6">
                                    <h6 class="mb-24 text-heading fw-bold">Customer Feedback</h6>
                                    
                                    <div class="d-flex align-items-start gap-20 pb-28 border-bottom border-gray-100 mb-28">
                                        <img src="/assets/images/thumbs/comment-img1.png" alt="" class="w-48 h-48 object-fit-cover rounded-circle flex-shrink-0" onerror="this.src='/assets/images/logo/favicon.png'">
                                        <div class="flex-grow-1">
                                            <div class="flex-between align-items-start gap-8">
                                                <div>
                                                    <h6 class="mb-4 text-sm fw-semibold">Ramesh Kumar</h6>
                                                    <div class="flex-align gap-4">
                                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                    </div>
                                                </div>
                                                <span class="text-gray-400 text-xs">2 days ago</span>
                                            </div>
                                            <h6 class="mb-8 text-sm mt-12 fw-semibold">Great quality & fresh delivery!</h6>
                                            <p class="text-gray-600 text-sm mb-12">Product was packaged neatly and delivered on time. High quality and completely genuine. Would definitely order again!</p>
                                            <div class="flex-align gap-16">
                                                <button type="button" class="flex-align gap-6 text-gray-500 hover-text-main-600 border-0 bg-transparent text-xs p-0">
                                                    <i class="ph-bold ph-thumbs-up"></i> Helpful (12)
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start gap-20 pb-28 border-bottom border-gray-100 mb-36">
                                        <img src="/assets/images/thumbs/comment-img2.png" alt="" class="w-48 h-48 object-fit-cover rounded-circle flex-shrink-0" onerror="this.src='/assets/images/logo/favicon.png'">
                                        <div class="flex-grow-1">
                                            <div class="flex-between align-items-start gap-8">
                                                <div>
                                                    <h6 class="mb-4 text-sm fw-semibold">Ananya Sharma</h6>
                                                    <div class="flex-align gap-4">
                                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                        <span class="text-xs text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                    </div>
                                                </div>
                                                <span class="text-gray-400 text-xs">1 week ago</span>
                                            </div>
                                            <h6 class="mb-8 text-sm mt-12 fw-semibold">Authentic taste and value for money</h6>
                                            <p class="text-gray-600 text-sm mb-12">Very satisfied with the taste and consistency. Customer service and doorstep delivery were prompt and smooth.</p>
                                            <div class="flex-align gap-16">
                                                <button type="button" class="flex-align gap-6 text-gray-500 hover-text-main-600 border-0 bg-transparent text-xs p-0">
                                                    <i class="ph-bold ph-thumbs-up"></i> Helpful (8)
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Write a Review Form -->
                                    <div class="mt-32">
                                        <h6 class="mb-14 text-heading fw-bold">Write a Review</h6>
                                        <span class="text-gray-700 text-sm d-block mb-8">What is your rating for this item?</span>
                                        <div class="flex-align gap-6 mb-20 cursor-pointer text-warning-600 text-lg" id="star-rating-select">
                                            <i class="ph-fill ph-star" data-rating="1"></i>
                                            <i class="ph-fill ph-star" data-rating="2"></i>
                                            <i class="ph-fill ph-star" data-rating="3"></i>
                                            <i class="ph-fill ph-star" data-rating="4"></i>
                                            <i class="ph-fill ph-star" data-rating="5"></i>
                                        </div>
                                        <form action="javascript:void(0)" onsubmit="submitProductReview(this)">
                                            <div class="mb-16">
                                                <label for="review-title" class="text-heading text-xs fw-semibold mb-6">Review Title</label>
                                                <input type="text" class="form-control rounded-8 py-10 px-16 border-gray-200 text-sm" id="review-title" placeholder="Summary of your experience..." required>
                                            </div>
                                            <div class="mb-20">
                                                <label for="review-desc" class="text-heading text-xs fw-semibold mb-6">Review Content</label>
                                                <textarea class="form-control rounded-8 py-10 px-16 border-gray-200 text-sm" id="review-desc" rows="4" placeholder="Write detailed feedback about taste, quality, packaging..." required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-main rounded-pill px-32 py-10 fw-semibold text-sm">Submit Review</button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Rating Summary Card -->
                                <div class="col-lg-6">
                                    <div class="ms-lg-4">
                                        <h6 class="mb-24 text-heading fw-bold">Customers Feedback & Rating</h6>
                                        <div class="d-flex flex-wrap gap-24 align-items-center mb-32">
                                            <div class="border border-gray-100 rounded-16 px-36 py-40 flex-center flex-column flex-shrink-0 text-center bg-gray-50">
                                                <h2 class="mb-4 text-main-600 fw-bold">4.8</h2>
                                                <div class="flex-center gap-4 mb-8">
                                                    <span class="text-sm text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                    <span class="text-sm text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                    <span class="text-sm text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                    <span class="text-sm text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                    <span class="text-sm text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                </div>
                                                <span class="text-gray-500 text-xs">Average Product Rating</span>
                                            </div>
                                            <div class="border border-gray-100 rounded-16 p-24 flex-grow-1 bg-white">
                                                <div class="flex-align gap-8 mb-16">
                                                    <span class="text-gray-900 text-xs fw-semibold flex-shrink-0 w-16">5★</span>
                                                    <div class="progress w-100 bg-gray-100 rounded-pill h-8">
                                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: 82%"></div>
                                                    </div>
                                                    <span class="text-gray-500 text-xs flex-shrink-0 w-32 text-end">82%</span>
                                                </div>
                                                <div class="flex-align gap-8 mb-16">
                                                    <span class="text-gray-900 text-xs fw-semibold flex-shrink-0 w-16">4★</span>
                                                    <div class="progress w-100 bg-gray-100 rounded-pill h-8">
                                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: 12%"></div>
                                                    </div>
                                                    <span class="text-gray-500 text-xs flex-shrink-0 w-32 text-end">12%</span>
                                                </div>
                                                <div class="flex-align gap-8 mb-16">
                                                    <span class="text-gray-900 text-xs fw-semibold flex-shrink-0 w-16">3★</span>
                                                    <div class="progress w-100 bg-gray-100 rounded-pill h-8">
                                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: 4%"></div>
                                                    </div>
                                                    <span class="text-gray-500 text-xs flex-shrink-0 w-32 text-end">4%</span>
                                                </div>
                                                <div class="flex-align gap-8 mb-16">
                                                    <span class="text-gray-900 text-xs fw-semibold flex-shrink-0 w-16">2★</span>
                                                    <div class="progress w-100 bg-gray-100 rounded-pill h-8">
                                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: 1%"></div>
                                                    </div>
                                                    <span class="text-gray-500 text-xs flex-shrink-0 w-32 text-end">1%</span>
                                                </div>
                                                <div class="flex-align gap-8 mb-0">
                                                    <span class="text-gray-900 text-xs fw-semibold flex-shrink-0 w-16">1★</span>
                                                    <div class="progress w-100 bg-gray-100 rounded-pill h-8">
                                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: 1%"></div>
                                                    </div>
                                                    <span class="text-gray-500 text-xs flex-shrink-0 w-32 text-end">1%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<!-- ========================= Product Details End =============================== -->

<!-- ========================= Similar Products Slider Start =============================== -->
@if($relatedProducts->count() > 0)
<section class="new-arrival pb-80">
    <div class="container container-lg">
        <div class="section-heading mb-32">
            <div class="flex-between flex-wrap gap-8">
                <div>
                    <h5 class="mb-4 text-heading fw-bold">You Might Also Like</h5>
                    <p class="text-gray-500 text-xs mb-0">Frequently bought together and recommended items</p>
                </div>
                <div class="flex-align gap-16">
                    <a href="/products?category={{ $product->category_id }}" class="text-sm fw-semibold text-main-600 hover-text-decoration-underline flex-align gap-4">
                        All Products <i class="ph ph-arrow-right"></i>
                    </a>
                    <div class="flex-align gap-8">
                        <button type="button" id="new-arrival-prev" class="slick-prev slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1" aria-label="Previous">
                            <i class="ph ph-caret-left"></i>
                        </button>
                        <button type="button" id="new-arrival-next" class="slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1" aria-label="Next">
                            <i class="ph ph-caret-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="new-arrival__slider arrow-style-two">
            @foreach($relatedProducts as $rp)
                @php
                    $rv = $rp->variants->first();
                    $rpVariantsWithUnit = $rp->variants->filter(fn($v) => !empty($v->value) && !empty($v->unit));
                    $rpDiscount = 0;
                    if ($rv && $rv->mrp_price && $rv->mrp_price > $rv->offer_price) {
                        $rpDiscount = round((($rv->mrp_price - $rv->offer_price) / $rv->mrp_price) * 100);
                    }
                @endphp

                <div class="px-8">
                    <div class="product-card h-100 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 bg-white">
                        
                        <!-- Discount Badge -->
                        @if($rpDiscount > 0)
                            <span class="badge bg-danger-600 text-white rounded-pill px-8 py-4 text-xs fw-bold position-absolute top-12 start-12 z-2">
                                {{ $rpDiscount }}% OFF
                            </span>
                        @endif

                        <!-- Wishlist button -->
                        @auth
                        <button type="button"
                            class="wishlist-btn-related position-absolute z-2"
                            data-product-id="{{ $rp->id }}"
                            data-variant-id="{{ $rv->id ?? '' }}"
                            style="top:12px; right:12px; background:none; border:none; padding:0; cursor:pointer;"
                            title="Add to wishlist">
                            <i class="ph{{ in_array($rp->id, $wishlistProductIds ?? []) ? '-fill text-danger-600' : ' text-gray-400' }} ph-heart text-2xl hover-text-danger-600 transition-1"></i>
                        </button>
                        @endauth

                        <!-- Thumbnail -->
                        <a href="/product/{{ $rp->slug }}" class="product-card__thumb flex-center rounded-12 bg-gray-50 position-relative">
                            <img src="{{ Str::startsWith($rp->product_image, 'http') ? $rp->product_image : env('MAIN_URL') . 'images/' . $rp->product_image }}"
                                 alt="{{ $rp->product_name }}"
                                 class="w-auto" style="max-height: 170px; object-fit: contain;"
                                 onerror="this.src='/assets/images/thumbs/product-img1.png'">
                        </a>

                        <!-- Content -->
                        <div class="product-card__content mt-16">
                            <span class="product-card-category mb-4">
                                {{ $rp->cate_name ?? 'Product' }}
                                @if($rp->subcate_name) / {{ $rp->subcate_name }} @endif
                            </span>

                            <h6 class="title text-md fw-semibold mb-8">
                                <a href="/product/{{ $rp->slug }}" class="link text-line-2 text-heading hover-text-main-600 transition-1">
                                    {{ $rp->product_name }}
                                </a>
                            </h6>

                            <!-- Variant Dropdown -->
                            @if($rpVariantsWithUnit->count() > 0)
                            <div class="variant-dropdown-wrap mb-12 mt-12">
                                <div class="custom-variant-select" data-product-id="{{ $rp->id }}">
                                    <div class="variant-selected-display">
                                        @php $rpFirst = $rpVariantsWithUnit->first(); @endphp
                                        <span class="variant-label fw-medium">{{ $rpFirst->value }}{{ $rpFirst->unit->short_name ?? '' }}</span>
                                        <span class="variant-price-tag fw-semibold text-main-600">₹{{ $rpFirst->offer_price }}</span>
                                        <i class="ph ph-caret-down variant-arrow text-gray-500"></i>
                                    </div>
                                    <ul class="variant-options-list">
                                        @foreach($rpVariantsWithUnit as $v)
                                        <li class="variant-option {{ $loop->first ? 'selected' : '' }}"
                                            data-variant-id="{{ $v->id }}"
                                            data-offer="{{ $v->offer_price }}"
                                            data-mrp="{{ $v->mrp_price }}">
                                            <span class="opt-size">{{ $v->value }}{{ $v->unit->short_name ?? '' }}</span>
                                            <span class="opt-price">
                                                @if($v->mrp_price)
                                                    <span class="opt-mrp text-gray-400 text-decoration-line-through me-4 text-xs">₹{{ $v->mrp_price }}</span>
                                                @endif
                                                <span class="opt-offer fw-semibold text-heading">₹{{ $v->offer_price }}</span>
                                            </span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @else
                            <div class="variant-dropdown-wrap mb-12 mt-12" style="visibility: hidden;">
                                <div class="variant-selected-display" style="opacity: 0; pointer-events: none;">
                                    <span>&nbsp;</span>
                                </div>
                            </div>
                            @endif

                            <!-- Price -->
                            <div class="product-card__price my-12 flex-align gap-8">
                                <span class="text-heading text-md fw-bold">₹{{ $rv->offer_price ?? 0 }}</span>
                                @if($rv && $rv->mrp_price && $rv->mrp_price > $rv->offer_price)
                                    <span class="text-gray-400 text-xs fw-medium text-decoration-line-through">₹{{ $rv->mrp_price }}</span>
                                @endif
                            </div>

                            <!-- Cart Control -->
                            @php
                                $rpInCart = in_array($rv->id ?? 0, $cartProductIds);
                                $rpCartItem = $cartItems->where('product_varient_id', $rv->id ?? 0)->first();
                            @endphp
                            <div class="cart-control" data-product-id="{{ $rp->id }}" data-variant-id="{{ $rv->id ?? '' }}">
                                @if($rpInCart)
                                    <div class="qty-box d-flex rounded-pill overflow-hidden border border-gray-200 bg-white">
                                        <button type="button" class="quantity__minus h-36 w-36 flex-center bg-gray-50 hover-bg-main-600 hover-text-white text-md fw-bold border-0">−</button>
                                        <input type="number" class="quantity__input flex-grow-1 border-0 text-center fw-semibold w-24 px-2" value="{{ $rpCartItem->product_quantity ?? 1 }}" min="1" readonly>
                                        <button type="button" class="quantity__plus h-36 w-36 flex-center bg-gray-50 hover-bg-main-600 hover-text-white text-md fw-bold border-0">+</button>
                                    </div>
                                @else
                                    <button type="button"
                                        class="add-to-cart-btn product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-10 px-20 rounded-pill flex-center gap-8 fw-semibold w-100 border-0">
                                        Add To Cart <i class="ph ph-shopping-cart"></i>
                                    </button>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
<!-- ========================= Similar Products Slider End =============================== -->

<script>
function showLoginAlert() {
    Swal.fire({
        icon: 'warning',
        title: 'Login Required',
        text: 'Please log in to add products to your wishlist or cart',
        confirmButtonColor: '#1c799b',
        confirmButtonText: 'Login Now'
    }).then(r => { if (r.isConfirmed) window.location.href = "/login"; });
}

function copyProductLink() {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(window.location.href).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Link Copied!',
                text: 'Product link copied to clipboard',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        });
    } else {
        const temp = document.createElement('input');
        temp.value = window.location.href;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        Swal.fire({
            icon: 'success',
            title: 'Link Copied!',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }
}

function submitProductReview(form) {
    Swal.fire({
        icon: 'success',
        title: 'Thank You!',
        text: 'Your review has been submitted for moderation.',
        confirmButtonColor: '#1c799b'
    });
    form.reset();
}

// Sync quantity via AJAX
function syncCart(variantId, quantity, input) {
    fetch("{{ route('cart.ajaxUpdate') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_varient_id: variantId, quantity: quantity })
    }).then(r => r.json()).then(data => {
        if (data.success) input.value = quantity;
    });
}

// Add to cart for Detail Product
function attachDetailAddToCart() {
    const control = document.querySelector('.detail-cart-control');
    if (!control) return;
    const btn = control.querySelector('.add-to-cart-btn');
    if (!btn) return;

    btn.addEventListener('click', function () {
        const productId = control.dataset.productId;
        const variantId = control.dataset.variantId;

        fetch("{{ route('cart.store') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ product_id: productId, product_varient_id: variantId, quantity: 1 })
        })
        .then(r => r.json())
        .then(data => {
            control.innerHTML = `
                <div class="qty-box border border-gray-200 rounded-pill py-8 px-16 flex-align bg-white shadow-xs">
                    <button type="button" class="quantity__minus p-4 text-gray-700 hover-text-main-600 flex-center border-0 bg-transparent"><i class="ph ph-minus"></i></button>
                    <input type="number" class="quantity__input border-0 text-center w-32 fw-semibold" value="1" min="1" readonly>
                    <button type="button" class="quantity__plus p-4 text-gray-700 hover-text-main-600 flex-center border-0 bg-transparent"><i class="ph ph-plus"></i></button>
                </div>`;
            attachDetailQtyEvents();
            updateCartCount(1);
            Swal.fire({ icon: 'success', title: data.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
        });
    });
}

function attachDetailQtyEvents() {
    const control = document.querySelector('.detail-cart-control');
    if (!control) return;
    const variantId = control.dataset.variantId;
    const input = control.querySelector('.quantity__input');
    const plusBtn = control.querySelector('.quantity__plus');
    const minusBtn = control.querySelector('.quantity__minus');

    if (plusBtn) {
        plusBtn.addEventListener('click', function () {
            syncCart(variantId, parseInt(input.value) + 1, input);
        });
    }

    if (minusBtn) {
        minusBtn.addEventListener('click', function () {
            const qty = parseInt(input.value);
            if (qty <= 1) {
                fetch("{{ route('cart.ajaxRemove') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ product_varient_id: variantId })
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        control.innerHTML = `
                            <button type="button" class="add-to-cart-btn btn btn-main rounded-pill flex-align d-inline-flex gap-8 px-44 py-12 fw-semibold shadow-sm transition-1">
                                <i class="ph ph-shopping-cart text-lg"></i> Add To Cart
                            </button>`;
                        attachDetailAddToCart();
                        updateCartCount(-1);
                    }
                });
            } else {
                syncCart(variantId, qty - 1, input);
            }
        });
    }
}

// Related products Cart Box
function showQtyBox(controlDiv, qty) {
    controlDiv.innerHTML = `
        <div class="qty-box d-flex rounded-pill overflow-hidden border border-gray-200 bg-white">
            <button type="button" class="quantity__minus h-36 w-36 flex-center bg-gray-50 hover-bg-main-600 hover-text-white text-md fw-bold border-0">−</button>
            <input type="number" class="quantity__input flex-grow-1 border-0 text-center fw-semibold w-24 px-2" value="${qty}" min="1" readonly>
            <button type="button" class="quantity__plus h-36 w-36 flex-center bg-gray-50 hover-bg-main-600 hover-text-white text-md fw-bold border-0">+</button>
        </div>`;
    attachCardQtyEvents(controlDiv);
}

function attachAddToCartEvent(controlDiv) {
    const btn = controlDiv.querySelector('.add-to-cart-btn');
    if (!btn) return;
    btn.addEventListener('click', function () {
        const productId = controlDiv.dataset.productId;
        const variantId = controlDiv.dataset.variantId;

        fetch("{{ route('cart.store') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ product_id: productId, product_varient_id: variantId, quantity: 1 })
        })
        .then(r => r.json())
        .then(data => {
            showQtyBox(controlDiv, 1);
            updateCartCount(1);
            Swal.fire({ icon: 'success', title: data.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
        });
    });
}

function attachCardQtyEvents(controlDiv) {
    const variantId = controlDiv.dataset.variantId;
    const input = controlDiv.querySelector('.quantity__input');
    const plusBtn = controlDiv.querySelector('.quantity__plus');
    const minusBtn = controlDiv.querySelector('.quantity__minus');

    if (plusBtn) {
        plusBtn.addEventListener('click', function () {
            syncCart(variantId, parseInt(input.value) + 1, input);
        });
    }

    if (minusBtn) {
        minusBtn.addEventListener('click', function () {
            const qty = parseInt(input.value);
            if (qty <= 1) {
                fetch("{{ route('cart.ajaxRemove') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ product_varient_id: variantId })
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        controlDiv.innerHTML = `
                            <button type="button"
                                class="add-to-cart-btn product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-10 px-20 rounded-pill flex-center gap-8 fw-semibold w-100 border-0">
                                Add To Cart <i class="ph ph-shopping-cart"></i>
                            </button>`;
                        attachAddToCartEvent(controlDiv);
                        updateCartCount(-1);
                    }
                });
            } else {
                syncCart(variantId, qty - 1, input);
            }
        });
    }
}

// Init all .cart-control (related products)
document.querySelectorAll('.cart-control').forEach(controlDiv => {
    if (controlDiv.querySelector('.qty-box')) {
        attachCardQtyEvents(controlDiv);
    } else {
        attachAddToCartEvent(controlDiv);
    }
});

function updateCartCount(delta) {
    const el = document.getElementById('cart-count');
    if (el) el.textContent = parseInt(el.textContent || 0) + delta;
}

// Variant Pill Click (Main Product)
document.querySelectorAll('.variant-pill').forEach(function (pill) {
    pill.addEventListener('click', function () {
        const variantId  = this.dataset.variantId;
        const offerPrice = this.dataset.offer;
        const mrpPrice   = this.dataset.mrp;
        const discount   = this.dataset.discount;
        const sku        = this.dataset.sku;
        const imgUrl     = this.dataset.img;

        document.querySelectorAll('.variant-pill').forEach(p => {
            p.classList.remove('active', 'bg-main-600', 'text-white', 'border-main-600', 'shadow-sm');
            p.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
        });
        this.classList.add('active', 'bg-main-600', 'text-white', 'border-main-600', 'shadow-sm');
        this.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');

        // Update pricing display
        document.getElementById('detail-offer-price').textContent = '₹' + offerPrice;
        const mrpEl = document.getElementById('detail-mrp-price');
        if (mrpEl) mrpEl.textContent = mrpPrice ? '₹' + mrpPrice : '';

        // Update discount badges
        const badgeEl = document.getElementById('detail-discount-badge');
        const imgBadgeEl = document.getElementById('detail-img-badge');
        if (discount && parseInt(discount) > 0) {
            if (badgeEl) {
                badgeEl.textContent = 'Save ' + discount + '%';
                badgeEl.classList.remove('d-none');
            }
            if (imgBadgeEl) {
                imgBadgeEl.textContent = discount + '% OFF';
                imgBadgeEl.classList.remove('d-none');
            }
        } else {
            if (badgeEl) badgeEl.classList.add('d-none');
            if (imgBadgeEl) imgBadgeEl.classList.add('d-none');
        }

        // Update SKU
        const skuEl = document.getElementById('detail-sku');
        if (skuEl && sku) skuEl.textContent = sku;

        // If variant has distinct image, switch to it
        if (imgUrl) {
            const firstThumbImg = document.querySelector('.product-details__thumb img');
            if (firstThumbImg) firstThumbImg.src = imgUrl;
        }

        // Update cart control
        const control = document.querySelector('.detail-cart-control');
        if (control) {
            control.dataset.variantId = variantId;
            if (control.querySelector('.qty-box')) {
                control.innerHTML = `
                    <button type="button" class="add-to-cart-btn btn btn-main rounded-pill flex-align d-inline-flex gap-8 px-44 py-12 fw-semibold shadow-sm transition-1">
                        <i class="ph ph-shopping-cart text-lg"></i> Add To Cart
                    </button>`;
                attachDetailAddToCart();
            }
        }

        // Update wishlist button
        const wishlistBtn = document.querySelector('.wishlist-btn');
        if (wishlistBtn) {
            wishlistBtn.dataset.variantId = variantId;
            const wIcon = wishlistBtn.querySelector('i');
            fetch(`/wishlist/check?product_id=${wishlistBtn.dataset.productId}&variant_id=${variantId}`, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(data => {
                wIcon.className = data.in_wishlist ? 'ph-fill ph-heart text-danger-600' : 'ph ph-heart';
            });
        }
    });
});

// Variant Dropdown in Related Products
function initVariantDropdowns() {
    document.querySelectorAll('.custom-variant-select').forEach(function (select) {
        if (select.dataset.initialized) return;
        select.dataset.initialized = 'true';

        const display = select.querySelector('.variant-selected-display');
        const options = select.querySelectorAll('.variant-option');

        display.addEventListener('click', function (e) {
            e.stopPropagation();
            document.querySelectorAll('.custom-variant-select.open').forEach(s => {
                if (s !== select) s.classList.remove('open');
            });
            select.classList.toggle('open');
        });

        options.forEach(function (option) {
            option.addEventListener('click', function () {
                const variantId  = this.dataset.variantId;
                const offerPrice = this.dataset.offer;
                const mrpPrice   = this.dataset.mrp;
                const sizeText   = this.querySelector('.opt-size').textContent;

                select.querySelector('.variant-label').textContent = sizeText;
                select.querySelector('.variant-price-tag').textContent = '₹' + offerPrice;
                options.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                select.classList.remove('open');

                const card = select.closest('.product-card__content');
                card.querySelector('.product-card__price').innerHTML = `
                    <span class="text-heading text-md fw-bold">₹${offerPrice}</span>
                    ${mrpPrice ? `<span class="text-gray-400 text-xs fw-medium text-decoration-line-through ms-6">₹${mrpPrice}</span>` : ''}
                `;

                const wishlistBtn = select.closest('.product-card').querySelector('.wishlist-btn-related');
                if (wishlistBtn) {
                    wishlistBtn.dataset.variantId = variantId;
                    const wIcon = wishlistBtn.querySelector('i');
                    fetch(`/wishlist/check?product_id=${wishlistBtn.dataset.productId}&variant_id=${variantId}`, {
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        wIcon.className = data.in_wishlist
                            ? 'ph-fill ph-heart text-danger-600 text-2xl'
                            : 'ph ph-heart text-gray-400 text-2xl';
                    });
                }

                const control = card.querySelector('.cart-control');
                if (control) {
                    control.dataset.variantId = variantId;
                    if (control.querySelector('.qty-box')) {
                        control.innerHTML = `
                            <button type="button"
                                class="add-to-cart-btn product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-10 px-20 rounded-pill flex-center gap-8 fw-semibold w-100 border-0">
                                Add To Cart <i class="ph ph-shopping-cart"></i>
                            </button>`;
                        attachAddToCartEvent(control);
                    }
                }
            });
        });
    });
}

document.addEventListener('click', function () {
    document.querySelectorAll('.custom-variant-select.open').forEach(s => s.classList.remove('open'));
});

initVariantDropdowns();

// Wishlist toggle for Main Product
const detailWishlistBtn = document.querySelector('.wishlist-btn');
if (detailWishlistBtn) {
    detailWishlistBtn.addEventListener('click', function () {
        const productId = this.dataset.productId;
        const variantId = this.dataset.variantId;
        const icon      = this.querySelector('i');
        const self      = this;

        if (icon.classList.contains('ph-fill')) {
            doWishlistToggle(self, productId, variantId, icon);
            return;
        }

        fetch(`/wishlist/check-product?product_id=${productId}&exclude_variant_id=${variantId}`, {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.has_other_variant) {
                Swal.fire({
                    icon: 'question',
                    title: 'Already in Wishlist',
                    html: `You already have <strong>${data.existing_variant}</strong> in your wishlist.<br>Do you want to add <strong>${data.new_variant}</strong> also?`,
                    showCancelButton: true,
                    confirmButtonColor: '#1c799b',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Add it!',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) doWishlistToggle(self, productId, variantId, icon);
                });
            } else {
                doWishlistToggle(self, productId, variantId, icon);
            }
        });
    });
}

// Wishlist toggle for Related Products
document.querySelectorAll('.wishlist-btn-related').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const productId = this.dataset.productId;
        const variantId = this.dataset.variantId;
        const icon      = this.querySelector('i');
        const self      = this;

        if (icon.classList.contains('ph-fill')) {
            doWishlistToggle(self, productId, variantId, icon);
            return;
        }

        fetch(`/wishlist/check-product?product_id=${productId}&exclude_variant_id=${variantId}`, {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.has_other_variant) {
                Swal.fire({
                    icon: 'question',
                    title: 'Already in Wishlist',
                    html: `You already have <strong>${data.existing_variant}</strong> in your wishlist.<br>Do you want to add <strong>${data.new_variant}</strong> also?`,
                    showCancelButton: true,
                    confirmButtonColor: '#1c799b',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Add it!',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) doWishlistToggle(self, productId, variantId, icon);
                });
            } else {
                doWishlistToggle(self, productId, variantId, icon);
            }
        });
    });
});

function doWishlistToggle(btn, productId, variantId, icon) {
    fetch("{{ route('wishlist.toggle') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId, variant_id: variantId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'added') {
            icon.classList.remove('ph-heart', 'text-gray-400');
            icon.classList.add('ph-fill', 'ph-heart', 'text-danger-600');
        } else {
            icon.classList.remove('ph-fill', 'text-danger-600');
            icon.classList.add('ph-heart', 'text-gray-400');
        }
        Swal.fire({
            icon: data.status === 'added' ? 'success' : 'info',
            title: data.status === 'added' ? 'Added to Wishlist!' : 'Removed from Wishlist',
            toast: true, position: 'top-end', showConfirmButton: false, timer: 1800, timerProgressBar: true
        });
    });
}

// Initial attach
if (document.querySelector('.detail-cart-control .qty-box')) {
    attachDetailQtyEvents();
} else {
    attachDetailAddToCart();
}

// Click handler for thumbnail navigation
document.querySelectorAll('.product-thumb-item').forEach(function (thumb, index) {
    thumb.addEventListener('click', function () {
        document.querySelectorAll('.product-thumb-item').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        if (typeof $ !== 'undefined' && $('.product-details__thumb-slider').hasClass('slick-initialized')) {
            $('.product-details__thumb-slider').slick('slickGoTo', index);
        } else {
            const targetImg = document.getElementById('main-preview-img-' + index);
            if (targetImg) {
                document.querySelectorAll('.product-details__thumb img').forEach(img => img.parentElement.parentElement.style.display = 'none');
                targetImg.parentElement.parentElement.style.display = 'block';
            }
        }
    });
});

// Live offer countdown ticker
(function() {
    let days = 2, hours = 18, mins = 42, secs = 15;
    const daysEl = document.querySelector('#countdown11 .days');
    const hoursEl = document.querySelector('#countdown11 .hours');
    const minsEl = document.querySelector('#countdown11 .minutes');
    const secsEl = document.querySelector('#countdown11 .seconds');

    if (secsEl) {
        setInterval(function() {
            secs--;
            if (secs < 0) {
                secs = 59;
                mins--;
                if (mins < 0) {
                    mins = 59;
                    hours--;
                    if (hours < 0) {
                        hours = 23;
                        days = Math.max(0, days - 1);
                    }
                }
            }
            if (daysEl) daysEl.textContent = days;
            if (hoursEl) hoursEl.textContent = hours < 10 ? '0' + hours : hours;
            if (minsEl) minsEl.textContent = mins < 10 ? '0' + mins : mins;
            if (secsEl) secsEl.textContent = secs < 10 ? '0' + secs : secs;
        }, 1000);
    }
})();

// Interactive Star Rating in Review Form
document.querySelectorAll('#star-rating-select i').forEach(function(star, index) {
    star.addEventListener('click', function() {
        const rating = index + 1;
        document.querySelectorAll('#star-rating-select i').forEach((s, i) => {
            if (i < rating) {
                s.className = 'ph-fill ph-star text-warning-600';
            } else {
                s.className = 'ph ph-star text-gray-300';
            }
        });
    });
});

// Carousel Prev/Next Arrow Handlers
document.addEventListener('click', function(e) {
    const prevBtn = e.target.closest('#new-arrival-prev');
    if (prevBtn) {
        e.preventDefault();
        if (window.jQuery && window.jQuery('.new-arrival__slider').hasClass('slick-initialized')) {
            window.jQuery('.new-arrival__slider').slick('slickPrev');
        }
    }
    const nextBtn = e.target.closest('#new-arrival-next');
    if (nextBtn) {
        e.preventDefault();
        if (window.jQuery && window.jQuery('.new-arrival__slider').hasClass('slick-initialized')) {
            window.jQuery('.new-arrival__slider').slick('slickNext');
        }
    }
});
</script>

@endsection