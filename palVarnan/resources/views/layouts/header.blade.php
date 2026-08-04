
<!-- ==================== Mobile Menu Start Here ==================== -->
<div class="mobile-menu scroll-sm d-lg-none d-block">
    <button type="button" class="close-button"> <i class="ph ph-x"></i> </button>
    <div class="mobile-menu__inner">
        <a href="/" class="mobile-menu__logo">
            <img src="/assets/images/logo/logo.png" alt="Logo"> 
        </a>
        <div class="mobile-menu__menu">
            <!-- Nav Menu Start -->
<ul class="nav-menu flex-align nav-menu--mobile">
    <li class="on-hover-item nav-menu__item has-submenu activePage">
        <a href="javascript:void(0)" class="nav-menu__link text-heading-two">Home</a>
        <ul class="on-hover-dropdown common-dropdown nav-submenu scroll-sm">
            <li class="common-dropdown__item nav-submenu__item activePage">
                <a href="/" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Home Grocery</a>
            </li>
            <li class="common-dropdown__item nav-submenu__item">
                <a href="index-two.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Home Electronics</a>
            </li>
            <li class="common-dropdown__item nav-submenu__item">
                <a href="index-three.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Home Fashion</a>
            </li>
        </ul>
    </li>
    <li class="on-hover-item nav-menu__item has-submenu">
        <a href="javascript:void(0)" class="nav-menu__link text-heading-two">Shop</a>
         <ul class="on-hover-dropdown common-dropdown nav-submenu scroll-sm">
            <li class="common-dropdown__item nav-submenu__item">
                <a href="shop.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Shop</a>
            </li>
            <li class="common-dropdown__item nav-submenu__item">
                <a href="product-details.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Shop Details</a>
            </li>
            <li class="common-dropdown__item nav-submenu__item">
                <a href="product-details-two.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Shop Details Two</a>
            </li>
        </ul>
    </li>
    <li class="on-hover-item nav-menu__item has-submenu">
        <span class="badge-notification bg-warning-600 text-white text-sm py-2 px-8 rounded-4">New</span>
        <a href="javascript:void(0)" class="nav-menu__link text-heading-two">Pages</a>
         <ul class="on-hover-dropdown common-dropdown nav-submenu scroll-sm">
            <li class="common-dropdown__item nav-submenu__item">
                <a href="{{ url('/cart') }}" class="flex-align gap-6 item-hover">
                    <span class="text-2xl text-heading d-flex position-relative me-6 mt-6 item-hover__text">
                        <i class="ph-bold ph-shopping-cart"></i>

                        <span id="cart-count"
                            class="w-18 h-18 flex-center rounded-circle bg-success-600 text-white text-xs position-absolute top-n6 end-n4">
                            {{ $cartCount }}
                        </span>
                    </span>

                    <span class="text-md text-neutral-500 item-hover__text fw-medium d-none d-lg-flex">
                        Cart
                    </span>
                </a>
            </li>
            <li class="common-dropdown__item nav-submenu__item">
                @auth
                <a href="/wishlist" class="flex-align gap-6 item-hover">
                    <span class="text-2xl text-heading d-flex position-relative me-6 mt-6 item-hover__text">
                        <i class="ph-bold ph-heart"></i>
                        <span class="w-18 h-18 flex-center rounded-circle bg-success-600 text-white text-xs position-absolute top-n6 end-n4">
                            {{ $wishlistCount }}
                        </span>
                    </span>
                    <span class="text-md text-neutral-500 item-hover__text fw-medium d-none d-lg-flex">Wishlist</span>
                </a>
                @endauth
            </li>
            <li class="common-dropdown__item nav-submenu__item">
                <a href="checkout.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Checkout </a>
            </li>
            <li class="common-dropdown__item nav-submenu__item">
                <a href="become-seller.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Become Seller</a>
            </li>
            <li class="common-dropdown__item nav-submenu__item">
                <a href="account.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Account</a>
            </li>
        </ul>
    </li>
    <li class="on-hover-item nav-menu__item has-submenu">
        <span class="badge-notification bg-tertiary-600 text-white text-sm py-2 px-8 rounded-4">New</span>
        <a href="javascript:void(0)" class="nav-menu__link text-heading-two">Vendors</a>
         <ul class="on-hover-dropdown common-dropdown nav-submenu scroll-sm">
            <li class="common-dropdown__item nav-submenu__item">
                <a href="vendor.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Vendors </a>
            </li>
            <li class="common-dropdown__item nav-submenu__item">
                <a href="vendor-details.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Vendor Details </a>
            </li>
            <li class="common-dropdown__item nav-submenu__item">
                <a href="vendor-two.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Vendors Two</a>
            </li>
            <li class="common-dropdown__item nav-submenu__item">
                <a href="vendor-two-details.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Vendors Two Details</a>
            </li>
        </ul>
    </li>
    <li class="on-hover-item nav-menu__item has-submenu">
        <a href="javascript:void(0)" class="nav-menu__link text-heading-two">Blog</a>
         <ul class="on-hover-dropdown common-dropdown nav-submenu scroll-sm">
            <li class="common-dropdown__item nav-submenu__item">
                <a href="blog.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Blog</a>
            </li>
            <li class="common-dropdown__item nav-submenu__item">
                <a href="blog-details.html" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Blog Details</a>
            </li>
        </ul>
    </li>
    <li class="nav-menu__item">
        <a href="contact.html" class="nav-menu__link text-heading-two">Contact Us</a>
    </li>
</ul>
<!-- Nav Menu End -->
        </div>
    </div>
</div>
<!-- ==================== Mobile Menu End Here ==================== -->


    <!-- ======================= Middle Header Two Start ========================= -->
<header class="header-middle border-bottom border-neutral-40 py-4">
    <div class="container container-lg">
        <nav class="header-inner flex-between gap-8">
            <!-- Logo Start -->
            <div class="logo">
                <a href="/" class="link">
                    <img src="/assets/images/logo/logo.png" alt="Logo"> 
                </a>
            </div>
            <!-- Logo End  -->

             <!-- Menu Start  -->
             <div class="header-menu d-lg-block d-none">
                <!-- Nav Menu Start -->
<ul class="nav-menu flex-align ">
   <li class="nav-menu__item">
        <a href="/" class="nav-menu__link text-heading-two">Home</a>
    </li>
    <li class="nav-menu__item">
        <a href="contact.html" class="nav-menu__link text-heading-two">About Us</a>
    </li>
    <li class="nav-menu__item">
        <a href="/products" class="nav-menu__link text-heading-two">Products</a>
    </li>
    <li class="nav-menu__item">
        <a href="contact.html" class="nav-menu__link text-heading-two">Contact Us</a>
    </li>
</ul>
<!-- Nav Menu End -->
            </div>
            <!-- Menu End  -->
             
             <!-- Middle Header Right start -->
             <div class="header-right flex-align">
                <!-- Dropdown Select Start -->
                <ul class="header-top__right style-two style-three flex-align flex-wrap">
                   
                    <li class="on-hover-item ">
                        <a href="javascript:void(0)" class="selected-text selected-text text-neutral-500 fw-semibold text-sm py-8 text-sm py-8">Rupee</a>
                        <!-- <ul class="selectable-text-list on-hover-dropdown common-dropdown common-dropdown--sm max-h-200 scroll-sm px-0 py-8">
                            <li>
                                <a href="javascript:void(0)" class="hover-bg-gray-100 text-gray-500 text-xs py-6 px-16 flex-align gap-8 rounded-0"> 
                                    
                                    Rupee
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="hover-bg-gray-100 text-gray-500 text-xs py-6 px-16 flex-align gap-8 rounded-0"> 
                                   
                                     Peso
                                </a>
                            </li>
                            
                        </ul> -->
                    </li>
                    <li class="d-sm-flex d-none">
                        <a href="javascript:void(0)" class="selected-text selected-text text-neutral-500 fw-semibold text-sm py-8 text-sm py-8 hover-text-heading">Order Tracking</a>
                    </li>
                </ul>
                <!-- Dropdown Select End -->
                <button type="button" class="toggle-mobileMenu d-lg-none ms-3n text-gray-800 text-4xl d-flex"> <i class="ph ph-list"></i> </button>
            </div>
            <!-- Middle Header Right End  -->
            
        </nav>
    </div>
</header>
<!-- ======================= Middle Header Two End ========================= -->

    <!-- ==================== Header Two Start Here ==================== -->
<header class="header bg-white  pt-24">
    <div class="container container-lg">
        <nav class="header-inner d-flex justify-content-between gap-16" style="margin-bottom: 25px;">
            <div class="d-flex w-100">

                <!-- Category Dropdown Start -->
                <div class="category-two h-100 d-none flex-shrink-0">
                    <button type="button" class="category__button flex-align gap-8 fw-medium bg-main-two-600 py-16 px-20 text-white h-100 md-rounded-top">
                        <span class="icon text-2xl d-md-flex d-none"><i class="ph ph-squares-four"></i></span>
                        <span class="d-lg-flex d-none">All</span>  Categories
                        <span class="arrow-icon text-md d-flex ms-auto"><i class="ph ph-caret-down"></i></span>
                    </button>

                    <div class="responsive-dropdown common-dropdown d-lg-none d-block nav-submenu p-0 submenus-submenu-wrapper shadow-none border border-gray-100">
    <button type="button" class="close-responsive-dropdown rounded-circle text-xl position-absolute inset-inline-end-0 inset-block-start-0 mt-4 me-8 d-lg-none d-flex"> <i class="ph ph-x"></i> </button>

    <div class="logo px-16 d-lg-none d-block">
        <a href="/" class="link">
            <img src="/assets/images/logo/logo.png" alt="Logo">
        </a>
    </div>

    <ul class="scroll-sm p-0 py-8 overflow-y-auto">
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Cell Phone</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Cell Phone</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Wear</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Wear</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Computer</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Computer</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Headphone</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Headphone</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Smart Screen</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Smart Screen</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Smart Home</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Smart Home</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Digital Accessories</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Digital Accessories</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span> Value Added Services</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title"> Value Added Services</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Car Products</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Car Products</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Ecological Products</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Ecological Products</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Flat</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Flat</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Commercial Terminal</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Commercial Terminal</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Headphone</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Headphone</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="has-submenus-submenu">
            <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                <span>Smart Screen</span>
                <span class="icon text-md d-flex ms-auto"><i class="ph ph-caret-right"></i></span>
            </a>

            <div class="submenus-submenu py-16">
                <h6 class="text-lg px-16 submenus-submenu__title">Smart Screen</h6>
                <ul class="submenus-submenu__list max-h-300 overflow-y-auto scroll-sm">
                    <li>
                        <a href="shop.html">Samsung</a>
                    </li>
                    <li>
                        <a href="shop.html">Iphone</a>
                    </li>
                    <li>
                        <a href="shop.html">Vivo</a>
                    </li>
                    <li>
                        <a href="shop.html">Oppo</a>
                    </li>
                    <li>
                        <a href="shop.html">Itel</a>
                    </li>
                    <li>
                        <a href="shop.html">Realme</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>
                </div> 
                <div class="category d-block on-hover-item text-white flex-shrink-0 w-310">
                    <button type="button" class="category__button flex-align gap-8 fw-medium p-16 bg-main-600 text-white rounded-top h-100 w-100">
                        <span class="icon text-2xl d-md-flex d-none"><i class="ph ph-squares-four"></i></span>
                        <span class="d-sm-flex d-none">All</span>  Categories
                        <span class="arrow-icon text-xl d-flex ms-auto"><i class="ph ph-caret-down"></i></span>
                    </button>

                    <div class="responsive-dropdown on-hover-dropdown common-dropdown nav-submenu p-0 submenus-submenu-wrapper">
                        <button type="button" class="close-responsive-dropdown rounded-circle text-xl position-absolute inset-inline-end-0 inset-block-start-0 mt-4 me-8 d-lg-none d-flex"> <i class="ph ph-x"></i> </button>
                        <div class="logo px-16 d-lg-none d-block">
                            <a href="/" class="link">
                                <img src="/assets/images/logo/logo.png" alt="Logo">
                            </a>
                        </div>
                        <ul class="scroll-sm p-0 py-8 w-300 max-h-400 overflow-y-auto">
                            <li class="has-submenus-submenu">
                                <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                                    <span class="text-xl d-flex"><i class="ph ph-carrot"></i></span>
                                    <span>Vegetables &amp; Fruit</span>
                                    
                                </a>
    
                               
                            </li>
                            <li class="has-submenus-submenu">
                                <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                                    <span class="text-xl d-flex"><i class="ph ph-brandy"></i></span>
                                    <span>Dairy & Eggs </span>
                                    
                                </a>
                              
                            </li>
                            <li class="has-submenus-submenu">
                                <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                                    <span class="text-xl d-flex"><i class="ph ph-brandy"></i></span>
                                    <span>Rice, Atta & Grains </span>
                                    
                                </a>
                              
                            </li>
                            <li class="has-submenus-submenu">
                                <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                                    <span class="text-xl d-flex"><i class="ph ph-brandy"></i></span>
                                    <span>Cooking Oil & Ghee</span>
                                 
                                </a>
                               
                            </li>
                            <li class="has-submenus-submenu">
                                <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                                    <span class="text-xl d-flex"><i class="ph ph-brandy"></i></span>
                                    <span>Masalas & Spices </span>
                                   
                                </a>
                                
                            </li>
                            <li class="has-submenus-submenu">
                                <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                                    <span class="text-xl d-flex"><i class="ph ph-brandy"></i></span>
                                    <span>Snacks & Namkeen</span>
                                  
                                </a>
                               
                            </li>
                            <li class="has-submenus-submenu">
                                <a href="javascript:void(0)" class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0">
                                    <span class="text-xl d-flex"><i class="ph ph-brandy"></i></span>
                                    <span>Biscuits & Bakery</span>
                                  
                                </a>
                               
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Category Dropdown End  -->
    
                <!-- Search Start  -->
                <form action="/search" method="GET" class="position-relative ms-20 max-w-870 w-100 d-md-block d-none">

                    <input type="text" id="globalSearch" name="q" class="form-control fw-medium placeholder-italic shadow-none bg-neutral-30 py-16 ps-30 pe-60" placeholder="Search products, variants, brands, categories..." autocomplete="off">

                    <button type="submit"
                        class="position-absolute top-50 translate-middle-y text-main-600 end-0 me-36 text-xl line-height-1">
                        <i class="ph-bold ph-magnifying-glass"></i>
                    </button>

                    <div id="searchResults"
                        class="position-absolute bg-white shadow rounded w-100 mt-2 d-none"
                        style="z-index:9999; max-height:400px; overflow-y:auto;">
                    </div>

                </form>
                <!-- Search End  -->
            </div>
             
            <!-- Header Middle Right start -->
           <div class="d-flex align-items-center gap-20-px flex-shrink-0">
            @auth
                <a href="/wishlist" class="flex-align gap-6 item-hover">
                    <span class="text-2xl text-heading d-flex position-relative me-6 mt-6 item-hover__text">
                        <i class="ph-bold ph-heart"></i>
                        <span class="w-18 h-18 flex-center rounded-circle bg-success-600 text-white text-xs position-absolute top-n6 end-n4">
                            {{ $wishlistCount }}
                        </span>
                    </span>
                    <span class="text-md text-neutral-500 item-hover__text fw-medium d-none d-lg-flex">Wishlist</span>
                </a>
            @endauth
                <a href="{{ url('/cart') }}" class="flex-align gap-6 item-hover">
                    <span class="text-2xl text-heading d-flex position-relative me-6 mt-6 item-hover__text">
                        <i class="ph-bold ph-shopping-cart"></i>

                        <span id="cart-count"
                            class="w-18 h-18 flex-center rounded-circle bg-success-600 text-white text-xs position-absolute top-n6 end-n4">
                            {{ $cartCount }}
                        </span>
                    </span>

                    <span class="text-md text-neutral-500 item-hover__text fw-medium d-none d-lg-flex">
                        Cart
                    </span>
                </a>
                @auth
                <div class="dropdown">
                    <a href="javascript:void(0)"
                    class="d-flex align-items-center gap-10 fw-medium text-main-600 py-14 px-24 bg-main-50 rounded-pill hover-bg-main-600 hover-text-white"
                    data-bs-toggle="dropdown">
                        <i class="ph-bold ph-user"></i>
                        {{ Auth::user()->name }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li>
                            <a class="dropdown-item" href="/myaccount">
                                My Profile
                            </a>
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <div class="dropdown">
                    <a href="javascript:void(0)"
                    class="d-flex align-items-center gap-10 fw-medium text-main-600 py-14 px-24 bg-main-50 rounded-pill hover-bg-main-600 hover-text-white"
                    data-bs-toggle="dropdown">
                        <i class="ph-bold ph-user"></i>
                        Account
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="/login">Login</a></li>
                        <li><a class="dropdown-item" href="/register">Register</a></li>
                    </ul>
                </div>
            @endauth
           </div>
            <!-- Header Middle Right End  -->
             
        </nav>
    </div>
</header>
<!-- ==================== Header End Here ==================== -->

<script>
document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("globalSearch");
    const resultsBox = document.getElementById("searchResults");

    let timeout = null;

    searchInput.addEventListener("keyup", function () {

        clearTimeout(timeout);

        let query = this.value.trim();

        if (query.length < 2) {
            resultsBox.classList.add("d-none");
            resultsBox.innerHTML = "";
            return;
        }

        timeout = setTimeout(() => {

            fetch(`/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {

                    resultsBox.innerHTML = '';

                    if (data.length === 0) {
                        resultsBox.innerHTML = `
                            <div class="p-3 text-muted">
                                No results found
                            </div>
                        `;
                    } else {

                        data.forEach(item => {

                            resultsBox.innerHTML += `
                                <a href="${item.url}" 
                                   class="d-flex align-items-center gap-3 p-3 border-bottom text-decoration-none text-dark hover-bg-light">

                                    <img src="${item.image}" 
                                         width="50"
                                         height="50"
                                         style="object-fit:cover;border-radius:6px;">

                                    <div>
                                        <div class="fw-semibold">
                                            ${item.name}
                                        </div>

                                        <small class="text-primary">
                                            ${item.type}
                                        </small>
                                    </div>

                                </a>
                            `;
                        });
                    }

                    resultsBox.classList.remove("d-none");
                });

        }, 300);

    });

    document.addEventListener("click", function(e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.classList.add("d-none");
        }
    });

});
</script>