@extends('layouts.app')
@section('main')


    <!-- ============================ Banner Section start =============================== -->
<div class="banner">
    <div class="container container-lg">
        <div class="banner-item rounded-24 overflow-hidden position-relative arrow-center" style="height: 450px;">
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
                            <div class="banner-item__content" style="margin-top: -95px;">
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
                            <div class="banner-item__content" style="margin-top: -95px;">
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
                        <a href="/products" class="w-100 h-100 flex-center">
                            <img src="{{ env('MAIN_URL') }}images/{{ $category->category_image }}" alt="{{ $category->category_name }}">
                        </a>
                    </div>
                    <div class="feature-item__content mt-16">
                        <h6 class="text-lg mb-8"><a href="/products" class="text-inherit">{{ $category->category_name }}</a></h6>
                        <!-- <span class="text-sm text-gray-400">125+ Products</span> -->
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
            <div class="col-xl-3 col-sm-6 col-xs-6 wow bounceIn" data-aos="fade-up" data-aos-duration="400">
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
            <div class="col-xl-3 col-sm-6 col-xs-6 wow bounceIn" data-aos="fade-up" data-aos-duration="600">
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
            <div class="col-xl-3 col-sm-6 col-xs-6 wow bounceIn" data-aos="fade-up" data-aos-duration="800">
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
            <div class="col-xl-3 col-sm-6 col-xs-6 wow bounceIn" data-aos="fade-up" data-aos-duration="1000">
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
            <ul class="nav common-tab nav-pills wow fadeInRight" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-medium text-sm border border-white hover-border-main-600 active" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all" aria-selected="true">All</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-medium text-sm border border-white hover-border-main-600" id="pills-grocery-tab" data-bs-toggle="pill" data-bs-target="#pills-grocery" type="button" role="tab" aria-controls="pills-grocery" aria-selected="false">Grocery</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-medium text-sm border border-white hover-border-main-600" id="pills-fruits-tab" data-bs-toggle="pill" data-bs-target="#pills-fruits" type="button" role="tab" aria-controls="pills-fruits" aria-selected="false">Fruits</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-medium text-sm border border-white hover-border-main-600" id="pills-juices-tab" data-bs-toggle="pill" data-bs-target="#pills-juices" type="button" role="tab" aria-controls="pills-juices" aria-selected="false">Juices</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-medium text-sm border border-white hover-border-main-600" id="pills-vegetables-tab" data-bs-toggle="pill" data-bs-target="#pills-vegetables" type="button" role="tab" aria-controls="pills-vegetables" aria-selected="false">Vegetables</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-medium text-sm border border-white hover-border-main-600" id="pills-snacks-tab" data-bs-toggle="pill" data-bs-target="#pills-snacks" type="button" role="tab" aria-controls="pills-snacks" aria-selected="false">Snacks</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-medium text-sm border border-white hover-border-main-600" id="pills-organic-tab" data-bs-toggle="pill" data-bs-target="#pills-organic" type="button" role="tab" aria-controls="pills-organic" aria-selected="false">Organic Foods</button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="pills-tabContent">

            <!-- ALL TAB -->
            <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab" tabindex="0">
                <div class="row g-12">

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1550583724-b2692b85b150?w=300&h=300&fit=crop&auto=format" alt="Vitamin C Supplement">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">C-500 Antioxidant Protect Dietary Supplement</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                   
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="400">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1600718374662-0483d2b9da44?w=300&h=300&fit=crop&auto=format" alt="Almond Milk">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Marcel's Modern Pantry Almond Unsweetened</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                   
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="600">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1563636619-e9143da7973b?w=300&h=300&fit=crop&auto=format" alt="Whole Milk">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">O Organics Milk, Whole, Vitamin D</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                   
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="800">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-info-600 px-8 py-4 text-sm text-white">Best Sale</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300&h=300&fit=crop&auto=format" alt="Organic Bread">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Whole Grains and Seeds Organic Bread</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                  
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1000">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1488477181946-6428a0291777?w=300&h=300&fit=crop&auto=format" alt="Strawberry Yogurt">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Lucerne Yogurt, Lowfat, Strawberry</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                 
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?w=300&h=300&fit=crop&auto=format" alt="Granola Bar">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Nature Valley Whole Grain Oats and Honey Protein</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                   
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300&h=300&fit=crop&auto=format" alt="Fresh Salad">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Fresh Garden Salad Mix Organic Greens</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                   
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="400">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1519996529931-28324d5a630e?w=300&h=300&fit=crop&auto=format" alt="Mixed Fruits">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Fresh Mixed Tropical Fruit Basket Assorted</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="800">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=300&h=300&fit=crop&auto=format" alt="Organic Blueberries">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Organic Blueberries Fresh Pint Antioxidant Rich</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="800">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=300&h=300&fit=crop&auto=format" alt="Atlantic Salmon">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Good & Gather Farmed Atlantic Salmon</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1000">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1559737558-2f5a35f4523b?w=300&h=300&fit=crop&auto=format" alt="Raw Shrimp">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Market Pantry 41/50 Raw Tail-Off Large Raw Shrimp</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-warning-600 px-8 py-4 text-sm text-white">New</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=300&h=300&fit=crop&auto=format" alt="Orange Juice">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Tropicana 100% Juice, Orange, No Pulp</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- GROCERY TAB -->
            <div class="tab-pane fade" id="pills-grocery" role="tabpanel" aria-labelledby="pills-grocery-tab" tabindex="0">
                <div class="row g-12">

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300&h=300&fit=crop&auto=format" alt="Organic Bread">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Whole Grains and Seeds Organic Bread</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="400">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1563636619-e9143da7973b?w=300&h=300&fit=crop&auto=format" alt="Whole Milk">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">O Organics Milk, Whole, Vitamin D</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="600">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-info-600 px-8 py-4 text-sm text-white">Best Sale</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1550583724-b2692b85b150?w=300&h=300&fit=crop&auto=format" alt="Vitamin Supplement">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">C-500 Antioxidant Protect Dietary Supplement</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="800">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1600718374662-0483d2b9da44?w=300&h=300&fit=crop&auto=format" alt="Almond Milk">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Marcel's Modern Pantry Almond Unsweetened</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1000">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?w=300&h=300&fit=crop&auto=format" alt="Granola Bar">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Nature Valley Whole Grain Oats and Honey Protein</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-warning-600 px-8 py-4 text-sm text-white">New</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1488477181946-6428a0291777?w=300&h=300&fit=crop&auto=format" alt="Yogurt">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Lucerne Yogurt, Lowfat, Strawberry</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- FRUITS TAB -->
            <div class="tab-pane fade" id="pills-fruits" role="tabpanel" aria-labelledby="pills-fruits-tab" tabindex="0">
                <div class="row g-12">

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1619566636858-adf3ef46400b?w=300&h=300&fit=crop&auto=format" alt="Fresh Fruits">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Fresh Mixed Tropical Fruit Basket Assorted</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="400">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?w=300&h=300&fit=crop&auto=format" alt="Apple">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Red Delicious Premium Apple Fresh Picked</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                   

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="800">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1528821128474-27f963b062bf?w=300&h=300&fit=crop&auto=format" alt="Watermelon">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Seedless Watermelon Fresh Whole Summer</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1000">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-warning-600 px-8 py-4 text-sm text-white">New</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1519996529931-28324d5a630e?w=300&h=300&fit=crop&auto=format" alt="Grapes">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Sweet Green Seedless Grapes Fresh Bunch</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=300&h=300&fit=crop&auto=format" alt="Blueberries">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Organic Blueberries Fresh Pint Antioxidant Rich</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- JUICES TAB -->
            <div class="tab-pane fade" id="pills-juices" role="tabpanel" aria-labelledby="pills-juices-tab" tabindex="0">
                <div class="row g-12">

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-warning-600 px-8 py-4 text-sm text-white">New</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=300&h=300&fit=crop&auto=format" alt="Orange Juice">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Tropicana 100% Juice, Orange, No Pulp</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                  

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="600">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1546173159-315724a31696?w=300&h=300&fit=crop&auto=format" alt="Green Smoothie">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Green Detox Smoothie Spinach Kale Banana Blend</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                  

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1000">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=300&h=300&fit=crop&auto=format" alt="Lemonade">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Simply Lemonade All Natural Fresh Squeezed Style</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=300&h=300&fit=crop&auto=format" alt="Coconut Water">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Vita Coco Pure Coconut Water Natural Electrolytes</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- VEGETABLES TAB -->
            <div class="tab-pane fade" id="pills-vegetables" role="tabpanel" aria-labelledby="pills-vegetables-tab" tabindex="0">
                <div class="row g-12">

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1540420773420-3366772f4999?w=300&h=300&fit=crop&auto=format" alt="Mixed Vegetables">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Fresh Mixed Vegetables Seasonal Garden Harvest</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="400">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-info-600 px-8 py-4 text-sm text-white">Best Sale</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1584270354949-c26b0d5b4a0c?w=300&h=300&fit=crop&auto=format" alt="Broccoli">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Fresh Organic Broccoli Crown Green Nutrient Rich</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="800">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1518977676601-b53f82aba655?w=300&h=300&fit=crop&auto=format" alt="Potatoes">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Russet Potato Bag 5 lb Farm Fresh Idaho</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1000">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-warning-600 px-8 py-4 text-sm text-white">New</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1582515073490-39981397c445?w=300&h=300&fit=crop&auto=format" alt="Tomatoes">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Vine Ripe Tomatoes Organic Cherry Red Fresh</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1587334274328-64186a80aeee?w=300&h=300&fit=crop&auto=format" alt="Carrots">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Organic Carrots Fresh Bunch Whole Baby Rainbow</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- SNACKS TAB -->
            <div class="tab-pane fade" id="pills-snacks" role="tabpanel" aria-labelledby="pills-snacks-tab" tabindex="0">
                <div class="row g-12">

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1621939514649-280e2ee25f60?w=300&h=300&fit=crop&auto=format" alt="Snacks">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Lays Classic Salted Potato Chips Party Pack</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="400">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?w=300&h=300&fit=crop&auto=format" alt="Granola Bar">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Nature Valley Whole Grain Oats and Honey Protein</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="600">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-warning-600 px-8 py-4 text-sm text-white">New</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1582058091505-f87a2e55a40f?w=300&h=300&fit=crop&auto=format" alt="Candy">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Skittles Original Fruit Candy Chewy Rainbow</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                  

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1000">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1560008581-09826d1de69e?w=300&h=300&fit=crop&auto=format" alt="Cookies">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Oreo Double Stuf Chocolate Sandwich Cookies Pack</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1585238342024-78d387f4a707?w=300&h=300&fit=crop&auto=format" alt="Popcorn">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Skinny Pop Original Popcorn Gluten Free Low Calorie</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ORGANIC FOODS TAB -->
            <div class="tab-pane fade" id="pills-organic" role="tabpanel" aria-labelledby="pills-organic-tab" tabindex="0">
                <div class="row g-12">

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300&h=300&fit=crop&auto=format" alt="Organic Salad">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Organic Garden Salad Fresh Mixed Greens Bowl</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="400">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-info-600 px-8 py-4 text-sm text-white">Best Sale</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300&h=300&fit=crop&auto=format" alt="Organic Bread">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Whole Grains and Seeds Organic Bread Loaf</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="600">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1488477181946-6428a0291777?w=300&h=300&fit=crop&auto=format" alt="Organic Yogurt">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Stonyfield Organic Whole Milk Plain Yogurt</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="800">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=300&h=300&fit=crop&auto=format" alt="Organic Blueberries">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Organic Blueberries Fresh Pint Antioxidant Rich</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1000">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-warning-600 px-8 py-4 text-sm text-white">New</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1563636619-e9143da7973b?w=300&h=300&fit=crop&auto=format" alt="Organic Milk">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Horizon Organic Whole Milk Grass Fed Vitamin D</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="1200">
                        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item">
                            <button type="button" class="wishlist-btn-two"><i class="ph-bold ph-heart"></i></button>
                            <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Sale 50%</span>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1550583724-b2692b85b150?w=300&h=300&fit=crop&auto=format" alt="Organic Supplement">
                            </a>
                            <div class="product-card__content p-sm-2 w-100">
                                <h6 class="title text-lg fw-semibold my-12"><a href="/products" class="link text-line-2">Garden of Life Raw Organic Protein Powder</a></h6>
                                
                                <div class="product-card__content mt-12">
                                    <div class="product-card__price mb-8">
                                        <span class="text-heading text-md fw-semibold">₹14.99 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through"> ₹28.99</span>
                                    </div>
                                    
                                    <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">Add To Cart <i class="ph ph-shopping-cart"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
<!-- ========================= Recommended End ================================ -->


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
                        <div class="product-card px-20 pt-16 pb-40 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 position-absolute inset-block-start-0 inset-inline-end-0 me-16 mt-16">
                                Add <i class="ph ph-shopping-cart"></i>
                            </a>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1459411621453-7b03977f4bfc?w=300&h=300&fit=crop&auto=format" alt="Broccoli">
                            </a>
                            <div class="product-card__content mt-12">
                                <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                    <span class="text-heading text-md fw-semibold">₹45.00 <span class="text-gray-500 fw-normal">/Qty</span></span>
                                    <span class="text-gray-400 text-md fw-semibold text-decoration-line-through">₹80.00</span>
                                </div>
                                <div class="flex-align gap-6">
                                    <span class="text-xs fw-bold text-gray-600">4.8</span>
                                    <span class="text-15 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                    <span class="text-xs fw-bold text-gray-600">(17k)</span>
                                </div>
                                <h6 class="title text-lg fw-semibold mt-12 mb-20">
                                    <a href="/products" class="link text-line-2">Fresh Broccoli Florets (500g)</a>
                                </h6>
                                <div class="mt-12">
                                    <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: 35%"></div>
                                    </div>
                                    <span class="text-gray-900 text-xs fw-medium mt-8">Sold: 18/35</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Product 2: Tomatoes --}}
                    <div class="" data-aos="fade-up" data-aos-duration="400">
                        <div class="product-card px-20 pt-16 pb-40 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 position-absolute inset-block-start-0 inset-inline-end-0 me-16 mt-16">
                                Add <i class="ph ph-shopping-cart"></i>
                            </a>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1546094096-0df4bcaaa337?w=300&h=300&fit=crop&auto=format" alt="Tomatoes">
                            </a>
                            <div class="product-card__content mt-12">
                                <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                    <span class="text-heading text-md fw-semibold">₹40.00 <span class="text-gray-500 fw-normal">/Kg</span></span>
                                    <span class="text-gray-400 text-md fw-semibold text-decoration-line-through">₹65.00</span>
                                </div>
                                <div class="flex-align gap-6">
                                    <span class="text-xs fw-bold text-gray-600">4.7</span>
                                    <span class="text-15 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                    <span class="text-xs fw-bold text-gray-600">(12k)</span>
                                </div>
                                <h6 class="title text-lg fw-semibold mt-12 mb-20">
                                    <a href="/products" class="link text-line-2">Farm Fresh Red Tomatoes (1 Kg)</a>
                                </h6>
                                <div class="mt-12">
                                    <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: 60%"></div>
                                    </div>
                                    <span class="text-gray-900 text-xs fw-medium mt-8">Sold: 24/40</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Product 3: Eggs --}}
                    <div class="" data-aos="fade-up" data-aos-duration="600">
                        <div class="product-card px-20 pt-16 pb-40 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 position-absolute inset-block-start-0 inset-inline-end-0 me-16 mt-16">
                                Add <i class="ph ph-shopping-cart"></i>
                            </a>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1491524062933-cb0289261700?w=300&h=300&fit=crop&auto=format" alt="Eggs">
                            </a>
                            <div class="product-card__content mt-12">
                                <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                    <span class="text-heading text-md fw-semibold">₹72.00 <span class="text-gray-500 fw-normal">/Dozen</span></span>
                                    <span class="text-gray-400 text-md fw-semibold text-decoration-line-through">₹90.00</span>
                                </div>
                                <div class="flex-align gap-6">
                                    <span class="text-xs fw-bold text-gray-600">4.9</span>
                                    <span class="text-15 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                    <span class="text-xs fw-bold text-gray-600">(20k)</span>
                                </div>
                                <h6 class="title text-lg fw-semibold mt-12 mb-20">
                                    <a href="/products" class="link text-line-2">Farm Fresh White Eggs (1 Dozen)</a>
                                </h6>
                                <div class="mt-12">
                                    <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: 70%"></div>
                                    </div>
                                    <span class="text-gray-900 text-xs fw-medium mt-8">Sold: 42/60</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Product 4: Milk --}}
                    <div class="" data-aos="fade-up" data-aos-duration="800">
                        <div class="product-card px-20 pt-16 pb-40 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 position-absolute inset-block-start-0 inset-inline-end-0 me-16 mt-16">
                                Add <i class="ph ph-shopping-cart"></i>
                            </a>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1563636619-e9143da7973b?w=300&h=300&fit=crop&auto=format" alt="Milk">
                            </a>
                            <div class="product-card__content mt-12">
                                <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                    <span class="text-heading text-md fw-semibold">₹55.00 <span class="text-gray-500 fw-normal">/Ltr</span></span>
                                    <span class="text-gray-400 text-md fw-semibold text-decoration-line-through">₹70.00</span>
                                </div>
                                <div class="flex-align gap-6">
                                    <span class="text-xs fw-bold text-gray-600">4.8</span>
                                    <span class="text-15 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                    <span class="text-xs fw-bold text-gray-600">(15k)</span>
                                </div>
                                <h6 class="title text-lg fw-semibold mt-12 mb-20">
                                    <a href="/products" class="link text-line-2">Full Cream Fresh Milk (1 Litre)</a>
                                </h6>
                                <div class="mt-12">
                                    <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: 50%"></div>
                                    </div>
                                    <span class="text-gray-900 text-xs fw-medium mt-8">Sold: 25/50</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Product 5: Onions --}}
                    <div class="" data-aos="fade-up" data-aos-duration="1000">
                        <div class="product-card px-20 pt-16 pb-40 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 position-absolute inset-block-start-0 inset-inline-end-0 me-16 mt-16">
                                Add <i class="ph ph-shopping-cart"></i>
                            </a>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1508747703725-719777637510?w=300&h=300&fit=crop&auto=format" alt="Onions">
                            </a>
                            <div class="product-card__content mt-12">
                                <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                    <span class="text-heading text-md fw-semibold">₹35.00 <span class="text-gray-500 fw-normal">/Kg</span></span>
                                    <span class="text-gray-400 text-md fw-semibold text-decoration-line-through">₹55.00</span>
                                </div>
                                <div class="flex-align gap-6">
                                    <span class="text-xs fw-bold text-gray-600">4.6</span>
                                    <span class="text-15 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                    <span class="text-xs fw-bold text-gray-600">(9k)</span>
                                </div>
                                <h6 class="title text-lg fw-semibold mt-12 mb-20">
                                    <a href="/products" class="link text-line-2">Fresh Red Onions (1 Kg)</a>
                                </h6>
                                <div class="mt-12">
                                    <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: 45%"></div>
                                    </div>
                                    <span class="text-gray-900 text-xs fw-medium mt-8">Sold: 27/60</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Product 6: Cooking Oil --}}
                    <div class="" data-aos="fade-up" data-aos-duration="1200">
                        <div class="product-card px-20 pt-16 pb-40 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 position-absolute inset-block-start-0 inset-inline-end-0 me-16 mt-16">
                                Add <i class="ph ph-shopping-cart"></i>
                            </a>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=300&h=300&fit=crop&auto=format" alt="Cooking Oil">
                            </a>
                            <div class="product-card__content mt-12">
                                <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                    <span class="text-heading text-md fw-semibold">₹160.00 <span class="text-gray-500 fw-normal">/Ltr</span></span>
                                    <span class="text-gray-400 text-md fw-semibold text-decoration-line-through">₹210.00</span>
                                </div>
                                <div class="flex-align gap-6">
                                    <span class="text-xs fw-bold text-gray-600">4.7</span>
                                    <span class="text-15 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                    <span class="text-xs fw-bold text-gray-600">(11k)</span>
                                </div>
                                <h6 class="title text-lg fw-semibold mt-12 mb-20">
                                    <a href="/products" class="link text-line-2">Pure Sunflower Cooking Oil (1 Litre)</a>
                                </h6>
                                <div class="mt-12">
                                    <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: 55%"></div>
                                    </div>
                                    <span class="text-gray-900 text-xs fw-medium mt-8">Sold: 33/60</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Product 7: Rice --}}
                    <div class="" data-aos="fade-up" data-aos-duration="600">
                        <div class="product-card px-20 pt-16 pb-40 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                            <a href="/cart" class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 position-absolute inset-block-start-0 inset-inline-end-0 me-16 mt-16">
                                Add <i class="ph ph-shopping-cart"></i>
                            </a>
                            <a href="/products" class="product-card__thumb flex-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1536304929831-ee1ca9d44906?w=300&h=300&fit=crop&auto=format" alt="Rice">
                            </a>
                            <div class="product-card__content mt-12">
                                <div class="product-card__price mb-8 d-flex align-items-center gap-8">
                                    <span class="text-heading text-md fw-semibold">₹85.00 <span class="text-gray-500 fw-normal">/Kg</span></span>
                                    <span class="text-gray-400 text-md fw-semibold text-decoration-line-through">₹110.00</span>
                                </div>
                                <div class="flex-align gap-6">
                                    <span class="text-xs fw-bold text-gray-600">4.9</span>
                                    <span class="text-15 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                    <span class="text-xs fw-bold text-gray-600">(22k)</span>
                                </div>
                                <h6 class="title text-lg fw-semibold mt-12 mb-20">
                                    <a href="/products" class="link text-line-2">Premium Basmati Rice (1 Kg)</a>
                                </h6>
                                <div class="mt-12">
                                    <div class="progress w-100 bg-color-three rounded-pill h-4" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: 80%"></div>
                                    </div>
                                    <span class="text-gray-900 text-xs fw-medium mt-8">Sold: 48/60</span>
                                </div>
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
        <div class="border border-main-500 bg-main-50 border-dashed rounded-8 py-20 d-flex align-items-center justify-content-evenly">
            <p class="h6 text-main-600 fw-normal">Super discount for your <a href="javascript:void(0)" class="fw-bold text-decoration-underline text-main-600 hover-text-decoration-none hover-text-primary-600 ">first purchase</a> </p>
            <div class="position-relative">
                <button class="copy-coupon-btn px-32 py-10 text-white text-uppercase bg-main-600 rounded-pill border-0 hover-bg-main-800 ">
                    FREE25BAC
                    <i class="ph ph-file-text text-lg line-height-1"></i>
                </button>
                <span class="copy-text bg-main-600 text-white fw-normal position-absolute px-16 py-6 rounded-pill bottom-100 start-50 translate-middle-x min-w-max mb-8 text-xs"></span>
            </div>
            <p class="text-md text-main-600 fw-normal">Use discount code to get <span class="fw-bold text-main-600">20% </span> discount for any item</p>
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

  @endsection