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
            Product Variant
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
                top: 79px;
                left: 25px;
                font-weight: bold;">
                    Search:</p>
                <div class="card-body">
                    <div class="position-relative">
                        <div class="modal-button mt-2">
                            <form class="needs-validation" id="productverfilterForm" novalidate
                                enctype="multipart/form-data">
                                <div class="row align-items-start">

                                    <div class="col-sm" style="width: 280px">
                                        <label class="form-label" for="select_category_select">Choose Category*</label>
                                        <select class="form-select custid" name="category_id" id="sel_category_select">
                                            <option value="" disabled selected>Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->category_name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-sm" style="width: 280px">
                                        <label class="form-label " for="select_category_select">Choose Product*</label>
                                        {{-- <select class="form-select produ1" name="product_id" id="select_product">
                                                <option value="" disabled selected>Select Product</option>
                                                @foreach ($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->product_name }}</option>
                                        @endforeach

                                            </select> --}}

                                        <select class="form-select proname product2" name="product_id" id="select_product">
                                            <option value="" disabled selected>Select Product.</option>

                                        </select>

                                    </div>
                                    <div class="col-sm">
                                        <div>
                                            <button type="submit" class="btn btn-success productver_filter_btn mb-4"
                                                style="position: relative;
                                                top: 29px;">submit</button>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div style="position: relative;
                                    top: 26px;">
                                            <button type="button" class="btn btn-success  add_btn_top_el mb-4"
                                                data-bs-toggle="modal" data-bs-target="#addProductvariModal"><i
                                                    class="mdi mdi-plus me-1"></i> Add
                                                Product Variant</button>
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

    <div class="modal fade" id="addProductvariModal" tabindex="-1" aria-labelledby="addProductvariModalLabel"
        aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductvariModalLabel">Add Product Variant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" id="addProductvarientForm" novalidate enctype="multipart/form-data">
                        @csrf


                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_country_select">Choose Country*</label>
                                    <select class="form-control select2-country" name="country_id[]" 
                                            id="add_country_select" multiple>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Choose Brand</label>
                                    <select id="add_brand_select" name="brand_id" class="form-control">
                                        <option value="" disabled selected>Select Brand</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="select_category_select">Choose Category*</label>
                                    <select class="form-select custid" name="categoryid" id="select_category_select">
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->category_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="select_subcategory_select">Choose Sub Category</label>
                                    <select class="form-select custid" name="subcategoryid" id="select_subcategory_select">
                                        <option value="" disabled selected>Select Sub Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_product_name">Product Name*</label>
                                    <select class="form-select proname product2" name="product_id" id="add_product_name">
                                        <option value="" disabled selected>Select Product.</option>

                                    </select>
                                </div>
                            </div>
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

                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="add_varient_image">Variant
                                                                Image*(600 *
                                                                600)</label>
                                                            <input type="file"
                                                                class="form-control image_el  needsclick"
                                                                id="add_varient_image" placeholder="Variant Image"
                                                                accept="image/*" name="Varient_image" required>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="add_varient_name">Variant
                                                                Name(Optional)</label>
                                                            <input type="text" class="form-control"
                                                                id="add_varient_name" name="varient_name"
                                                                placeholder="Variant Name" required>
                                                        </div>
                                                    </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">SKU (Auto-gen if empty)</label>
                                    <input type="text" class="form-control" name="sku" placeholder="SKU-CODE">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Barcode</label>
                                    <input type="text" class="form-control" name="barcode" placeholder="Barcode / EAN">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_item_code">Item Code</label>
                                    <input type="text" class="form-control" name="item_code" placeholder="Item Code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_unit_select">Select Product Unit*</label>
                                    <select class="form-select" name="unit_id" id="add_unit_select">
                                        <option value="" selected>Select Units</option>

                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->unit_name }} ({{ $unit->short_name }})</option>
                                        @endforeach
                                        @if($units->isEmpty())
                                            <option value="1">l</option>
                                            <option value="2">ml</option>
                                            <option value="3">g</option>
                                            <option value="4">kg</option>
                                            <option value="5">No's</option>
                                        @endif


                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_product_value">Product Unit Value*</label>
                                    <input type="text" class="form-control" id="add_product_value" name="value"
                                        placeholder="Product Value" required>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_product_mrp_price">Product MRP Price(ORIGINAL
                                        PRICE)*</label>
                                    <input type="text" class="form-control" id="add_product_mrp_price"
                                        name="mrp_price" placeholder="Product MRP price" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_product_offer_price">Product Selling Price(OFFER
                                        PRICE)*</label>
                                    <input type="text" class="form-control" id="add_product_offer_price"
                                        name="offer_price" placeholder="Product Selling price" required>
                                </div>
                            </div>
                            
                                    <!-- <div class="col-md-12 mb-3 stock-entries-wrapper">
                                                        <h6 class="mt-2">Stock Entries</h6>
                                                        <input type="hidden" class="stock-entry-count" name="stock_entry_count[]" value="1">
                                                        <div class="stock-entry-list">
                                                            <div class="stock-entry d-flex align-items-end gap-2 mb-5 mt-2">
                                                                <div style="flex:1">
                                                                    <label class="form-label">Choose Warehouse</label>
                                                                    <select class="form-select variant-warehouse-select" name="warehouse_id[]">
                                                                        <option value="" disabled selected>Select Warehouse</option>
                                                                    </select>
                                                                </div>
                                                                <div style="flex:1">
                                                                    <label class="form-label">Choose Store </label>
                                                                    <select class="form-select variant-branch-select" name="branch_id[]">
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
                                                                    <button type="button" class="btn btn-success add-var-stock-entry">+</button>
                                                                    <button type="button" class="btn btn-danger remove-var-stock-entry">−</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_Low_Stock">Low Stock*</label>
                                    <input type="text" class="form-control" id="add_Low_Stock" name="low_stock"
                                        placeholder="Low Stock" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="">Select Product GST*</label>
                                    <select class="form-select" name="product_gst" id="produgst">
                                        <option value="" selected>Select GST</option>
                                        <option value="0">0(GST Included)</option>
                                        <option value="5">5</option>
                                        <option value="12">12</option>
                                        <option value="18">18</option>
                                        <option value="28">28</option>



                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-md-12">
                                <h5>Product Thump Images</h5>
                                <input type="hidden" name="product_image_count[]"
                                    class="product_image_count2" value="1">
                                <div class="col-lg-12">
                                    <div id="dynamic-inputs1" class="dynamic-inputs2">


                                        <div class="d-flex product_fields2">
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <div class="mb-3">
                                                        <label class="form-label"
                                                            for="add_product_image">Product
                                                            Image*(750 *
                                                            600)</label>
                                                        <input type="file"
                                                            class="form-control image_el  needsclick"
                                                            id="add_product_image"
                                                            placeholder="Product Image"
                                                            name="product_image1[]" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-12 mt-4">
                                                    <div class="input-group-append">
                                                        <button
                                                            class="btn btn-danger delete-input2"
                                                            type="button">Delete</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 mt-3">
                                    <button id="add-input2" class="btn btn-success add-input2"
                                        type="button">Add
                                        Another Images</button>
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">

                                    <input type="checkbox" name="hot_deals" class="hot_value" value="0"> <label
                                        class="form-label" for="">New Arrivals</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">

                                    <input type="checkbox" name="Popular_products" class="popular_prod" value="0"> <label
                                        class="form-label" for="">Best seller</label>
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="mb-3">

                                    <input type="checkbox" name="pre_order" class="pre_order" value="0" id="preorderCheckbox"> <label
                                        class="form-label" for="">Pre Order</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <input type="checkbox" name="flash_sale" class="flash_sale" value="1" id="flashSaleCheckbox"> 
                                    <label class="form-label">Flash Sale</label>
                                </div>
                            </div>
                            <div class="col-md-3" id="flashSaleDateDiv" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Flash Sale End Date</label>
                                    <input type="datetime-local" class="form-control" id="add_flash_sale_date" name="flash_sale_date">
                                </div>
                            </div>
                            <div class="col-md-3 justify-content-start align-items-center"
                                                        id="preorderNoteDiv" style="display: none;">
                                                        <div class="mb-3 mt-4">
                                                            <textarea class="form-control" id="add_pre_note" name="pre_note" placeholder="Pre Order Note" maxlength="300"></textarea>
                                                        </div>
                                                    </div>
                        </div>
                        <div class="mb-3" style="">
                                                        <h5>Product Thump Images</h5>
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
                                                                                    class="form-control image_el  needsclick thumbim"
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



                        <div class="text-center">
                            <button class="btn btn-primary addvari_submit_btn mt-3" type="submit">Submit</button>
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
                    <form class="needs-validation" id="editProductVarientForm" novalidate>
                        <input type="hidden" id="edit_productvar_id" />
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

                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Choose Brand</label>
                                    <select id="edit_brand_select" name="brand_id" class="form-control">
                                        <option value="" disabled selected>Select Brand</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="select_category_select">Choose Category*</label>
                                    <select class="form-select custid" name="categoryid" id="edit_category_select">
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->category_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_subcategory_select">Choose Sub Category</label>
                                    <select class="form-select custid" name="subcategoryid" id="edit_subcategory_select">
                                        <option value="" disabled selected>Select Sub Category</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_name">Product Name*</label>
                                    <select class="form-select" name="product_id" id="edit_product_name">
                                        <option value="" selected>Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->product_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_varient_image">Variant Image*(600 *
                                        600)</label>
                                    <input type="file" class="form-control image_el" id="edit_varient_image"
                                        placeholder="Product Image" name="varient_image" accept="image/*" required>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex mb-4">
                                <div class="col-6">
                                    <div class="mb-2">Previous Image</div>
                                    <label class="edit_show_preview-container">
                                        <img src="" alt="edit_varient_image"  class="edit_preview_image"></label>
                                </div>

                                <div class="col-6 ">
                                    <div class="mb-2">New Image</div>
                                    <label for="edit_varient_image" class="edit_preview-container" id="preview-container2">
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">SKU (Auto-gen if empty)</label>
                                    <input type="text" class="form-control" name="sku" id="edit_sku" placeholder="SKU-CODE">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Barcode</label>
                                    <input type="text" class="form-control" name="barcode" id="edit_barcode" placeholder="Barcode / EAN">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Item Code</label>
                                    <input type="text" class="form-control" name="item_code" id="edit_item_code" placeholder="Item Code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_unit_select">Select Product Unit*</label>
                                    <select class="form-select" name="unit_id" id="edit_unit_select">
                                        <option value="" selected>Select Units</option>

                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->unit_name }} ({{ $unit->short_name }})</option>
                                        @endforeach
                                        @if($units->isEmpty())
                                            <option value="1">l</option>
                                            <option value="2">ml</option>
                                            <option value="3">g</option>
                                            <option value="4">kg</option>
                                            <option value="5">No's</option>
                                        @endif


                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_value">Value*</label>
                                    <input type="text" class="form-control" id="edit_product_value" name="value"
                                        placeholder="Product Value" required>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_mrp_price">Product MRP Price(ORIGINAL
                                        PRICE)*</label>
                                    <input type="text" class="form-control" id="edit_product_mrp_price"
                                        name="mrp_price" placeholder="Product MRP price" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_product_offer_price">Product Selling Price(OFFER
                                        PRICE)*</label>
                                    <input type="text" class="form-control" id="edit_product_offer_price"
                                        name="offer_price" placeholder="Product Selling price" required>
                                </div>
                            </div>
                            <!-- <div class="col-md-12 mb-3 edit-stock-entries-wrapper">
                                <h6 class="mt-2">Stock Entries</h6>

                                <div class="edit-stock-entry-list">

                                    <div class="edit-stock-entry d-flex align-items-end gap-2 mb-4 mt-2">

                                        <div style="flex:1">
                                            <label class="form-label">Choose Warehouse</label>
                                            <select class="form-select edit-warehouse-select" name="warehouse_id[]" id="edit_var_warehouse_select">
                                                <option value="" disabled selected>Select Warehouse</option>
                                            </select>
                                        </div>

                                        <div style="flex:1">
                                            <label class="form-label">Choose Store</label>
                                            <select class="form-select edit-branch-select" name="branch_id[]" id="edit_var_branch_select">
                                                <option value="" disabled selected>Select Store</option>
                                            </select>
                                        </div>

                                        <div style="flex:1">
                                            <label class="form-label">Stock Quantity*</label>
                                            <input type="number"
                                                class="form-control"
                                                name="product_quantity[]"
                                                placeholder="Quantity"
                                                required>
                                        </div>

                                        <div style="flex:1">
                                            <label>Batch No</label>
                                            <input type="text"
                                                name="batch_no[]"
                                                class="form-control">
                                        </div>

                                        <div style="flex:1">
                                            <label>MFG Date</label>
                                            <input type="date"
                                                name="manufacturing_date[]"
                                                class="form-control">
                                        </div>

                                        <div style="flex:1">
                                            <label>Expiry Date</label>
                                            <input type="date"
                                                name="expiry_date[]"
                                                class="form-control">
                                        </div>

                                        <div>
                                            <button type="button"
                                                class="btn btn-success add-edit-stock-entry">+</button>

                                            <button type="button"
                                                class="btn btn-danger remove-edit-stock-entry">−</button>
                                        </div>

                                    </div>

                                </div>
                            </div> -->

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_Low_Stock">Low Stock*</label>
                                    <input type="text" class="form-control" id="edit_Low_Stock" name="low_stock"
                                        placeholder="Low Stock" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="">Select Product GST</label>
                                    <select class="form-select" name="product_gst" id="edit_product_gst">
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
                                <div class="mb-3">

                                    <input type="checkbox" name="hot_deals" class="hot_value" value="0"
                                        id="edit_hot_product"> <label class="form-label" for="add_product_mrp_price">New Arrivals</label>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="mb-3">

                                    <input type="checkbox" name="popular_prod" class="popular_prod"  id="edit_bes_product" value="0"> <label
                                        class="form-label" for="">Best seller</label>
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="mb-3">

                                    <input type="checkbox" name="pre_order" class="pre_order"  id="edit_pre_product" value="1"> <label
                                        class="form-label" for="">Pre Order</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <input type="checkbox" name="flash_sale" class="flash_sale" id="edit_flash_product" value="1"> 
                                    <label class="form-label">Flash Sale</label>
                                </div>
                            </div>
                            <div class="col-md-3" id="editFlashSaleDateDiv" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Flash Sale End Date</label>
                                    <input type="datetime-local" class="form-control" id="edit_flash_sale_date" name="flash_sale_date">
                                </div>
                            </div>
                            {{-- <div class="col-md-3">
                                <div class="mb-3">

                                    <input type="checkbox" name="pre_order" class="pre_order" value="0" id="preorderCheckbox"> <label
                                        class="form-label" for="">Pre Order</label>
                                </div>
                            </div> --}}
                            <div class="col-md-3 justify-content-start align-items-center"
                                                        id="editpreorderNoteDiv" style="display: none;">
                                                        <div class="mb-3 mt-4">
                                                            <textarea class="form-control" id="edit_pre_note" name="pre_note" placeholder="Pre Order Note" maxlength="300"></textarea>
                                                        </div>
                                                    </div>

                            <div class="card" style="padding: 20px;border: 1px solid;">
                                <h5 >Product Thump Images</h5>
                                <div class="col-lg-12">
                                    <div id="dynamic-inputsedit">


                                        <div class="d-flex product_fieldsedit">
                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="edit_productthum_image">Product
                                                            Image*(600 *
                                                            600)</label>
                                                        <input type="file"
                                                            class="form-control image_el  needsclick"
                                                            id="edit_productthum_image" placeholder="Product Image"
                                                            name="product_image1[]" accept="image/*" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-5 d-flex mb-4">
                                                    <div class="col-6">
                                                        <div class="mb-2">Previous Image</div>
                                                        <label class="edit_show_preview-containernew">
                                                            <img src="" alt="image" class="edit_preview_image"></label>
                                                    </div>

                                                    <div class="col-6 ">
                                                        <div class="mb-2">New Image</div>
                                                        <label for="edit_productthum_image" class="edit_preview-containernew123">
                                                            <div class="flex justify-content-center">
                                                                <div class="text-center">
                                                                    <i class="display-4 col-12 text-muted mdi mdi-cloud-upload" style="font-size: 20px"></i>
                                                                </div>
                                                                <div>

                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-sm-12 mt-4">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-danger delete-inputdeit"
                                                            type="button">Delete</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 mt-3">
                                    <button id="add-inputedit" class="btn btn-success" type="button">Add
                                        Another Images</button>
                                </div>
                            </div>


                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary mt-3 editver_submit_btn" type="submit">Update</button>
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
        window.productvarient = @json($productvarient);
        window.subcategories = @json($subcategories);
        window.countries = @json($countries);
        
        window.allCategories = @json($categories);
        window.allBrands = @json($brands);
        window.allWarehouses = @json($warehouses);
        window.allBranches = @json($warehouseBranches);
    </script>
    <script src="{{ URL::asset('assets/js/app/ProductVarientPage.js') }}"></script>
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
});

</script>
<script>
   $(document).ready(function () {
    $('#edit_pre_product').on('change', function () {
        if ($(this).is(':checked')) {
            $('#editpreorderNoteDiv').show();
        } else {
            $('#editpreorderNoteDiv').hide();
            $('#edit_pre_note').val(""); // clear the value if unchecked
        }
    });
});

</script>
<script>
   $(document).ready(function () {
    $('#flashSaleCheckbox').on('change', function () {
        if ($(this).is(':checked')) {
            $('#flashSaleDateDiv').show();
        } else {
            $('#flashSaleDateDiv').hide();
            $('#add_flash_sale_date').val(""); 
        }
    });

    $('#edit_flash_product').on('change', function () {
        if ($(this).is(':checked')) {
            $('#editFlashSaleDateDiv').show();
        } else {
            $('#editFlashSaleDateDiv').hide();
            $('#edit_flash_sale_date').val(""); 
        }
    });
});

$('#addProductvariModal').on('shown.bs.modal', function () {
    if ($('#add_country_select').hasClass("select2-hidden-accessible")) {
        $('#add_country_select').select2('destroy');
    }
    $('#add_country_select').select2({
        placeholder: "Select Countries",
        width: '100%',
        dropdownParent: $('#addProductvariModal')
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

</script>

<!-- <script>

function generateEditStockRow(selectedWarehouseId = null, selectedBranchId = null, qty = '', batchNo = '', mfgDate = '', expiryDate = '') {
    const countryId = $("#edit_country_select").val();

    let warehouseOptions = `<option value="" disabled selected>Select Warehouse</option>`;
    (window.allWarehouses || [])
        .filter(w => w.country_id == countryId)
        .forEach(function (warehouse) {
            const isSelected = selectedWarehouseId && warehouse.id == selectedWarehouseId ? 'selected' : '';
            warehouseOptions += `<option value="${warehouse.id}" ${isSelected}>${warehouse.warehouse_name}</option>`;
        });

    let branchOptions = `<option value="" disabled selected>Select Store</option>`;
    if (selectedWarehouseId) {
        (window.allBranches || [])
            .filter(b => b.warehouse_id == selectedWarehouseId)
            .forEach(function (branch) {
                const isSelected = selectedBranchId && branch.id == selectedBranchId ? 'selected' : '';
                branchOptions += `<option value="${branch.id}" ${isSelected}>${branch.branch_name}</option>`;
            });
    }

    return `
    <div class="edit-stock-entry d-flex align-items-end gap-2 mb-4 mt-2">
        <div style="flex:1">
            <label class="form-label">Choose Warehouse</label>
            <select class="form-select edit-warehouse-select" name="warehouse_id[]">
                ${warehouseOptions}
            </select>
        </div>
        <div style="flex:1">
            <label class="form-label">Choose Store</label>
            <select class="form-select edit-branch-select" name="branch_id[]">
                ${branchOptions}
            </select>
        </div>
        <div style="flex:1">
            <label class="form-label">Stock Quantity*</label>
            <input type="number" class="form-control" name="product_quantity[]" value="${qty}" placeholder="Quantity" required>
        </div>
        <div style="flex:1">
            <label>Batch No</label>
            <input type="text" name="batch_no[]" class="form-control" value="${batchNo}">
        </div>
        <div style="flex:1">
            <label>MFG Date</label>
            <input type="date" name="manufacturing_date[]" class="form-control" value="${mfgDate}">
        </div>
        <div style="flex:1">
            <label>Expiry Date</label>
            <input type="date" name="expiry_date[]" class="form-control" value="${expiryDate}">
        </div>
        <div>
            <button type="button" class="btn btn-success add-edit-stock-entry">+</button>
            <button type="button" class="btn btn-danger remove-edit-stock-entry">−</button>
        </div>
    </div>`;
}

$(document).ready(function () {

   
    $(document).on('click', '.add-edit-stock-entry', function () {
        $('.edit-stock-entry-list').append(generateEditStockRow());
    });

  
    $(document).on('click', '.remove-edit-stock-entry', function () {
        if ($('.edit-stock-entry').length > 1) {
            $(this).closest('.edit-stock-entry').remove();
        }
    });

});
</script>

<script>
$(document).ready(function () {

   
    let warehouseOptions = '<option value="" disabled selected>Select Warehouse</option>';

    window.allWarehouses.forEach(function (warehouse) {
        warehouseOptions += `
            <option value="${warehouse.id}">
                ${warehouse.warehouse_name}
            </option>
        `;
    });

    $('.variant-warehouse-select').html(warehouseOptions);

   
    let branchOptions = '<option value="" disabled selected>Select Store</option>';

    window.allBranches.forEach(function (branch) {
        branchOptions += `
            <option value="${branch.id}">
                ${branch.branch_name}
            </option>
        `;
    });

    $('.variant-branch-select').html(branchOptions);

});
</script> -->

@endsection
