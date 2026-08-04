<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.Dashboard'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Pal Varnan
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php if(Auth::user()->role == 1): ?>
                Welcome Admin!
            <?php elseif(Auth::user()->role == 2): ?>
                Welcome Account!
            <?php elseif(Auth::user()->role == 3): ?>
                Welcome Packing!
            <?php elseif(Auth::user()->role == 4): ?>
                Welcome Dispatch!
            <?php elseif(Auth::user()->role == 5): ?>
                Welcome Delivery!
            <?php endif; ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row justify-content-center">
        <?php if(Auth::user()->role == 1): ?>
            




            

            


            

            

            


            
            
            
            <div class="col-xl-3">
                <a href="<?php echo e(route('categories.index')); ?>">
                    <div class="card" style="background: #007d44;border-radius:15px;">
                        <div class="card-body" style="">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="mdi mdi-food  icon nav-icon"></i></h2>
                                    <h3 class="text-white">Category</h3>
                                    <h3 class="text-white mb-0"><?php echo e($categoryCount); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            

            <div class="col-xl-3">
                <a href="<?php echo e(route('productvarient.index')); ?>">
                    <div class="card" style="background: #0899ae;border-radius:15px;">
                        <div class="card-body" style="">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="bx bxs-basket icon nav-icon"></i></h2>
                                    <h3 class="text-white">Product Variant</h3>
                                    <h3 class="text-white mb-0"><?php echo e($productCount); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>



            <div class="col-xl-3">
                <a href="<?php echo e(route('bannerImages.index')); ?>">
                    <div class="card" style="background: #ce8437;border-radius:15px;">
                        <div class="card-body" style="">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="mdi mdi-image-area icon nav-icon"></i></h2>
                                    <h3 class="text-white">Banner Images</h3>
                                    <h3 class="text-white mb-0"><?php echo e($bannerImagesCount); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3">
                <a href="<?php echo e(route('productOrders.index')); ?>">
                    <div class="card bg-primary" style="border-radius:15px;">
                        <div class="card-body" >
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="mdi mdi-truck-fast-outline icon nav-icon"></i></h2>
                                    <h3 class="text-white">Order Count</h3>
                                    <h3 class="text-white mb-0"><?php echo e($billing); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3">
                <a href="<?php echo e(route('productpacking.index')); ?>">
                    <div class="card " style="border-radius:15px;background-color:#c7173b;">
                        <div class="card-body" >
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="bx bx-store icon nav-icon"></i></h2>
                                    <h3 class="text-white">In Packing</h3>
                                    <h3 class="text-white mb-0"><?php echo e($pending); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3">
                <a href="<?php echo e(route('productdispatch.index')); ?>">
                    <div class="card bg-primary" style="border-radius:15px;">
                        <div class="card-body" >
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="bx bxs-basket icon nav-icon"></i></h2>
                                    <h3 class="text-white">Dispatch Order</h3>
                                    <h3 class="text-white mb-0"><?php echo e($dispatch); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php elseif(Auth::user()->role == 2): ?>

        <?php elseif(Auth::user()->role == 3): ?>

        <?php elseif(Auth::user()->role == 4): ?>

        <?php elseif(Auth::user()->role == 5): ?>
            <div class="col-xl-4">
                <a href="<?php echo e(route('productdelivery.index')); ?>">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="bx bx-map icon nav-icon"></i></h2>
                                    <h3 class="text-white">Out Of Delivery </h3>
                                    <h3 class="text-white mb-0"><?php echo e($delivery); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-4">
                <a href="<?php echo e(route('productcomplete.index')); ?>">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="text-center py-3">
                                <ul class="bg-bubbles ps-0">
                                    <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                    <li><i class="bx bx-tachometer font-size-24"></i></li>
                                    <li><i class="bx bx-store font-size-24"></i></li>
                                    <li><i class="bx bx-cube font-size-24"></i></li>
                                    <li><i class="bx bx-cylinder font-size-24"></i></li>
                                    <li><i class="bx bx-command font-size-24"></i></li>
                                    <li><i class="bx bx-hourglass font-size-24"></i></li>
                                    <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                    <li><i class="bx bx-coffee font-size-24"></i></li>
                                    <li><i class="bx bx-polygon font-size-24"></i></li>
                                </ul>
                                <div class="main-wid position-relative">
                                    <h2 class="text-white"><i class="bx bx-map icon nav-icon"></i></h2>
                                    <h3 class="text-white">Delivered Order </h3>
                                    <h3 class="text-white mb-0"><?php echo e($productOrdercompleteCount); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>




    </div>


    

    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    
    <script src="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/js/app.js')); ?>"></script>

    <script>
        window.milkOrders = <?php echo json_encode($milkOrders, 15, 512) ?>;
        window.productOrders = <?php echo json_encode($productOrders, 15, 512) ?>;
    </script>
    <script src="<?php echo e(URL::asset('assets/js/app/DashboardPage.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/index.blade.php ENDPATH**/ ?>