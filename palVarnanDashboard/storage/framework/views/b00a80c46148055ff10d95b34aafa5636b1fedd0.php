
<?php $__env->startSection('title'); ?>
    Pal Varnan - Gift Color
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.css')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Home <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Gift Color <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="modal-button mt-2">
                        <div class="row align-items-start">
                            <div class="col-sm">
                                <a href="<?php echo e(url('/giftproducts')); ?>" class="btn btn-light btn-sm mb-4" style="background: #0c716a; color: #fff;">
                                        <i class="mdi mdi-arrow-left"></i> Back
                                    </a>
                                <button type="button" class="btn btn-success mb-4"
                                    data-bs-toggle="modal" data-bs-target="#addGiftColorModal">
                                    <i class="mdi mdi-plus me-1"></i> Add Gift Color
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="addGiftColorModal" tabindex="-1" aria-labelledby="addGiftColorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGiftColorModalLabel">Add Gift Color</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addGiftColorForm" novalidate>
                        <?php echo csrf_field(); ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Country.selection')): ?>
                        <div class="mb-3">
                            <label class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="add_country_id" name="country_id[]" multiple>
                                <option value="">Select Country</option>
                                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($country->id); ?>">
                                        <?php echo e($country->name); ?> (<?php echo e($country->currency); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label" for="add_color">Gift Color <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_color" name="color"
                                placeholder="e.g. red, blue, black" required>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary add_submit_btn mt-2" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="editGiftColorModal" tabindex="-1" aria-labelledby="editGiftColorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGiftColorModalLabel">Edit Gift Color</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editGiftColorForm" novalidate>
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="edit_gift_id">
                        <input type="hidden" id="edit_country_id_hidden" name="country_id">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Country.selection')): ?>
                        <div class="mb-3">
                            <label class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="edit_country_id" name="country_id[]" multiple>
                                <option value="">Select Country</option>
                                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($country->id); ?>">
                                        <?php echo e($country->name); ?> (<?php echo e($country->currency); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label" for="edit_color">Gift Color <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_color" name="color"
                                placeholder="e.g. red, blue, black" required>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary edit_submit_btn mt-2" type="submit">Update</button>
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
        window.gifts             = <?php echo json_encode($gifts, 15, 512) ?>;
        window.countries         = <?php echo json_encode($countries, 15, 512) ?>;
        window.allowedCountryIds = <?php echo json_encode($allowedCountryIds, 15, 512) ?>;
        window.giftUrls = {
            store:   "<?php echo e(route('gift-color.store')); ?>",
            update:  "<?php echo e(url('gift-color/update')); ?>",
            destroy: "<?php echo e(url('gift-color/destroy')); ?>",
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="<?php echo e(URL::asset('assets/js/app/GiftColorPage.js')); ?>"></script>
    <script>
        // Add modal — init select2
        $('#addGiftColorModal').on('shown.bs.modal', function () {
            if ($('#add_country_id').hasClass("select2-hidden-accessible")) {
                $('#add_country_id').select2('destroy');
            }
            $('#add_country_id').select2({
                placeholder: "Select Countries",
                width: '100%',
                dropdownParent: $('#addGiftColorModal')
            });
        });

        // Edit modal — init select2
        $('#editGiftColorModal').on('shown.bs.modal', function () {
            if ($('#edit_country_id').hasClass("select2-hidden-accessible")) {
                $('#edit_country_id').select2('destroy');
            }
            $('#edit_country_id').select2({
                placeholder: "Select Countries",
                width: '100%',
                dropdownParent: $('#editGiftColorModal')
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/pages/gifts/gift_color.blade.php ENDPATH**/ ?>