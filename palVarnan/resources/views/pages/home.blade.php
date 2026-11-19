@extends('layouts.app')
@section('main')


    <!-- ============================ Banner Section start =============================== -->
<div class="banner">
    <div class="container container-lg">
        <div class="banner-item rounded-24 overflow-hidden position-relative arrow-center" style="min-height: 350px; height: 450px;">
            <a href="#featureSection" class="scroll-down w-84 h-84 text-center flex-center bg-main-600 rounded-circle border border-5 text-white border-white position-absolute start-50 translate-middle-x bottom-0 hover-bg-main-800" style="z-index: 10;">
                <span class="icon line-height-0"><i class="ph ph-caret-double-down"></i></span> 
            </a>

            @if($banners->count() > 1)
            <div class="flex-align">
                <button type="button" id="banner-prev" class="slick-prev slick-arrow flex-center rounded-circle box-shadow-4xl bg-white text-xl hover-bg-main-600 hover-text-white transition-1">
                    <i class="ph ph-caret-left"></i>
                </button>
                <button type="button" id="banner-next" class="slick-next slick-arrow flex-center rounded-circle box-shadow-4xl bg-white text-xl hover-bg-main-600 hover-text-white transition-1">
                    <i class="ph ph-caret-right"></i>
                </button>
            </div>
            @endif

            <div class="banner-slider">
                @forelse($banners as $banner)
                    <div class="banner-slider__item">
                        <div class="banner-slider__inner flex-between position-relative">
                            <div class="banner-item__content">
                                <span class="fw-semibold text-success-600 text-capitalize mb-8 animate-left-right animation-delay-08">
                                    {{ $banner->badge_text }}
                                </span>
                                <h2 class="banner-item__title max-w-700 mb-30 animate-left-right animation-delay-1">
                                    {{ $banner->main_title }}
                                    <span class="text-main-600">{{ $banner->highlight_text }}</span>
                                </h2>
                                <div class="d-flex align-items-center gap-16 animate-left-right animation-delay-12">
                                    <a href="/{{ $banner->link_url }}" class="btn btn-main d-inline-flex align-items-center rounded-pill gap-8">
                                        Explore Shop
                                        <span class="icon text-xl d-flex"><i class="ph ph-shopping-cart-simple"></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="banner-item__thumb animate-scale animation-delay-12">
                                {{-- Use only ONE image path - pick whichever works in your setup --}}
                                <img src="{{ env('MAIN_URL') }}images/{{ $banner->bg_image }}" alt="{{ $banner->main_title }}">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="banner-slider__item">
                        <div class="banner-slider__inner flex-between position-relative">
                            <div class="banner-item__content">
                                <span class="fw-semibold text-success-600 text-capitalize mb-8 animate-left-right animation-delay-08">
                                    Save up to 50% off on your first order
                                </span>
                                <h2 class="banner-item__title max-w-700 mb-30 animate-left-right animation-delay-1">
                                    Daily Grocery Order and Get
                                    <span class="text-main-600">Express</span> Delivery
                                </h2>
                                <div class="d-flex align-items-center gap-16 animate-left-right animation-delay-12">
                                    <a href="/products" class="btn btn-main d-inline-flex align-items-center rounded-pill gap-8">
                                        Explore Shop
                                        <span class="icon text-xl d-flex"><i class="ph ph-shopping-cart-simple"></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="banner-item__thumb animate-scale animation-delay-12">
                                <img src="assets/images/thumbs/banner-img1.png" alt="">
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
<!-- ============================ Banner Section End =============================== -->

    <!-- ============================ Feature Section start =============================== -->
<div class="feature" id="featureSection">
    <div class="container container-lg">
        <div class="d-flex align-items-center justify-content-between mb-24 flex-wrap gap-12 pt-24">
            <div>
                <h5 class="mb-0 fw-bold text-heading">Popular Categories</h5>
                <span class="text-neutral-500 text-xs">Explore fresh farm selections and pantry essentials</span>
            </div>
            <a href="{{ url('/categories') }}" class="btn btn-sm btn-outline-main rounded-pill px-16 d-flex align-items-center gap-6 fw-semibold text-xs">
                <span>View All Categories</span>
                <i class="ph-bold ph-arrow-right"></i>
            </a>
        </div>

        <div class="position-relative arrow-center gradient-shadow">
            <div class="flex-align">
                <button type="button" id="feature-item-wrapper-prev" class="slick-prev slick-arrow flex-center rounded-circle bg-white text-xl hover-bg-main-600 hover-text-white transition-1">
                    <i class="ph ph-caret-left"></i>
                </button>
                <button type="button" id="feature-item-wrapper-next" class="slick-next slick-arrow flex-center rounded-circle bg-white text-xl hover-bg-main-600 hover-text-white transition-1">
                    <i class="ph ph-caret-right"></i>
                </button>
            </div>
            <div class="feature-item-wrapper">

            @foreach($categories as $category)
                <div class="feature-item text-center wow bounceIn" data-aos="fade-up" data-aos-duration="400">
                    <div class="feature-item__thumb rounded-circle">
                        <a href="/products?category={{ \Illuminate\Support\Str::slug($category->category_name) }}" class="w-100 h-100 flex-center" title="{{ $category->category_name }}">
                            <img src="{{ env('MAIN_URL') }}images/{{ $category->category_image }}" alt="{{ $category->category_name }}">
                        </a>
                    </div>
                    <div class="feature-item__content mt-16">
                        <h6 class="text-lg mb-8"><a href="/products?category={{ \Illuminate\Support\Str::slug($category->category_name) }}" class="text-inherit">{{ $category->category_name }}</a></h6>
                    </div>
                </div>
            @endforeach

                

            </div>
        </div>
    </div>
</div>
<!-- ============================ Feature Section End =============================== -->

    <!-- ======================== promotional banner Start ============================== -->
<section class="promotional-banner pt-80">
    <div class="container container-lg">
        <div class="row gy-4">
            <div class="col-xl-3 col-sm-6 col-6 wow bounceIn" data-aos="fade-up" data-aos-duration="400">
                <div class="promotional-banner-item position-relative rounded-24 overflow-hidden z-1 py-52 ps-40 pe-24 h-100">
                    <img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/promotional-banner-img1.png" alt="" class="position-absolute inset-block-start-0 inset-inline-start-0 w-100 h-100 object-fit-cover z-n1">
                    <div class="promotional-banner-item__content">
                        <h6 class="promotional-banner-item__title text-2xl max-w-184">Everyday Fresh Meat</h6>
                        <div class="d-flex align-items-end gap-8">
                            <span class="text-heading fst-italic text-sm">Starting at</span>
                            <h6 class="text-danger-600 mb-0 text-xl"> ₹60.99</h6>
                        </div>
                        <a href="/products" class="btn btn-main d-inline-flex align-items-center rounded-pill gap-8 mt-24">
                            Shop Now
                            <span class="icon text-xl d-flex"><i class="ph ph-arrow-right"></i></span> 
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-6 wow bounceIn" data-aos="fade-up" data-aos-duration="600">
                <div class="promotional-banner-item position-relative rounded-24 overflow-hidden z-1 py-52 ps-40 pe-24 h-100">
                    <img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/promotional-banner-img2.png" alt="" class="position-absolute inset-block-start-0 inset-inline-start-0 w-100 h-100 object-fit-cover z-n1">
                    <div class="promotional-banner-item__content">
                        <h6 class="promotional-banner-item__title text-2xl max-w-184">Daily Fresh Vegetables</h6>
                        <div class="d-flex align-items-end gap-8">
                            <span class="text-heading fst-italic text-sm">Starting at</span>
                            <h6 class="text-danger-600 mb-0 text-xl"> ₹60.99</h6>
                        </div>
                        <a href="/products" class="btn btn-main d-inline-flex align-items-center rounded-pill gap-8 mt-24">
                            Shop Now
                            <span class="icon text-xl d-flex"><i class="ph ph-arrow-right"></i></span> 
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-6 wow bounceIn" data-aos="fade-up" data-aos-duration="800">
                <div class="promotional-banner-item position-relative rounded-24 overflow-hidden z-1 py-52 ps-40 pe-24 h-100">
                    <img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/promotional-banner-img3.png" alt="" class="position-absolute inset-block-start-0 inset-inline-start-0 w-100 h-100 object-fit-cover z-n1">
                    <div class="promotional-banner-item__content">
                        <h6 class="promotional-banner-item__title text-2xl max-w-184">Everyday Fresh Milk</h6>
                        <div class="d-flex align-items-end gap-8">
                            <span class="text-heading fst-italic text-sm">Starting at</span>
                            <h6 class="text-danger-600 mb-0 text-xl"> ₹60.99</h6>
                        </div>
                        <a href="/products" class="btn btn-main d-inline-flex align-items-center rounded-pill gap-8 mt-24">
                            Shop Now
                            <span class="icon text-xl d-flex"><i class="ph ph-arrow-right"></i></span> 
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-6 wow bounceIn" data-aos="fade-up" data-aos-duration="1000">
                <div class="promotional-banner-item position-relative rounded-24 overflow-hidden z-1 py-52 ps-40 pe-24 h-100">
                    <img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/promotional-banner-img4.png" alt="" class="position-absolute inset-block-start-0 inset-inline-start-0 w-100 h-100 object-fit-cover z-n1">
                    <div class="promotional-banner-item__content">
                        <h6 class="promotional-banner-item__title text-2xl max-w-184">Everyday Fresh Fruits</h6>
                        <a href="/products" class="btn btn-main d-inline-flex align-items-center rounded-pill gap-8 mt-24">
                            Shop Now
                            <span class="icon text-xl d-flex"><i class="ph ph-arrow-right"></i></span> 
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ======================== promotional banner End ============================== -->
    

    <!-- ========================= Recommended Start ================================ -->
<section class="recommended overflow-hidden pt-80">
    <div class="container container-lg">
        <div class="section-heading flex-between flex-wrap gap-16">
            <h5 class="mb-0 wow fadeInLeft">Recommended for you</h5>
            <ul class="nav common-tab nav-pills wow fadeInRight flex-nowrap overflow-x-auto pb-4" id="recommendedCatPills" role="tablist">
                <li class="nav-item flex-shrink-0" role="presentation">
                  <button class="nav-link fw-medium text-sm active filter-rec-cat-btn" data-cat-id="all" type="button">All</button>
                </li>
                @php
                    $recCatIds = $featuredProducts->pluck('category_id')->filter()->unique();
                    $recCategories = $categories->whereIn('id', $recCatIds);
                    if ($recCategories->isEmpty()) {
                        $recCategories = $categories->take(8);
                    }
                @endphp
                @foreach($recCategories as $rcat)
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link fw-medium text-sm filter-rec-cat-btn" data-cat-id="{{ $rcat->id }}" type="button">{{ $rcat->category_name }}</button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="tab-content" id="pills-tabContent">

            <!-- ALL TAB -->
            <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab" tabindex="0">
                <div class="row g-16">
                    @forelse($featuredProducts as $product)
                        @php
                            $displayVariants = $product->variants;
                            $variant = $displayVariants->first();
                            $offerPrice = $variant->offer_price ?? $product->product_price ?? 0;
                            $mrpPrice = $variant->mrp_price ?? $variant->product_mrp_price ?? 0;
                            $imgSrc = $product->product_image
                                ? (Str::startsWith($product->product_image, 'http') ? $product->product_image : (Str::startsWith($product->product_image, 'assets/') ? asset($product->product_image) : asset('storage/' . $product->product_image)))
                                : asset('assets/images/thumbs/product-img1.png');
                            $inCart = $variant ? in_array($variant->id, $cartProductIds ?? []) : false;
                            $cartItem = ($variant && isset($cartItems)) ? $cartItems->where('product_varient_id', $variant->id)->first() : null;
                            $isOutOfStock = ($variant ? (int)($variant->product_qty ?? 0) : 0) <= 0;
                        @endphp
                        <div class="col-xxl-3 col-lg-4 col-sm-6 col-6 recommended-card-col" data-cat-id="{{ $product->category_id }}" data-aos="fade-up">
                            <div class="product-card h-100 p-12 p-sm-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item bg-white d-flex flex-column justify-content-between" style="box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                                <div class="w-100">
                                    <a href="/product/{{ $product->slug }}" class="product-card__thumb w-100 flex-center overflow-hidden rounded-12 bg-gray-50 mb-12" style="height: 160px;">
                                        <img src="{{ $imgSrc }}" alt="{{ $product->product_name }}" class="w-100 h-100 object-fit-contain p-8" onerror="this.src='/assets/images/logo/logo.png'">
                                    </a>
                                    <div class="product-card__content w-100">
                                        <span class="text-xs text-main-600 fw-semibold d-block mb-4 text-truncate">{{ $product->category->category_name ?? 'Farm Fresh' }}</span>
                                        <h6 class="title text-sm text-sm-base fw-semibold mb-8" style="min-height: 38px;">
                                            <a href="/product/{{ $product->slug }}" class="link text-line-2 text-inherit">{{ $product->product_name }}</a>
                                        </h6>

                                        <!-- Custom Variant Dropdown / Slot -->
                                        <div class="variant-slot w-100 my-8">
                                            @if($displayVariants && $displayVariants->count() > 0)
                                                <div class="variant-dropdown-wrap w-100">
                                                    <div class="custom-variant-select" data-product-id="{{ $product->id }}">
                                                        @php
                                                            $first = $displayVariants->first();
                                                            if ($product->is_gift) {
                                                                $firstLabelParts = [];
                                                                if ($first->size) $firstLabelParts[] = $first->size->size;
                                                                if ($first->color) $firstLabelParts[] = $first->color->color;
                                                                $firstLabel = !empty($firstLabelParts) ? implode(' • ', $firstLabelParts) : ($first->varient_name ?: 'Standard');
                                                            } else {
                                                                $firstLabel = ($first->value ?? '') . ($first->unit->short_name ?? '');
                                                            }
                                                        @endphp
                                                        <div class="variant-selected-display" role="button" tabindex="0"
                                                            aria-haspopup="listbox" aria-expanded="false" aria-label="Select variant">
                                                            <span class="variant-label text-truncate">{{ $firstLabel }}</span>
                                                            <span class="variant-price-tag">{{ $currencySymbol ?? '₹' }}{{ $first->offer_price }}</span>
                                                            <i class="ph ph-caret-down variant-arrow" aria-hidden="true"></i>
                                                        </div>
                                                        <ul class="variant-options-list" role="listbox" aria-label="Available variants">
                                                            @foreach($displayVariants as $v)
                                                                @php
                                                                    if ($product->is_gift) {
                                                                        $vLabelParts = [];
                                                                        if ($v->size) $vLabelParts[] = $v->size->size;
                                                                        if ($v->color) $vLabelParts[] = $v->color->color;
                                                                        $vLabel = !empty($vLabelParts) ? implode(' • ', $vLabelParts) : ($v->varient_name ?: 'Standard');
                                                                    } else {
                                                                        $vLabel = ($v->value ?? '') . ($v->unit->short_name ?? '');
                                                                    }
                                                                @endphp
                                                                <li class="variant-option {{ $loop->first ? 'selected' : '' }}"
                                                                    role="option" tabindex="0"
                                                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                                                    data-variant-id="{{ $v->id }}"
                                                                    data-offer="{{ $v->offer_price }}"
                                                                    data-mrp="{{ $v->mrp_price ?? $v->product_mrp_price ?? 0 }}"
                                                                    data-stock="{{ (int)($v->product_qty ?? 0) }}">
                                                                    <span class="opt-size">{{ $vLabel }}@if(($v->product_qty ?? 0) <= 0) <small class="text-danger-600 fw-bold ms-1" style="font-size:10px;">(Out)</small>@endif</span>
                                                                    <span class="opt-price">
                                                                        @if($v->mrp_price && $v->mrp_price > $v->offer_price)
                                                                            <span class="opt-mrp">{{ $currencySymbol ?? '₹' }}{{ $v->mrp_price }}</span>
                                                                        @endif
                                                                        <span class="opt-offer">{{ $currencySymbol ?? '₹' }}{{ $v->offer_price }}</span>
                                                                    </span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="variant-placeholder-pill text-xs py-6 px-12 rounded-8 bg-gray-50 border border-gray-100 text-neutral-500 d-inline-flex align-items-center gap-6">
                                                    <i class="ph-bold {{ $product->is_gift ? 'ph-gift' : 'ph-package' }} text-main-600"></i>
                                                    <span>{{ $product->is_gift ? 'Gift Item' : 'Standard Pack' }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="product-card__content mt-12 w-100">
                                    <!-- Price Section -->
                                    <div class="product-card__price d-flex align-items-center justify-content-center flex-wrap gap-8 my-8 text-center w-100">
                                        <div class="d-flex align-items-baseline justify-content-center gap-6">
                                            <span class="text-heading text-base text-sm-lg fw-bold text-main-600 main-price-text">
                                                {{ $currencySymbol ?? '₹' }}{{ number_format($offerPrice, 2) }}
                                            </span>
                                            @if($variant && $mrpPrice > $offerPrice)
                                                <span class="text-neutral-400 text-xs fw-medium text-decoration-line-through mrp-price-text">
                                                    {{ $currencySymbol ?? '₹' }}{{ number_format($mrpPrice, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($variant && $mrpPrice > $offerPrice)
                                            <span class="badge bg-success-50 text-success-700 text-xs px-6 py-2 rounded-6 fw-bold save-badge">
                                                Save {{ $currencySymbol ?? '₹' }}{{ number_format($mrpPrice - $offerPrice, 2) }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Cart Control Button -->
                                    <div class="cart-control mt-8 w-100" data-product-id="{{ $product->id }}" data-variant-id="{{ $variant->id ?? '' }}">
                                        @if($isOutOfStock)
                                            <button type="button" class="btn btn-outline-neutral-300 py-8 py-sm-10 px-12 px-sm-16 rounded-pill w-100 justify-content-center text-xs fw-semibold text-neutral-400" disabled>
                                                Out of Stock
                                            </button>
                                        @elseif($inCart)
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
                                                class="add-to-cart-btn product-card__cart btn py-8 py-sm-10 px-12 px-sm-16 rounded-pill flex-align gap-6 w-100 justify-content-center text-xs fw-semibold">
                                                <span>Add To Cart</span> <i class="ph-bold ph-shopping-cart-simple text-sm"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-40">
                            <p class="text-gray-500">No products available in this region currently.</p>
                        </div>
                    @endforelse
                    <div class="col-12 text-center py-48 d-none" id="noCategoryProducts">
                        <div class="d-inline-flex flex-column align-items-center">
                            <span class="w-56 h-56 rounded-circle bg-neutral-50 flex-center text-2xl text-neutral-400 mb-12">
                                <i class="ph ph-package"></i>
                            </span>
                            <h6 class="text-neutral-700 text-base fw-semibold mb-4">No Products Found In This Category</h6>
                            <p class="text-neutral-500 text-sm mb-16">Explore our full catalog to discover all fresh arrivals.</p>
                            <a href="/products" class="btn btn-main btn-sm rounded-pill px-20">Browse All Products</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= Recommended End ================================ -->


<style>
/* Recommended For You Section - Uniform Cards & Add To Cart Button Style */
.recommended .product-card {
    height: 100% !important;
    width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    align-items: stretch !important;
    background: #ffffff;
    border-radius: 16px;
    padding: 14px 12px !important;
    border: 1px solid #edf2f7;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.recommended .product-card > div {
    width: 100% !important;
}

.recommended .product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -6px rgba(2, 105, 102, 0.12), 0 4px 8px -2px rgba(0, 0, 0, 0.04);
    border-color: rgba(2, 105, 102, 0.35) !important;
}

.recommended .product-card__thumb {
    height: 160px;
    width: 100% !important;
    border-radius: 12px;
    background: #f8fafc;
    overflow: hidden;
    position: relative;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-bottom: 10px;
}

.recommended .product-card__thumb img {
    max-height: 140px;
    max-width: 100%;
    width: auto;
    object-fit: contain;
    border-radius: 10px;
    transition: transform 0.35s ease;
}

.recommended .product-card:hover .product-card__thumb img {
    transform: scale(1.06);
}

.recommended .product-card > .product-card__content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    justify-content: space-between;
    padding: 0 !important;
}

.recommended .product-card .title {
    min-height: 42px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    font-size: 14px;
    line-height: 1.35;
    margin-top: 4px !important;
    margin-bottom: 8px !important;
}

.recommended .product-card__price {
    white-space: nowrap;
    margin-bottom: 8px !important;
}

.recommended .product-card__cart {
    background: #e6f5f4 !important;
    color: #026966 !important;
    border: 1px solid rgba(2, 105, 102, 0.2) !important;
    font-weight: 600 !important;
    font-size: 13.5px !important;
    padding: 9px 12px !important;
    white-space: nowrap !important;
    border-radius: 50rem !important;
    transition: all 0.2s ease;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    margin-top: auto !important;
    text-decoration: none !important;
}

.recommended .product-card__cart:hover {
    background: #026966 !important;
    color: #ffffff !important;
    border-color: #026966 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(2, 105, 102, 0.25);
}

.recommended .product-card__cart i {
    font-size: 16px;
}

@media (max-width: 575px) {
    .recommended .product-card__cart {
        font-size: 12px !important;
        padding: 8px 6px !important;
        gap: 4px !important;
    }
    .recommended .product-card__cart i {
        font-size: 14px !important;
    }
}

/* Custom Variant Dropdown in Home Page */
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
    min-height: 36px;
    gap: 6px;
    padding: 6px 10px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    background: #f8fafc;
    font-size: 12.5px;
    transition: all 0.18s ease;
}
.variant-selected-display:hover {
    border-color: #026966;
    background: #f0fdfa;
}
.custom-variant-select.open .variant-selected-display {
    border-color: #026966;
    background: #f0fdfa;
    box-shadow: 0 0 0 3px rgba(2, 105, 102, 0.12);
}
.custom-variant-select.open {
    z-index: 50;
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
    color: #026966;
    font-size: 11.5px;
    background: #e6f5f4;
    padding: 2px 6px;
    border-radius: 6px;
}
.variant-arrow {
    flex: 0 0 auto;
    font-size: 12px;
    color: #64748b;
    transition: transform 0.2s ease;
}
.custom-variant-select.open .variant-arrow {
    transform: rotate(180deg);
}
.variant-options-list {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 12px 30px -8px rgba(15, 23, 42, 0.2);
    z-index: 1050;
    list-style: none;
    margin: 0;
    padding: 4px;
    max-height: 200px;
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
    gap: 6px;
    padding: 7px 8px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.15s ease;
}
.variant-option:hover,
.variant-option.selected {
    background: #f0fdfa;
    color: #026966;
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
    gap: 4px;
}
.variant-option .opt-mrp {
    text-decoration: line-through;
    color: #94a3b8;
    font-size: 10px;
}
.variant-option .opt-offer {
    color: #026966;
    font-weight: 700;
}
.variant-placeholder-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 5px 12px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

/* Quantity Box in Home Page */
.qty-box {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    border-radius: 50rem !important;
    background: #e6f5f4 !important;
    border: 1.5px solid #026966 !important;
    padding: 2px 6px !important;
    height: 38px !important;
    transition: all 0.2s ease;
    width: 100% !important;
}
.qty-box .quantity__minus,
.qty-box .quantity__plus {
    width: 28px !important;
    height: 28px !important;
    border-radius: 50% !important;
    border: none !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 15px !important;
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
    font-size: 13px !important;
    color: #026966 !important;
    padding: 0 !important;
}
.qty-box-label {
    font-size: 11px;
    font-weight: 700;
    color: #026966;
    display: flex;
    align-items: center;
    gap: 3px;
}

/* Hot Deals Slider Card Equal Height & Bottom CTA Alignment */
.hot-deals-slider .slick-track {
    display: flex !important;
}

.hot-deals-slider .slick-slide {
    height: inherit !important;
    display: flex !important;
}

.hot-deals-slider .slick-slide > div {
    display: flex;
    width: 100%;
    height: 100%;
}

.hot-deals-slider .product-card {
    height: 100% !important;
    width: 100%;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    background: #ffffff;
    border-radius: 16px;
    padding: 16px !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.hot-deals-slider .product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -6px rgba(2, 105, 102, 0.12), 0 4px 8px -2px rgba(0, 0, 0, 0.04);
    border-color: rgba(2, 105, 102, 0.35) !important;
}

.hot-deals-slider .product-card__thumb {
    height: 180px;
    width: 100%;
    border-radius: 12px;
    background: #f8fafc;
    overflow: hidden;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}

.hot-deals-slider .product-card__thumb img {
    max-height: 155px;
    width: 100%;
    object-fit: cover;
    border-radius: 10px;
    transition: transform 0.35s ease;
}

.hot-deals-slider .product-card:hover .product-card__thumb img {
    transform: scale(1.06);
}

.hot-deals-slider .product-card__content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    justify-content: space-between;
}

.hot-deals-slider .product-card .title {
    min-height: 44px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    font-size: 15px;
    line-height: 1.4;
}

.hot-deals-slider .product-card__price {
    white-space: nowrap;
}

.hot-deals-slider .product-card__cart {
    background: #e6f5f4 !important;
    color: #026966 !important;
    border: 1px solid rgba(2, 105, 102, 0.2) !important;
    font-weight: 600;
    transition: all 0.2s ease;
}

.hot-deals-slider .product-card__cart:hover {
    background: #026966 !important;
    color: #ffffff !important;
    border-color: #026966 !important;
    transform: translateY(-1px);
}
</style>

    <!-- ========================= hot-deals Start ================================ -->
<section class="hot-deals pt-80 overflow-hidden">
    <div class="container container-lg">
        <div class="section-heading">
            <div class="flex-between flex-wrap gap-8">
                <h5 class="mb-0 wow fadeInLeft">Hot Deals Today</h5>
                <div class="flex-align gap-16 wow fadeInRight">
                    <a href="/products" class="text-sm fw-medium text-gray-700 hover-text-main-600 hover-text-decoration-underline">View All Deals</a>
                    <div class="flex-align gap-8">
                        <button type="button" id="deals-prev" class="slick-prev slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1">
                            <i class="ph ph-caret-left"></i>
                        </button>
                        <button type="button" id="deals-next" class="slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1">
                            <i class="ph ph-caret-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-12">
            <div class="col-md-4" data-aos="zoom-in">
                <div class="hot-deals position-relative rounded-16 bg-main-600 overflow-hidden ps-40 pe-24 pt-80 pb-120 z-1">
                    <img src="assets/images/shape/offer-shape.png" alt="" class="position-absolute inset-block-start-0 inset-inline-start-0 z-n1 w-100 h-100 opacity-6">

                    <img src="assets/images/thumbs/basket-img.png" alt="Basket Thumb" class="position-absolute inset-inline-end-0 inset-block-end-0">
                    
                    <span class="text-primary-600 bg-yellow text-heading py-4 px-12 rounded-4 text-sm fw-medium">Fresh Grocery</span>
                    <div class="">
                        <h5 class="text-white mb-8 mt-12">Deals of the Day</h5>
                        <p class="fw-semibold text-success-600">Save up to 50% off on your first order</p>
                        <div class="countdown mt-24 mb-24" id="countdown4">
                            <ul class="countdown-list d-flex align-items-center flex-wrap">
                                <li class="countdown-list__item py-8 px-12 text-heading flex-align gap-4 text-sm fw-medium colon-white"><span class="days"></span> D</li>
                                <li class="countdown-list__item py-8 px-12 text-heading flex-align gap-4 text-sm fw-medium colon-white"><span class="hours"></span> H</li>
                                <li class="countdown-list__item py-8 px-12 text-heading flex-align gap-4 text-sm fw-medium colon-white"><span class="minutes"></span> M</li>
                                <li class="countdown-list__item py-8 px-12 text-heading flex-align gap-4 text-sm fw-medium colon-white"><span class="seconds"></span> S</li>
                            </ul>
                        </div>
                        <a href="/products" class="mt-16 btn bg-white hover-text-white hover-bg-main-800 text-main-600 fw-medium d-inline-flex align-items-center rounded-pill gap-8" tabindex="0">
                            Explore Shop
                            <span class="icon text-xl d-flex"><i class="ph-bold ph-shopping-cart"></i></span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="hot-deals-slider arrow-style-two">

                    {{-- Product 1: Broccoli --}}
                    <div class="" data-aos="fade-up" data-aos-duration="200">
                        <div class="product-card h-100 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden position-relative">
                                <img src="https://images.unsplash.com/photo-1459411621453-7b03977f4bfc?w=300&h=300&fit=crop&auto=format" alt="Broccoli">
                                <span class="position-absolute top-8 start-8 badge bg-danger-600 text-white text-xs px-8 py-4 rounded-8 fw-bold">44% OFF</span>
                            </a>
                            <div class="product-card__content mt-12">
                                <div>
                                    <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                        <span class="text-heading text-md fw-bold">₹45.00 <span class="text-gray-500 text-xs fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-sm fw-medium text-decoration-line-through text-nowrap">₹80.00</span>
                                    </div>
                                    <div class="flex-align gap-6 mb-8">
                                        <span class="text-xs fw-bold text-gray-700">4.8</span>
                                        <span class="text-14 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-gray-500">(17k)</span>
                                    </div>
                                    <h6 class="title text-base fw-semibold mb-12">
                                        <a href="/products" class="link text-line-2">Fresh Broccoli Florets (500g)</a>
                                    </h6>
                                    <div class="mb-16">
                                        <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-main-600 rounded-pill" style="width: 35%"></div>
                                        </div>
                                        <span class="text-gray-900 text-xs fw-medium mt-6 d-block">Sold: 18/35</span>
                                    </div>
                                </div>
                                <a href="/cart" class="product-card__cart btn py-10 px-20 rounded-pill flex-align gap-8 w-100 justify-content-center text-sm fw-semibold">
                                    Add To Cart <i class="ph-bold ph-shopping-cart-simple"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Product 2: Tomatoes --}}
                    <div class="" data-aos="fade-up" data-aos-duration="400">
                        <div class="product-card h-100 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden position-relative">
                                <img src="https://images.unsplash.com/photo-1546094096-0df4bcaaa337?w=300&h=300&fit=crop&auto=format" alt="Tomatoes">
                                <span class="position-absolute top-8 start-8 badge bg-danger-600 text-white text-xs px-8 py-4 rounded-8 fw-bold">38% OFF</span>
                            </a>
                            <div class="product-card__content mt-12">
                                <div>
                                    <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                        <span class="text-heading text-md fw-bold">₹40.00 <span class="text-gray-500 text-xs fw-normal">/Kg</span></span>
                                        <span class="text-gray-400 text-sm fw-medium text-decoration-line-through text-nowrap">₹65.00</span>
                                    </div>
                                    <div class="flex-align gap-6 mb-8">
                                        <span class="text-xs fw-bold text-gray-700">4.7</span>
                                        <span class="text-14 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-gray-500">(12k)</span>
                                    </div>
                                    <h6 class="title text-base fw-semibold mb-12">
                                        <a href="/products" class="link text-line-2">Farm Fresh Red Tomatoes (1 Kg)</a>
                                    </h6>
                                    <div class="mb-16">
                                        <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-main-600 rounded-pill" style="width: 60%"></div>
                                        </div>
                                        <span class="text-gray-900 text-xs fw-medium mt-6 d-block">Sold: 24/40</span>
                                    </div>
                                </div>
                                <a href="/cart" class="product-card__cart btn py-10 px-20 rounded-pill flex-align gap-8 w-100 justify-content-center text-sm fw-semibold">
                                    Add To Cart <i class="ph-bold ph-shopping-cart-simple"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Product 3: Eggs --}}
                    <div class="" data-aos="fade-up" data-aos-duration="600">
                        <div class="product-card h-100 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden position-relative">
                                <img src="https://images.unsplash.com/photo-1491524062933-cb0289261700?w=300&h=300&fit=crop&auto=format" alt="Eggs">
                                <span class="position-absolute top-8 start-8 badge bg-danger-600 text-white text-xs px-8 py-4 rounded-8 fw-bold">20% OFF</span>
                            </a>
                            <div class="product-card__content mt-12">
                                <div>
                                    <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                        <span class="text-heading text-md fw-bold">₹72.00 <span class="text-gray-500 text-xs fw-normal">/Dozen</span></span>
                                        <span class="text-gray-400 text-sm fw-medium text-decoration-line-through text-nowrap">₹90.00</span>
                                    </div>
                                    <div class="flex-align gap-6 mb-8">
                                        <span class="text-xs fw-bold text-gray-700">4.9</span>
                                        <span class="text-14 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-gray-500">(20k)</span>
                                    </div>
                                    <h6 class="title text-base fw-semibold mb-12">
                                        <a href="/products" class="link text-line-2">Farm Fresh White Eggs (1 Dozen)</a>
                                    </h6>
                                    <div class="mb-16">
                                        <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-main-600 rounded-pill" style="width: 70%"></div>
                                        </div>
                                        <span class="text-gray-900 text-xs fw-medium mt-6 d-block">Sold: 42/60</span>
                                    </div>
                                </div>
                                <a href="/cart" class="product-card__cart btn py-10 px-20 rounded-pill flex-align gap-8 w-100 justify-content-center text-sm fw-semibold">
                                    Add To Cart <i class="ph-bold ph-shopping-cart-simple"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Product 4: Milk --}}
                    <div class="" data-aos="fade-up" data-aos-duration="800">
                        <div class="product-card h-100 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden position-relative">
                                <img src="https://images.unsplash.com/photo-1563636619-e9143da7973b?w=300&h=300&fit=crop&auto=format" alt="Milk">
                                <span class="position-absolute top-8 start-8 badge bg-danger-600 text-white text-xs px-8 py-4 rounded-8 fw-bold">21% OFF</span>
                            </a>
                            <div class="product-card__content mt-12">
                                <div>
                                    <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                        <span class="text-heading text-md fw-bold">₹55.00 <span class="text-gray-500 text-xs fw-normal">/Ltr</span></span>
                                        <span class="text-gray-400 text-sm fw-medium text-decoration-line-through text-nowrap">₹70.00</span>
                                    </div>
                                    <div class="flex-align gap-6 mb-8">
                                        <span class="text-xs fw-bold text-gray-700">4.8</span>
                                        <span class="text-14 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-gray-500">(15k)</span>
                                    </div>
                                    <h6 class="title text-base fw-semibold mb-12">
                                        <a href="/products" class="link text-line-2">Full Cream Fresh Milk (1 Litre)</a>
                                    </h6>
                                    <div class="mb-16">
                                        <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-main-600 rounded-pill" style="width: 50%"></div>
                                        </div>
                                        <span class="text-gray-900 text-xs fw-medium mt-6 d-block">Sold: 25/50</span>
                                    </div>
                                </div>
                                <a href="/cart" class="product-card__cart btn py-10 px-20 rounded-pill flex-align gap-8 w-100 justify-content-center text-sm fw-semibold">
                                    Add To Cart <i class="ph-bold ph-shopping-cart-simple"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Product 5: Onions --}}
                    <div class="" data-aos="fade-up" data-aos-duration="1000">
                        <div class="product-card h-100 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden position-relative">
                                <img src="https://images.unsplash.com/photo-1508747703725-719777637510?w=300&h=300&fit=crop&auto=format" alt="Onions">
                                <span class="position-absolute top-8 start-8 badge bg-danger-600 text-white text-xs px-8 py-4 rounded-8 fw-bold">36% OFF</span>
                            </a>
                            <div class="product-card__content mt-12">
                                <div>
                                    <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                        <span class="text-heading text-md fw-bold">₹35.00 <span class="text-gray-500 text-xs fw-normal">/Kg</span></span>
                                        <span class="text-gray-400 text-sm fw-medium text-decoration-line-through text-nowrap">₹55.00</span>
                                    </div>
                                    <div class="flex-align gap-6 mb-8">
                                        <span class="text-xs fw-bold text-gray-700">4.6</span>
                                        <span class="text-14 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-gray-500">(9k)</span>
                                    </div>
                                    <h6 class="title text-base fw-semibold mb-12">
                                        <a href="/products" class="link text-line-2">Fresh Red Onions (1 Kg)</a>
                                    </h6>
                                    <div class="mb-16">
                                        <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-main-600 rounded-pill" style="width: 45%"></div>
                                        </div>
                                        <span class="text-gray-900 text-xs fw-medium mt-6 d-block">Sold: 27/60</span>
                                    </div>
                                </div>
                                <a href="/cart" class="product-card__cart btn py-10 px-20 rounded-pill flex-align gap-8 w-100 justify-content-center text-sm fw-semibold">
                                    Add To Cart <i class="ph-bold ph-shopping-cart-simple"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Product 6: Cooking Oil --}}
                    <div class="" data-aos="fade-up" data-aos-duration="1200">
                        <div class="product-card h-100 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden position-relative">
                                <img src="https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=300&h=300&fit=crop&auto=format" alt="Cooking Oil">
                                <span class="position-absolute top-8 start-8 badge bg-danger-600 text-white text-xs px-8 py-4 rounded-8 fw-bold">24% OFF</span>
                            </a>
                            <div class="product-card__content mt-12">
                                <div>
                                    <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                        <span class="text-heading text-md fw-bold">₹160.00 <span class="text-gray-500 text-xs fw-normal">/Ltr</span></span>
                                        <span class="text-gray-400 text-sm fw-medium text-decoration-line-through text-nowrap">₹210.00</span>
                                    </div>
                                    <div class="flex-align gap-6 mb-8">
                                        <span class="text-xs fw-bold text-gray-700">4.7</span>
                                        <span class="text-14 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-gray-500">(11k)</span>
                                    </div>
                                    <h6 class="title text-base fw-semibold mb-12">
                                        <a href="/products" class="link text-line-2">Pure Sunflower Cooking Oil (1 Litre)</a>
                                    </h6>
                                    <div class="mb-16">
                                        <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-main-600 rounded-pill" style="width: 55%"></div>
                                        </div>
                                        <span class="text-gray-900 text-xs fw-medium mt-6 d-block">Sold: 33/60</span>
                                    </div>
                                </div>
                                <a href="/cart" class="product-card__cart btn py-10 px-20 rounded-pill flex-align gap-8 w-100 justify-content-center text-sm fw-semibold">
                                    Add To Cart <i class="ph-bold ph-shopping-cart-simple"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Product 7: Rice --}}
                    <div class="" data-aos="fade-up" data-aos-duration="600">
                        <div class="product-card h-100 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden position-relative">
                                <img src="https://images.unsplash.com/photo-1536304929831-ee1ca9d44906?w=300&h=300&fit=crop&auto=format" alt="Rice">
                                <span class="position-absolute top-8 start-8 badge bg-danger-600 text-white text-xs px-8 py-4 rounded-8 fw-bold">23% OFF</span>
                            </a>
                            <div class="product-card__content mt-12">
                                <div>
                                    <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                        <span class="text-heading text-md fw-bold">₹85.00 <span class="text-gray-500 text-xs fw-normal">/Kg</span></span>
                                        <span class="text-gray-400 text-sm fw-medium text-decoration-line-through text-nowrap">₹110.00</span>
                                    </div>
                                    <div class="flex-align gap-6 mb-8">
                                        <span class="text-xs fw-bold text-gray-700">4.9</span>
                                        <span class="text-14 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-gray-500">(22k)</span>
                                    </div>
                                    <h6 class="title text-base fw-semibold mb-12">
                                        <a href="/products" class="link text-line-2">Premium Basmati Rice (1 Kg)</a>
                                    </h6>
                                    <div class="mb-16">
                                        <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-main-600 rounded-pill" style="width: 80%"></div>
                                        </div>
                                        <span class="text-gray-900 text-xs fw-medium mt-6 d-block">Sold: 48/60</span>
                                    </div>
                                </div>
                                <a href="/cart" class="product-card__cart btn py-10 px-20 rounded-pill flex-align gap-8 w-100 justify-content-center text-sm fw-semibold">
                                    Add To Cart <i class="ph-bold ph-shopping-cart-simple"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= hot-deals End ================================ -->

    
    <!-- Super Discount Start -->
 <div class="pt-80">
     <div class="container container-lg">
        <div class="border border-main-500 bg-main-50 border-dashed rounded-16 py-20 px-24 d-flex flex-column flex-lg-row align-items-center justify-content-between justify-content-lg-evenly gap-16 text-center text-lg-start">
            <p class="h6 text-main-600 fw-normal mb-0">Super discount for your <a href="{{ url('/products') }}" class="fw-bold text-decoration-underline text-main-600 hover-text-decoration-none hover-text-primary-600">first purchase</a></p>
            <div class="position-relative flex-shrink-0">
                <button type="button" class="copy-coupon-btn px-28 py-10 text-white text-uppercase bg-main-600 rounded-pill border-0 hover-bg-main-800 d-inline-flex align-items-center justify-content-center gap-8 fw-bold" style="white-space: nowrap !important; letter-spacing: 0.5px;">
                    <span>FREE25BAC</span>
                    <i class="ph ph-copy text-lg line-height-1"></i>
                </button>
                <span class="copy-text bg-main-600 text-white fw-normal position-absolute px-16 py-6 rounded-pill bottom-100 start-50 translate-middle-x min-w-max mb-8 text-xs"></span>
            </div>
            <p class="text-md text-main-600 fw-normal mb-0">Use discount code to get <span class="fw-bold text-main-600">20% </span> discount for any item</p>
        </div>
     </div>
 </div>
<!-- Super Discount End -->

    <!-- ========================== Short Product Section Start ============================== -->
<div class="short-product pt-110">
    <div class="container container-lg">
        <div class="row gy-4">

            <!-- Featured Products -->
            <div class="col-xxl-3 col-lg-4 col-sm-6" data-aos="fade-up" data-aos-duration="600">
                <div class="p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                    <div class="p-16 bg-main-50 rounded-16 mb-32">
                        <h6 class="underlined-line position-relative mb-0 pb-16 d-inline-block">Featured Products</h6>
                    </div>
                    <div class="short-product-list arrow-style-two max-h-unset">
                        <div class="d-flex flex-column gap-44">
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img1.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                   
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Fresh Organic Broccoli Florets Vegetables</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹12.00</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹18.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img2.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                    <div class="flex-align gap-6">
                                        <span class="text-xs fw-bold text-gray-500">4.7</span>
                                        <span class="text-15 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-bold text-gray-500">(12k)</span>
                                    </div>
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Nature's Best Baby Spinach Leaves</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹9.00</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹14.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img3.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Farm Fresh Roma Tomatoes</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹7.00</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹11.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img4.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                   
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Organic Sweet Bell Peppers Mix</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹10.00</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹15.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-44">
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img1.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Fresh Organic Broccoli Florets Vegetables</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹12.00</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹18.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img2.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Nature's Best Baby Spinach Leaves</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹9.00</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹14.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img3.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                   
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Farm Fresh Roma Tomatoes</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹7.00</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹11.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img4.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                   
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Organic Sweet Bell Peppers Mix</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹10.00</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹15.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products -->
            <div class="col-xxl-3 col-lg-4 col-sm-6" data-aos="fade-up" data-aos-duration="700">
                <div class="p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                    <div class="p-16 bg-main-50 rounded-16 mb-32">
                        <h6 class="underlined-line position-relative mb-0 pb-16 d-inline-block">Top Selling Products</h6>
                    </div>
                    <div class="short-product-list arrow-style-two max-h-unset">
                        <div class="d-flex flex-column gap-44">
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img5.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Tropicana Pure Orange Juice 1L</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹5.99</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹8.99</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img6.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                   
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Dannon Whole Milk Plain Yogurt</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹4.49</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹6.99</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img7.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Organic Valley Whole Milk</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹6.99</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹9.99</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img8.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Chobani Greek Yogurt Strawberry</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹3.99</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹5.99</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-44">
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img5.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Tropicana Pure Orange Juice 1L</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹5.99</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹8.99</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img6.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                   
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Dannon Whole Milk Plain Yogurt</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹4.49</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹6.99</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img7.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Organic Valley Whole Milk</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹6.99</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹9.99</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img8.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Chobani Greek Yogurt Strawberry</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹3.99</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹5.99</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- On-sale Products -->
            <div class="col-xxl-3 col-lg-4 col-sm-6" data-aos="fade-up" data-aos-duration="800">
                <div class="p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                    <div class="p-16 bg-main-50 rounded-16 mb-32">
                        <h6 class="underlined-line position-relative mb-0 pb-16 d-inline-block">On-sale Products</h6>
                    </div>
                    <div class="short-product-list arrow-style-two max-h-unset">
                        <div class="d-flex flex-column gap-44">
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img9.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Dave's Killer Bread Organic Thin-Sliced</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹5.49</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹7.99</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img4.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                    
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Organic Sliced Mushrooms Pack</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹3.99</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹5.99</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img7.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Kirkland Organic Almond Milk</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹4.99</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹7.49</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img4.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Green Giant Frozen Broccoli Florets</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹2.99</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹4.99</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-44">
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img9.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                   
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Dave's Killer Bread Organic Thin-Sliced</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹5.49</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹7.99</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img4.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Organic Sliced Mushrooms Pack</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹3.99</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹5.99</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img7.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                  
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Kirkland Organic Almond Milk</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹4.99</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹7.49</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-align gap-16">
                                <div class="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                                    <a href="/products" class="link"><img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/short-product-img4.png" alt=""></a>
                                </div>
                                <div class="product-card__content mt-12">
                                   
                                    <h6 class="title text-lg fw-semibold mt-8 mb-8">
                                        <a href="/products" class="link text-line-1">Green Giant Frozen Broccoli Florets</a>
                                    </h6>
                                    <div class="product-card__price flex-align gap-8">
                                        <span class="text-heading text-md fw-semibold d-block">₹2.99</span>
                                        <span class="text-gray-400 text-md fw-semibold d-block">₹4.99</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deals of the Week -->
            <div class="col-xxl-3 col-lg-4 col-sm-6" data-aos="fade-up" data-aos-duration="900">
                <div class="product-card h-100 p-24 pt-32 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item pt-32">
                    <button type="button" class="wishlist-btn-two">
                        <i class="ph-bold ph-heart"></i>
                    </button>
                    <div class="">
                        <h6 class="position-relative mb-0 pb-12 d-inline-block">Deals of the week</h6>
                        <div class="countdown mb-10" id="countdown26">
                            <ul class="countdown-list flex-align flex-wrap">
                                <li class="countdown-list__item colon-red py-8 px-12 flex-align gap-4 text-sm fw-medium box-shadow-4xl rounded-5 bg-main-600 text-white">
                                    <span class="days"></span> D
                                </li>
                                <li class="countdown-list__item colon-red py-8 px-12 flex-align gap-4 text-sm fw-medium box-shadow-4xl rounded-5 bg-main-600 text-white">
                                    <span class="hours"></span> H
                                </li>
                                <li class="countdown-list__item colon-red py-8 px-12 flex-align gap-4 text-sm fw-medium box-shadow-4xl rounded-5 bg-main-600 text-white">
                                    <span class="minutes"></span> M
                                </li>
                                <li class="countdown-list__item colon-red py-8 px-12 flex-align gap-4 text-sm fw-medium box-shadow-4xl rounded-5 bg-main-600 text-white">
                                    <span class="seconds"></span> S
                                </li>
                            </ul>
                        </div>
                        <p class="text-neutral-300 fw-medium text-sm">Don't miss this opportunity at a special</p>
                    </div>
                    <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                        <img src="https://wowtheme7.com/tf/marketpro/assets/images/thumbs/product-img32.png" alt="">
                    </a>
                    <div class="product-card__content w-100">
                        <div class="flex-align gap-4">
                            <!-- <div class="flex-align gap-2 me-4">
                                <span class="text-12 fw-medium text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                <span class="text-12 fw-medium text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                <span class="text-12 fw-medium text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                <span class="text-12 fw-medium text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                <span class="text-12 fw-medium text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                            </div>
                            <span class="text-xs fw-medium text-heading">(3)</span> -->
                        </div>
                        <div class="d-flex align-items-center gap-12 mt-6">
                            <h6 class="text-danger-600 mb-0 text-lg"> ₹60.99</h6>
                            <h6 class="text-neutral-300 fw-medium mb-0 text-lg">₹79.99</h6>
                        </div>
                        <h6 class="title text-md fw-semibold mt-10 mb-0">
                            <a href="/products" class="link text-line-2 fw-bold">Perfectly Packed Meat Combos for Delicious and Flavorful Meals Every Day</a>
                        </h6>
                        <p class="text-gray-500 text-sm mt-12 pb-12 border-bottom border-neutral-100 mb-8">This product is about to run out</p>
                        <div class="progress w-100 bg-gray-100 rounded-pill h-8" role="progressbar" aria-label="Basic example" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-success-600 rounded-pill" style="width: 35%"></div>
                        </div>
                        <div class="d-flex align-items-center gap-6 mt-6">
                            <span class="text-sm text-gray-500">available only:</span>
                            <h6 class="text-danger-600 mb-0 text-md fw-semibold"> ₹60.99</h6>
                        </div>
                        <a href="/cart" class="product-card__cart btn bg-success-600 text-white hover-bg-success-700 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-16 w-100 justify-content-center">
                            Add To Cart <i class="ph ph-shopping-cart"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- ========================== Short Product Section End ============================== -->

<script>
document.addEventListener("DOMContentLoaded", function () {
    const activeCurrency = '{{ $currencySymbol ?? "₹" }}';

    function closeAllVariantSelects(except) {
        document.querySelectorAll('.custom-variant-select.open').forEach(function (s) {
            if (s === except) return;
            s.classList.remove('open');
            const d = s.querySelector('.variant-selected-display');
            if (d) d.setAttribute('aria-expanded', 'false');
        });
    }

    function applyVariant(select, option, options) {
        const variantId  = option.dataset.variantId;
        const offerPrice = option.dataset.offer;
        const mrpPrice   = option.dataset.mrp;
        const sizeText   = option.querySelector('.opt-size').textContent;

        // Update display text & price tag
        const vLabel = select.querySelector('.variant-label');
        if (vLabel) vLabel.textContent = sizeText;
        const vPriceTag = select.querySelector('.variant-price-tag');
        if (vPriceTag) vPriceTag.textContent = activeCurrency + offerPrice;

        options.forEach(function (o) {
            o.classList.toggle('selected', o === option);
            o.setAttribute('aria-selected', o === option ? 'true' : 'false');
        });

        select.classList.remove('open');
        const displayEl = select.querySelector('.variant-selected-display');
        if (displayEl) displayEl.setAttribute('aria-expanded', 'false');

        // Update card price & cart control
        const cardRoot = select.closest('.product-card');
        if (cardRoot) {
            const priceBox = cardRoot.querySelector('.product-card__price');
            if (priceBox) {
                const hasDiscount = mrpPrice && Number(mrpPrice) > Number(offerPrice);
                const savings = hasDiscount ? (Number(mrpPrice) - Number(offerPrice)) : 0;
                priceBox.innerHTML = `
                    <div class="d-flex align-items-baseline justify-content-center gap-6">
                        <span class="text-heading text-base text-sm-lg fw-bold text-main-600 main-price-text">${activeCurrency}${parseFloat(offerPrice).toFixed(2)}</span>
                        ${hasDiscount ? `<span class="text-neutral-400 text-xs fw-medium text-decoration-line-through mrp-price-text">${activeCurrency}${parseFloat(mrpPrice).toFixed(2)}</span>` : ''}
                    </div>
                    ${hasDiscount ? `<span class="badge bg-success-50 text-success-700 text-xs px-6 py-2 rounded-6 fw-bold save-badge">Save ${activeCurrency}${savings.toFixed(2)}</span>` : ''}
                `;
            }

            const control = cardRoot.querySelector('.cart-control');
            if (control) {
                control.dataset.variantId = variantId;
                const stock = parseInt(option.dataset.stock !== undefined ? option.dataset.stock : 99);
                if (stock <= 0) {
                    control.innerHTML = `
                        <button type="button" class="btn btn-outline-neutral-300 py-8 py-sm-10 px-12 px-sm-16 rounded-pill w-100 justify-content-center text-xs fw-semibold text-neutral-400" disabled>
                            Out of Stock
                        </button>`;
                } else {
                    control.innerHTML = `
                        <button type="button"
                            class="add-to-cart-btn product-card__cart btn py-8 py-sm-10 px-12 px-sm-16 rounded-pill flex-align gap-6 w-100 justify-content-center text-xs fw-semibold">
                            <span>Add To Cart</span> <i class="ph-bold ph-shopping-cart-simple text-sm"></i>
                        </button>`;
                    attachAddToCartEvent(control);
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

        display.addEventListener('click', function (e) {
            e.stopPropagation();
            setOpen(!select.classList.contains('open'));
        });

        display.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                setOpen(!select.classList.contains('open'));
            } else if (e.key === 'Escape') {
                setOpen(false);
            }
        });

        options.forEach(function (option) {
            option.addEventListener('click', function (e) {
                e.stopPropagation();
                applyVariant(select, option, options);
            });
        });
    });

    document.addEventListener('click', function () {
        closeAllVariantSelects();
    });

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
                                    class="add-to-cart-btn product-card__cart btn py-8 py-sm-10 px-12 px-sm-16 rounded-pill flex-align gap-6 w-100 justify-content-center text-xs fw-semibold">
                                    <span>Add To Cart</span> <i class="ph-bold ph-shopping-cart-simple text-sm"></i>
                                </button>`;
                            attachAddToCartEvent(controlDiv);
                            if (typeof updateCartCount === 'function') {
                                updateCartCount(-1);
                            }
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
        }).then(async r => {
            const data = await r.json();
            if (r.ok && data.success) {
                input.value = data.quantity !== undefined ? data.quantity : quantity;
                if (data.clamped) {
                    Swal.fire({ icon: 'info', title: data.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
                }
            } else {
                Swal.fire({ icon: 'warning', title: data.message || 'Could not update quantity', toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
            }
        }).catch(() => {
            Swal.fire({ icon: 'error', title: 'Network error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
        });
    }

    function attachAddToCartEvent(controlDiv) {
        const btn = controlDiv.querySelector('.add-to-cart-btn');
        if (!btn) return;

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (btn.disabled) return;
            btn.disabled = true;
            const origContent = btn.innerHTML;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Adding...`;

            const productId = controlDiv.dataset.productId;
            const variantId = controlDiv.dataset.variantId;

            fetch("{{ route('cart.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId, product_varient_id: variantId, quantity: 1 })
            }).then(async r => {
                const data = await r.json();
                if (r.ok && data.status !== 'error') {
                    showQtyBox(controlDiv, 1);
                    if (typeof updateCartCount === 'function') {
                        updateCartCount(1);
                    }
                    Swal.fire({
                        icon: 'success', title: data.message || 'Added to cart successfully!',
                        toast: true, position: 'top-end',
                        showConfirmButton: false, timer: 2000, timerProgressBar: true
                    });
                } else {
                    btn.disabled = false;
                    btn.innerHTML = origContent;
                    Swal.fire({
                        icon: 'warning', title: data.message || 'This product variant is currently unavailable.',
                        toast: true, position: 'top-end',
                        showConfirmButton: false, timer: 3000, timerProgressBar: true
                    });
                }
            }).catch(() => {
                btn.disabled = false;
                btn.innerHTML = origContent;
                Swal.fire({
                    icon: 'error', title: 'Network error',
                    toast: true, position: 'top-end',
                    showConfirmButton: false, timer: 2000
                });
            });
        });
    }

    document.querySelectorAll('.cart-control').forEach(controlDiv => {
        if (controlDiv.querySelector('.qty-box')) {
            attachQtyEvents(controlDiv);
        } else {
            attachAddToCartEvent(controlDiv);
        }
    });

    // Recommended Section Category Filter Pills
    document.querySelectorAll('.filter-rec-cat-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-rec-cat-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const selectedCat = this.dataset.catId;
            let visibleCount = 0;

            document.querySelectorAll('.recommended-card-col').forEach(cardCol => {
                if (selectedCat === 'all' || cardCol.dataset.catId === selectedCat) {
                    cardCol.classList.remove('d-none');
                    visibleCount++;
                } else {
                    cardCol.classList.add('d-none');
                }
            });

            const noProductsBox = document.getElementById('noCategoryProducts');
            if (noProductsBox) {
                if (visibleCount === 0) {
                    noProductsBox.classList.remove('d-none');
                } else {
                    noProductsBox.classList.add('d-none');
                }
            }
        });
    });

    // Copy coupon button
    const copyBtn = document.querySelector('.copy-coupon-btn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            const code = 'FREE25BAC';
            navigator.clipboard.writeText(code).then(() => {
                const copyText = copyBtn.parentElement.querySelector('.copy-text');
                if (copyText) {
                    copyText.textContent = 'Copied: ' + code;
                    copyText.style.display = 'block';
                    setTimeout(() => { copyText.style.display = 'none'; }, 2000);
                }
            });
        });
    }
});
</script>

  @endsection