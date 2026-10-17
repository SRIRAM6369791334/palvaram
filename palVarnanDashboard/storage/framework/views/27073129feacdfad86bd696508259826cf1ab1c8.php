
<?php $__env->startSection('title'); ?>
    Pal Varnan - Gift Variants
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.css')); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            min-height: 38px;
            padding: 2px 6px;
        }
        .select2-container { width: 100% !important; }
        .preview-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100px;
            border: 1px dashed #ccc;
            border-radius: 5px;
            cursor: pointer;
        }
        
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Home <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Gift Variants <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center px-3 pt-3">
                    <p class="mb-0 fw-bold">Search:</p>
                    <a href="<?php echo e(url('/giftproducts')); ?>" class="btn btn-light btn-sm"
                        style="background:#0c716a;color:#fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <div class="modal-button mt-2">
                        <button type="button" class="btn btn-success mb-4"
                            data-bs-toggle="modal" data-bs-target="#addVariantModal">
                            <i class="mdi mdi-plus me-1"></i> Add Gift Variant
                        </button>
                    </div>
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>

  
    <div class="modal fade" id="addVariantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Gift Variant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addVariantForm" novalidate enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>

                       
                        <div class="filter-bar mb-4">
                          
                            <div class="row g-2">
                               
                                <div class="col-md-6">
                                    <label class="form-label">Country*</label>
                                    <select class="form-select select2" name="country_id[]"
                                        id="add_country_select" multiple>
                                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            
                                <div class="col-md-6">
                                    <label class="form-label">Category</label>
                                    <select class="form-select select2" id="add_category_select">
                                        <option value="">All Categories</option>
                                    </select>
                                </div>
                               
                                <div class="col-md-6">
                                    <label class="form-label">Sub Category</label>
                                    <select class="form-select select2" id="add_subcategory_select">
                                        <option value="">All Sub Categories</option>
                                    </select>
                                </div>
                               
                                <div class="col-md-6">
                                    <label class="form-label">Product Name*</label>
                                    <select class="form-select select2" name="product_id" id="add_product_select">
                                        <option value="" disabled selected>Select Country first</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                      
                        <div class="row">
                          
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Variant Image* (600 × 600)</label>
                                    <input type="file" class="form-control" id="add_varient_image"
                                        name="varient_image" accept="image/*" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_varient_image" class="preview-container"
                                    id="add_varient_preview">
                                    <div class="text-center">
                                        <i class="display-4 text-muted mdi mdi-cloud-upload"></i>
                                        <span class="d-block">Upload Variant Image</span>
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Size</label>
                                    <select class="form-select select2" name="size_id" id="add_size_select">
                                        <option value="">Select Size</option>
                                        <?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($size->id); ?>"><?php echo e($size->size); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Color</label>
                                    <select class="form-select select2" name="color_id" id="add_color_select">
                                        <option value="">Select Color</option>
                                        <?php $__currentLoopData = $colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($color->id); ?>"><?php echo e($color->color); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Variant Name (Optional)</label>
                                    <input type="text" class="form-control" name="varient_name"
                                        placeholder="e.g. Red - Large">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">SKU (Auto-gen if empty)</label>
                                    <input type="text" class="form-control" name="sku"
                                        placeholder="SKU-CODE">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Barcode</label>
                                    <input type="text" class="form-control" name="barcode"
                                        placeholder="Barcode / EAN">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_item_code">Item Code</label>
                                    <input type="text" class="form-control" name="item_code" placeholder="Item Code">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">MRP Price*</label>
                                    <input type="number" class="form-control" id="add_mrp_price"
                                        name="mrp_price" placeholder="0.00" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Selling Price*</label>
                                    <input type="number" class="form-control" id="add_offer_price"
                                        name="offer_price" placeholder="0.00" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Low Stock Alert*</label>
                                    <input type="number" class="form-control" id="add_low_stock"
                                        name="low_stock" placeholder="5" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Product GST</label>
                                    <select class="form-select select2" name="product_gst">
                                        <option value="" selected>Select GST</option>
                                        <option value="0">0 (GST Included)</option>
                                        <option value="5">5</option>
                                        <option value="12">12</option>
                                        <option value="18">18</option>
                                        <option value="28">28</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Flash Sale End Date</label>
                                    <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input flash-sale-chk"
                                            name="flash_sale" id="add_flash_sale_chk" value="1">
                                        <label class="form-check-label">Flash Sale</label>
                                    </div>
                                    <input type="datetime-local" class="form-control flash-date-input"
                                        name="flash_sale_date" id="add_flash_sale_date"
                                        style="display:none;">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center gap-4 mt-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        name="hot_deals" value="1">
                                    <label class="form-check-label">New Arrivals</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        name="popular_prod" value="1">
                                    <label class="form-check-label">Best Seller</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button class="btn btn-primary add_submit_btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

  
    <div class="modal" id="editVariantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Gift Variant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editVariantForm" novalidate enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="edit_variant_id">

                        
                        <div class="filter-bar mb-4">
                           
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Country*</label>
                                    <select class="form-select select2" name="country_id[]"
                                        id="edit_country_select" multiple>
                                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Category</label>
                                    <select class="form-select select2" id="edit_category_select">
                                        <option value="">All Categories</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sub Category</label>
                                    <select class="form-select select2" id="edit_subcategory_select">
                                        <option value="">All Sub Categories</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Product Name*</label>
                                    <select class="form-select select2" name="product_id"
                                        id="edit_product_select">
                                        <option value="" disabled selected>Select Product</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                       
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Variant Image (600 × 600)</label>
                                    <input type="file" class="form-control" id="edit_varient_image"
                                        name="varient_image" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-6 d-flex gap-3 align-items-center">
                                <div>
                                    <div class="mb-1 small">Previous</div>
                                    <img src="" class="edit_varient_preview_image"
                                        style="width:80px;height:80px;object-fit:cover;border-radius:5px;">
                                </div>
                                <div>
                                    <div class="mb-1 small">New</div>
                                    <label for="edit_varient_image" class="edit_varient_preview_container"
                                        style="width:80px;height:80px;display:flex;align-items:center;
                                               justify-content:center;border:1px dashed #ccc;
                                               border-radius:5px;cursor:pointer;">
                                        <i class="mdi mdi-cloud-upload text-muted"
                                            style="font-size:24px;"></i>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Size</label>
                                    <select class="form-select select2" name="size_id" id="edit_size_select">
                                        <option value="">Select Size</option>
                                        <?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($size->id); ?>"><?php echo e($size->size); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Color</label>
                                    <select class="form-select select2" name="color_id" id="edit_color_select">
                                        <option value="">Select Color</option>
                                        <?php $__currentLoopData = $colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($color->id); ?>"><?php echo e($color->color); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Variant Name (Optional)</label>
                                    <input type="text" class="form-control" id="edit_varient_name"
                                        name="varient_name" placeholder="e.g. Red - Large">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">SKU</label>
                                    <input type="text" class="form-control" id="edit_sku"
                                        name="sku" placeholder="SKU-CODE">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Barcode</label>
                                    <input type="text" class="form-control" id="edit_barcode"
                                        name="barcode" placeholder="Barcode / EAN">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Item Code</label>
                                    <input type="text" class="form-control" name="item_code" id="edit_item_code" placeholder="Item Code">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">MRP Price*</label>
                                    <input type="number" class="form-control" id="edit_mrp_price"
                                        name="mrp_price" placeholder="0.00" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Selling Price*</label>
                                    <input type="number" class="form-control" id="edit_offer_price"
                                        name="offer_price" placeholder="0.00" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Low Stock Alert*</label>
                                    <input type="number" class="form-control" id="edit_low_stock"
                                        name="low_stock" placeholder="5" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Product GST</label>
                                    <select class="form-select select2" id="edit_product_gst" name="product_gst">
                                        <option value="">Select GST</option>
                                        <option value="0">0 (GST Included)</option>
                                        <option value="5">5</option>
                                        <option value="12">12</option>
                                        <option value="18">18</option>
                                        <option value="28">28</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input flash-sale-chk"
                                            name="flash_sale" id="edit_flash_sale_chk" value="1">
                                        <label class="form-check-label">Flash Sale</label>
                                    </div>
                                    <input type="datetime-local" class="form-control flash-date-input"
                                        name="flash_sale_date" id="edit_flash_sale_date"
                                        style="display:none;">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center gap-4 mt-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        name="hot_deals" id="edit_hot_deals" value="1">
                                    <label class="form-check-label">New Arrivals</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        name="popular_prod" id="edit_popular_prod" value="1">
                                    <label class="form-check-label">Best Seller</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button class="btn btn-primary edit_submit_btn" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('assets/libs/gridjs/gridjs.min.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="<?php echo e(URL::asset('assets/js/app.js')); ?>"></script>
    <script>
        window.variants         = <?php echo json_encode($variants, 15, 512) ?>;
        window.allCategories    = <?php echo json_encode($categories, 15, 512) ?>;
        window.subcategories    = <?php echo json_encode($subcategories, 15, 512) ?>;
        window.allProducts      = <?php echo json_encode($products, 15, 512) ?>;
        window.countries        = <?php echo json_encode($countries, 15, 512) ?>;
        window.sizes            = <?php echo json_encode($sizes, 15, 512) ?>;
        window.colors           = <?php echo json_encode($colors, 15, 512) ?>;
      
        window.allowedCountryIds = <?php echo json_encode($allowedCountryIds, 15, 512) ?>;

        window.giftVariantUrls = {
            store:           "<?php echo e(route('gift_variants.store')); ?>",
            update:          "<?php echo e(url('gift-variants/update')); ?>",
            destroy:         "<?php echo e(url('gift-variants/destroy')); ?>",
            filteredProducts:"<?php echo e(route('gift_variants.filtered_products')); ?>",
        };
    </script>
    <script src="<?php echo e(URL::asset('assets/js/app/GiftProductVariantPage.js')); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('editVariantModal');
            if (modal && modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/pages/gifts/product_varient.blade.php ENDPATH**/ ?>