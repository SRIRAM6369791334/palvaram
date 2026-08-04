<header id="page-topbar" class="isvertical-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="index" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="<?php echo e(URL::asset('assets/images/smlo.png')); ?>" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo e(URL::asset('assets/images/smlo.png')); ?>" alt="" height="22"> <span
                            class="logo-txt"><?php echo app('translator')->get('translation.Yesbe'); ?></span>
                    </span>
                </a>

                <a href="index" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="<?php echo e(URL::asset('assets/images/smlo.png')); ?>" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo e(URL::asset('assets/images/smlo.png')); ?>" alt="" height="22"> <span
                            class="logo-txt"><?php echo app('translator')->get('translation.Yesbe'); ?></span>
                    </span>
                </a>

            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Country.selection')): ?>
        <div class="dropdown d-inline-block me-2">
            <button class="btn header-item d-flex align-items-center country-dropdown-btn"
                type="button" data-bs-toggle="dropdown" aria-expanded="false">

                 <span class="ms-1" id="selectedCountryText">
                    <?php echo e(session('country_name') ?? $selectedCountry ?? 'Select Country'); ?>

                </span>
                <i class="mdi mdi-chevron-down ms-1"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end p-2 country-dropdown">
                <li>
                    <a href="javascript:void(0)" 
                    class="dropdown-item country-select"
                    data-id=""
                    data-name="All Countries">
                         All Countries
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="javascript:void(0)" 
                        class="dropdown-item country-select"
                        data-id="<?php echo e($country->id); ?>"
                        data-name="<?php echo e($country->name); ?>">
                            <?php echo e($country->name); ?> (<?php echo e($country->currency); ?>)
                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="d-flex">
            <div class="dropdown d-inline-block">
                <a href="<?php echo e(route("lowstock")); ?>">
                <button type="button" class="btn header-item noti-icon"
                   >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-bell icon-sm">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="noti-dot bg-danger rounded-pill"><?php echo e($stockscount); ?></span>
                </button>
            </a>
                
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-clock icon-sm">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <?php if(($expiringCount ?? 0) > 0): ?>
                        <span class="noti-dot bg-warning rounded-pill"><?php echo e($expiringCount); ?></span>
                    <?php endif; ?>
                </button>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="expiring-dropdown">
                    <div class="p-3 border-bottom">
                        <h6 class="m-0 font-size-15">Expiring Soon (no offer set)</h6>
                    </div>
                    <div data-simplebar style="max-height: 280px; overflow-y:auto;">
                        <?php $__empty_1 = true; $__currentLoopData = ($expiringItems ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e(url('/stock-tracking')); ?>" class="text-reset notification-item d-block px-3 py-2 border-bottom">
                                <div class="fw-semibold"><?php echo e($item->productname); ?></div>
                                <div class="small text-warning">
                                    Expires <?php echo e(\Carbon\Carbon::parse($item->expiry_date)->diffForHumans()); ?>

                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-3 text-muted small">No items expiring soon.</div>
                        <?php endif; ?>
                    </div>
                    <?php if(($expiringCount ?? 0) > 0): ?>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link" href="<?php echo e(url('/stock-tracking')); ?>">Set offers now</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-truck icon-sm">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    <?php if(($overdueDistributionsCount ?? 0) > 0): ?>
                        <span class="noti-dot bg-danger rounded-pill"><?php echo e($overdueDistributionsCount); ?></span>
                    <?php endif; ?>
                </button>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="overdue-dropdown">
                    <div class="p-3 border-bottom">
                        <h6 class="m-0 font-size-15">Awaiting Receipt Confirmation</h6>
                    </div>
                    <div data-simplebar style="max-height: 280px; overflow-y:auto;">
                        <?php $__empty_1 = true; $__currentLoopData = ($overdueDistributions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $daysPending = \Carbon\Carbon::parse($item->distributed_at)->diffInDays(now());
                            ?>
                            <a href="<?php echo e(url('/stock-distribution')); ?>" class="text-reset notification-item d-block px-3 py-2 border-bottom">
                                <div class="fw-semibold"><?php echo e($item->product_name); ?></div>
                                <div class="small text-muted">
                                    <?php echo e($item->warehouse_name ?? '—'); ?> → <?php echo e($item->branch_name ?? '—'); ?> • Qty: <?php echo e($item->quantity); ?>

                                </div>
                                <div class="small text-danger">
                                    Pending <?php echo e($daysPending); ?>d — not yet confirmed received
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-3 text-muted small">No overdue distributions.</div>
                        <?php endif; ?>
                    </div>
                    <?php if(($overdueDistributionsCount ?? 0) > 0): ?>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link" href="<?php echo e(url('/stock-distribution')); ?>">Review distributions</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>


            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item user text-start d-flex align-items-center"
                    id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="<?php echo e(asset('/assets/images/logoooo.png')); ?>" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">Admin</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end pt-0">
                    <a class="dropdown-item " href="javascript:void();"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                            class="bx bx-power-off font-size-16 align-middle me-1"></i> <span
                            key="t-logout">Logout</span></a>
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                        <?php echo csrf_field(); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on("click", ".country-select", function () {
    let countryId = $(this).data("id");
    let countryName = $(this).data("name");

    $.ajax({
        url: "/set-country",
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            country_id: countryId,
            country_name: countryName
        },
        success: function () {
            location.reload(); // ✅ VERY IMPORTANT
        }
    });
});
</script>

<style>
    
.country-dropdown-btn {
    /* border: 1px solid #ddd; */
    border-radius: 20px;
    padding: 6px 35px;
    height: 40px;
    background: #f5e698 !important;
    transition: 0.3s;
}

.country-dropdown-btn:hover {
    /* background: #c8add0; */
    background: #f5e698;
}

.country-dropdown {
    max-height: 250px;
    overflow-y: auto;
    min-width: 200px;
    border-radius: 10px;
}

.country-dropdown .dropdown-item {
    border-radius: 8px;
    padding: 8px 10px;
}

.country-dropdown .dropdown-item:hover {
    background: #f1f1f1;
}
</style><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/layouts/topbar.blade.php ENDPATH**/ ?>