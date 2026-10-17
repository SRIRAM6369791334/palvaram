<?php $__env->startSection('title'); ?>
    Pal Varnan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(URL::asset('assets/libs/flatpickr/flatpickr.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/customer_page.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Home
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Customers
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center px-3 pt-3">

                    <p class="mb-0 fw-bold">
                        Search:
                    </p>

                    <a href="<?php echo e(url('/userpage')); ?>" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>

                </div>
                <div class="position-relative">
                    <div class="modal-button mt-2">
                        <form class="needs-validation" id="customerForm" novalidate enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                            <div class="row align-items-start" style="position: relative;right: 18px;">


                                


                            </div>
                        </form>
                        <!-- end row -->
                    </div>
                </div>
                <div class="card-body">

                    <div id="table-gridjs" class=""></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg custom ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addUserForm" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_username">Username*</label>
                                    <input type="text" class="form-control" id="add_username" name="user_name"
                                        placeholder="User name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_email">Email*</label>
                                    <input type="email" class="form-control" id="add_email" placeholder="Email"
                                        name="email" required>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="form-label" for="add_phone_number">Phone Number*</label>
                                    <div class="input-group">

                                        <span class="input-group-text" id="inputGroupPrepend">+91</span>
                                        <input type="tel" maxlength="10" pattern="^(?!^91)[6-9]\d{9}$"
                                            class="form-control" id="add_phone_number" name="phone_number"
                                            placeholder="Phone Number" required>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_password">Password*</label>
                                    <div class="input-group">
                                        <input class="form-control" id="add_password" placeholder="Password"
                                            name="password" required type="password">
                                        <span class="input-group-text btn btn-outline-secondary" id="inputGroupAppend"
                                            type="button">
                                            <i class="bx bx-hide icon"></i>
                                            <i class="bx bx-show icon"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="add_guest_select">User Entry</label>
                                    <select class="form-select select2" name="is_guest_user" id="add_guest_select">
                                        <option value="" selected>Select User</option>

                                        <option value="2">Direct</option>
                                        <option value="3">Phone call</option>


                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <h4>Address Details</h4>
                            </div>

                            <div class="row">


                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="address_line_one">Door No /
                                            Address Line 1*</label>
                                        <input type="text" class="form-control" id="address_line_one"
                                            name="address_line_one" placeholder="Enter Address" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="address_line_two">Street Name/ Address Line
                                            2*</label>
                                        <input type="text" class="form-control" id="address_line_two"
                                            name="address_line_two" placeholder="Enter Address" required>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="landmark">Landmark (optional)</label>
                                        <input type="text" class="form-control" id="landmark" name="landmark"
                                            placeholder="Enter Landmark(optional)">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="address_type_select">Select Address Type*</label>
                                        <select class="form-select select2" name="address_type_id" id="address_type_select"
                                            required>
                                            <option value="" disabled selected>Select Address Type</option>
                                            <option value="1">Home</option>
                                            <option value="2">Work</option>
                                            <option value="3">Others</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6 others_container">
                                    <div class="mb-3">
                                        <label class="form-label" for="pin_code_type">PinCode*</label>
                                        <input type="text" class="form-control pincode_type" id="pin_code_type" pattern="[0-9]{6}"
                                            name="pincode" placeholder="Enter pin code" required />
                                        <div id="addressDetails"></div>

                                    </div>
                                </div>

                                <div class="col-md-6 others_container">
                                    <div class="mb-3">
                                        <label class="form-label" for="pin_code_type">District*</label>
                                        <input type="text" class="form-control" id="pin_district" name="city"
                                            required readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 others_container">
                                    <div class="mb-3">
                                        <label class="form-label" for="pin_code_type">State*</label>
                                        <input type="text" class="form-control" id="pin_state" name="state"
                                            required readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 others_container">
                                    <div class="mb-3">
                                        <label class="form-label" for="pin_code_type">City*</label>
                                        <select class="form-select custid select2" name="area_name" id="city_input" required>

                                            <option value="" disabled selected>Select Category</option>

                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary mt-3 add_submit_btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editUserForm" novalidate>
                        <input type="hidden" id="edit_user_id" />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_username">Username*</label>
                                    <input type="text" class="form-control" id="edit_username" name="user_name"
                                        placeholder="User name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_email">Email*</label>
                                    <input type="email" class="form-control" id="edit_email" placeholder="Email"
                                        name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="form-label" for="edit_phone_number">Phone Number*</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroupPrepend">+91</span>
                                        <input type="tel" pattern="^(?!^91)[6-9]\d{9}$" class="form-control"
                                            name="phone_number" maxlength="10" id="edit_phone_number"
                                            placeholder="Phone Number" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_password">Change Password*</label>
                                    <div class="input-group">
                                        <input class="form-control" id="edit_password" placeholder="Password"
                                            name="password" required type="password" autocomplete="off">
                                        <span class="input-group-text btn btn-outline-secondary" id="inputGroupAppend"
                                            type="button">
                                            <i v-if="!showPassword" class="bx bx-hide icon"></i>
                                            <i v-if="showPassword" class="bx bx-show icon"></i>

                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_guest_select">User Entry</label>
                                    <select class="form-select select2" name="is_guest_user" id="edit_guest_select">
                                        <option value="" selected>Select User</option>

                                        <option value="2">Direct</option>
                                        <option value="3">Phone call</option>


                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <h4>Address Details</h4>
                            </div>

                            <div class="row">


                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="edit_address_line_one">Door No /
                                            Address Line 1*</label>
                                        <input type="text" class="form-control" id="edit_address_line_one"
                                            name="address_line_one" placeholder="Enter Address" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="address_line_two">Street Name/ Address Line
                                            2*</label>
                                        <input type="text" class="form-control" id="edit_address_line_two"
                                            name="address_line_two" placeholder="Enter Address" required>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="landmark">Landmark (optional)</label>
                                        <input type="text" class="form-control" id="edit_landmark" name="landmark"
                                            placeholder="Enter Landmark(optional)">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="address_type_select">Select Address Type*</label>
                                        <select class="form-select select2" name="address_type_id" id="edit_address_type_select"
                                            required>
                                            <option value="" disabled selected>Select Address Type</option>
                                            <option value="1">Home</option>
                                            <option value="2">Work</option>
                                            <option value="3">Others</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6 others_container">
                                    <div class="mb-3">
                                        <label class="form-label" for="pin_code_type">PinCode*</label>
                                        <input type="text" class="form-control pincode_type1" id="pin_code_type1" pattern="[0-9]{6}"
                                            name="pincode" placeholder="Enter pin code" required />
                                        <div id="addressDetails"></div>

                                    </div>
                                </div>

                                <div class="col-md-6 others_container">
                                    <div class="mb-3">
                                        <label class="form-label" for="pin_code_type">District*</label>
                                        <input type="text" class="form-control" id="pin_district1" name="city"
                                            required readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 others_container">
                                    <div class="mb-3">
                                        <label class="form-label" for="pin_code_type">State*</label>
                                        <input type="text" class="form-control" id="pin_state1" name="state"
                                            required readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 others_container">
                                    <div class="mb-3">
                                        <label class="form-label" for="pin_code_type">City*</label>
                                        <select class="form-select custid select2" name="area_name" id="city_input1" required>

                                            <option value="" disabled selected>Select Area</option>

                                        </select>
                                    </div>
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

    

    
    
    


    

    
    
    



    <div class="modal fade" id="manualUserModal" tabindex="-1" aria-labelledby="manualUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg custom-modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manualUserModalLabel">Order Manually</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="add_category_select">Choose Product Type</label>
                                <select class="form-select select2" name="product_id" id="add_category_select" required>
                                    <option value="" disabled selected>Select Product Type</option>

                                    <option value="others">Yesbe Products</option>
                                    
                                </select>
                            </div>
                        </div>
                        <form class="needs-validation" id="manualOrderForm" novalidate>


                            <div class="milk_product_container">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="add_product_select">Choose Product*</label>
                                        <select class="form-select select2" name="product_id" id="add_product_select" required>
                                            <option value="" disabled selected>Select Product</option>

                                        </select>
                                    </div>
                                </div>

                                

                                <div class="col-md-12 mb-3  selected_product">
                                    <div class="card mx-auto selected_product_card">
                                        <img class="card-img-top manual_card_image mx-auto" src=""
                                            alt="Card image cap" lazy>
                                        <div class="card-body">

                                            <h4 class="manual_product_name text-center"></h4>
                                            <div class="row align-items-center">
                                                <div class="col-6">Product Price:</div>
                                                <div class="col-6">
                                                    <span class="manual_regular_price"></span>
                                                    <span class="manual_mrp_price"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="label-control">Plan Type*</label>
                                    <div>
                                        <select class="form-select my-2 plan_select select2" name="plan_type" value="select"
                                            id="select1" required>
                                            <option value="" hidden="">Choose Plan</option>
                                            <option value="1">Monthly</option>
                                            <option value="2">One Time</option>
                                            <option value="3">Customized</option>
                                        </select>
                                        <div class="form-group show_el row mb-3" style="display:none;" id="month"
                                            data-plan-type="1">
                                            <div class="col-6">
                                                <label class="label-control">From</label>
                                                <div class="date-input-container">
                                                    <input type="text"
                                                        class="form-control f-month date1 date_picker_from_date"
                                                        id="date_from" placeholder="From-Date" name="f-monthly">
                                                </div>
                                            </div>
                                            <div class="col-6 ">
                                                <label class="label-control">To</label>
                                                <div class="date-input-container">
                                                    <input type="date"
                                                        class="form-control f-month  date_picker_to_date"
                                                        placeholder="To-Date" name="to-monthly" readonly>
                                                </div>


                                            </div>
                                        </div>
                                        <div class="form-group show_el mb-3" style="display:none;" id="onetime"
                                            data-plan-type="2">
                                            <div class="date-input-container">
                                                <input type="text" name="onetime"
                                                    class="form-control onetime date1 date_picker_one_time"
                                                    placeholder="Pick a date">
                                            </div>
                                        </div>
                                        <div class="form-group show_el mb-3 customized_calendar_conatainer"
                                            data-plan-type="3" style="display:none;" id="customize">

                                            <div class="date-input-container">
                                                <input type="text"
                                                    class="form-control customize datepicker-customized date1"
                                                    name="customize" placeholder="Pick Custom Dates">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="add_product_quantity">No of Days*</label>
                                        <input type="text" class="form-control" id="add_product_quantity"
                                            name="product_quantity" placeholder="Total number of days" required readonly>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="text-center">
                                <button class="btn btn-primary manual_submit_btn mt-3" type="submit">Submit</button>
                            </div>
                        </form>

                        <form class="needs-validation" id="manualProductOrderForm" novalidate>
                            <div class="other_product_container">
                                <div class="group_container">
                                    <div class="row sticky-top bg-white">
                                        <div class="col-md-3">
                                            <label class="form-label" for="add_other_product_select">Choose
                                                Category*</label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" for="add_other_product_select">Choose
                                                Product*</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label" for="add_other_product_select">Choose
                                                varient*</label>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label" for="other_product_quantity">Product
                                                Quantity*</label>
                                        </div>

                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-info btn-sm add_col_btn"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>
                                    </div>


                                    <div class="row mt-3 product_row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <select class="form-select other_category_select select2" name="catgegory_id[]"
                                                    required>
                                                    <option value="" disabled selected>Select Category</option>
                                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($category->id != env('MILK_PRODUCT')): ?>
                                                            <option value="<?php echo e($category->id); ?>">
                                                                <?php echo e($category->category_name); ?>

                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <select class="form-select other_product_select select2" name="product_id[]"
                                                    required>
                                                    <option value="">Choose Category First</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <select class="form-select other_product_varient select2" name="productvar_id[]"
                                                    required>
                                                    <option value="">Choose product First</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <input type="text" class="form-control other_product_quantity"
                                                    id="other_product_quantity" name="product_quantity[]"
                                                    placeholder="Product Quantity" required>
                                                    <input type="hidden" class="hidden_qty">
                                                    <span class="qty_error" style="color: red"></span>
                                            </div>
                                        </div>


                                    </div>
                                </div>


                                

                                <div class="form-group  mb-3">
                                    <div class="mb-3">
                                        <select class="form-select other_select_type select2" name="selecttype"
                                            id="other_select_type">
                                            <option value=""> Select Type</option>
                                            <option value="2">Walk In</option>
                                            <option value="3">Phone Call</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="text-center">
                                <button class="btn btn-primary manual_submit_btn mt-3" type="submit">Submit</button>
                            </div>
                        </form>


                    </div>




                </div>
            </div>
        </div>
    </div>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    
    <script src="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/js/app.js')); ?>"></script>
    <script>
        window.users = <?php echo json_encode($users, 15, 512) ?>;
        window.milkProductId = "<?php echo e(env('MILK_PRODUCT')); ?>";
    </script>
    <script src="<?php echo e(URL::asset('assets/libs/flatpickr/flatpickr.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('assets/js/app/CustomersPage.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/pages/customer.blade.php ENDPATH**/ ?>