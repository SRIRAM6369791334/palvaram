<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    
    <!-- LOGO -->
    <div class="navbar-brand-box" style="background:#ffff;">
        <a href="<?php echo e(url('/')); ?>" class="logo logo-dark">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('assets/images/smlo.png')); ?>" alt="" style="width:55px; border-radius: 16px;">
            </span>
            <span class="logo-lg">
                <img src="<?php echo e(URL::asset('assets/images/logochip.png')); ?>" alt="" height="22"
                    style="width:40px">
            </span>
        </a>

        <a href="<?php echo e(url('/')); ?>" class="logo logo-light">
            <span class="logo-lg">
                <img src="<?php echo e(URL::asset('assets/images/logoooo.png')); ?>" alt="" style="width:55px; border-radius: 16px;">
            </span>
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('assets/images/logoooo.png')); ?>" alt="" height="22"
                    style="width:50px;margin-left: -10px;">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 font-size-16 header-item vertical-menu-btn">
        <i class="fa fa-fw fa-bars"></i>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu" style="background: #0c716a">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">



                
                    <li class="menu-title" data-key="t-menu">Menu</li>
                    <li>
                        <a href="<?php echo e(url('/')); ?>">
                            <i class="bx bx-tachometer icon nav-icon"></i>
                            <span class="menu-item" data-key="t-dashboards"><?php echo app('translator')->get('translation.Dashboard'); ?></span>
                        </a>
                    </li>
                    


                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Banner Images.index')): ?>

                    <!-- <li>
                        <a href="<?php echo e(route('bannerImages.index')); ?>">
                            <i class="mdi mdi-image-area nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Banner Images</span>
                        </a>
                    </li> -->

                    <li>
                        <a href="<?php echo e(route('home_promotions.index')); ?>">
                            <i class="mdi mdi-star-box nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Banners</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if( auth()->user()->can('Roles.index') || auth()->user()->can('Users.index') || auth()->user()->can('Customers.index') ): ?>

                    <li>
                        <a href="/userpage">
                            <i class="bx bxs-user icon nav-icon"></i>
                            <span class="menu-item" data-key="t-calendar">Users, Customer, Roles</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <li>
                        <a href="<?php echo e(route('contacts.index')); ?>">
                            <i class="mdi mdi-email-mark-as-unread icon nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Contact Inquiries</span>
                            <?php
                                $inquiryBadge = \App\Models\Contact::count();
                            ?>
                            <?php if($inquiryBadge > 0): ?>
                                <span class="badge rounded-pill bg-danger float-end me-2"><?php echo e($inquiryBadge); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>



                    <?php if( auth()->user()->can('Countries.index') || auth()->user()->can('Warehouse.index') || auth()->user()->can('Warehouse Branch.index') ): ?>
                    <li>
                        <a href="<?php echo e(route('admin.location.index')); ?>">
                            <i class="bx bx-globe icon nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Locations</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    
                    
                    <?php if( auth()->user()->can('Categories.index') || auth()->user()->can('Subcategories.index') || auth()->user()->can('Brands.index') || auth()->user()->can('Products.index') || auth()->user()->can('Product Varient.index') ): ?>
                   
                    <li>
                        <a href="/product">
                            <i class="bx bxs-basket nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Products</span>
                        </a>
                    </li>

                   <!-- <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Subcategories.index')): ?>
                    <li>
                        <a href="<?php echo e(route('subcategories.index')); ?>">
                            <i class="bx bx-store icon nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Sub Category</span>

                        </a>
                    </li> 
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Brands.index')): ?>
                    <li>
                        <a href="<?php echo e(route('brands.index')); ?>">
                            <i class="bx bx-award nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Brands</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Products.index')): ?>
                    <li>
                        <a href="<?php echo e(route('products.index')); ?>">
                            <i class="bx bxs-basket nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Products</span>
                            
                        </a>
                    </li>
                    <?php endif; ?>

                    
                    <li>
                        <a href="<?php echo e(route('productvarient.index')); ?>">
                            <i class="mdi mdi-timer-sand-empty nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Product Variant</span>

                        </a>
                    </li> -->
                    <?php endif; ?>

                    <?php if( auth()->user()->can('Gift Categories.index') || auth()->user()->can('Gift Subcategories.index') || auth()->user()->can('Gift Products.index') ): ?>
                    <li>
                        <a href="/giftproducts">
                            <i class="bx bx-gift icon nav-icon"></i>
                            <span class="menu-item">Gift Items</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if( auth()->user()->can('Stocks.index') || auth()->user()->can('Stock Transfer.index') || auth()->user()->can('Stock Distribution.index') || auth()->user()->can('Stock Tracking.index') || auth()->user()->can('Stock Management.index') ): ?>
                    
                    <li>
                        <a href="/transfer">
                            <i class="bx bxs-report nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Product Stock</span>
                        </a>
                    </li>
                 
                    <!-- <li>
                        <a href="/transfer-stock">
                            <i class="bx bxs-report nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Product Stock Transfer</span>
                        </a>
                    </li> -->
                    <?php endif; ?>

                    

                    
                    

                    

                    

                    <?php if( auth()->user()->can('All Orders.index') || auth()->user()->can('New Orders.index') || auth()->user()->can('Package Orders.index') || auth()->user()->can('Dispatched Orders.index') || auth()->user()->can('Out For Delivery Orders.index') ||
                         auth()->user()->can('Delivered Orders.index') || auth()->user()->can('Supplier Details.index') || auth()->user()->can('Purchase Orders.index') || auth()->user()->can('Other Orders.index') ): ?>

                    <li>
                        <a href="/orders">
                            <i class="mdi mdi-truck-delivery nav-icon"></i>
                            <span class="menu-item" data-key="t-chat"> Orders</span>
                            <!-- <?php if($productOrderBIllingCount): ?>
                                <span class="badge rounded-pill bg-success"><?php echo e($productOrderBIllingCount); ?></span>
                            <?php endif; ?> -->

                        </a>
                    </li>

                    
                    <!-- <li>
                        <a href="<?php echo e(route('productOrders.index')); ?>">
                            <i class="mdi mdi-food nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">New Orders</span>
                            <?php if($productOrderBIllingCount): ?>
                                <span class="badge rounded-pill bg-success"><?php echo e($productOrderBIllingCount); ?></span>
                            <?php endif; ?>

                        </a>
                    </li> -->
                    

                    

                   
                    <!-- <li>
                    <a href="<?php echo e(route('productpacking.index')); ?>">
                        <i class="mdi mdi-gift nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Package Orders</span>
                        <?php if($productOrderPendingCount): ?>
                            <span class="badge rounded-pill bg-success"><?php echo e($productOrderPendingCount); ?></span>
                        <?php endif; ?>

                    </a>
                </li>
                
                    <li>
                    <a href="<?php echo e(route('productdispatch.index')); ?>">
                        <i class="mdi mdi-motorbike nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Dispatched Orders</span>
                        <?php if($productOrderDispatchCount): ?>
                            <span class="badge rounded-pill bg-success"><?php echo e($productOrderDispatchCount); ?></span>
                        <?php endif; ?>

                    </a>
                </li>
               
                    <li>
                    <a href="<?php echo e(route('productdelivery.index')); ?>">
                        <i class="mdi mdi-truck-delivery nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Out for Delivery Orders</span>
                        <?php if($productOrderDeliveryCount): ?>
                            <span class="badge rounded-pill bg-success"><?php echo e($productOrderDeliveryCount); ?></span>
                        <?php endif; ?>

                    </a>
                </li> -->
              
                    

                
                    <!-- <li>
                    <a href="<?php echo e(route('productcomplete.index')); ?>">
                        <i class="mdi mdi-dns nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Delivered Order</span>

                    </a>
                </li> -->
                <?php endif; ?>

                    


                    <?php if( auth()->user()->can('Product Wise Report.index') || auth()->user()->can('Order Wise Report.index') || auth()->user()->can('Inventory Report.index') || auth()->user()->can('Purchase Report.index') ): ?>
                    <li>
                        <a href="/reports">
                            <i class="mdi mdi-autorenew nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Reports</span>

                        </a>
                    </li>
                    <?php endif; ?>

                    
                    <!-- <li class="menu-title" data-key="t-applications">Shipping Charges</li>
                    <li>
                        <a href="/admin/shipping-amount">
                            <i class="mdi mdi-autorenew nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Shipping Charges</span>

                        </a>
                    </li>
                    <li>
                        <a href="/admin/free-ship">
                            <i class="mdi mdi-autorenew nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Free Shipping</span>

                        </a>
                    </li>
                    <li class="menu-title" data-key="t-applications">Coupon Management</li>
                    <li>
                        <a href="/admin/coupons">
                            <i class="mdi mdi-autorenew nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Coupons</span>

                        </a>
                    </li>

                    <li class="menu-title" data-key="t-applications">Scrolling Bar</li>
                    <li>
                        <a href="/scrollingbar">
                            <i class="mdi mdi-autorenew nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Scrolling Message</span>

                        </a>
                    </li> -->

                    

                    <!-- <li class="menu-title" data-key="t-applications">Product Reviews</li> -->
                    
                <!-- <li>
                    <a href="<?php echo e(route('review.index')); ?>">
                        <i class="mdi mdi-comment-text-multiple nav-icon"></i>
                        <span class="menu-item" data-key="t-chat">Reviews</span>
                    </a>
                </li> -->
                <!-- <li class="menu-title" data-key="t-applications">Bulk Order Enquries</li> -->

                <!-- <li>
                        <a href="<?php echo e(route('bulkenquiry.index')); ?>">
                            <i class="mdi mdi-plus-circle-outline nav-icon"></i>
                            <span class="menu-item" data-key="t-chat">Bulk Order Enquries</span>
                        </a>
                    </li> -->

                    
             
                    

                    <?php if(Auth::user()->role == 2): ?>
                        <li class="menu-title" data-key="t-applications">Orders</li>
                        <li>
                            <a href="<?php echo e(route('productOrders.index')); ?>">
                                <i class="mdi mdi-food nav-icon"></i>
                                <span class="menu-item" data-key="t-chat">Billing Products</span>
                                <?php if($productOrderBIllingCount): ?>
                                    <span class="badge rounded-pill bg-success"><?php echo e($productOrderBIllingCount); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>

                    <?php elseif(Auth::user()->role == 3): ?>
                        <li class="menu-title" data-key="t-applications">Orders</li>
                        <li>
                            <a href="<?php echo e(route('productpacking.index')); ?>">
                                <i class="mdi mdi-food nav-icon"></i>
                                <span class="menu-item" data-key="t-chat">Package Orders</span>
                                <?php if($productOrderPendingCount): ?>
                                    <span class="badge rounded-pill bg-success"><?php echo e($productOrderPendingCount); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>

                    <?php elseif(Auth::user()->role == 4): ?>
                        <li class="menu-title" data-key="t-applications">Orders</li>
                        <li>
                            <a href="<?php echo e(route('productdispatch.index')); ?>">
                                <i class="mdi mdi-food nav-icon"></i>
                                <span class="menu-item" data-key="t-chat">Dispatched Orders</span>
                                <?php if($productOrderDispatchCount): ?>
                                    <span class="badge rounded-pill bg-success"><?php echo e($productOrderDispatchCount); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>

                    <?php elseif(Auth::user()->role == 5): ?>
                        <li class="menu-title" data-key="t-applications">Orders</li>
                        <li>
                            <a href="<?php echo e(route('productdelivery.index')); ?>">
                                <i class="mdi mdi-food nav-icon"></i>
                                <span class="menu-item" data-key="t-chat">Out of Delivery Orders</span>
                                <?php if($productOrderDeliveryCount): ?>
                                    <span class="badge rounded-pill bg-success"><?php echo e($productOrderDeliveryCount); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('productcomplete.index')); ?>">
                                <i class="mdi mdi-food nav-icon"></i>
                                <span class="menu-item" data-key="t-chat">Delivered Order</span>
                            </a>
                        </li>
                    <?php endif; ?>
              
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
<?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>