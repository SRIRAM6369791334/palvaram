@extends('layouts.master')
@section('title')
    Pal Varnan - Gift Products
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
    {{-- Select2 for multi-country --}}
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
        #editProductModal {
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }
        #editProductModal .modal-dialog {
            margin: 1.75rem auto !important;
            top: 0 !important;
            transform: none !important;
        }

        #editProductModal.show,
        body > #editProductModal.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        body > #editProductModal {
            display: none;
        }
        
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Home @endslot
        @slot('title') Gift Products @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center px-3 pt-3">
                    <p class="mb-0 fw-bold">Search:</p>
                    <a href="{{ url('/giftproducts') }}" class="btn btn-light btn-sm" style="background:#0c716a;color:#fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <div class="position-relative">
                        <div class="modal-button mt-2">
                            <div class="row align-items-start">
                                <div class="col-sm">
                                    <button type="button" class="btn btn-success mb-4"
                                        data-bs-toggle="modal" data-bs-target="#addProductModal">
                                        <i class="mdi mdi-plus me-1"></i> Add Gift Product
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="table-gridjs"></div>
                </div>
            </div>
        </div>
    </div>

   
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Gift Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addProductForm" novalidate enctype="multipart/form-data">
                        @csrf

                       
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Choose Country*</label>
                                    <select class="form-select select2" name="country_id[]" id="add_country_select" multiple>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Choose Gift Category*</label>
                                    <select class="form-select select2" name="category_id" id="add_category_select">
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Choose Gift SubCategory</label>
                                    <select class="form-select select2" name="subcategory_id" id="add_subcategory_select">
                                        <option value="" disabled selected>Select Sub Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product Name*</label>
                                    <input type="text" class="form-control" id="add_product_name"
                                        name="product_name" placeholder="Gift Product name" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Product Description</label>
                                    <textarea class="form-control" id="add_product_description"
                                        name="product_description" rows="3"></textarea>
                                </div>
                            </div>

                          
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product Image* (600 x 600)</label>
                                    <input type="file" class="form-control" id="add_product_image"
                                        name="product_image" accept="image/*" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="add_product_image" class="preview-container" id="add_preview_container">
                                    <div class="text-center">
                                        <i class="display-4 text-muted mdi mdi-cloud-upload"></i>
                                        <span class="d-block">Upload Image</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                    <div class="card" style="padding: 20px;border: 1px solid;">
                        <div class="gift-variant-card">

                            <h5 class="gift-variant-title">
                                Gift Product Variant
                            </h5>

                            <div class="gift-variant-block">

                       
                        <div class="gift-variant-block border rounded p-3 mb-3" data-variant-index="0">
                            <h5 class="mb-3">
                               Gift Variant 
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Variant Image* (600 x 600)</label>
                                        <input type="file" class="form-control" id="add_varient_image"
                                            name="varient_image" accept="image/*" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="add_varient_image" class="preview-container" id="add_varient_preview">
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
                                            @foreach($sizes as $size)
                                                <option value="{{ $size->id }}">{{ $size->size }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Color</label>
                                        <select class="form-select select2" name="color_id" id="add_color_select">
                                            <option value="">Select Color</option>
                                            @foreach($colors as $color)
                                                <option value="{{ $color->id }}">{{ $color->color }}</option>
                                            @endforeach
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
                                        <input type="text" class="form-control" name="sku" placeholder="SKU-CODE">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Barcode</label>
                                        <input type="text" class="form-control" name="barcode"
                                            placeholder="Barcode / EAN">
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
                                        <input type="datetime-local" class="form-control gift-flash-date-input"
                                            name="flash_sale_date" style="display:none;">
                                        <div class="form-check mt-2">
                                            <input type="checkbox" class="form-check-input gift-flash-sale-chk"
                                                name="flash_sale" value="1">
                                            <label class="form-check-label">Flash Sale</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-center gap-4 mt-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="hot_deals"
                                            id="add_hot_deals" value="1">
                                        <label class="form-check-label">New Arrivals</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="popular_prod"
                                            id="add_popular_prod" value="1">
                                        <label class="form-check-label">Best Seller</label>
                                    </div>
                                </div>

                             
                                <div class="col-12 mt-3">
                                    <h6 class="fw-bold">Thumb Images</h6>
                                    <div id="gift-thumb-list-0" class="gift-thumb-list">
                                        <div class="gift-thumb-entry d-flex align-items-end gap-2 mb-2">
                                            <div style="flex:1">
                                                <label class="form-label">Thumb Image (600 x 600)</label>
                                                <input type="file" class="form-control gift-thumb-input"
                                                    name="thumb_images[0][]" accept="image/*">
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-success btn-sm add-thumb-btn"
                                                    data-variant="0">+</button>
                                                <button type="button" class="btn btn-danger btn-sm remove-thumb-btn"
                                                    style="display:none;">−</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="gift-variants-wrapper"></div>

                            <div class="mt-3">
                                <button type="button"
                                    id="add_variant_btn"
                                    class="btn btn-success">
                                    Add Another Variant
                                </button>
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

   
    <div class="modal " id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Gift Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editProductForm" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="edit_product_id">
                        <!-- <input type="hidden" id="edit_variant_id"> -->

                        <div class="row">
                            {{-- Country (multi) --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Choose Country*</label>
                                    <select class="form-select select2" name="country_id[]" id="edit_country_select" multiple>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Choose Gift Category*</label>
                                    <select class="form-select select2" name="category_id" id="edit_category_select">
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Choose Gift SubCategory</label>
                                    <select class="form-select select2" name="subcategory_id" id="edit_subcategory_select">
                                        <option value="" disabled selected>Select SubCategory</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product Name*</label>
                                    <input type="text" class="form-control" id="edit_product_name"
                                        name="product_name" placeholder="Gift Product name" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Product Description</label>
                                    <textarea class="form-control" id="edit_product_description"
                                        name="product_description" rows="3"></textarea>
                                </div>
                            </div>

                            {{-- Product Image --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product Image (600 x 600)</label>
                                    <input type="file" class="form-control" id="edit_product_image"
                                        name="product_image" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-6 d-flex gap-3 align-items-center">
                                <div>
                                    <div class="mb-1">Previous</div>
                                    <img src="" class="edit_preview_image"
                                        style="width:80px;height:80px;object-fit:cover;border-radius:5px;">
                                </div>
                                <div>
                                    <div class="mb-1">New</div>
                                    <label for="edit_product_image" class="edit_preview-container"
                                        style="width:80px;height:80px;display:flex;align-items:center;justify-content:center;border:1px dashed #ccc;border-radius:5px;cursor:pointer;">
                                        <i class="mdi mdi-cloud-upload text-muted" style="font-size:24px;"></i>
                                    </label>
                                </div>
                            </div>

                            

                           
                     
                           
                            
                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary edit_submit_btn mt-3" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script>
        window.products          = @json($products);
        window.subcategories     = @json($subcategories);
        window.allCategories     = @json($categories);
        window.allowedCountryIds = @json($allowedCountryIds);
        window.countries         = @json($countries);
        window.sizes             = @json($sizes);
        window.colors            = @json($colors);
        window.giftUrls = {
            store:   "{{ route('gift_products.store') }}",
            update:  "{{ url('updateGiftProducts') }}",
            destroy: "{{ url('destroyGiftProducts') }}"
        };
    </script>
    <script src="{{ URL::asset('assets/js/app/GiftProductPage.js') }}"></script>

<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('editProductModal');
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
</script>

@endsection