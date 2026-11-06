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
            Countries
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center px-3 pt-3">

                    <p class="mb-0 fw-bold">
                        Search:
                    </p>

                    <a href="<?php echo e(url('/location')); ?>" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>

                </div>
                <div class="card-body">
                    <div class="position-relative">
                        <div class="modal-button mt-2">
                            <div class="row align-items-start">
                                <div class="col-sm">
                                    <div>
                                        <button type="button" class="btn btn-success add_btn_top_el mb-4"
                                            data-bs-toggle="modal" data-bs-target="#addCountriesModal"><i
                                                class="mdi mdi-plus me-1"></i> Add
                                            Country</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Country Modal -->
    <div class="modal fade" id="addCountriesModal" tabindex="-1" aria-labelledby="addCountriesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCountriesModalLabel">Add Country</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addCountriesForm" novalidate>
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="add_countryname">Country Name</label><span
                                        class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_countryname" name="name"
                                        placeholder="Country name" maxlength="50" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Tax</label><span class="dark-asterisk">*</span>
                                    <input type="text" class="form-control" id="add_currency" name="currency"
                                        placeholder="Tax " required>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary add_submit_btn mt-3" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Country Modal -->
    <div class="modal fade" id="editCountriesModal" tabindex="-1" aria-labelledby="editCountriesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCountriesModalLabel">Edit Country</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editCountriesForm" novalidate>
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="edit_countries_id" />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_countryname">Country Name*</label>
                                    <input type="text" class="form-control" id="edit_countryname"
                                        name="name" placeholder="Country name" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Tax</label>
                                    <input type="text" class="form-control" id="edit_currency"
                                        name="currency" placeholder="Tax" required>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary mt-3 edit_submit_btn" type="submit">Update</button>
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
        window.countries = <?php echo json_encode($countries, 15, 512) ?>;
    </script>
    <script src="<?php echo e(URL::asset('assets/js/app/CountriesPage.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/pages/countries.blade.php ENDPATH**/ ?>