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

        <div class="position-relative arrow-center">
            <div class="flex-align">
                <button type="button" id="feature-item-wrapper-prev" class="slick-prev slick-arrow flex-center rounded-circle bg-white text-xl hover-bg-main-600 hover-text-white transition-1">
                    <i class="ph ph-caret-left"></i>
                </button>
                <button type="button" id="feature-item-wrapper-next" class="slick-next slick-arrow flex-center rounded-circle bg-white text-xl hover-bg-main-600 hover-text-white transition-1">
                    <i class="ph ph-caret-right"></i>
                </button>
            </div>
            <div class="feature-item-wrapper">

            @php
                $pastelBgs = ['#E8F9E9', '#FCEDED', '#F6EBF3', '#FCF2E7', '#F5FBE3', '#E3FBE9', '#E9E3FB', '#ECF8F2', '#F8F5E5', '#F0F8E5'];
            @endphp
            @foreach($categories as $category)
                <div class="feature-item text-center">
                    <div class="feature-item__thumb rounded-circle" style="background-color: {{ $pastelBgs[$loop->index % count($pastelBgs)] }};">
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
<section class="promotional-banner pt-40 pt-lg-80">
    <div class="container container-lg">
        <div class="row g-12 g-sm-16 g-xl-24">
            @php
                $cardThemes = [
                    ['bg' => 'linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%)', 'border' => '#fed7aa'],
                    ['bg' => 'linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)', 'border' => '#bbf7d0'],
                    ['bg' => 'linear-gradient(135deg, #fefce8 0%, #fef08a 100%)', 'border' => '#fde047'],
                    ['bg' => 'linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%)', 'border' => '#99f6e4'],
                ];
            @endphp
            @forelse($promoCategories as $index => $pCat)
                @php
                    $theme = $cardThemes[$index % 4];
                    $catSlug = \Illuminate\Support\Str::slug($pCat->category_name);
                    $mainUrl = rtrim(env('MAIN_URL', 'http://127.0.0.1:8001/'), '/');
                    $catImg = !empty($pCat->category_image) ? $mainUrl . '/images/' . ltrim($pCat->category_image, '/') : null;
                @endphp
                <div class="col-xl-3 col-sm-6 col-6">
                    <div class="promotional-banner-item position-relative rounded-20 rounded-xl-24 overflow-hidden z-1 p-12 p-sm-20 py-xl-40 ps-xl-32 pe-xl-24 h-100 d-flex flex-column justify-content-between group-item"
                         style="min-height: 170px; background: {{ $theme['bg'] }}; border: 1px solid {{ $theme['border'] }}; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
                        
                        <!-- Floating Category Image Cutout on Right -->
                        @if($catImg)
                            <img src="{{ $catImg }}" alt="{{ $pCat->category_name }}" 
                                 class="position-absolute end-0 bottom-0 pointer-events-none transition-2 promo-cat-img"
                                 style="max-height: 95px; max-width: 46%; object-fit: contain; z-index: 1; mix-blend-mode: multiply; margin-right: 6px; margin-bottom: 6px;"
                                 onerror="if(!this.dataset.tried){this.dataset.tried='1';this.src='{{ env('MAIN_URL') }}images/{{ $pCat->category_image }}';}">
                        @endif

                        <!-- Card Content on Left -->
                        <div class="promotional-banner-item__content position-relative w-100" style="z-index: 2;">
                            <h6 class="promotional-banner-item__title mb-2 mb-sm-8 text-heading fw-bold text-capitalize" 
                                style="font-size: clamp(15px, 3.8vw, 22px); line-height: 1.25; word-break: normal; overflow-wrap: normal; hyphens: none; white-space: normal;">
                                {{ $pCat->category_name }}
                            </h6>
                            <div class="d-flex align-items-baseline gap-4 gap-sm-6 flex-wrap my-2 my-sm-8" style="white-space: nowrap !important;">
                                <span class="text-heading fst-italic text-xs text-sm-sm">Starting at</span>
                                <span class="text-danger-600 mb-0 text-15 text-sm-xl fw-bold" style="white-space: nowrap !important;">
                                    {{ $currencySymbol }}{{ number_format($pCat->min_price, 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Shop Now Button: Full Pill on Desktop, Round Icon on Mobile -->
                        <div class="position-relative mt-auto pt-6" style="z-index: 2;">
                            {{-- Desktop & Tablet: Full Button --}}
                            <a href="{{ url('/products?category=' . $catSlug) }}" class="btn btn-main d-none d-sm-inline-flex align-items-center rounded-pill gap-6 py-sm-8 px-sm-16 text-xs text-sm-sm fw-semibold shadow-sm">
                                <span>Shop Now</span>
                                <span class="icon text-xs text-sm-base d-flex"><i class="ph ph-arrow-right"></i></span> 
                            </a>
                            {{-- Mobile Only: Stylish Round Icon Button --}}
                            <a href="{{ url('/products?category=' . $catSlug) }}" class="btn btn-main d-inline-flex d-sm-none align-items-center justify-content-center rounded-circle shadow-sm" style="width: 36px; height: 36px; min-width: 36px; padding: 0;" title="Shop Now">
                                <i class="ph-bold ph-arrow-right" style="font-size: 15px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-20">
                    <p class="text-neutral-500">Explore our wide range of groceries and products.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
<!-- ======================== promotional banner End ============================== -->
    

    <!-- ========================= Recommended Start ================================ -->
<section class="recommended overflow-hidden pt-80">
    <div class="container container-lg">
        <div class="section-heading flex-between flex-wrap gap-16">
            <h5 class="mb-0">Recommended for you</h5>
            <ul class="nav common-tab nav-pills flex-nowrap overflow-x-auto pb-4" id="recommendedCatPills" role="tablist">
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
                <div class="row g-12 g-sm-16 gy-16 gy-sm-24">
                    @forelse($featuredProducts as $product)
                        @php
                            $displayVariants = $product->variants;
                            $variant = $displayVariants->first();
                            $offerPrice = $variant->offer_price ?? $product->product_price ?? 0;
                            $mrpPrice = $variant->mrp_price ?? $variant->product_mrp_price ?? 0;
                            $mainUrl = rtrim(env('MAIN_URL', 'http://127.0.0.1:8001/'), '/');
                            if (!empty($product->product_image)) {
                                if (Str::startsWith($product->product_image, ['http://', 'https://'])) {
                                    $imgSrc = $product->product_image;
                                } elseif (Str::startsWith($product->product_image, 'assets/')) {
                                    $imgSrc = asset($product->product_image);
                                } else {
                                    $imgSrc = $mainUrl . '/images/' . ltrim($product->product_image, '/');
                                }
                            } else {
                                $imgSrc = asset('assets/images/thumbs/product-two-img1.png');
                            }
                            $inCart = $variant ? in_array($variant->id, $cartProductIds ?? []) : false;
                            $cartItem = ($variant && isset($cartItems)) ? $cartItems->where('product_varient_id', $variant->id)->first() : null;
                            $isOutOfStock = ($variant ? (int)($variant->product_qty ?? 0) : 0) <= 0;
                        @endphp
                        <div class="col-xxl-3 col-lg-4 col-sm-6 col-6 recommended-card-col" data-cat-id="{{ $product->category_id }}">
                            <div class="product-card h-100 p-12 p-sm-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item bg-white d-flex flex-column justify-content-between" style="box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                                <div class="w-100">
                                    <a href="/product/{{ $product->slug }}" class="product-card__thumb w-100 flex-center overflow-hidden rounded-12 bg-gray-50 mb-12" style="height: 160px;">
                                        <img src="{{ $imgSrc }}" alt="{{ $product->product_name }}" class="w-100 h-100 object-fit-contain p-8" onerror="if (!this.dataset.tried) { this.dataset.tried = '1'; this.src='{{ env('MAIN_URL') }}images/{{ $product->product_image }}'; } else { this.src='/assets/images/thumbs/product-two-img1.png'; }">
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
    padding: 14px 12px 18px 12px !important;
    border: 1px solid #edf2f7;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.recommended-card-col {
    margin-bottom: 0px;
}
@media (min-width: 768px) {
    .recommended-card-col {
        margin-bottom: 24px;
    }
}

/* Short Product Section (Featured, Top Selling, Trending) - 2x2 Grid on Mobile */
@media (max-width: 575px) {
    .short-product-list .d-flex.flex-column.gap-20 {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 10px !important;
    }
    .short-product-list .flex-align.gap-14 {
        flex-direction: column !important;
        align-items: stretch !important;
        padding: 8px !important;
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        height: 100%;
        gap: 6px !important;
    }
    .short-product-list .flex-align.gap-14 .w-80.h-80 {
        width: 100% !important;
        height: 90px !important;
        border-radius: 8px;
        background: #ffffff;
        margin-bottom: 4px;
    }
    .short-product-list .product-card__content {
        width: 100% !important;
        text-align: left;
    }
    .short-product-list .product-card__content .title {
        font-size: 13px !important;
        min-height: 32px;
        line-height: 1.25;
        margin-bottom: 4px !important;
    }
    .short-product-list .product-card__price {
        gap: 6px !important;
        flex-wrap: wrap;
    }
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
                <h5 class="mb-0">Hot Deals Today</h5>
                <div class="flex-align gap-16">
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

        @php
            $dealsOfDayEndDate = null;
            if (isset($hotDeals) && $hotDeals->count() > 0) {
                foreach($hotDeals as $hd) {
                    if ($hd->variants && $hd->variants->count() > 0) {
                        foreach($hd->variants as $hv) {
                            if (!empty($hv->flash_sale_date) && strtotime($hv->flash_sale_date) > time()) {
                                $dealsOfDayEndDate = date('Y-m-d H:i:s', strtotime($hv->flash_sale_date));
                                break 2;
                            }
                        }
                    }
                }
            }
            if (!$dealsOfDayEndDate) {
                $dealsOfDayEndDate = now()->endOfDay()->format('Y-m-d H:i:s');
            }
        @endphp

        <div class="row g-12">
            <div class="col-md-4">
                <div class="hot-deals position-relative rounded-16 bg-main-600 overflow-hidden ps-40 pe-24 pt-80 pb-120 z-1">
                    <img src="assets/images/shape/offer-shape.png" alt="" class="position-absolute inset-block-start-0 inset-inline-start-0 z-n1 w-100 h-100 opacity-6">

                    <img src="assets/images/thumbs/basket-img.png" alt="Basket Thumb" class="position-absolute inset-inline-end-0 inset-block-end-0">
                    
                    <span class="text-primary-600 bg-yellow text-heading py-4 px-12 rounded-4 text-sm fw-medium">Fresh Grocery</span>
                    <div class="">
                        <h5 class="text-white mb-8 mt-12">Deals of the Day</h5>
                        <p class="fw-semibold text-success-600">Save up to 50% off on your first order</p>
                        <div class="countdown mt-24 mb-24" id="countdown4" data-date="{{ $dealsOfDayEndDate }}">
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

                    @forelse($hotDeals as $hProd)
                        @php
                            $hVariant = $hProd->variants->first();
                            $hOffer = $hVariant->offer_price ?? $hProd->product_price ?? 0;
                            $hMrp = $hVariant->mrp_price ?? $hVariant->product_mrp_price ?? 0;
                            $hDiscount = ($hMrp > $hOffer && $hMrp > 0) ? round((($hMrp - $hOffer) / $hMrp) * 100) : 0;
                            if (!empty($hProd->product_image)) {
                                if (Str::startsWith($hProd->product_image, ['http://', 'https://'])) {
                                    $hImg = $hProd->product_image;
                                } elseif (Str::startsWith($hProd->product_image, 'assets/')) {
                                    $hImg = asset($hProd->product_image);
                                } else {
                                    $hImg = rtrim(env('MAIN_URL', 'http://127.0.0.1:8001/'), '/') . '/images/' . ltrim($hProd->product_image, '/');
                                }
                            } else {
                                $hImg = asset('assets/images/thumbs/product-two-img1.png');
                            }
                            $hInCart = $hVariant ? in_array($hVariant->id, $cartProductIds ?? []) : false;
                            $hCartItem = ($hVariant && isset($cartItems)) ? $cartItems->where('product_varient_id', $hVariant->id)->first() : null;
                            $hOutOfStock = ($hVariant ? (int)($hVariant->product_qty ?? 0) : 0) <= 0;
                            $hUnitLabel = '';
                            if ($hVariant) {
                                if ($hProd->is_gift) {
                                    $parts = [];
                                    if ($hVariant->size) $parts[] = $hVariant->size->size;
                                    if ($hVariant->color) $parts[] = $hVariant->color->color;
                                    $hUnitLabel = !empty($parts) ? implode(' • ', $parts) : ($hVariant->varient_name ?: '');
                                } else {
                                    $hUnitLabel = ($hVariant->value ?? '') . ($hVariant->unit->short_name ?? '');
                                }
                            }
                        @endphp
                        <div class="px-6">
                            <div class="product-card h-100 p-12 p-sm-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 bg-white d-flex flex-column justify-content-between">
                                <div>
                                    <a href="/product/{{ $hProd->slug }}" class="product-card__thumb flex-center overflow-hidden position-relative rounded-12 bg-gray-50 mb-12" style="height: 160px;">
                                        <img src="{{ $hImg }}" alt="{{ $hProd->product_name }}" class="w-100 h-100 object-fit-contain p-8" onerror="if (!this.dataset.tried) { this.dataset.tried = '1'; this.src='{{ env('MAIN_URL') }}images/{{ $hProd->product_image }}'; } else { this.src='/assets/images/thumbs/product-two-img1.png'; }">
                                        @if($hDiscount > 0)
                                            <span class="position-absolute top-8 start-8 badge bg-danger-600 text-white text-xs px-8 py-4 rounded-8 fw-bold">{{ $hDiscount }}% OFF</span>
                                        @endif
                                    </a>
                                    <div class="product-card__content mt-8">
                                        <span class="text-xs text-main-600 fw-semibold d-block mb-4 text-truncate">{{ $hProd->category->category_name ?? 'Farm Fresh' }}</span>
                                        <h6 class="title text-sm fw-semibold mb-8" style="min-height: 38px;">
                                            <a href="/product/{{ $hProd->slug }}" class="link text-line-2 text-inherit">{{ $hProd->product_name }}</a>
                                        </h6>
                                        <div class="product-card__price mb-8 d-flex align-items-center gap-8 flex-wrap">
                                            <span class="text-heading text-md fw-bold text-main-600">{{ $currencySymbol }}{{ number_format($hOffer, 2) }}</span>
                                            @if($hMrp > $hOffer)
                                                <span class="text-gray-400 text-xs fw-medium text-decoration-line-through text-nowrap">{{ $currencySymbol }}{{ number_format($hMrp, 2) }}</span>
                                            @endif
                                            @if($hUnitLabel)
                                                <span class="badge bg-neutral-50 text-neutral-600 text-xs px-6 py-2 rounded-4">{{ $hUnitLabel }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="cart-control mt-8 w-100" data-product-id="{{ $hProd->id }}" data-variant-id="{{ $hVariant->id ?? '' }}">
                                    @if($hOutOfStock)
                                        <button type="button" class="btn btn-outline-neutral-300 py-8 px-12 rounded-pill w-100 justify-content-center text-xs fw-semibold text-neutral-400" disabled>
                                            Out of Stock
                                        </button>
                                    @elseif($hInCart)
                                        <div class="qty-box">
                                            <button type="button" class="quantity__minus">−</button>
                                            <div class="d-flex align-items-center gap-4">
                                                <span class="qty-box-label">In Cart:</span>
                                                <input type="number" class="quantity__input" value="{{ $hCartItem->product_quantity ?? 1 }}" min="1" readonly>
                                            </div>
                                            <button type="button" class="quantity__plus">+</button>
                                        </div>
                                    @else
                                        <button type="button"
                                            class="add-to-cart-btn product-card__cart btn py-8 px-12 rounded-pill flex-align gap-6 w-100 justify-content-center text-xs fw-semibold">
                                            <span>Add To Cart</span> <i class="ph-bold ph-shopping-cart-simple text-sm"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-24 text-center">
                            <p class="text-neutral-500 mb-0">No deals available today. Check back soon!</p>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= hot-deals End ================================ -->

    
    <!-- Super Discount Start -->
 <div class="pt-60 pt-lg-80">
     <div class="container container-lg">
        @if($activeCoupon)
            <div class="border border-main-500 bg-main-50 border-dashed rounded-16 py-20 px-16 px-md-24 d-flex flex-column flex-lg-row align-items-center justify-content-between justify-content-lg-evenly gap-12 gap-lg-16 text-center text-lg-start">
                <p class="h6 text-main-600 fw-normal mb-0 text-15 text-sm-base">Special discount coupon available now!</p>
                <div class="position-relative flex-shrink-0 my-4 my-lg-0">
                    <button type="button" class="copy-coupon-btn px-20 px-sm-28 py-8 py-sm-10 text-white text-uppercase bg-main-600 rounded-pill border-0 hover-bg-main-800 d-inline-flex align-items-center justify-content-center gap-8 fw-bold text-sm" style="white-space: nowrap !important; letter-spacing: 0.5px;">
                        <span>{{ $activeCoupon->codename }}</span>
                        <i class="ph ph-copy text-base text-sm-lg line-height-1"></i>
                    </button>
                    <span class="copy-text bg-main-600 text-white fw-normal position-absolute px-16 py-6 rounded-pill bottom-100 start-50 translate-middle-x min-w-max mb-8 text-xs"></span>
                </div>
                <p class="text-main-600 fw-normal mb-0 text-xs text-sm-sm text-lg-base">
                    Use discount code <strong class="text-heading">{{ $activeCoupon->codename }}</strong> to get 
                    <span class="fw-bold text-main-600">
                        @if(str_contains(strtolower($activeCoupon->discount_type ?? ''), 'percent') || str_contains($activeCoupon->codename, 'PERCENT'))
                            {{ $activeCoupon->discount }}% OFF
                        @else
                            {{ $currencySymbol }}{{ $activeCoupon->discount }} OFF
                        @endif
                    </span>
                    @if($activeCoupon->min_spend)
                        on orders above {{ $currencySymbol }}{{ $activeCoupon->min_spend }}
                    @else
                        on your purchase
                    @endif
                </p>
            </div>
        @else
            <div class="border border-neutral-200 bg-neutral-30 border-dashed rounded-16 py-18 px-16 text-center">
                <p class="text-neutral-500 fw-medium mb-0 text-sm">
                    <i class="ph-bold ph-ticket text-main-600 me-6 text-base align-middle"></i>
                    Coupon is not available currently. Stay tuned for upcoming offers!
                </p>
            </div>
        @endif
     </div>
 </div>
<!-- Super Discount End -->

    <!-- ========================== Short Product Section Start ============================== -->
<div class="short-product pt-80">
    <div class="container container-lg">
        <div class="row gy-4">

            <!-- 1. Featured Products -->
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 bg-white">
                    <div class="p-16 bg-main-50 rounded-16 mb-24">
                        <h6 class="underlined-line position-relative mb-0 pb-12 d-inline-block fw-bold text-heading">Featured Products</h6>
                    </div>
                    <div class="short-product-list arrow-style-two max-h-unset">
                        @forelse($shortFeatured->chunk(4) as $chunk)
                            <div class="d-flex flex-column gap-20">
                                @foreach($chunk as $p)
                                    @php
                                        $pVar = $p->variants->first();
                                        $pOffer = $pVar->offer_price ?? $p->product_price ?? 0;
                                        $pMrp = $pVar->mrp_price ?? $pVar->product_mrp_price ?? 0;
                                        if (!empty($p->product_image)) {
                                            if (Str::startsWith($p->product_image, ['http://', 'https://'])) {
                                                $pImg = $p->product_image;
                                            } elseif (Str::startsWith($p->product_image, 'assets/')) {
                                                $pImg = asset($p->product_image);
                                            } else {
                                                $pImg = rtrim(env('MAIN_URL', 'http://127.0.0.1:8001/'), '/') . '/images/' . ltrim($p->product_image, '/');
                                            }
                                        } else {
                                            $pImg = asset('assets/images/thumbs/product-two-img1.png');
                                        }
                                    @endphp
                                    <div class="flex-align gap-14 p-6 rounded-12 hover-bg-neutral-30 transition-1">
                                        <div class="w-80 h-80 rounded-12 border border-gray-100 flex-shrink-0 overflow-hidden bg-gray-50 flex-center">
                                            <a href="/product/{{ $p->slug }}" class="w-100 h-100 flex-center">
                                                <img src="{{ $pImg }}" alt="{{ $p->product_name }}" class="w-100 h-100 object-fit-contain p-6" onerror="if (!this.dataset.tried) { this.dataset.tried = '1'; this.src='{{ env('MAIN_URL') }}images/{{ $p->product_image }}'; } else { this.src='/assets/images/thumbs/product-two-img1.png'; }">
                                            </a>
                                        </div>
                                        <div class="product-card__content flex-grow-1 min-w-0">
                                            <h6 class="title text-sm fw-semibold mb-6">
                                                <a href="/product/{{ $p->slug }}" class="link text-line-2 text-heading hover-text-main-600">{{ $p->product_name }}</a>
                                            </h6>
                                            <div class="product-card__price d-flex align-items-center gap-8">
                                                <span class="text-heading text-sm fw-bold text-main-600">{{ $currencySymbol }}{{ number_format($pOffer, 2) }}</span>
                                                @if($pMrp > $pOffer)
                                                    <span class="text-gray-400 text-xs fw-medium text-decoration-line-through">{{ $currencySymbol }}{{ number_format($pMrp, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="p-16 text-center text-neutral-500 text-sm">No featured products.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- 2. Top Selling Products -->
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 bg-white">
                    <div class="p-16 bg-main-50 rounded-16 mb-24">
                        <h6 class="underlined-line position-relative mb-0 pb-12 d-inline-block fw-bold text-heading">Top Selling Products</h6>
                    </div>
                    <div class="short-product-list arrow-style-two max-h-unset">
                        @forelse($topSelling->chunk(4) as $chunk)
                            <div class="d-flex flex-column gap-20">
                                @foreach($chunk as $p)
                                    @php
                                        $pVar = $p->variants->first();
                                        $pOffer = $pVar->offer_price ?? $p->product_price ?? 0;
                                        $pMrp = $pVar->mrp_price ?? $pVar->product_mrp_price ?? 0;
                                        if (!empty($p->product_image)) {
                                            if (Str::startsWith($p->product_image, ['http://', 'https://'])) {
                                                $pImg = $p->product_image;
                                            } elseif (Str::startsWith($p->product_image, 'assets/')) {
                                                $pImg = asset($p->product_image);
                                            } else {
                                                $pImg = rtrim(env('MAIN_URL', 'http://127.0.0.1:8001/'), '/') . '/images/' . ltrim($p->product_image, '/');
                                            }
                                        } else {
                                            $pImg = asset('assets/images/thumbs/product-two-img1.png');
                                        }
                                    @endphp
                                    <div class="flex-align gap-14 p-6 rounded-12 hover-bg-neutral-30 transition-1">
                                        <div class="w-80 h-80 rounded-12 border border-gray-100 flex-shrink-0 overflow-hidden bg-gray-50 flex-center">
                                            <a href="/product/{{ $p->slug }}" class="w-100 h-100 flex-center">
                                                <img src="{{ $pImg }}" alt="{{ $p->product_name }}" class="w-100 h-100 object-fit-contain p-6" onerror="if (!this.dataset.tried) { this.dataset.tried = '1'; this.src='{{ env('MAIN_URL') }}images/{{ $p->product_image }}'; } else { this.src='/assets/images/thumbs/product-two-img1.png'; }">
                                            </a>
                                        </div>
                                        <div class="product-card__content flex-grow-1 min-w-0">
                                            <h6 class="title text-sm fw-semibold mb-6">
                                                <a href="/product/{{ $p->slug }}" class="link text-line-2 text-heading hover-text-main-600">{{ $p->product_name }}</a>
                                            </h6>
                                            <div class="product-card__price d-flex align-items-center gap-8">
                                                <span class="text-heading text-sm fw-bold text-main-600">{{ $currencySymbol }}{{ number_format($pOffer, 2) }}</span>
                                                @if($pMrp > $pOffer)
                                                    <span class="text-gray-400 text-xs fw-medium text-decoration-line-through">{{ $currencySymbol }}{{ number_format($pMrp, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="p-16 text-center text-neutral-500 text-sm">No top selling products.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- 3. Trending Products -->
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 bg-white">
                    <div class="p-16 bg-main-50 rounded-16 mb-24">
                        <h6 class="underlined-line position-relative mb-0 pb-12 d-inline-block fw-bold text-heading">Trending Products</h6>
                    </div>
                    <div class="short-product-list arrow-style-two max-h-unset">
                        @forelse($trending->chunk(4) as $chunk)
                            <div class="d-flex flex-column gap-20">
                                @foreach($chunk as $p)
                                    @php
                                        $pVar = $p->variants->first();
                                        $pOffer = $pVar->offer_price ?? $p->product_price ?? 0;
                                        $pMrp = $pVar->mrp_price ?? $pVar->product_mrp_price ?? 0;
                                        if (!empty($p->product_image)) {
                                            if (Str::startsWith($p->product_image, ['http://', 'https://'])) {
                                                $pImg = $p->product_image;
                                            } elseif (Str::startsWith($p->product_image, 'assets/')) {
                                                $pImg = asset($p->product_image);
                                            } else {
                                                $pImg = rtrim(env('MAIN_URL', 'http://127.0.0.1:8001/'), '/') . '/images/' . ltrim($p->product_image, '/');
                                            }
                                        } else {
                                            $pImg = asset('assets/images/thumbs/product-two-img1.png');
                                        }
                                    @endphp
                                    <div class="flex-align gap-14 p-6 rounded-12 hover-bg-neutral-30 transition-1">
                                        <div class="w-80 h-80 rounded-12 border border-gray-100 flex-shrink-0 overflow-hidden bg-gray-50 flex-center">
                                            <a href="/product/{{ $p->slug }}" class="w-100 h-100 flex-center">
                                                <img src="{{ $pImg }}" alt="{{ $p->product_name }}" class="w-100 h-100 object-fit-contain p-6" onerror="if (!this.dataset.tried) { this.dataset.tried = '1'; this.src='{{ env('MAIN_URL') }}images/{{ $p->product_image }}'; } else { this.src='/assets/images/thumbs/product-two-img1.png'; }">
                                            </a>
                                        </div>
                                        <div class="product-card__content flex-grow-1 min-w-0">
                                            <h6 class="title text-sm fw-semibold mb-6">
                                                <a href="/product/{{ $p->slug }}" class="link text-line-2 text-heading hover-text-main-600">{{ $p->product_name }}</a>
                                            </h6>
                                            <div class="product-card__price d-flex align-items-center gap-8">
                                                <span class="text-heading text-sm fw-bold text-main-600">{{ $currencySymbol }}{{ number_format($pOffer, 2) }}</span>
                                                @if($pMrp > $pOffer)
                                                    <span class="text-gray-400 text-xs fw-medium text-decoration-line-through">{{ $currencySymbol }}{{ number_format($pMrp, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="p-16 text-center text-neutral-500 text-sm">No trending products.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- 4. Deals of the Week -->
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                @if($dealOfWeek)
                    @php
                        $dwVar = $dealOfWeek->variants->first();
                        $dwOffer = $dwVar->offer_price ?? $dealOfWeek->product_price ?? 0;
                        $dwMrp = $dwVar->mrp_price ?? $dwVar->product_mrp_price ?? 0;
                        if (!empty($dealOfWeek->product_image)) {
                            if (Str::startsWith($dealOfWeek->product_image, ['http://', 'https://'])) {
                                $dwImg = $dealOfWeek->product_image;
                            } elseif (Str::startsWith($dealOfWeek->product_image, 'assets/')) {
                                $dwImg = asset($dealOfWeek->product_image);
                            } else {
                                $dwImg = rtrim(env('MAIN_URL', 'http://127.0.0.1:8001/'), '/') . '/images/' . ltrim($dealOfWeek->product_image, '/');
                            }
                        } else {
                            $dwImg = asset('assets/images/thumbs/product-two-img1.png');
                        }
                        $dwInCart = $dwVar ? in_array($dwVar->id, $cartProductIds ?? []) : false;
                        $dwCartItem = ($dwVar && isset($cartItems)) ? $cartItems->where('product_varient_id', $dwVar->id)->first() : null;
                        $dwOutOfStock = ($dwVar ? (int)($dwVar->product_qty ?? 0) : 0) <= 0;
                    @endphp
                    <div class="product-card h-100 p-20 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item bg-white d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-12">
                                <h6 class="position-relative mb-0 fw-bold text-heading text-16">Deal of the Week</h6>
                                <span class="badge bg-danger-50 text-danger-600 fw-bold text-xs px-8 py-4 rounded-pill">Special Deal</span>
                            </div>
                            @php
                                $dealOfWeekEndDate = null;
                                if (isset($dealOfWeek) && $dealOfWeek && $dealOfWeek->variants) {
                                    foreach($dealOfWeek->variants as $dv) {
                                        if (!empty($dv->flash_sale_date) && strtotime($dv->flash_sale_date) > time()) {
                                            $dealOfWeekEndDate = date('Y-m-d H:i:s', strtotime($dv->flash_sale_date));
                                            break;
                                        }
                                    }
                                }
                                if (!$dealOfWeekEndDate) {
                                    $dealOfWeekEndDate = now()->endOfWeek()->format('Y-m-d H:i:s');
                                }
                            @endphp
                            <div class="countdown mb-12" id="countdown26" data-date="{{ $dealOfWeekEndDate }}">
                                <ul class="countdown-list flex-align flex-wrap gap-4">
                                    <li class="countdown-list__item colon-red py-6 px-10 flex-align gap-4 text-xs fw-bold rounded-6 bg-main-600 text-white">
                                        <span class="days"></span> D
                                    </li>
                                    <li class="countdown-list__item colon-red py-6 px-10 flex-align gap-4 text-xs fw-bold rounded-6 bg-main-600 text-white">
                                        <span class="hours"></span> H
                                    </li>
                                    <li class="countdown-list__item colon-red py-6 px-10 flex-align gap-4 text-xs fw-bold rounded-6 bg-main-600 text-white">
                                        <span class="minutes"></span> M
                                    </li>
                                    <li class="countdown-list__item colon-red py-6 px-10 flex-align gap-4 text-xs fw-bold rounded-6 bg-main-600 text-white">
                                        <span class="seconds"></span> S
                                    </li>
                                </ul>
                            </div>
                            <a href="/product/{{ $dealOfWeek->slug }}" class="product-card__thumb flex-center overflow-hidden rounded-12 bg-gray-50 mb-12" style="height: 180px;">
                                <img src="{{ $dwImg }}" alt="{{ $dealOfWeek->product_name }}" class="w-100 h-100 object-fit-contain p-8" onerror="if (!this.dataset.tried) { this.dataset.tried = '1'; this.src='{{ env('MAIN_URL') }}images/{{ $dealOfWeek->product_image }}'; } else { this.src='/assets/images/thumbs/product-two-img1.png'; }">
                            </a>
                            <div class="product-card__content w-100">
                                <div class="d-flex align-items-center gap-8 mt-6">
                                    <span class="text-danger-600 mb-0 text-lg fw-bold">{{ $currencySymbol }}{{ number_format($dwOffer, 2) }}</span>
                                    @if($dwMrp > $dwOffer)
                                        <span class="text-neutral-400 fw-medium mb-0 text-sm text-decoration-line-through">{{ $currencySymbol }}{{ number_format($dwMrp, 2) }}</span>
                                    @endif
                                </div>
                                <h6 class="title text-sm fw-semibold mt-8 mb-8">
                                    <a href="/product/{{ $dealOfWeek->slug }}" class="link text-line-2 fw-bold text-heading hover-text-main-600">{{ $dealOfWeek->product_name }}</a>
                                </h6>
                            </div>
                        </div>
                        <div class="cart-control mt-12 w-100" data-product-id="{{ $dealOfWeek->id }}" data-variant-id="{{ $dwVar->id ?? '' }}">
                            @if($dwOutOfStock)
                                <button type="button" class="btn btn-outline-neutral-300 py-10 px-16 rounded-pill w-100 justify-content-center text-xs fw-semibold text-neutral-400" disabled>
                                    Out of Stock
                                </button>
                            @elseif($dwInCart)
                                <div class="qty-box">
                                    <button type="button" class="quantity__minus">−</button>
                                    <div class="d-flex align-items-center gap-4">
                                        <span class="qty-box-label">In Cart:</span>
                                        <input type="number" class="quantity__input" value="{{ $dwCartItem->product_quantity ?? 1 }}" min="1" readonly>
                                    </div>
                                    <button type="button" class="quantity__plus">+</button>
                                </div>
                            @else
                                <button type="button"
                                    class="add-to-cart-btn product-card__cart btn bg-success-600 text-white hover-bg-success-700 hover-text-white py-10 px-20 rounded-pill flex-align gap-8 w-100 justify-content-center text-sm fw-semibold">
                                    <span>Add To Cart</span> <i class="ph ph-shopping-cart"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
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

    function syncCart(variantId, quantity, input) {
        fetch("{{ route('cart.ajaxUpdate') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ product_varient_id: variantId, quantity: quantity })
        }).then(async r => {
            const data = await r.json();
            if (r.ok && data.success) {
                const finalQty = data.quantity !== undefined ? data.quantity : quantity;
                document.querySelectorAll(`.cart-control[data-variant-id="${variantId}"] .quantity__input`).forEach(inp => {
                    inp.value = finalQty;
                });
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

    // Global Delegated Click Listener for Cart Actions (works seamlessly with Slick Clones)
    document.addEventListener('click', function (e) {
        // 1. Add to Cart button
        const addBtn = e.target.closest('.add-to-cart-btn');
        if (addBtn) {
            e.preventDefault();
            e.stopPropagation();
            if (addBtn.disabled) return;

            const controlDiv = addBtn.closest('.cart-control');
            if (!controlDiv) return;

            const productId = controlDiv.dataset.productId;
            const variantId = controlDiv.dataset.variantId;
            if (!productId || !variantId) return;

            addBtn.disabled = true;
            const origContent = addBtn.innerHTML;
            addBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Adding...`;

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
                    document.querySelectorAll(`.cart-control[data-variant-id="${variantId}"]`).forEach(cd => {
                        cd.innerHTML = `
                            <div class="qty-box">
                                <button type="button" class="quantity__minus">−</button>
                                <div class="d-flex align-items-center gap-4">
                                    <span class="qty-box-label">In Cart:</span>
                                    <input type="number" class="quantity__input" value="1" min="1" readonly>
                                </div>
                                <button type="button" class="quantity__plus">+</button>
                            </div>`;
                    });

                    if (typeof updateCartCount === 'function') {
                        updateCartCount(1);
                    }
                    Swal.fire({
                        icon: 'success', title: data.message || 'Added to cart successfully!',
                        toast: true, position: 'top-end',
                        showConfirmButton: false, timer: 2000, timerProgressBar: true
                    });
                } else {
                    addBtn.disabled = false;
                    addBtn.innerHTML = origContent;
                    Swal.fire({
                        icon: 'warning', title: data.message || 'This product variant is currently unavailable.',
                        toast: true, position: 'top-end',
                        showConfirmButton: false, timer: 3000, timerProgressBar: true
                    });
                }
            }).catch(() => {
                addBtn.disabled = false;
                addBtn.innerHTML = origContent;
                Swal.fire({
                    icon: 'error', title: 'Network error',
                    toast: true, position: 'top-end',
                    showConfirmButton: false, timer: 2000
                });
            });
            return;
        }

        // 2. Quantity Plus button
        const plusBtn = e.target.closest('.quantity__plus');
        if (plusBtn) {
            e.preventDefault();
            e.stopPropagation();
            const controlDiv = plusBtn.closest('.cart-control');
            if (!controlDiv) return;
            const input = controlDiv.querySelector('.quantity__input');
            const variantId = controlDiv.dataset.variantId;
            if (!input || !variantId) return;

            const newQty = parseInt(input.value) + 1;
            syncCart(variantId, newQty, input);
            return;
        }

        // 3. Quantity Minus button
        const minusBtn = e.target.closest('.quantity__minus');
        if (minusBtn) {
            e.preventDefault();
            e.stopPropagation();
            const controlDiv = minusBtn.closest('.cart-control');
            if (!controlDiv) return;
            const input = controlDiv.querySelector('.quantity__input');
            const variantId = controlDiv.dataset.variantId;
            if (!input || !variantId) return;

            const currentQty = parseInt(input.value);
            if (currentQty <= 1) {
                fetch("{{ route('cart.ajaxRemove') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ product_varient_id: variantId })
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        document.querySelectorAll(`.cart-control[data-variant-id="${variantId}"]`).forEach(cd => {
                            cd.innerHTML = `
                                <button type="button"
                                    class="add-to-cart-btn product-card__cart btn py-8 py-sm-10 px-12 px-sm-16 rounded-pill flex-align gap-6 w-100 justify-content-center text-xs fw-semibold">
                                    <span>Add To Cart</span> <i class="ph-bold ph-shopping-cart-simple text-sm"></i>
                                </button>`;
                        });
                        if (typeof updateCartCount === 'function') {
                            updateCartCount(-1);
                        }
                    }
                });
            } else {
                syncCart(variantId, currentQty - 1, input);
            }
            return;
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
            const code = this.querySelector('span')?.textContent?.trim() || 'FREE25BAC';
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

    // Dynamic Countdown Timer Initialization
    if (typeof initializeCountdown === 'function') {
        initializeCountdown('countdown4', '{{ $dealsOfDayEndDate }}');
        initializeCountdown('countdown26', '{{ $dealOfWeekEndDate }}');
    }
});
</script>

  @endsection