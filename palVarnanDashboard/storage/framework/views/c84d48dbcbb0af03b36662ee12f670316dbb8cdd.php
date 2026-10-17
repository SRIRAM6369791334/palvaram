<?php $__env->startSection('title'); ?>
    Pal Varnan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Home
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Product Low Stock
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="position-relative">
                        <div class="modal-button mt-2">
                            <div class="row align-items-start">
                                <div class="col-sm">

                                </div>

                            </div>
                            <!-- end row -->
                        </div>
                    </div>
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editstockModal" tabindex="-1" aria-labelledby="editstockModallLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editstockModalLabel">Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addStockLowForm" novalidate>
                        <?php echo csrf_field(); ?>
                        <div class="row">
                        <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="stock_quantity1">Stock Quantity*</label>
                                    <input type="text" class="form-control" id="stock_quantity1"
                                        name="stock_quantity" placeholder="Product Quantity" required>

                                        <input type="hidden" name="productid" id="productid">
                                </div>
                            </div>
                         </div>


                        <div class="text-center">
                            <button class="btn btn-primary addstock_submit_btn mt-3" type="submit">Add Quantity</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/js/app.js')); ?>"></script>
    <script>
        window.lowstocks = <?php echo json_encode($lowstocks, 15, 512) ?>;
    </script>
    <script src="<?php echo e(URL::asset('assets/js/app/LowStockPage.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/pages/lowstock.blade.php ENDPATH**/ ?>