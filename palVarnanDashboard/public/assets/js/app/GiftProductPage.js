

const addValidator = new JustValidate("#addProductForm", {
    validateBeforeSubmitting: true,
});

const editValidator = new JustValidate("#editProductForm", {
    validateBeforeSubmitting: true,
});

// ─── Country-based Category Loader ───────────────────────────────────────────

function loadCategoriesByCountry(countryIds, categorySelectId) {
    const select = document.getElementById(categorySelectId);
    if (!select) return;
    select.innerHTML = '<option value="" disabled selected>Select Category</option>';

    if (!countryIds || countryIds.length === 0) return;

    const ids = Array.isArray(countryIds)
        ? countryIds.map(id => parseInt(id))
        : [parseInt(countryIds)];

    const filtered = (window.allCategories || []).filter(cat => {
        const catIds = cat.country_id
            ? cat.country_id.toString().split(',').map(id => parseInt(id.trim()))
            : [];
        return catIds.some(id => ids.includes(id));
    });

    filtered.forEach(cat => {
        select.innerHTML += `<option value="${cat.id}">${cat.category_name}</option>`;
    });
}

// ─── Subcategory Loader ───────────────────────────────────────────────────────

function populateSubcategories(categoryId, targetSelectId, selectedId = null) {
    const select = $(`#${targetSelectId}`);
    select.empty().append('<option value="" disabled selected>Select Sub Category</option>');
    if (!categoryId) return;
    const filtered = (window.subcategories || []).filter(sub => sub.category_name == categoryId);
    filtered.forEach(sub => {
        const selected = (selectedId && sub.id == selectedId) ? 'selected' : '';
        select.append(`<option value="${sub.id}" ${selected}>${sub.subcategory_name}</option>`);
    });
}

// ─── Country Name Helper ──────────────────────────────────────────────────────

function resolveCountryName(countryId) {
    if (!countryId) return '-';
    const ids = countryId.toString().split(',').map(id => id.trim());
    const names = ids.map(id => {
        const found = (window.countries || []).find(c => c.id == id);
        return found ? found.name : id;
    }).filter(Boolean);
    return names.join(', ') || '-';
}

// ─── Variant HTML Builder ─────────────────────────────────────────────────────

let variantIndex = 0;

function buildVariantHTML() {
    variantIndex++;
    const vi = variantIndex;
    const sizeOptions = (window.sizes || []).map(s =>
        `<option value="${s.id}">${s.size}</option>`
    ).join('');
    const colorOptions = (window.colors || []).map(c =>
        `<option value="${c.id}">${c.color}</option>`
    ).join('');

    return `
    <div class="gift-variant-block border rounded p-3 mb-3 position-relative" data-variant-index="${vi}">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Variant #${vi}</h6>
            <button type="button" class="btn btn-danger btn-sm remove-variant-btn">
                <i class="mdi mdi-delete"></i> Remove Variant
            </button>
        </div>
        <div class="row">
           
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Variant Image* (600 x 600)</label>
                    <input type="file" class="form-control gift-variant-image-input"
                        name="varient_image[${vi}]" accept="image/*" required
                        data-preview="gift-variant-preview-${vi}">
                </div>
            </div>
            <div class="col-md-6">
                <label for="gift-variant-image-${vi}" class="preview-container" id="gift-variant-preview-${vi}"
                    style="display:flex;align-items:center;justify-content:center;height:80px;border:1px dashed #ccc;border-radius:5px;cursor:pointer;">
                    <div class="text-center">
                        <i class="display-4 text-muted mdi mdi-cloud-upload"></i>
                        <span class="d-block">Upload Variant Image</span>
                    </div>
                </label>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Size</label>
                    <select class="form-select" name="size_id[${vi}]">
                        <option value="">Select Size</option>
                        ${sizeOptions}
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Color</label>
                    <select class="form-select" name="color_id[${vi}]">
                        <option value="">Select Color</option>
                        ${colorOptions}
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Variant Name (Optional)</label>
                    <input type="text" class="form-control" name="varient_name[${vi}]" placeholder="e.g. Red - Large">
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">SKU (Auto-gen if empty)</label>
                    <input type="text" class="form-control" name="sku[${vi}]" placeholder="SKU-CODE">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Barcode</label>
                    <input type="text" class="form-control" name="barcode[${vi}]" placeholder="Barcode / EAN">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">MRP Price*</label>
                    <input type="number" class="form-control gift-mrp-input" name="mrp_price[${vi}]"
                        placeholder="0.00" step="0.01" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Selling Price*</label>
                    <input type="number" class="form-control gift-offer-input" name="offer_price[${vi}]"
                        placeholder="0.00" step="0.01" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Low Stock Alert*</label>
                    <input type="number" class="form-control" name="low_stock[${vi}]"
                        placeholder="5" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Product GST</label>
                    <select class="form-select" name="product_gst[${vi}]">
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
                        name="flash_sale_date[${vi}]" style="display:none;">
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input gift-flash-sale-chk"
                            name="flash_sale[${vi}]" value="1">
                        <label class="form-check-label">Flash Sale</label>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-center gap-4 mt-2">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="hot_deals[${vi}]" value="1">
                    <label class="form-check-label">New Arrivals</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="popular_prod[${vi}]" value="1">
                    <label class="form-check-label">Best Seller</label>
                </div>
            </div>

            
            <div class="col-12 mt-3">
                <h6 class="fw-bold">Thumb Images</h6>
                <div id="gift-thumb-list-${vi}" class="gift-thumb-list">
                    <div class="gift-thumb-entry d-flex align-items-end gap-2 mb-2">
                        <div style="flex:1">
                            <label class="form-label">Thumb Image (600 x 600)</label>
                            <input type="file" class="form-control gift-thumb-input"
                                name="thumb_images[${vi}][]" accept="image/*">
                        </div>
                        <div>
                            <button type="button" class="btn btn-success btn-sm add-thumb-btn"
                                data-variant="${vi}">+</button>
                            <button type="button" class="btn btn-danger btn-sm remove-thumb-btn" style="display:none;">−</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>`;
}

// ─── On Page Load ─────────────────────────────────────────────────────────────

$(document).ready(function () {

    // Initialize Select2 for country selects if available
    if ($.fn.select2) {
        $('#add_country_select').select2({
            placeholder: 'Select Country',
            allowClear: true,
            multiple: true,
            dropdownParent: $('#addProductModal'),
        });
        $('#edit_country_select').select2({
            placeholder: 'Select Country',
            allowClear: true,
            multiple: true,
            dropdownParent: $('#editProductModal'),
        });
    }

    // Auto-select country for single-country users
    if (window.allowedCountryIds && window.allowedCountryIds.length === 1) {
        const countryId = window.allowedCountryIds[0];
        if ($.fn.select2) {
            $('#add_country_select').val([String(countryId)]).trigger('change');
        } else {
            $('#add_country_select').val(countryId).trigger('change');
        }
        loadCategoriesByCountry([countryId], 'add_category_select');
    }

    // ── ADD: country change ──
    $(document).on("change", "#add_country_select", function () {
        const vals = $(this).val(); // array for select2 multi, string for regular
        const ids = vals ? (Array.isArray(vals) ? vals : [vals]) : [];
        loadCategoriesByCountry(ids, 'add_category_select');
        $("#add_category_select").val("").trigger("change");
        $("#add_subcategory_select").html('<option value="" disabled selected>Select Sub Category</option>');
    });

    // ── ADD: category change ──
    $(document).on("change", "#add_category_select", function () {
        populateSubcategories($(this).val(), 'add_subcategory_select');
    });

    // ── EDIT: country change ──
    $(document).on("change", "#edit_country_select", function () {
        const vals = $(this).val();
        const ids = vals ? (Array.isArray(vals) ? vals : [vals]) : [];
        loadCategoriesByCountry(ids, 'edit_category_select');
        $("#edit_category_select").val("").trigger("change");
        $("#edit_subcategory_select").html('<option value="" disabled selected>Select Sub Category</option>');
    });

    // ── EDIT: category change ──
    $(document).on("change", "#edit_category_select", function () {
        populateSubcategories($(this).val(), 'edit_subcategory_select');
    });

    // ── Flash sale checkbox toggle ──
    $(document).on("change", ".gift-flash-sale-chk", function () {
        const $dateInput = $(this).closest(".mb-3").find(".gift-flash-date-input");
        $dateInput.toggle($(this).is(":checked"));
    });
   
    $(document).on("change", "#add_flash_sale_checkbox", function () {
        $("#add_flash_sale_date").toggle($(this).is(":checked"));
    });

    // ── ADD VARIANT BUTTON ──
    $(document).on("click", "#add_variant_btn", function () {
        $("#gift-variants-wrapper").append(buildVariantHTML());
    });

    // ── REMOVE VARIANT ──
    $(document).on("click", ".remove-variant-btn", function () {
        $(this).closest(".gift-variant-block").remove();
    });

    // ── ADD THUMB IMAGE ROW ──
    $(document).on("click", ".add-thumb-btn", function () {
        const vi = $(this).data("variant");
        const $list = $(`#gift-thumb-list-${vi}`);
        const newRow = `
        <div class="gift-thumb-entry d-flex align-items-end gap-2 mb-2">
            <div style="flex:1">
                <label class="form-label">Thumb Image (600 x 600)</label>
                <input type="file" class="form-control gift-thumb-input"
                    name="thumb_images[${vi}][]" accept="image/*">
            </div>
            <div>
                <button type="button" class="btn btn-success btn-sm add-thumb-btn" data-variant="${vi}">+</button>
                <button type="button" class="btn btn-danger btn-sm remove-thumb-btn">−</button>
            </div>
        </div>`;
        $list.append(newRow);
        updateThumbButtons($list);
    });

    // ── REMOVE THUMB IMAGE ROW ──
    $(document).on("click", ".remove-thumb-btn", function () {
        const $list = $(this).closest(".gift-thumb-list");
        $(this).closest(".gift-thumb-entry").remove();
        updateThumbButtons($list);
    });

    function updateThumbButtons($list) {
        const $entries = $list.find(".gift-thumb-entry");
        $entries.each(function () {
            $(this).find(".remove-thumb-btn").toggle($entries.length > 1);
        });
    }

    // ── VARIANT IMAGE PREVIEW (delegated) ──
    $(document).on("change", ".gift-variant-image-input", function () {
        const previewId = $(this).data("preview");
        const file = this.files[0];
        if (!file || !file.type.match("image.*")) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const $preview = $(`#${previewId}`);
            const img = $("<img>").attr("src", e.target.result)
                .css({ width: "80px", height: "80px", "object-fit": "cover", "border-radius": "5px" });
            const removeBtn = $("<button>").addClass("btn btn-danger btn-sm d-block mt-1 gift-variant-img-remove")
                .text("Remove").attr("data-preview", previewId);
            $preview.empty().append(img).append(removeBtn);
        };
        reader.readAsDataURL(file);
    });

    $(document).on("click", ".gift-variant-img-remove", function (e) {
        e.preventDefault();
        const previewId = $(this).data("preview");
        const $preview = $(`#${previewId}`);
        $preview.html(`<div class="text-center">
            <i class="display-4 text-muted mdi mdi-cloud-upload"></i>
            <span class="d-block">Upload Variant Image</span>
        </div>`);
        // Clear the file input
        $preview.closest(".row").find(".gift-variant-image-input").val("");
    });

    // ── ADD product image preview ──
    const addProdPreviewBackup = $("#add_preview_container").html();
    $("#add_product_image").on("change", function () {
        const file = this.files[0];
        if (!file || !file.type.match("image.*")) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = $("<img>").attr("src", e.target.result)
                .css({ width: "80px", height: "80px", "object-fit": "cover", "border-radius": "5px" });
            const removeBtn = $("<button>").addClass("btn btn-danger btn-sm d-block mt-1 add-prod-img-remove")
                .text("Remove");
            $("#add_preview_container").empty().append(img).append(removeBtn);
        };
        reader.readAsDataURL(file);
    });
    $(document).on("click", ".add-prod-img-remove", function (e) {
        e.preventDefault();
        $("#add_preview_container").html(addProdPreviewBackup);
        $("#add_product_image").val("");
    });

    // ── EDIT product image preview ──
    $("#edit_product_image").on("change", function () {
        const file = this.files[0];
        if (!file || !file.type.match("image.*")) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            $(".edit_preview-container").html(
                `<img src="${e.target.result}" style="width:80px;height:80px;object-fit:cover;border-radius:5px;">`
            );
        };
        reader.readAsDataURL(file);
    });

 

    // ── ADD default variant image preview (first variant in modal) ──
    $("#add_varient_image").on("change", function () {
        const file = this.files[0];
        if (!file || !file.type.match("image.*")) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = $("<img>").attr("src", e.target.result)
                .css({ width: "80px", height: "80px", "object-fit": "cover", "border-radius": "5px" });
            $("#add_varient_preview").empty().append(img);
        };
        reader.readAsDataURL(file);
    });

    // ── Reset add modal on close ──
    $("#addProductModal").on("hidden.bs.modal", function () {
        $("#addProductForm")[0].reset();
        $("#add_preview_container").html(addProdPreviewBackup);
        $("#gift-variants-wrapper").empty();
        variantIndex = 0;
        $("#add_subcategory_select").html('<option value="" disabled selected>Select Sub Category</option>');
        if (window.allowedCountryIds && window.allowedCountryIds.length > 1) {
            if ($.fn.select2) {
                $("#add_country_select").val(null).trigger("change");
            }
        }
    });

// ── Edit button click ──
$(document).on("click", ".edit_btn", function () {
    const $btn = $(this);
    const countryId     = $btn.attr("data-countryid");
    const categoryId    = $btn.attr("data-categoryid");
    const subcategoryId = $btn.attr("data-subcategoryid");
    const imagePath     = $btn.attr("data-productimage");

    $("#edit_product_id").val($btn.attr("data-productid"));

    $("#edit_product_name").val($btn.attr("data-productname"));
    $("#edit_product_description").val($btn.attr("data-productdescription"));
    $("#edit_mrp_price").val($btn.attr("data-mrpprice"));
    $("#edit_offer_price").val($btn.attr("data-offerprice"));
    $("#edit_low_stock").val($btn.attr("data-lowstock"));
   
   

    const m = document.getElementById('editProductModal');
console.log('display:', m.style.display);
console.log('computed display:', getComputedStyle(m).display);
console.log('computed visibility:', getComputedStyle(m).visibility);
console.log('computed opacity:', getComputedStyle(m).opacity);

  

    $(".edit_preview_image").attr("src", `/images/${imagePath}`);
    const varientImg = $btn.attr("data-varientimage");

    const countryIds = countryId ? countryId.toString().split(',').map(s => s.trim()) : [];
    if ($.fn.select2) {
        $("#edit_country_select").val(countryIds).trigger("change.select2");
    } else {
        $("#edit_country_select").val(countryIds).trigger("change");
    }
    loadCategoriesByCountry(countryIds, 'edit_category_select');

    setTimeout(() => {
        $("#edit_category_select").val(categoryId);
        populateSubcategories(categoryId, 'edit_subcategory_select', subcategoryId);

        const modal = document.getElementById('editProductModal');

        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('role', 'dialog');
        modal.removeAttribute('aria-hidden');
        modal.classList.add('show');

        modal.style.setProperty('display', 'block', 'important');

        // Add backdrop
        if (!document.getElementById('editModalBackdrop')) {
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop show';
            backdrop.id = 'editModalBackdrop';
            document.body.appendChild(backdrop);
        }

        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
        modal.scrollTop = 0;

    }, 300);
});

// ── Close edit modal ──
$(document).on("click", "#editProductModal .btn-close, #editProductModal [data-bs-dismiss='modal']", function () {
    closeEditModal();
});
$(document).on("click", "#editProductModal", function (e) {
    if ($(e.target).attr("id") === "editProductModal") closeEditModal();
});

$("#editProductModal").on("hidden.bs.modal", function () {
    $("#editProductForm")[0].reset();
    $(".edit_preview-container").html('<i class="mdi mdi-cloud-upload text-muted" style="font-size:24px;"></i>');

});

    // ── Delete button ──
    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-productid");
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: giftUrls.destroy + "/" + id,
                    method: "post",
                    dataType: "json",
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        gridjsReRender(response.products);
                        Swal.fire("Deleted!", "Record Deleted Successfully.", "success");
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
                    },
                });
            }
        });
    });


});

// ─── Validators ───────────────────────────────────────────────────────────────

addValidator
    .addField("#add_country_select", [{ rule: "required", errorMessage: "*Country is required" }])
    .addField("#add_category_select", [{ rule: "required", errorMessage: "*Category selection is required" }])
    .addField("#add_product_name", [{ rule: "required", errorMessage: "*Product Name is required" }])
    .addField("#add_product_image", [{ rule: "minFilesCount", value: 1, errorMessage: "*Upload Product Image" }])
    .addField("#add_mrp_price", [
        { rule: "required", errorMessage: "*Price is required" },
        { rule: "number", errorMessage: "*Price must be a number" },
    ])
    .addField("#add_offer_price", [
        { rule: "required", errorMessage: "*Selling Price is required" },
        { rule: "number", errorMessage: "*Selling Price must be a number" },
    ])
    .addField("#add_low_stock", [
        { rule: "required", errorMessage: "*Low Stock is required" },
        { rule: "number", errorMessage: "*Low Stock must be a number" },
    ])
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", true).html("Uploading...");
        addProductFormSubmit(event);
    });

editValidator
    .addField("#edit_category_select", [{ rule: "required", errorMessage: "*Category selection is required" }])
    .addField("#edit_product_name", [{ rule: "required", errorMessage: "*Product Name is required" }])
   
    .onSuccess((event) => {
        $(".edit_submit_btn").attr("disabled", true).html("Updating...");
        editProductFormSubmit(event);
    });

// ─── Grid.js Row Builder ──────────────────────────────────────────────────────

function buildRow(prod, index) {
    const variant = (prod.variants && prod.variants.length > 0) ? prod.variants[0] : {};

    return [
        index + 1,
        resolveCountryName(prod.country_id),
        prod.cate_name || '-',
        prod.subcate_name || 'N/A',
        prod.product_name,
        prod.product_mrp_price,
        prod.product_regular_price,
        gridjs.html(`<img src="/images/${prod.product_image}" style="width:50px;height:50px;object-fit:cover;border-radius:5px;" onerror="this.style.display='none'"/>`),
        gridjs.html(`
            <div class="d-flex gap-2 justify-content-center">
                <button 
                    data-productid="${prod.id}"
                    data-countryid="${prod.country_id || ''}"
                    data-categoryid="${prod.category_id}"
                    data-subcategoryid="${prod.subcategory_id || ''}"
                    data-productname="${prod.product_name}"
                    data-productdescription="${(prod.product_description || '').replace(/"/g, '&quot;')}"
                    data-mrpprice="${prod.product_mrp_price}"
                    data-offerprice="${prod.product_regular_price}"
                    data-lowstock="${prod.low_stock || 5}"
                    data-productimage="${prod.product_image || ''}"
                    data-variantid="${variant.id || ''}"
                    data-varientimage="${variant.varient_img || ''}"
                    data-sku="${variant.sku || ''}"
                    data-barcode="${variant.barcode || ''}"
                    data-varientname="${(variant.varient_name || '').replace(/"/g, '&quot;')}"
                    data-gst="${variant.product_gst ?? ''}"
                    data-hotdeals="${variant.hot_deals || 0}"
                    data-popularprod="${variant.Popular_products || 0}"
                    data-flashsale="${variant.flash_sale || 0}"
                    data-flashsaledate="${variant.flash_sale_date || ''}"
                    data-sizeid="${variant.size_id || ''}"
                    data-colorid="${variant.color_id || ''}"
                   
                    class="btn btn-secondary btn-sm edit_btn">Edit</button>
                <button class="btn btn-danger btn-sm delete_btn"
                    data-productid="${prod.id}">Delete</button>
            </div>
        `),
    ];
}

// ─── Grid.js Init ─────────────────────────────────────────────────────────────

const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Country",
        "Category",
        "Sub Category",
        "Product Name",
        "MRP Price",
        "Selling Price",
        "Image",
        { name: "Action", sort: false },
    ],
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: (window.products || []).map((prod, index) => buildRow(prod, index)),
    style: {
        table: { border: "1px solid #ccc" },
        th: { "background-color": "rgba(0, 0, 0, 0.1)", color: "#000", "border-bottom": "3px solid #ccc", "text-align": "center", "border-right": "0.5px solid #ccc" },
        td: { "text-align": "center", "border-right": "0.5px solid #ccc", "border-bottom": "0.5px solid #ccc" },
    },
});

gridNew.render(document.getElementById("table-gridjs"));

function gridjsReRender(products) {
    gridNew
        .updateConfig({
            data: (products || []).map((prod, index) => buildRow(prod, index)),
        })
        .forceRender();
}

// ─── AJAX: Add Product ────────────────────────────────────────────────────────

function addProductFormSubmit(e) {
    const formdata = new FormData(e.target);

    // Append multi-country values correctly for select2
    if ($.fn.select2) {
        const countryVals = $("#add_country_select").val();
        formdata.delete('country_id');
        formdata.delete('country_id[]');
        if (countryVals && countryVals.length) {
            countryVals.forEach(v => formdata.append('country_id[]', v));
        }
    }

    $.ajax({
        url: giftUrls.store,
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            $("#addProductModal").modal('hide');
            // $(".modal-backdrop").remove();
            gridjsReRender(response.products);
            Swal.fire("Added", "Gift Product Added Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            let message = errorThrown;
            if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                message = Object.values(jqXHR.responseJSON.errors).flat().join("<br>");
            } else if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                message = jqXHR.responseJSON.message;
            }
            Swal.fire({ icon: "error", title: textStatus.toUpperCase(), html: message });
        },
    });
}

// ─── AJAX: Edit Product ───────────────────────────────────────────────────────

function editProductFormSubmit(e) {
    const formdata = new FormData(e.target);

    // Append multi-country values for select2
    if ($.fn.select2) {
        const countryVals = $("#edit_country_select").val();
        formdata.delete('country_id');
        formdata.delete('country_id[]');
        if (countryVals && countryVals.length) {
            countryVals.forEach(v => formdata.append('country_id[]', v));
        }
    }

    $.ajax({
        url: giftUrls.update + "/" + $("#edit_product_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
success: function (response) {
    $(".edit_submit_btn").removeAttr("disabled").html("Update");
    closeEditModal();
    gridjsReRender(response.products);
    Swal.fire("Updated", "Gift Product Updated Successfully.", "success");
},
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            let message = errorThrown;
            if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                message = Object.values(jqXHR.responseJSON.errors).flat().join("<br>");
            } else if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                message = jqXHR.responseJSON.message;
            }
            Swal.fire({ icon: "error", title: textStatus.toUpperCase(), html: message });
        },
    });
}

function openEditModal() {
    const $modal = $("#editProductModal");
    const $backdrop = $("<div>").addClass("modal-backdrop fade show").attr("id", "editModalBackdrop");

    $("body").append($backdrop).addClass("modal-open");
    $modal.css({ display: "block", "z-index": "1055" }).addClass("show").removeAttr("aria-hidden").attr("aria-modal", "true").attr("role", "dialog");

    // Trigger Bootstrap's shown event for any listeners
    $modal[0].dispatchEvent(new Event('shown.bs.modal'));
}

function closeEditModal() {
    const $modal = $("#editProductModal");
    $modal
        .removeClass("show")
        .removeAttr("aria-modal")
        .removeAttr("role")
        .attr("aria-hidden", "true")
        .css({ display: "", "z-index": "" });

    $("#editModalBackdrop").remove();
    $("body").removeClass("modal-open").css("overflow", "");

    $("#editProductForm")[0].reset();
    $(".edit_preview-container").html('<i class="mdi mdi-cloud-upload text-muted" style="font-size:24px;"></i>');

}