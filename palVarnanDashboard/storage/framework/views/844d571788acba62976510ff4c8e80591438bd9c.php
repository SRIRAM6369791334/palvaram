

<?php $__env->startSection('title'); ?>
    All Orders
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(URL::asset('assets/libs/flatpickr/flatpickr.min.css')); ?>">

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> Home <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> All Orders <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center px-3 pt-3">

                <p class="mb-0 fw-bold">
                    Search:
                </p>

                <a href="<?php echo e(url('/orders')); ?>" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                    <i class="mdi mdi-arrow-left"></i> Back
                </a>

            </div>
            <div class="card-body">
                <div id="table-gridjs"></div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/app.js')); ?>"></script>

<script>
    window.productOrders = <?php echo json_encode($productOrders, 15, 512) ?>;
</script>

<script src="<?php echo e(URL::asset('assets/js/app/AllOrdersPage.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/pages/all_orders.blade.php ENDPATH**/ ?>