// ============================================================
// DROPDOWN LOADERS
// ============================================================

function loadWarehousesByCountry(countryId, warehouseSelectId, branchSelectId) {
    const warehouseSelect = document.getElementById(warehouseSelectId);
    const branchSelect = document.getElementById(branchSelectId);
    if (!warehouseSelect || !branchSelect) return;

    warehouseSelect.innerHTML = '<option value="" disabled selected>Select Warehouse</option>';
    branchSelect.innerHTML = '<option value="" disabled selected>Select Store</option>';

    if (!countryId || !window.allWarehouses) return;

    window.allWarehouses
        .filter(w => w.country_id == countryId)
        .forEach(w => {
            warehouseSelect.innerHTML += `<option value="${w.id}">${w.warehouse_name}</option>`;
        });
}

function loadBranchesByWarehouse(warehouseId, branchSelectId) {
    const branchSelect = document.getElementById(branchSelectId);
    if (!branchSelect) return;

    branchSelect.innerHTML = '<option value="" disabled selected>Select Store</option>';

    if (!warehouseId || !window.allBranches) return;

    window.allBranches
        .filter(b => b.warehouse_id == warehouseId)
        .forEach(b => {
            branchSelect.innerHTML += `<option value="${b.id}">${b.branch_name}</option>`;
        });
}

function loadBrandsByCountry(countryId, brandSelectId, selectedId = null) {
    const select = document.getElementById(brandSelectId);
    if (!select) return;

    let options = '<option value="" disabled selected>Select Brand</option>';

    if (!countryId || !window.allBrands) {
        select.innerHTML = options;
        return;
    }

    window.allBrands
        .filter(b => b.country_id == countryId)
        .forEach(b => {
            const isSelected = selectedId && b.id == selectedId ? 'selected' : '';
            options += `<option value="${b.id}" ${isSelected}>${b.brand_name}</option>`;
        });

    select.innerHTML = options;
}

function loadCategoriesByCountry(countryId, categorySelectId) {
    const select = document.getElementById(categorySelectId);
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected>Select Category</option>';

    if (!countryId || !window.allCategories) return;

    window.allCategories
        .filter(cat => {
            const ids = cat.country_id
                ? cat.country_id.toString().split(',').map(id => parseInt(id.trim()))
                : [];
            return ids.includes(parseInt(countryId));
        })
        .forEach(cat => {
            select.innerHTML += `<option value="${cat.id}">${cat.category_name}</option>`;
        });
}

function populateSubCategories(select, categoryId, selectedId = null) {
    select.empty();
    select.append('<option value="" disabled selected>Select Sub Category</option>');
    if (window.subcategories && categoryId) {
        const filtered = window.subcategories.filter(item => item.category_name == categoryId);
        filtered.forEach(item => {
            const isSelected = selectedId && item.id == selectedId ? 'selected' : '';
            select.append(`<option value="${item.id}" ${isSelected}>${item.subcategory_name}</option>`);
        });
    }
}

// ============================================================
// VARIANT PAGE — STOCK ENTRY +/- CLONE
// ============================================================

function getVarStockEntryHtml(countryId) {
    const warehouseOpts = (window.allWarehouses || [])
        .filter(w => w.country_id == countryId)
        .map(w => `<option value="${w.id}">${w.warehouse_name}</option>`)
        .join('');

    return `
    <div class="stock-entry d-flex align-items-end gap-2 mb-5">
        <div style="flex:1">
            <label>Warehouse</label>
            <select class="form-select variant-warehouse-select" name="warehouse_id[]">
                <option value="" disabled selected>Select Warehouse</option>
                ${warehouseOpts}
            </select>
        </div>

        <div style="flex:1">
            <label>Store</label>
            <select class="form-select variant-branch-select" name="branch_id[]">
                <option value="" disabled selected>Select Store</option>
            </select>
        </div>

        <div style="flex:1">
            <label>Qty</label>
            <input type="number" name="product_quantity[]" class="form-control" required>
        </div>

        <div style="flex:1">
            <label>Batch No</label>
            <input type="text" name="batch_no[]" class="form-control">
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
    </div>`;
}

function updateVarStockButtons($list) {
    const $entries = $list.find(".stock-entry");
    $entries.each(function () {
        $(this).find(".remove-var-stock-entry").toggle($entries.length > 1);
    });
}

function updateVarStockCount($wrapper) {
    $wrapper.find(".stock-entry-count").val($wrapper.find(".stock-entry").length);
}

// Add stock row
$(document).on("click", ".add-var-stock-entry", function () {
    const countryId = $("#add_country_select").val();
    const $wrapper = $(this).closest(".stock-entries-wrapper");
    const $list = $wrapper.find(".stock-entry-list");
    $list.append(getVarStockEntryHtml(countryId));
    updateVarStockButtons($list);
    updateVarStockCount($wrapper);
});

// Remove stock row
$(document).on("click", ".remove-var-stock-entry", function () {
    const $wrapper = $(this).closest(".stock-entries-wrapper");
    const $list = $wrapper.find(".stock-entry-list");
    $(this).closest(".stock-entry").remove();
    updateVarStockButtons($list);
    updateVarStockCount($wrapper);
});

// Warehouse → Branch cascade
$(document).on("change", ".variant-warehouse-select", function () {
    const warehouseId = $(this).val();
    const $branchSelect = $(this).closest(".stock-entry").find(".variant-branch-select");
    $branchSelect.html('<option value="" disabled selected>Select Store</option>');
    if (!warehouseId) return;
    (window.allBranches || []).filter(b => b.warehouse_id == warehouseId).forEach(b => {
        $branchSelect.append(`<option value="${b.id}">${b.branch_name}</option>`);
    });
});

// When country changes in add modal — repopulate warehouses in all stock rows
$(document).on("change", "#add_country_select", function () {
    const countryId = $(this).val();
    const warehouseOpts = '<option value="" disabled selected>Select Warehouse</option>' +
        (window.allWarehouses || [])
            .filter(w => w.country_id == countryId)
            .map(w => `<option value="${w.id}">${w.warehouse_name}</option>`)
            .join('');

    $(".stock-entries-wrapper .variant-warehouse-select").each(function () {
        $(this).html(warehouseOpts);
        $(this).closest(".stock-entry").find(".variant-branch-select")
            .html('<option value="" disabled selected>Select Store</option>');
    });
});

// ============================================================
// GRID.JS TABLE
// ============================================================

const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "category",
        "Country",
        "Product Name",
        "Product MRP Price",
        "Product Offer Price",
        // "Product Qty",
        { name: "Action", sort: false },
    ],
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: productvarient.map((productvarient, index) => buildRow(productvarient, index)),
    style: {
        table: { border: "1px solid #ccc" },
        th: {
            "background-color": "rgba(0, 0, 0, 0.1)",
            color: "#000",
            "border-bottom": "3px solid #ccc",
            "text-align": "center",
            "border-right": "0.5px solid #ccc",
        },
        td: {
            "text-align": "center",
            "border-right": "0.5px solid #ccc",
            "border-bottom": "0.5px solid #ccc",
        },
    },
});

gridNew.render(document.getElementById("table-gridjs"));

function buildProductNameHtml(pv) {
    if (!pv.value || pv.value === "null" || pv.value === "") {
        return `<span>${pv.product_name}</span><p>-</p>`;
    }
    if (pv.unit_short_name) {
        return `<span>${pv.product_name}</span><p>${pv.value} ${pv.unit_short_name}</p>`;
    }
    const uid = pv.unit_id || pv.varient;
    const unitMap = { 1: 'l', 2: 'ml', 3: 'g', 4: 'kg' };
    const unit = unitMap[uid] || "No's";
    return `<span>${pv.product_name}</span><p>${pv.value} ${unit}</p>`;
}

function buildActionHtml(pv) {
    return `<div>
        <button data-bs-toggle="modal"
            data-categoryid="${pv.categoryid}"
            data-countryid="${pv.country_id}"
            data-subcategoryid="${pv.subcategoryid}"
            data-produid="${pv.product_id}"
            data-brandid="${pv.brand_id}"
            data-productverid="${pv.id}"
            data-productname="${pv.product_name}"
            data-productunit="${pv.varient}"
            data-productvarvalue="${pv.value}"
            data-productvarimrpprice="${pv.mrp_price}"
            data-productvarioffer="${pv.offer_price}"
            data-productvarqut="${pv.product_qty}"
            data-hotpro="${pv.hot_deals}"
            data-varientimage="${pv.varient_img}"
            data-bespro="${pv.Popular_products}"
            data-preord="${pv.pre_order}"
            data-prenote="${pv.pre_note}"
            data-flashsale="${pv.flash_sale}"
            data-flashsaledate="${pv.flash_sale_date}"
            data-productget="${pv.product_gst}"
            data-produlowsto="${pv.low_stock}"
            data-sku="${pv.sku || ''}"
            data-barcode="${pv.barcode || ''}"
            data-itemcode="${pv.item_code || ''}"
            data-unitid="${pv.unit_id || pv.varient}"
            data-bs-target="#editProductModal"
            class="btn btn-secondary edit_btn">Edit</button>
        <button class="btn btn-danger mt-1 delete_btn" data-productverid="${pv.id}">Delete</button>
    </div>`;
}

function buildRow(pv, index) {
    return [
        index + 1,
        pv.category_name,
        pv.country_name || '-',
        gridjs.html(buildProductNameHtml(pv)),
        pv.mrp_price,
        pv.offer_price,
        // pv.product_qty,
        gridjs.html(buildActionHtml(pv)),
    ];
}

function gridjsReRender(data) {
    gridNew.updateConfig({
        data: data.map((pv, index) => buildRow(pv, index)),
    }).forceRender();
}

$(document).on("change", "#add_country_select", function () {
    const selectedIds = $(this).val(); // now array
    const countryId = selectedIds && selectedIds.length > 0 ? selectedIds[0] : null;

    loadWarehousesByCountry(countryId, 'add_var_warehouse_select', 'add_var_branch_select');
    loadCategoriesByCountry(countryId, 'select_category_select');
    loadBrandsByCountry(countryId, 'add_brand_select');

    $("#select_subcategory_select").html('<option value="" disabled selected>Select Sub Category</option>');
    $("#add_product_name").html('<option value="" disabled selected>Select Product</option>');
});

$(document).on("change", "#add_var_warehouse_select", function () {
    loadBranchesByWarehouse($(this).val(), 'add_var_branch_select');
});

// ============================================================
// ADD MODAL — Category → Subcategory + Product cascade
// ============================================================

$(document).on("change", "#select_category_select", function () {
    const id = $(this).val();
    populateSubCategories($("#select_subcategory_select"), id);

    $(".proname").empty().append('<option value="" disabled selected>Processing...</option>');

    $.ajax({
        type: "GET",
        url: "Getproduct/" + id,
        dataType: "JSON",
        success: function (response) {
            $(".proname").empty().append('<option value="" disabled selected>Select Product</option>');
            response.forEach(el => {
                $(".proname").append(`<option value="${el.id}">${el.product_name}</option>`);
            });
        },
    });
});

// ADD MODAL — Subcategory → Product cascade
$("#select_subcategory_select").on("change", function () {
    const id = $(this).val();
    $(".proname").empty().append('<option value="" disabled selected>Processing...</option>');

    $.ajax({
        type: "GET",
        url: "Getsubcategory/" + id,
        dataType: "JSON",
        success: function (response) {
            $(".proname").empty().append('<option value="" disabled selected>Select Product</option>');
            response.forEach(el => {
                $(".proname").append(`<option value="${el.id}">${el.product_name}</option>`);
            });
        },
    });
});

// Replace existing handler
$(document).on("change", "#edit_country_select", function (e) {
    
    if (e.originalEvent === undefined) return; // triggered programmatically — skip

    const selectedIds = $(this).val();
    const countryId = selectedIds && selectedIds.length > 0 ? selectedIds[0] : null;

    loadWarehousesByCountry(countryId, 'edit_var_warehouse_select', 'edit_var_branch_select');
    loadCategoriesByCountry(countryId, 'edit_category_select');
    loadBrandsByCountry(countryId, 'edit_brand_select');

    $("#edit_subcategory_select").html('<option value="" disabled selected>Select Sub Category</option>');
    $("#edit_product_name").html('<option value="" disabled selected>Select Product</option>');
});

$(document).on("change", ".edit-warehouse-select", function () {
    const warehouseId = $(this).val();
    const $branchSelect = $(this).closest(".edit-stock-entry").find(".edit-branch-select");

    $branchSelect.html('<option value="" disabled selected>Select Store</option>');

    if (!warehouseId) return;

    (window.allBranches || [])
        .filter(b => b.warehouse_id == warehouseId)
        .forEach(b => {
            $branchSelect.append(`<option value="${b.id}">${b.branch_name}</option>`);
        });
});

// ============================================================
// EDIT MODAL — Category → Subcategory + Product cascade
// ============================================================

$(document).on("change", "#edit_category_select", function () {
    const id = $(this).val();
    populateSubCategories($("#edit_subcategory_select"), id);

    $("#edit_product_name").empty().append('<option value="" disabled selected>Processing...</option>');

    $.ajax({
        type: "GET",
        url: "Getproduct/" + id,
        dataType: "JSON",
        success: function (response) {
            $("#edit_product_name").empty().append('<option value="" disabled selected>Select Product</option>');
            response.forEach(el => {
                $("#edit_product_name").append(`<option value="${el.id}">${el.product_name}</option>`);
            });
        },
    });
});

// EDIT MODAL — Subcategory → Product cascade
$("#edit_subcategory_select").on("change", function () {
    const id = $(this).val();
    $("#edit_product_name").empty().append('<option value="" disabled selected>Processing...</option>');

    $.ajax({
        type: "GET",
        url: "Getsubcategory/" + id,
        dataType: "JSON",
        success: function (response) {
            $("#edit_product_name").empty().append('<option value="" disabled selected>Select Product</option>');
            response.forEach(el => {
                $("#edit_product_name").append(`<option value="${el.id}">${el.product_name}</option>`);
            });
        },
    });
});

$(document).on("click", ".edit_btn", function () {
    const $btn = $(this);

    let catId     = $btn.attr("data-categoryid");
    let countryId = $btn.attr("data-countryid");
    let subCatId  = $btn.attr("data-subcategoryid");
    const ProId   = $btn.attr("data-produid");
    const brandId = $btn.data("brandid");
    const imagePath = $btn.attr("data-varientimage");

    // Sanitize nulls
    if (!subCatId || subCatId === "null" || subCatId === "undefined") subCatId = null;
    if (!catId    || catId    === "null" || catId    === "undefined") catId = null;

    // --- Step 1: Set Country (Select2) WITHOUT triggering change handler ---
    const countryArray = countryId
        ? countryId.toString().split(',').map(s => s.trim())
        : [];

    // Use setTimeout to ensure Select2 is initialized after modal shown
    setTimeout(() => {
        $("#edit_country_select").val(countryArray).trigger("change.select2");
        // DO NOT trigger regular "change" — that would reset category/brand/product
    }, 250);

    // --- Step 2: Load categories and brands manually (bypass change handler) ---
    const firstCountryId = countryArray.length > 0 ? countryArray[0] : null;

    loadCategoriesByCountry(firstCountryId, 'edit_category_select');
    loadBrandsByCountry(firstCountryId, 'edit_brand_select', brandId);

    // Set category after loadCategoriesByCountry populates the options
    setTimeout(() => {
        $("#edit_category_select").val(catId);
        populateSubCategories($("#edit_subcategory_select"), catId, subCatId);
        if (subCatId) {
            $("#edit_subcategory_select").val(subCatId);
        }
    }, 100);

    // --- Step 3: Load Products ---
    const productUrl = subCatId
        ? "Getsubcategory/" + subCatId
        : "Getproduct/" + catId;

    if (productUrl && (subCatId || catId)) {
        $.ajax({
            type: "GET",
            url: productUrl,
            dataType: "JSON",
            success: function (response) {
                $("#edit_product_name")
                    .empty()
                    .append('<option value="" disabled selected>Select Product</option>');
                response.forEach(function (product) {
                    $("#edit_product_name").append(
                        `<option value="${product.id}">${product.product_name}</option>`
                    );
                });
                $("#edit_product_name").val(ProId);
            },
        });
    }

    // --- Step 4: Fix image preview ---
    const cleanImagePath = imagePath ? imagePath.replace(/^\/+/, '') : '';
    $(".edit_preview_image").first().attr("src", `/images/${cleanImagePath}`);

    // --- Step 5: Fill scalar fields ---
    const proVerId           = $btn.attr("data-productverid");
    const hotProValue        = $btn.attr("data-hotpro");
    const besProValue        = $btn.attr("data-bespro");
    const preordValue        = $btn.attr("data-preord");
    const flashSaleValue     = $btn.attr("data-flashsale");
    const flashSaleDateValue = $btn.attr("data-flashsaledate");

    $("#edit_productvar_id").val(proVerId);
    $("#edit_product_value").val($btn.attr("data-productvarvalue"));
    $("#edit_product_mrp_price").val($btn.attr("data-productvarimrpprice"));
    $("#edit_product_offer_price").val($btn.attr("data-productvarioffer"));
    $("#edit_Low_Stock").val($btn.attr("data-produlowsto"));
    $("#edit_unit_select").val($btn.attr("data-unitid"));
    $("#edit_product_gst").val($btn.attr("data-productget"));
    $("#edit_pre_note").val($btn.attr("data-prenote"));
    $("#edit_sku").val($btn.attr("data-sku"));
    $("#edit_barcode").val($btn.attr("data-barcode"));
    $("#edit_item_code").val($btn.attr("data-itemcode"));

    // --- Step 6: Checkboxes ---
    $("#edit_hot_product").prop("checked", hotProValue === "1").val(hotProValue);
    $("#edit_bes_product").prop("checked", besProValue === "1").val(besProValue);
    $("#edit_pre_product").prop("checked", preordValue === "1").val(preordValue);
    $("#edit_flash_product").prop("checked", flashSaleValue === "1").val(flashSaleValue);

    if (preordValue === "1") {
        $('#editpreorderNoteDiv').show();
    } else {
        $('#editpreorderNoteDiv').hide();
        $('#edit_pre_note').val("");
    }

    if (flashSaleValue === "1") {
        $('#editFlashSaleDateDiv').show();
        if (flashSaleDateValue && flashSaleDateValue !== "null") {
            $('#edit_flash_sale_date').val(flashSaleDateValue);
        }
    } else {
        $('#editFlashSaleDateDiv').hide();
        $('#edit_flash_sale_date').val("");
    }

    // --- Step 7: Load thumbnail images ---
    $.ajax({
        url: "/getthump/" + proVerId,
        method: "GET",
        dataType: "json",
        success: function (response) {
            let htmlContent = "";
            $.each(response, function (index, item) {
                htmlContent += `
                <div class="d-flex product_fieldsedit" id="thumb_existing_${item.id}">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="mb-3">
                                <label class="form-label">Existing Image</label>
                                <img src="/images/${item.product_child_image}"
                                    alt="thumb image" class="img-fluid" style="max-height: 100px;">
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-12 mt-4">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger delete-inputdeit"
                                    data-varient-id="${item.id}">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
            $("#dynamic-inputsedit").html(htmlContent);
        },
    });
});



const addValidator = new JustValidate("#editProductVarientForm", {
    validateBeforeSubmitting: true,
});

const addValidator1 = new JustValidate("#addProductvarientForm", {
    validateBeforeSubmitting: true,
});

const imageValidationMap = {};

$(document).on("change", ".thumbim", function () {
    const fileInput = this;
    const file = fileInput.files[0];
    const fieldId = fileInput.name + "_" + Math.random().toString(36).substring(2, 9);
    $(fileInput).attr("data-image-id", fieldId);

    if (!file) {
        imageValidationMap[fieldId] = true;
        return;
    }

    if (!file.type.match(/^image\/(jpeg|png|gif|bmp|webp)$/)) {
        imageValidationMap[fieldId] = false;
        fileInput.value = "";
        Swal.fire({ icon: 'error', title: 'Invalid File Type', text: 'Only image files (JPG, PNG, GIF, BMP, WEBP) are allowed!' });
        return;
    }

    const img = new Image();
    img.src = URL.createObjectURL(file);
    img.onload = function () {
        if (img.width === 600 && img.height === 600) {
            imageValidationMap[fieldId] = true;
        } else {
            imageValidationMap[fieldId] = false;
            fileInput.value = "";
            Swal.fire({ icon: 'error', title: 'Invalid Image Size', text: 'Only 600x600 px image is allowed!' });
        }
    };
});

$("#add_varient_image").on("change", function () {
    const file = this.files[0];
    if (!file) return;

    if (!file.type.match(/^image\/(jpeg|png|gif|bmp|webp)$/)) {
        this.value = "";
        $(".product_remove_btn").trigger("click");
        addValidator1.showErrors({ "#add_varient_image": "*Only image files (JPG, PNG, etc.) are allowed" });
        return;
    }

    if (file.type.startsWith("image/")) {
        let img = new Image();
        img.src = window.URL.createObjectURL(file);
        img.decode().then(() => {
            if (!(img.width === 600 && img.height === 600)) {
                $(".product_remove_btn").trigger("click");
                addValidator1.showErrors({ "#add_varient_image": "*Variant Image resolution should be 600 × 600" });
            }
        });
    }
});

$("#edit_varient_image").on("change", function () {
    const file = this.files[0];
    if (!file) return;

    if (!file.type.match(/^image\/(jpeg|png|gif|bmp|webp)$/)) {
        this.value = "";
        $(".edit_product_remove_btn").trigger("click");
        addValidator.showErrors({ "#edit_varient_image": "*Only image files (JPG, PNG, etc.) are allowed" });
        return;
    }

    if (file.type.startsWith("image/")) {
        let img = new Image();
        img.src = window.URL.createObjectURL(file);
        img.decode().then(() => {
            if (!(img.width === 600 && img.height === 600)) {
                $(".edit_product_remove_btn").trigger("click");
                addValidator.showErrors({ "#edit_varient_image": "*Variant Image resolution should be 600 × 600" });
            }
        });
    }
});

// ---- Edit Form Validator ----
addValidator
    .addField("#edit_country_select",  [{ rule: "required", errorMessage: "*Country Field is required" }])
    .addField("#edit_brand_select",    [{ rule: "required", errorMessage: "*Brand is required" }])
    .addField("#edit_category_select", [{ rule: "required", errorMessage: "*Category Field is required" }])
    .addField("#edit_product_name",    [{ rule: "required", errorMessage: "*Product Field is required" }])
    .addField("#edit_unit_select",     [{ rule: "required", errorMessage: "*Product Unit Field is required" }])
    .addField("#edit_product_gst",     [{ rule: "required", errorMessage: "*GST Field is required" }])
    .addField("#edit_varient_image", [{
        validator: (value, fields) => new Promise((resolve) => {
            const file = fields["#edit_varient_image"].elem.files[0];
            if (!file || !file.type.startsWith("image/")) return resolve(false);
            const img = new Image();
            img.onload = () => {
                if (!(img.width === 600 && img.height === 600)) $(".edit_product_remove_btn").trigger("click");
                resolve(img.width === 600 && img.height === 600);
            };
            img.onerror = () => resolve(false);
            img.src = URL.createObjectURL(file);
        }),
        errorMessage: "*Variant Image resolution must be exactly 600 × 600",
    }])
    .addField("#edit_product_value", [
        { rule: "required", errorMessage: "*Product Value Field is required" },
        { rule: "number",   errorMessage: "*Product Value should be numbers" },
        { rule: "minLength", value: 1, errorMessage: "*Product value should be at least 1 character" },
        { rule: "maxLength", value: 3, errorMessage: "*Product value should be at Maximum 3 characters" },
    ])
    .addField("#edit_product_mrp_price", [
        { rule: "required", errorMessage: "*Product MRP Field is required" },
        { rule: "number",   errorMessage: "*Product MRP should be numbers" },
        { rule: "minLength", value: 1, errorMessage: "*Product MRP should be at least 1 character" },
        { rule: "maxLength", value: 5, errorMessage: "*Product MRP should be at Maximum 5 characters" },
    ])
    .addField("#edit_product_offer_price", [
        { rule: "required", errorMessage: "*Product Offer Field is required" },
        { rule: "number",   errorMessage: "*Product Offer Price should be numbers" },
        { rule: "minLength", value: 1, errorMessage: "*Product Offer should be at least 1 character" },
        { rule: "maxLength", value: 5, errorMessage: "*Product Offer should be at Maximum 5 characters" },
        {
            validator: (value) => parseInt(value) <= parseInt($("#edit_product_mrp_price").val()),
            errorMessage: "Should not be above MRP Price",
        },
    ])
    
    .addField("#edit_Low_Stock", [
        { rule: "required", errorMessage: "*Product low Stock Field is required" },
        { rule: "number",   errorMessage: "*Product low Stock should be numbers" },
        { rule: "minLength", value: 1, errorMessage: "*Product low Stock should be at least 1 character" },
        { rule: "maxLength", value: 5, errorMessage: "*Product low Stock should be at Maximum 5 characters" },
    ])
    .onSuccess((event) => {
        $(".editver_submit_btn").attr("disabled", true).html("Uploading.....");
        editProductVarientFormSubmit(event);
    });

// ---- Add Form Validator ----
addValidator1
    .addField("#add_country_select",      [{ rule: "required", errorMessage: "*Country Field is required" }])
    .addField("#add_brand_select",        [{ rule: "required", errorMessage: "*Brand is required" }])
    .addField("#select_category_select",  [{ rule: "required", errorMessage: "*Category Field is required" }])
    .addField("#produgst",                [{ rule: "required", errorMessage: "*GST Field is required" }])
    .addField("#add_varient_image", [
        { rule: "minFilesCount", value: 1, errorMessage: "*Variant Image Upload Field is required" },
        {
            validator: (value, fields) => new Promise((resolve) => {
                const file = fields["#add_varient_image"].elem.files[0];
                if (!file || !file.type.startsWith("image/")) return resolve(false);
                const img = new Image();
                img.onload = () => {
                    if (!(img.width === 600 && img.height === 600)) $(".product_remove_btn").trigger("click");
                    resolve(img.width === 600 && img.height === 600);
                };
                img.onerror = () => resolve(false);
                img.src = URL.createObjectURL(file);
            }),
            errorMessage: "*Variant Image resolution must be exactly 600 × 600",
        },
    ])
    .addField('#add_varient_name', [
        { rule: 'minLength', value: 3, errorMessage: 'Variant name must be at least 3 characters' },
        { rule: 'maxLength', value: 20, errorMessage: 'Variant name must be less than 20 characters' },
    ])
    .addField('.thumbim', [{
        validator: (value, fields) => {
            for (let i = 0; i < fields.length; i++) {
                const input = fields[i].elem;
                const id = $(input).attr('data-image-id');
                if (input.files.length > 0 && imageValidationMap[id] !== true) return false;
            }
            return true;
        },
        errorMessage: '*Only 600x600 px image is allowed',
    }])
    .addField("#add_product_name", [{ rule: "required", errorMessage: "*Product Field is required" }])
    .addField("#add_unit_select",  [{ rule: "required", errorMessage: "*Product Unit Field is required" }])
    .addField("#add_product_value", [
        { rule: "required", errorMessage: "*Product Value Field is required" },
        { rule: "number",   errorMessage: "*Product Value should be numbers" },
        { rule: "minLength", value: 1, errorMessage: "*Product value should be at least 1 character" },
        { rule: "maxLength", value: 3, errorMessage: "*Product value should be at Maximum 3 characters" },
    ])
    .addField("#add_product_mrp_price", [
        { rule: "required", errorMessage: "*Product MRP Field is required" },
        { rule: "number",   errorMessage: "*Product MRP should be numbers" },
        { rule: "minLength", value: 1, errorMessage: "*Product MRP should be at least 1 character" },
        { rule: "maxLength", value: 4, errorMessage: "*Product MRP should be at Maximum 4 characters" },
    ])
    .addField("#add_product_offer_price", [
        { rule: "required", errorMessage: "*Product Offer Field is required" },
        { rule: "number",   errorMessage: "*Product Offer Price should be numbers" },
        { rule: "minLength", value: 1, errorMessage: "*Product Offer should be at least 1 character" },
        { rule: "maxLength", value: 4, errorMessage: "*Product Offer should be at Maximum 4 characters" },
        {
            validator: (value) => parseInt(value) <= parseInt($("#add_product_mrp_price").val()),
            errorMessage: "Should not be above MRP Price",
        },
    ])
   
    .addField("#add_Low_Stock", [
        { rule: "required", errorMessage: "*Product Low Stock Field is required" },
        { rule: "number",   errorMessage: "*Product Low Stock should be numbers" },
        { rule: "minLength", value: 1, errorMessage: "*Product Low Stock should be at least 1 character" },
        { rule: "maxLength", value: 4, errorMessage: "*Product Low Stock should be at Maximum 4 characters" },
    ])
    .addField("#add_pre_note", [{
        validator: () => $('#preorderCheckbox').is(':checked') ? $('#add_pre_note').val().trim().length > 0 : true,
        errorMessage: "*Pre Order Note is required",
    }])
    .onSuccess((event) => {
        $(".addvari_submit_btn").attr("disabled", true).html("Uploading.....");
        addProductvarientFormSubmit(event);
    });

// ============================================================
// FILTER FORM VALIDATOR
// ============================================================

const addValidator2 = new JustValidate("#productverfilterForm", {
    validateBeforeSubmitting: true,
});
addValidator2
    .addField("#sel_category_select", [{ rule: "required", errorMessage: "*Category is required" }])
    .addField("#select_product",      [{ rule: "required", errorMessage: "*Product is required" }])
    .onSuccess((event) => {
        $(".productver_filter_btn").attr("disabled", true).html("Uploading.....");
        productverfilterFormSubmit(event);
    });

// Filter form — category loads products
$("#sel_category_select").on("change", function () {
    const id = $(this).val();
    $(".proname").empty().append('<option value="" disabled selected>Processing...</option>');
    $.ajax({
        type: "GET",
        url: "Getproduct/" + id,
        dataType: "JSON",
        success: function (response) {
            $(".proname").empty().append('<option value="" disabled selected>Select Product</option>');
            response.forEach(el => {
                $(".proname").append(`<option value="${el.id}">${el.product_name}</option>`);
            });
        },
    });
});

function productverfilterFormSubmit(event) {
    const formData = new FormData(event.target);
    $.ajax({
        url: "getproductverfilter",
        method: "POST",
        dataType: "json",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".productver_filter_btn").removeAttr("disabled").html("Submit");
            gridjsReRender(response.productvarient);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".productver_filter_btn").removeAttr("disabled").html("Submit");
            Swal.fire(textStatus.toUpperCase(), textStatus, "warning");
        },
    });
}

// ============================================================
// ADD FORM SUBMIT
// ============================================================

function addProductvarientFormSubmit(e) {
    e.preventDefault();

    const quantity = parseInt($('#add_product_quantity').val());
    const lowStock = parseInt($('#add_Low_Stock').val());

    $(".addvari_submit_btn").removeAttr("disabled").html("Submit");

    if (!isNaN(quantity) && !isNaN(lowStock) && lowStock >= quantity) {
        Swal.fire({ icon: 'warning', title: 'Invalid Stock Input', text: 'Low Stock must be less than Stock Quantity.' });
        return;
    }

    const formdata = new FormData(e.target);
    $(".addvari_submit_btn").attr("disabled", true).html("Submitting...");

    $.ajax({
        url: "addproductvarient",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".addvari_submit_btn").removeAttr("disabled").html("Submit");
            $("#addProductvarientForm")[0].reset();
            $("#addProductvariModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(response.productvarient);
            Swal.fire("Added", "Records Added Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".editver_submit_btn, .addvari_submit_btn").removeAttr("disabled").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

// ============================================================
// EDIT FORM SUBMIT
// ============================================================

function editProductVarientFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updateProductsvarient/" + $("#edit_productvar_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $("#editProductVarientForm")[0].reset();
            $("#editProductModal").hide();
            $(".modal-backdrop").remove();
            $(".edit_product_remove_btn").trigger("click");
            document.body.style.overflowY = "scroll";
            gridjsReRender(response.productvarient);
            $(".editver_submit_btn").removeAttr("disabled").html("Update");
            Swal.fire("Updated", "Records Updated Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".editver_submit_btn").removeAttr("disabled").html("Update");
            $(".addvari_submit_btn").removeAttr("disabled").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

// ============================================================
// DELETE
// ============================================================

$(document).on("click", ".delete_btn", function () {
    const id = $(this).attr("data-productverid");
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
                url: "destroyvarient/" + id,
                method: "post",
                dataType: "json",
                success: function (response) {
                    gridjsReRender(response.productvarient);
                    Swal.fire("Deleted!", "Records Deleted Successfully.", "success");
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
                },
            });
        }
    });
});

// ============================================================
// CHECKBOX TOGGLE VALUES
// ============================================================

$(".hot_value").on("change", function () {
    $(this).val($(this).prop("checked") ? 1 : 0);
});

// ============================================================
// DYNAMIC THUMBNAIL IMAGE INPUTS (ADD)
// ============================================================

$(document).ready(function () {
    $(".add-input1").click(function () {
        var currentValue = parseInt($(".product_image_count").val());
        $(".product_image_count").val(currentValue + 1);
        var inputGroup = `
        <div class="d-flex product_fields1">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-3">
                        <label class="form-label">Product Image*(600 * 600)</label>
                        <input type="file" class="form-control thumbim image_el needsclick"
                            id="add_thumbproduct_image" placeholder="Product Image" name="product_image1[]" required>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-12 mt-4">
                    <div class="input-group-append">
                        <button class="btn btn-danger delete-input1" type="button">Delete</button>
                    </div>
                </div>
            </div>
        </div>`;
        $(".dynamic-inputs1").append(inputGroup);
    });

    $(document).on("click", ".delete-input1", function () {
        $(this).closest(".product_fields1").remove();
    });
});

// ============================================================
// DYNAMIC THUMBNAIL IMAGE INPUTS (EDIT)
// ============================================================

$('#add-inputedit').click(function () {
    var inputGroup = `
    <div class="d-flex product_fieldsedit">
        <div class="row">
            <div class="col-lg-5">
                <div class="mb-3">
                    <label class="form-label">New Image (600x600)</label>
                    <input type="file" class="form-control image_el new-image-input needsclick" name="product_image1[]">
                </div>
            </div>
            <div class="col-lg-2 col-sm-12 mt-4">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger delete-inputdeit">Delete</button>
                </div>
            </div>
        </div>
    </div>`;
    $('#dynamic-inputsedit').append(inputGroup);
});

$(document).on('change', '.new-image-input', function () {
    const input = this;
    if (input.files && input.files[0]) {
        const img = new Image();
        img.onload = function () {
            if (this.width !== 600 || this.height !== 600) {
                Swal.fire({ icon: 'error', title: 'Invalid Image Size', text: 'Please upload an image of exactly 600 x 600 pixels.' });
                input.value = '';
            }
        };
        img.onerror = function () {
            Swal.fire({ icon: 'error', title: 'Invalid File', text: 'Unable to load image for validation.' });
            input.value = '';
        };
        img.src = URL.createObjectURL(input.files[0]);
    }
});

$(document).on('click', '.delete-inputdeit', function () {
    const id = $(this).data("varient-id");
    const element = $(this).closest('.product_fieldsedit');

    if (id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This image will be deleted permanently.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "destroyVarientThumpImages/" + id,
                    method: "post",
                    dataType: "json",
                    success: function () {
                        $("#thumb_existing_" + id).remove();
                        Swal.fire("Deleted!", "Image deleted successfully.", "success");
                    },
                    error: function () {
                        Swal.fire("Error", "Something went wrong.", "error");
                    }
                });
            }
        });
    } else {
        element.remove();
    }
});

// ============================================================
// IMAGE PREVIEW — ADD MODAL
// ============================================================

$(function () {
    const backupHtml = $("#preview-container1").html();

    $("#add_varient_image").on("change", function () {
        var file = $(this)[0].files[0];
        if (file && file.type.match("image.*")) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = $("<img>").attr("src", e.target.result);
                var removeBtn = $("<button>").addClass("btn btn-danger product_remove_btn mt-2").text("Remove");
                $("#preview-container1").empty().append(img).append(removeBtn);
                removeBtn.on("click", function (e) {
                    e.preventDefault();
                    $("#preview-container1").html(backupHtml);
                    $("#add_varient_image").val("");
                });
            };
            reader.readAsDataURL(file);
        }
    });
});

// ============================================================
// IMAGE PREVIEW — EDIT MODAL
// ============================================================

$(function () {
    const backupHtml = $(".edit_preview-container").html();

    $("#edit_varient_image").on("change", function () {
        var file = $(this)[0].files[0];
        if (file && file.type.match("image.*")) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = $("<img>").attr("src", e.target.result);
                var removeBtn = $("<button>").addClass("btn btn-danger edit_product_remove_btn mt-2").text("Remove");
                $(".edit_preview-container").empty().append(img).append(removeBtn);
                removeBtn.on("click", function (e) {
                    e.preventDefault();
                    $(".edit_preview-container").html(backupHtml);
                    $("#edit_varient_image").val("");
                });
            };
            reader.readAsDataURL(file);
        }
    });
});

$(document).on('click', '.add-edit-stock-entry', function () {
    $('.edit-stock-entry-list').append(generateEditStockRow());
});

$(document).ready(function () {
    function validateStockFields() {
        const qty = parseInt($('#edit_product_quantity').val());
        const lowStock = parseInt($('#edit_Low_Stock').val());
        if (!isNaN(qty) && !isNaN(lowStock) && lowStock >= qty) {
            Swal.fire({ icon: 'warning', title: 'Invalid Low Stock Value', text: 'Low Stock should be less than Stock Quantity.' });
            $('#edit_Low_Stock').val('').focus();
        }
    }
    $('#edit_product_quantity, #edit_Low_Stock').on('blur', validateStockFields);
});