@extends('layouts.master')
@section('title')
    Pal Varnan
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endsection




@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Product
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center px-3 pt-3">

                   <div></div>

                    <a href="{{ url('/product') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>

                </div>
                <p
                    style="position: relative;
                top:82px;
                left: 25px;
                font-weight: bold;">
                    Search:</p>
                <div class="card-body">
                    <div class="position-relative">
                        <div class="modal-button mt-2">
                            <form class="needs-validation" id="productfilterForm" novalidate enctype="multipart/form-data">
                                @csrf
                                <div class="row align-items-start">
                                    <div class="col-sm" style="width: 280px">
                                        <label class="form-label" for="select_category_select">Choose Category*</label>
                                        <select class="form-select select2" name="category_id" id="select_category_select">
                                            <option value="" disabled selected>Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->category_name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-sm">
                                        <div>
                                            <button type="submit" class="btn btn-success product_filter_btn mb-4"
                                                style="position: relative;
                                        top: 29px;">submit</button>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div style="position: relative;
                                    top: 26px; display: flex; gap: 10px;">
                                            <button type="button" class="btn btn-success  add_btn_top_el mb-4"
                                                data-bs-toggle="modal" data-bs-target="#addProductModal"
                                                style="width:130px"><i class="mdi mdi-plus me-1"></i> Add
                                                Product</button>
                                            <button type="button" class="btn btn-info mb-4"
                                                data-bs-toggle="modal" data-bs-target="#bulkUploadModal"
                                                style="width:150px"><i class="mdi mdi-upload me-1"></i> Bulk Upload</button>
                                        </div>
                                    </div>

                                </div>
                            </form>
                            <!-- end row -->
                        </div>
                    </div>
                    <div id="table-gridjs" class="mt-5"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addProductForm" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="add_country_select">Choose Country*</label>
                                    <select class="form-control select2-country" name="country_id[]" 
                                            id="add_country_select" multiple>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="add_warehouse_select">Choose Warehouse*</label>
                                    <select class="form-select select2" name="warehouse_id" id="add_warehouse_select">
                                        <option value="" disabled selected>Select Warehouse</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="add_branch_select">Choose Store*</label>
                                    <select class="form-select select2" name="branch_id" id="add_branch_select">
                                        <option value="" disabled selected>Select Store</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="add_category_select">Choose Category*</label>
                                    <select class="form-select select2" name="category_id" id="add_category_select">
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="add_brand_select">Choose Brand</label>
                                    <select class="form-select select2" name="brand_id" id="add_brand_select">
                                        <option value="" selected>Select Brand</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">
                                                {{ $brand->brand_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="add_subcategory_select">Choose Sub Category*</label>
                                    <select class="form-select select2" name="subcategory_id" id="add_subcategory_select">
                                        <option value="" disabled selected>Select Sub Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="add_product_name">Product Name*</label>
                                    <input type="text" class="form-control" id="add_product_name" name="product_name"
                                        placeholder="Product name" maxlength="50" required>
                                </div>
                            </div>

                            <div class="col-md-4 d-none">
                                <div class="mb-3">
                                    <label class="form-label" for="add_product_slug">Product Slug (Auto-generated if empty)</label>
                                    <input type="text" class="form-control" id="add_product_slug" name="slug"
                                        placeholder="product-slug">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="subcate_size_append mt-3 mb-3">

                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="add_product_description">Product Ingredients</label>
                                    <textarea type="text" class="form-control" id="add_product_description" name="product_description"
                                        placeholder="Product Ingredients" maxlength="500"></textarea>


                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_product_image">Product Image*(600 *
                                        600)</label>
                                    <input type="file" class="form-control image_el dropzone needsclick"
                                        id="add_product_image" placeholder="Product Image" accept="image/*"
                                        name="product_image" required />
                                </div>
                            </div>

                            <label for="add_product_image" class="preview-container" id="preview-container">
                                <div class="flex justify-content-center">
                                    <div class="text-center">
                                        <i class="display-4 col-12 text-muted mdi mdi-cloud-upload"></i>
                                    </div>
                                    <div>
                                        <span class="col-12">Upload Image</span>
                                    </div>
                                </div>
                            </label>


                            <!--end::Input group-->
                            {{-- <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label" for="add_brand_name">Brand
                                        Name*</label>
                                    <input type="text" class="form-control" id="add_brand_name" name="brand_name"
                                        placeholder="Brand Name" maxlength="200" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label" for="add_material_name">Material*</label>
                                    <input type="text" class="form-control" id="add_material_name"
                                        name="brand_material" placeholder="Brand Material" maxlength="200" required>
                                </div>
                            </div> --}}

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_product_specification">Product
                                        Description</label>
                                    <textarea type="text" class="form-control" id="add_product_specification" name="product_specification"
                                        placeholder="Product Description" maxlength="300"></textarea>
                                </div>
                            </div>



                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_material_color">Type*</label>
                                    <input type="text" class="form-control" id="add_product_type" name="brand_type"
                                        placeholder="Product Type" maxlength="200" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_material_color">Return Approval Date*</label>
                                    <input type="text" class="form-control" id="add_product_type" name="approval_days"
                                        placeholder="Approval days" maxlength="2" required
                                        oninput="this.value = this.value.replace(/[^0-9]/g, ''); if (this.value > 30) this.value = 30;">
                                </div>
                            </div> --}}
                        </div>

                        <div class="col-lg-12">
                            <div class="row">
                                <div class="card" style="padding: 20px;border: 1px solid;">
                                    <h5>Product Variant</h5>
                                    <div class="col-lg-12">
                                        <div id="dynamic-inputs">


                                            <div class="d-flex product_fields">
                                                <div class="row">

                                                    <label for="add_varient_image" class="preview-container"
                                                        id="preview-container1">
                                                        <div class="flex justify-content-center">
                                                            <div class="text-center">
                                                                <i
                                                                    class="display-4 col-12 text-muted mdi mdi-cloud-upload"></i>
                                                            </div>
                                                            <div>
                                                                <span class="col-12">Upload Image</span>
                                                            </div>
                                                        </div>
                                                    </label>

                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="add_varient_image">Variant
                                                                Image*(600 *
                                                                600)</label>
                                                            <input type="file"
                                                                class="form-control image_el dropzone needsclick"
                                                                id="add_varient_image" placeholder="Varient Image"
                                                                accept="image/*" name="Varient_image[]" required>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="add_varient_name">Variant
                                                                Name(Optional)</label>
                                                            <input type="text" class="form-control"
                                                                id="add_varient_name" name="varient_name[]"
                                                                placeholder="Varient Name" required>
                                                        </div>
                                                    </div>
                                                   
                                                   
                                                    <!-- <div class="col-md-12 mb-3 stock-entries-wrapper">
                                                        <h6 class="mt-2">Stock Entries</h6>
                                                        <input type="hidden" class="stock-entry-count" name="stock_entry_count[]" value="1">
                                                        <div class="stock-entry-list">
                                                            <div class="stock-entry d-flex align-items-end gap-2 mb-5 mt-2">
                                                                <div style="flex:1">
                                                                    <label class="form-label">Choose Warehouse</label>
                                                                    <select class="form-select variant-warehouse-select select2" name="warehouse_id[]">
                                                                        <option value="" disabled selected>Select Warehouse</option>
                                                                    </select>
                                                                </div>
                                                                <div style="flex:1">
                                                                    <label class="form-label">Choose Store </label>
                                                                    <select class="form-select variant-branch-select select2" name="branch_id[]">
                                                                        <option value="" disabled selected>Select Store</option>
                                                                    </select>
                                                                </div>
                                                                <div style="flex:1">
                                                                    <label class="form-label">Stock Quantity*</label>
                                                                    <input type="number" class="form-control" name="product_quantity[]"
                                                                        placeholder="Quantity" required min="0">
                                                                </div>
                                                                <div style="flex:1">
                                                                    <label>Batch No (Auto-gen if empty)</label>
                                                                    <input type="text" name="batch_no[]" class="form-control" readonly>
                                                                </div>


                                                                <div style="flex:1">
                                                                    <label>MFG Date</label>
                                                                    <input type="date" name="manufacturing_date[]" class="form-control">
                                                                </div>


                                                                <div style="flex:1">
                                                                    <label>Expiry Date</label>
                                                                    <input type="date" name="expiry_date[]" class="form-control">
                                                                </div>
                                                                <div>
                                                                    <button type="button" class="btn btn-success add-stock-entry">+</button>
                                                                    <button type="button" class="btn btn-danger remove-stock-entry" style="display:none;">−</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="add_product_sku">SKU (Auto-gen if empty)</label>
                                                            <input type="text" class="form-control"
                                                                name="sku[]" placeholder="SKU-CODE">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="add_product_barcode">Barcode</label>
                                                            <input type="text" class="form-control"
                                                                name="barcode[]" placeholder="Barcode / EAN">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="add_item_code">Item Code</label>
                                                            <input type="text" class="form-control"
                                                                name="item_code" placeholder="Item Code">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="add_unit_select">Product Unit*</label>
                                                            <select class="form-select select2" name="unit_id[]" id="add_unit_select">
                                                                <option value="" selected>Select Units</option>
                                                                @foreach($units as $unit)
                                                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }} ({{ $unit->short_name }})</option>
                                                                @endforeach
                                                                {{-- Fallback --}}
                                                                @if($units->isEmpty())
                                                                    <option value="1">Litre (l)</option>
                                                                    <option value="2">Millilitre (ml)</option>
                                                                    <option value="3">Gram (g)</option>
                                                                    <option value="4">Kilogram (kg)</option>
                                                                    <option value="5">Number (No's)</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="add_product_quantity">Variant
                                                                Value*</label>
                                                            <input type="text" class="form-control"
                                                                id="add_product_value" name="product_value[]"
                                                                placeholder="Product Value" required>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="product_mrp_price">Product MRP
                                                                Price(ORIGINAL
                                                                PRICE)*</label>
                                                            <input type="text" class="form-control"
                                                                id="product_mrp_price" name="product_mrp_price[]"
                                                                placeholder="Product MRP price" required>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="product_offer_price">Product
                                                                Selling Price(OFFER
                                                                PRICE)*</label>
                                                            <input type="text" class="form-control"
                                                                id="product_offer_price" name="product_offer_price[]"
                                                                placeholder="Product Selling price" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="product_low_stock">Low Stock
                                                                *</label>
                                                            <input type="text" class="form-control"
                                                                id="product_low_stock" name="low_stock[]"
                                                                placeholder="Product low stock" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="">Product
                                                                GST</label>
                                                            <select class="form-select select2" name="product_gst[]"
                                                                id="prodgst">
                                                                <option value="" selected>Select GST</option>
                                                                <option value="0">0 (GST Included)</option>
                                                                <option value="5">5</option>
                                                                <option value="12">12</option>
                                                                <option value="18">18</option>
                                                                <option value="28">28</option>


                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                    </div>
                                                    <div class="col-md-3  d-flex justify-content-start align-items-center">
                                                        <div class="mb-3 mt-4">

                                                            <input type="checkbox" name="hot_deals[]" class="hot_value"
                                                                value="0"> <label class="form-label">New
                                                                Arrivals</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3  d-flex justify-content-start align-items-center">
                                                        <div class="mb-3 mt-4">

                                                            <input type="checkbox" name="popular_prod[]" class="pop_prod"
                                                                value="0"> <label class="form-label">Best
                                                                Seller</label>
                                                        </div>
                                                    </div>

                                            
                                                    <div class="col-md-3 justify-content-start align-items-center"
                                                        id="preorderNoteDiv" style="display: none;">
                                                        <div class="mb-3 mt-4">
                                                            <textarea class="form-control" id="add_pre_note" name="pre_note[]" placeholder="Pre Order Note" maxlength="300"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 d-flex justify-content-start align-items-center">
                                                        <div class="mb-3 mt-4">
                                                            <input type="checkbox" name="flash_sale[]" class="flash_sale_checkbox" value="1"> 
                                                            <label class="form-label">Flash Sale</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 flash_sale_date_container" style="display: none;">
                                                        <div class="mb-3 mt-4">
                                                            <label class="form-label">Flash Sale End Date</label>
                                                            <input type="datetime-local" class="form-control flash_sale_date_input" name="flash_sale_date[]">
                                                        </div>
                                                    </div>

                                                    <div class="mb-3" style="">
                                                        <h5>Product Thumbnail Images</h5>
                                                        <input type="hidden" name="product_image_count[]"
                                                            class="product_image_count" value="1">
                                                        <div class="col-lg-12">
                                                            <div id="dynamic-inputs1" class="dynamic-inputs1">


                                                                <div class="d-flex product_fields1">
                                                                    <div class="row">
                                                                        <div class="col-lg-8">
                                                                            <div class="mb-3">
                                                                                <label class="form-label"
                                                                                    for="add_product_image">Product
                                                                                    Image*(600 *
                                                                                    600)</label>
                                                                                <input type="file"
                                                                                    class="form-control image_el dropzone needsclick thumbim"
                                                                                    id="add_thumbproduct_image"
                                                                                    placeholder="Product Image"
                                                                                    name="product_image1[]" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 col-sm-12 mt-4">
                                                                            <div class="input-group-append">
                                                                                <button
                                                                                    class="btn btn-danger delete-input1"
                                                                                    type="button">Delete</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 mt-3">
                                                            <button id="add-input1" class="btn btn-success add-input1"
                                                                type="button">Add
                                                                Another Images</button>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <hr>
                                                    {{-- <div class="col-lg-3 col-sm-12 mt-4">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger delete-input"
                                                                type="button">Delete Varient</button>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-lg-3 mt-3">
                                        <button id="add-input" class="btn btn-success" type="button">Add
                                            Another Variant</button>
                                    </div>
                                </div>


                                {{-- <div class="card" style="padding: 20px;">
                                    <h5>Product Thump Images</h5>
                                    <div class="col-lg-12">
                                        <div id="dynamic-inputs1">


                                            <div class="d-flex product_fields1">
                                                <div class="row">
                                                    <div class="col-lg-8">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="add_product_image">Product
                                                                Image*(750 *
                                                                600)</label>
                                                            <input type="file"
                                                                class="form-control image_el dropzone needsclick"
                                                                id="add_product_image" placeholder="Product Image"
                                                                name="product_image1[]" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-sm-12 mt-4">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger delete-input1"
                                                                type="button">Delete</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mt-3">
                                        <button id="add-input1" class="btn btn-success" type="button">Add
                                            Another Images</button>
                                    </div>
                                </div> --}}


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

    <!-- Bulk Upload Modal -->
    <div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkUploadModalLabel">Bulk Product Upload</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading">Instructions</h4>
                        <p>1. Download the Excel template first.</p>
                        <p>2. Fill in your product and variant details. Use the same Product Name for different variants to group them.</p>
                        <p>3. If you want to upload images, provide the filename in the Excel and upload a ZIP file containing those images.</p>
                        <hr>
                        <a href="{{ route('products.downloadTemplate') }}" class="btn btn-soft-primary btn-sm"><i class="mdi mdi-download me-1"></i> Download Excel Template</a>
                    </div>

                    <form id="bulkUploadForm" class="needs-validation" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="bulk_category_select">Choose Category*</label>
                                <select class="form-select select2" name="category_id" id="bulk_category_select" required>
                                    <option value="" disabled selected>Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="bulk_subcategory_select">Choose Sub Category (Optional)</label>
                                <select class="form-select select2" name="subcategory_id" id="bulk_subcategory_select">
                                    <option value="" selected>Select Sub Category</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="bulk_brand_select">Choose Brand (Optional)</label>
                                <select class="form-select select2" name="brand_id" id="bulk_brand_select">
                                    <option value="" selected>Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">
                                            {{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="bulk_excel_file">Upload Excel File (.xlsx, .xls)*</label>
                                <input type="file" class="form-control" name="excel_file" id="bulk_excel_file" accept=".xlsx, .xls" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="bulk_zip_file">Upload Images ZIP (Optional)</label>
                                <input type="file" class="form-control" name="zip_file" id="bulk_zip_file" accept=".zip">
                                <small class="text-muted">Put all images in the root of the ZIP file.</small>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary bulk_upload_submit_btn">Start Bulk Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="editProductForm" novalidate>
                        <input type="hidden" id="edit_product_id" />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_country_select">Choose Country*</label>
                                    <select class="form-control select2-country" name="country_id[]" 
                                        id="edit_country_select" multiple>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_warehouse_select">Choose Warehouse</label>
                                    <select class="form-select select2" name="warehouse_id" id="edit_warehouse_select">
                                        <option value="" disabled selected>Select Warehouse</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_branch_select">Choose Store</label>
                                    <select class="form-select select2" name="branch_id" id="edit_branch_select">
                                        <option value="" disabled selected>Select Store</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_category_select">Choose Category*</label>
                                    <select class="form-select select2" name="category_id" id="edit_category_select">
                                        <option value="" disabled>Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_brand_select">Choose Brand</label>
                                    <select class="form-select select2" name="brand_id" id="edit_brand_select">
                                        <option value="" selected>Select Brand</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">
                                                {{ $brand->brand_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_subcategory_select">Choose Sub Category*</label>
                                    <select class="form-select select2" name="subcategory_id" id="edit_subcategory_select">
                                        <option value="" disabled selected>Select Sub Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_name">Product Name*</label>
                                    <input type="text" class="form-control" id="edit_product_name"
                                        name="product_name" placeholder="Product name" required>
                                </div>
                            </div>

                            <div class="col-md-6 d-none">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_slug">Product Slug</label>
                                    <input type="text" class="form-control" id="edit_product_slug"
                                        name="slug" placeholder="product-slug">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_description">Product Ingredients</label>
                                    <textarea type="text" class="form-control" id="edit_product_description" name="product_description"
                                        placeholder="Product Ingredients" maxlength="400"></textarea>
                                </div>
                            </div>

                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_quantity">Product Quantity*</label>
                                    <input type="text" class="form-control" id="edit_product_quantity"
                                        name="product_quantity" placeholder="Product Quantity" required>
                                </div>
                            </div> --}}
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_unit">Product Unit*</label>
                                    <select class="form-select select2" name="unit_value" id="edit_unit_select">
                                        <option value="0"  selected>Select Units</option>

                                            <option value="1">l</option>
                                            <option value="2">ml</option>
                                            <option value="3">g</option>
                                            <option value="4">kg</option>

                                    </select>
                                </div>
                            </div> --}}
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_value">Value*</label>
                                    <input type="text" class="form-control" id="edit_product_value"
                                        name="product_value" placeholder="Product Value" required>
                                </div>
                            </div> --}}
                            {{--
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_regular_price">Product MRP
                                        Price(Original Price)*</label>
                                    <input type="text" class="form-control" id="edit_product_regular_price"
                                        name="product_regular_price"
                                        placeholder="Product MRP
                                        Price" required>
                                </div>
                            </div> --}}

                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_mrp_price">Product Selling Price(Offer
                                        Price)*</label>
                                    <input type="text" class="form-control" id="edit_product_mrp_price"
                                        name="product_mrp_price" placeholder="Product Selling Price" required>
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_image">Product Image*(600 *
                                        600)</label>
                                    <input type="file" class="form-control image_el" id="edit_product_image"
                                        placeholder="Product Image" name="product_image" accept="image/*" required>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex mb-4">
                                <div class="col-6">
                                    <div class="mb-2">Previous Image</div>
                                    <label class="edit_show_preview-container">
                                        <img src="" alt="edit_product_image" class="edit_preview_image"></label>
                                </div>

                                <div class="col-6 ">
                                    <div class="mb-2">New Image</div>
                                    <label for="edit_product_image" class="edit_preview-container">
                                        <div class="flex justify-content-center">
                                            <div class="text-center">
                                                <i class="display-4 col-12 text-muted mdi mdi-cloud-upload"></i>
                                            </div>
                                            <div>
                                                <span class="col-12">Upload Image</span>
                                            </div>
                                        </div>
                                    </label>

                                </div>

                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_specification">Product
                                        Description</label>
                                    <textarea type="text" class="form-control" id="edit_product_specification" name="product_specification"
                                        placeholder="Product Description" maxlength="300"></textarea>
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
@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        window.products = @json($products);
        window.brands = @json($brands);
        window.units = @json($units);
        window.subcategories = @json($subcategories);
        window.countries = @json($countries);
        window.allCategories = @json($categories);
        window.allBrands = @json($brands);
        window.allSubcategories = @json($subcategories);
        window.allWarehouses = @json($warehouses);
        window.allBranches = @json($warehouseBranches);
        window.allowedCountryIds = @json($allowedCountryIds);
    </script>
    <script src="{{ URL::asset('assets/js/app/ProductPage.js') }}"></script>
    <script>
   $(document).ready(function () {
    $('#preorderCheckbox').on('change', function () {
        if ($(this).is(':checked')) {
            $('#preorderNoteDiv').show();
        } else {
            $('#preorderNoteDiv').hide();
            $('#add_pre_note').val(""); // clear the value if unchecked
        }
    });

    $(document).on('change', '.flash_sale_checkbox', function() {
        var container = $(this).closest('.row').find('.flash_sale_date_container');
        var input = container.find('.flash_sale_date_input');
        if ($(this).is(':checked')) {
            container.show();
        } else {
            container.hide();
            input.val("");
        }
    });
});

$('#editProductModal').on('shown.bs.modal', function () {
    if ($('#edit_country_select').hasClass("select2-hidden-accessible")) {
        $('#edit_country_select').select2('destroy');
    }
    $('#edit_country_select').select2({
        placeholder: "Select Countries",
        width: '100%',
        dropdownParent: $('#editProductModal')
    });
});

$('#addProductModal').on('shown.bs.modal', function () {
    if ($('#add_country_select').hasClass("select2-hidden-accessible")) {
        $('#add_country_select').select2('destroy');
    }
    $('#add_country_select').select2({
        placeholder: "Select Countries",
        width: '100%',
        dropdownParent: $('#addProductModal')
    });
});


</script>
@endsection
