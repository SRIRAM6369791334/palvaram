<?php $__env->startSection('title'); ?>
    Pal Varnan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(URL::asset('assets/libs/flatpickr/flatpickr.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Home
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Product Dispatch Orders
        <?php $__env->endSlot(); ?>
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
    <div class="modal fade" id="DispatchModal" tabindex="-1" aria-labelledby="DispatchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="DispatchModalLabel">Dispatch Status </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="#" method="POST" id="changestatus2" novalidate="novalidate">
                        <input type="hidden" name="user_id" id="cusiddiapac">
                        <input type="hidden" name="phone_number" id="cusnum">
                        <div class="row">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="order_id_input2">Order Id*</label>
                                    <input type="text" name="order_id" class="form-control " value=""
                                        id="order_id_input2" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="customer_name_input2">Customer Name*</label>
                                    <input type="text" name="customer_name" class="form-control " value=""
                                        id="customer_name_input2" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="add_status_select">Select Status*</label>
                                    <select class="form-select select2" name="select_status"
                                    id="add_status_select2">
                                    <option value="" selected>Select status</option>
                                    <option value="3">Out for Delivery</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-2">
                                <div class="form-group">
                                    <label class="label-control" for="delivery_person_name">Delivery Person Name*</label>
                                    <input type="text" name="delivery_person_name" class="form-control" id="delivery_person_name" required>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-2">
                                <div class="form-group">
                                    <label class="label-control" for="delivery_person_phone">Delivery Person Contact*</label>
                                    <input type="text" name="delivery_person_phone" class="form-control" id="delivery_person_phone" required>
                                </div>
                            </div>

                        </div>


                        <div class="text-center">
                            <button class="btn btn-primary mt-3 adddispatch_submit_btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="Refund3Modal" tabindex="-1" aria-labelledby="Refund3ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="Refund3ModalLabel">Product Refund Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="#" method="POST" id="changestatus3" novalidate="novalidate">
                        <input type="hidden" name="user_id" id="cusid2">
                        <div class="row">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="order_id_input">Order Id*</label>
                                    <input type="text" name="order_id" class="form-control " value=""
                                        id="order_id_input3" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="customer_name_input">Customer Name*</label>
                                    <input type="text" name="customer_name" class="form-control " value=""
                                        id="customer_name_input3" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <label class="label-control" for="customer_name_input">Customer Name*</label>
                                   <textarea class="form-control "  id="customer_resons_input3" readonly></textarea>
                                </div>
                            </div>

                        </div>


                        <div class="text-center">
                            <button class="btn btn-primary mt-3 reson3_submit_btn" type="submit">Submit</button>
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
        window.productDispaths = <?php echo json_encode($productDispaths, 15, 512) ?>;
    </script>
    

    <script src="<?php echo e(URL::asset('assets/js/app/ProductdispatchPage.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/pages/product_dispatch.blade.php ENDPATH**/ ?>