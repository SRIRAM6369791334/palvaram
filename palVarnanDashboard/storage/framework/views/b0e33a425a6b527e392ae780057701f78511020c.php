

<?php $__env->startSection('title'); ?>
    Roles
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> Home <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Role Details <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Roles List</h5>

                    <button type="button"
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#addRoleModal">
                        <i class="mdi mdi-plus"></i> Add Role
                    </button>
                    <a href="<?php echo e(url('/userpage')); ?>" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                </div>

                <div id="table-gridjs"></div>

            </div>
        </div>
    </div>
</div>


<!-- ================= ADD ROLE MODAL ================= -->
<div class="modal fade" id="addRoleModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Add Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="addRoleForm">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text"
                               class="form-control"
                               name="name"
                               id="add_role_name"
                               placeholder="Enter role name">
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary addrole_submit_btn">
                            Submit
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>


<!-- ================= EDIT ROLE MODAL ================= -->
<div class="modal fade" id="editRoleModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="editRoleForm">

                    <input type="hidden" id="edit_role_id">

                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text"
                               class="form-control"
                               name="name"
                               id="edit_role_name">
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary editrole_submit_btn">
                            Update
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>


<!-- ================= PERMISSION MODAL ================= -->
<div class="modal fade" id="permissionModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 id="permission_role_title">Manage Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="permissionForm">
                    <?php echo csrf_field(); ?>

                    <input type="hidden" id="permission_role_id">

                    <div class="row" id="permission_container"></div>

                    <div class="text-center mt-3">
                        <button class="btn btn-success permission_submit_btn">
                            Save Permissions
                        </button>
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
    window.roles = <?php echo json_encode($roles, 15, 512) ?>;
    window.permissions = <?php echo json_encode($permissions, 15, 512) ?>;
</script>

<script src="<?php echo e(URL::asset('assets/js/app/RolesPage.js')); ?>"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/pages/roles.blade.php ENDPATH**/ ?>