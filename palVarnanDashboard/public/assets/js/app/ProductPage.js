const addValidator = new JustValidate("#addProductForm", {
    validateBeforeSubmitting: true,
});
const editValidator = new JustValidate("#editProductForm", {
    validateBeforeSubmitting: true,
});

// ============================================================
// WAREHOUSE / BRANCH / CATEGORY / BRAND HELPERS
// ============================================================

function buildWarehouseOptions(countryId) {
    if (!countryId) return '';
    return (window.allWarehouses || [])
        .filter(w => w.country_id == countryId)
        .map(w => `<option value="${w.id}">${w.warehouse_name}</option>`)
        .join('');
}

function loadCategoriesByCountry(countryId, categorySelectId) {
    const select = document.getElementById(categorySelectId);
    if (!select) return;
    select.innerHTML = '<option value="" disabled selected>Select Category</option>';
    if (!countryId) return;
    (window.allCategories || []).filter(cat => {
        const ids = cat.country_id ? cat.country_id.toString().split(',').map(id => parseInt(id.trim())) : [];
        return ids.includes(parseInt(countryId));
    }).forEach(cat => {
        select.innerHTML += `<option value="${cat.id}">${cat.category_name}</option>`;
    });
}

function loadBrandsByCountry(countryId, brandSelectId) {
    const select = document.getElementById(brandSelectId);
    if (!select) return;
    select.innerHTML = '<option value="" selected>Select Brand</option>';
    if (!countryId) return;
    (window.allBrands || []).filter(brand => {
        const ids = brand.country_id ? brand.country_id.toString().split(',').map(id => parseInt(id.trim())) : [];
        return ids.includes(parseInt(countryId));
    }).forEach(brand => {
        select.innerHTML += `<option value="${brand.id}">${brand.brand_name}</option>`;
    });
}

function loadWarehousesByCountry(countryId, warehouseSelectId, branchSelectId) {
    const warehouseSelect = document.getElementById(warehouseSelectId);
    const branchSelect = document.getElementById(branchSelectId);
    if (!warehouseSelect || !branchSelect) return;
    warehouseSelect.innerHTML = '<option value="" disabled selected>Select Warehouse</option>';
    branchSelect.innerHTML = '<option value="" disabled selected>Select Store</option>';
    if (!countryId) return;
    (window.allWarehouses || []).filter(w => w.country_id == countryId).forEach(w => {
        warehouseSelect.innerHTML += `<option value="${w.id}">${w.warehouse_name}</option>`;
    });
}

function loadBranchesByWarehouse(warehouseId, branchSelectId) {
    const branchSelect = document.getElementById(branchSelectId);
    if (!branchSelect) return;
    branchSelect.innerHTML = '<option value="" disabled selected>Select Store</option>';
    if (!warehouseId) return;
    (window.allBranches || []).filter(b => b.warehouse_id == warehouseId).forEach(b => {
        branchSelect.innerHTML += `<option value="${b.id}">${b.branch_name}</option>`;
    });
}

function getStockEntryHtml(countryId) {
    return `
    <div class="stock-entry d-flex align-items-end gap-2 mb-2">
        <div style="flex:1">
            <label>Warehouse</label>
            <select class="form-select variant-warehouse-select" name="warehouse_id[]">
                <option value="" disabled selected>Select Warehouse</option>
                ${buildWarehouseOptions(countryId)}
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
            <button type="button" class="btn btn-success add-stock-entry">+</button>
            <button type="button" class="btn btn-danger remove-stock-entry">−</button>
        </div>
    </div>`;
}

// Add new stock row
$(document).on("click", ".add-stock-entry", function () {
    const countryId = $("#add_country_select").val();
    const $wrapper = $(this).closest(".stock-entries-wrapper");
    const $list = $wrapper.find(".stock-entry-list");
    $list.append(getStockEntryHtml(countryId));
    updateStockEntryButtons($list);
    updateStockCount($wrapper);
});

// Remove stock row
$(document).on("click", ".remove-stock-entry", function () {
    const $wrapper = $(this).closest(".stock-entries-wrapper");
    const $list = $wrapper.find(".stock-entry-list");
    $(this).closest(".stock-entry").remove();
    updateStockEntryButtons($list);
    updateStockCount($wrapper);
});

function updateStockEntryButtons($list) {
    const $entries = $list.find(".stock-entry");
    $entries.each(function () {
        $(this).find(".remove-stock-entry").toggle($entries.length > 1);
    });
}

function updateStockCount($wrapper) {
    const count = $wrapper.find(".stock-entry").length;
    $wrapper.find(".stock-entry-count").val(count);
}

// Warehouse → Branch cascade (scoped to individual stock-entry row)
$(document).on("change", ".variant-warehouse-select", function () {
    const warehouseId = $(this).val();
    const $branchSelect = $(this).closest(".stock-entry").find(".variant-branch-select");
    $branchSelect.html('<option value="" disabled selected>Select Store</option>');
    if (!warehouseId) return;

    // Get already selected branch IDs in this variant (excluding current row)
    const $wrapper = $(this).closest(".stock-entries-wrapper");
    const $currentEntry = $(this).closest(".stock-entry");
    const usedBranchIds = [];
    $wrapper.find(".variant-branch-select").each(function () {
        const val = $(this).val();
        if (val && $(this).closest(".stock-entry")[0] !== $currentEntry[0]) {
            usedBranchIds.push(val);
        }
    });

    (window.allBranches || []).filter(b => b.warehouse_id == warehouseId).forEach(b => {
        const isUsed = usedBranchIds.includes(String(b.id));
        $branchSelect.append(
            `<option value="${b.id}" ${isUsed ? 'disabled' : ''}>${b.branch_name}${isUsed ? ' (already selected)' : ''}</option>`
        );
    });
});


$(document).on("change", ".variant-branch-select", function () {
    const $wrapper = $(this).closest(".stock-entries-wrapper");

    // Collect all currently selected branch IDs in this variant
    const selectedBranches = {};
    $wrapper.find(".stock-entry").each(function () {
        const $entry = $(this);
        const branchVal = $entry.find(".variant-branch-select").val();
        const warehouseVal = $entry.find(".variant-warehouse-select").val();
        if (branchVal && warehouseVal) {
            selectedBranches[branchVal] = true;
        }
    });

    // Re-render options for all OTHER branch selects in this variant
    $wrapper.find(".stock-entry").each(function () {
        const $entry = $(this);
        const $branchSel = $entry.find(".variant-branch-select");
        const $warehouseSel = $entry.find(".variant-warehouse-select");
        const currentBranchVal = $branchSel.val();
        const warehouseId = $warehouseSel.val();

        if (!warehouseId) return;

        // Rebuild options
        $branchSel.html('<option value="" disabled selected>Select Store</option>');
        (window.allBranches || []).filter(b => b.warehouse_id == warehouseId).forEach(b => {
            const isUsedElsewhere = selectedBranches[String(b.id)] && String(b.id) !== currentBranchVal;
            $branchSel.append(
                `<option value="${b.id}" 
                    ${String(b.id) === currentBranchVal ? 'selected' : ''}
                    ${isUsedElsewhere ? 'disabled' : ''}>
                    ${b.branch_name}${isUsedElsewhere ? ' (already selected)' : ''}
                </option>`
            );
        });
    });
});

$(document).on("change", "#add_country_select", function () {
    const selectedIds = $(this).val(); // now returns array
    const countryId = selectedIds ? selectedIds[0] : null; // use first for filtering

    loadCategoriesByCountry(countryId, 'add_category_select');
    loadBrandsByCountry(countryId, 'add_brand_select');
    $("#add_subcategory_select").html(
        '<option value="" disabled selected>Select Sub Category</option>'
    );
});

$(document).on("change", "#edit_country_select", function (e) {
    if (!e.originalEvent) return;

    const selectedIds = $(this).val();
    const countryId = selectedIds && selectedIds.length > 0 ? selectedIds[0] : null;

    loadCategoriesByCountry(countryId, 'edit_category_select');
    loadBrandsByCountry(countryId, 'edit_brand_select');
    $("#edit_subcategory_select").html('<option value="" disabled selected>Select Sub Category</option>');
});

// Edit modal — warehouse change
$(document).on("change", "#edit_warehouse_select", function () {
    loadBranchesByWarehouse($(this).val(), 'edit_branch_select');
});

$(function () {
    if (window.allowedCountryIds && window.allowedCountryIds.length === 1) {
        const countryId = window.allowedCountryIds[0];
        // Select2 needs this approach for pre-selection
        $('#add_country_select').val([String(countryId)]).trigger('change');
        loadCategoriesByCountry(countryId, 'add_category_select');
        loadBrandsByCountry(countryId, 'add_brand_select');
    }
});

$("#add_product_image").on("change", function () {
    const file = this.files[0];
    if (!file) return;
    if (!file.type.match(/^image\/(jpeg|png|gif|bmp|webp)$/)) {
        this.value = "";
        $(".product_remove_btn").trigger("click");
        addValidator.showErrors({ "#add_product_image": "*Only image files are allowed" });
        return;
    }
    let img = new Image();
    img.src = window.URL.createObjectURL(file);
    img.decode().then(() => {
        if (!(img.width === 600 && img.height === 600)) {
            $(".product_remove_btn").trigger("click");
            addValidator.showErrors({ "#add_product_image": "*Product Image resolution should be 600 × 600" });
        }
    });
});

$("#edit_product_image").on("change", function () {
    const file = this.files[0];
    if (!file) return;
    if (!file.type.match(/^image\/(jpeg|png|gif|bmp|webp)$/)) {
        this.value = "";
        $(".edit_product_remove_btn").trigger("click");
        editValidator.showErrors({ "#edit_product_image": "*Only image files are allowed" });
        return;
    }
    let img = new Image();
    img.src = window.URL.createObjectURL(file);
    img.decode().then(() => {
        if (!(img.width === 600 && img.height === 600)) {
            $(".edit_product_remove_btn").trigger("click");
            editValidator.showErrors({ "#edit_product_image": "*Product Image resolution should be 600 × 600" });
        }
    });
});

$("#add_varient_image").on("change", function () {
    const file = this.files[0];
    if (!file) return;
    if (!file.type.match(/^image\/(jpeg|png|gif|bmp|webp)$/)) {
        this.value = "";
        $(".product_remove_btn").trigger("click");
        addValidator.showErrors({ "#add_varient_image": "*Only image files are allowed" });
        return;
    }
    let img = new Image();
    img.src = window.URL.createObjectURL(file);
    img.decode().then(() => {
        if (!(img.width === 600 && img.height === 600)) {
            $(".product_remove_btn").trigger("click");
            addValidator.showErrors({ "#add_varient_image": "*Variant Image resolution should be 600 × 600" });
        }
    });
});

const imageValidationMap = {};

$(document).on("change", ".thumbim", function () {
    const fileInput = this;
    const file = fileInput.files[0];
    const fieldId = fileInput.name + "_" + Math.random().toString(36).substring(2, 9);
    $(fileInput).attr("data-image-id", fieldId);
    if (!file) { imageValidationMap[fieldId] = true; return; }
    if (!file.type.match(/^image\/(jpeg|png|gif|bmp|webp)$/)) {
        imageValidationMap[fieldId] = false;
        fileInput.value = "";
        Swal.fire({ icon: 'error', title: 'Invalid File Type', text: 'Only image files are allowed!' });
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

// ============================================================
// VALIDATORS
// ============================================================

addValidator
    .addField("#add_country_select", [{ rule: "required", errorMessage: "*Country is required" }])
    .addField("#add_category_select", [{ rule: "required", errorMessage: "*Category is required" }])
    .addField("#prodgst", [{ rule: "required", errorMessage: "*GST is required" }])
    .addField("#add_product_name", [
        { rule: "required", errorMessage: "*Product Name is required" },
        { rule: "minLength", value: 3, errorMessage: "*Min 3 characters" },
        { rule: "maxLength", value: 50, errorMessage: "*Max 50 characters" },
    ])
    .addField(".thumbim", [{
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
    .addField('#add_varient_name', [
        { rule: 'minLength', value: 3, errorMessage: 'Variant name must be at least 3 characters' },
        { rule: 'maxLength', value: 20, errorMessage: 'Variant name must be less than 20 characters' }
    ])
    .addField("#product_mrp_price", [
        { rule: "required", errorMessage: "*MRP is required" },
        { rule: "number", errorMessage: "*Must be a number" },
    ])
    .addField("#product_offer_price", [
        { rule: "required", errorMessage: "*Offer Price is required" },
        { rule: "number", errorMessage: "*Must be a number" },
        {
            validator: (value) => parseInt(value) <= parseInt($("#product_mrp_price").val()),
            errorMessage: "Should not be above MRP Price",
        },
    ])
    .addField("#add_product_description", [
        { rule: "minLength", value: 3, errorMessage: "*Min 3 characters" },
        { rule: "maxLength", value: 500, errorMessage: "*Max 500 characters" },
    ])
    .addField("#add_product_image", [
        { rule: "minFilesCount", value: 1, errorMessage: "*Product Image is required" },
        {
            validator: (value, fields) => new Promise((resolve) => {
                const file = fields["#add_product_image"].elem.files[0];
                if (!file || !file.type.startsWith("image/")) return resolve(false);
                const img = new Image();
                img.onload = () => resolve(img.width === 600 && img.height === 600);
                img.onerror = () => resolve(false);
                img.src = URL.createObjectURL(file);
            }),
            errorMessage: "*Must be exactly 600 × 600",
        },
    ])
    .addField("#add_varient_image", [
        { rule: "minFilesCount", value: 1, errorMessage: "*Variant Image is required" },
        {
            validator: (value, fields) => new Promise((resolve) => {
                const file = fields["#add_varient_image"].elem.files[0];
                if (!file || !file.type.startsWith("image/")) return resolve(false);
                const img = new Image();
                img.onload = () => resolve(img.width === 600 && img.height === 600);
                img.onerror = () => resolve(false);
                img.src = URL.createObjectURL(file);
            }),
            errorMessage: "*Must be exactly 600 × 600",
        },
    ])
    .addField("#add_product_specification", [
        { rule: "minLength", value: 3, errorMessage: "*Min 3 characters" },
        { rule: "maxLength", value: 500, errorMessage: "*Max 500 characters" },
    ])
    .addField("#add_product_value", [
        { rule: "required", errorMessage: "*Product Value is required" },
        { rule: "minLength", value: 1, errorMessage: "*Min 1 character" },
        { rule: "maxLength", value: 5, errorMessage: "*Max 5 characters" },
    ])
    .addField("#add_unit_select", [{ rule: "required", errorMessage: "*Unit is required" }])
    .addField("#add_pre_note", [{
        validator: () => $('#preorderCheckbox').is(':checked') ? $('#add_pre_note').val().trim().length > 0 : true,
        errorMessage: "*Pre Order Note is required",
    }])
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", true).html("Uploading.....");
        addProductFormSubmit(event);
    });

editValidator
    .addField("#edit_country_select", [{ rule: "required", errorMessage: "*Country is required" }])
    .addField("#edit_category_select", [{ rule: "required", errorMessage: "*Category is required" }])
    .addField("#edit_product_name", [
        { rule: "required", errorMessage: "*Product Name is required" },
        { rule: "minLength", value: 3, errorMessage: "*Min 3 characters" },
        { rule: "maxLength", value: 50, errorMessage: "*Max 50 characters" },
    ])
    .addField("#edit_product_image", [{
        validator: (value, fields) => new Promise((resolve) => {
            const file = fields["#edit_product_image"].elem.files[0];
            if (!file || !file.type.startsWith("image/")) return resolve(false);
            const img = new Image();
            img.onload = () => resolve(img.width === 600 && img.height === 600);
            img.onerror = () => resolve(false);
            img.src = URL.createObjectURL(file);
        }),
        errorMessage: "*Must be exactly 600 × 600",
    }])
    .addField("#edit_product_description", [
        { rule: "minLength", value: 3, errorMessage: "*Min 3 characters" },
        { rule: "maxLength", value: 500, errorMessage: "*Max 500 characters" },
    ])
    .addField("#edit_product_specification", [
        { rule: "minLength", value: 3, errorMessage: "*Min 3 characters" },
        { rule: "maxLength", value: 500, errorMessage: "*Max 500 characters" },
    ])
    .onSuccess((event) => {
        $(".edit_submit_btn").attr("disabled", true).html("Uploading.....");
        editProductFormSubmit(event);
    });

// ============================================================
// GRIDJS
// ============================================================

const gridNew = new gridjs.Grid({
    columns: ["S.NO","Category","Sub Category","Country","Brand","Product Name","Product Image","Ingredients","Description",{ name: "Action", sort: false }],
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: products.map((product, index) => [
        index + 1,
        product.cate_name,
        product.subcate_name,
        product.country_name || '-',
        product.brand ? product.brand.brand_name : '-',
        product.product_name,
        gridjs.html(`<img class="bannerImage_image_el gridImage" src="images/${product.product_image}" alt="product_${index}"/>`),
        product.product_description || 'No Ingredients',
        product.product_specification || 'No Description',
        gridjs.html(`<div>
            <button data-bs-toggle="modal"
                data-productid="${product.id}" data-countryid="${product.country_id}"
                data-categoryid="${product.category_id}" data-brandid="${product.brand_id}"
                data-slug="${product.slug}" data-subcategoryid="${product.subcategory_id}"
                data-productname="${product.product_name}" data-productquantity="${product.product_quantity}"
                data-productregularprice="${product.product_regular_price}" data-productmrpprice="${product.product_mrp_price}"
                data-productdescription="${product.product_description}" data-productimage="${product.product_image}"
                data-productspecification="${product.product_specification}" data-productunit="${product.unit_value}"
                data-productvalue="${product.product_value}"
                data-bs-target="#editProductModal" class="btn btn-secondary edit_btn">Edit</button>
            <button class="btn btn-danger delete_btn" data-productid="${product.id}">Delete</button>
        </div>`),
    ]),
    style: {
        table: { border: "1px solid #ccc" },
        th: { "background-color": "rgba(0,0,0,0.1)", color: "#000", "border-bottom": "3px solid #ccc", "text-align": "center", "border-right": "0.5px solid #ccc" },
        td: { "text-align": "center", "border-right": "0.5px solid #ccc", "border-bottom": "0.5px solid #ccc" },
    },
});
gridNew.render(document.getElementById("table-gridjs"));

function gridjsReRender(products) {
    gridNew.updateConfig({
        data: products.map((product, index) => [
            index + 1,
            product.cate_name,
            product.subcate_name,
            product.country_name || '-',
            product.brand ? product.brand.brand_name : '-',
            product.product_name,
            gridjs.html(`<img class="bannerImage_image_el gridImage" src="images/${product.product_image}" alt="product_${index}"/>`),
            product.product_description || 'No Ingredients',
            product.product_specification || 'No Description',
            gridjs.html(`<div>
                <button data-bs-toggle="modal"
                    data-productid="${product.id}" data-countryid="${product.country_id}"
                    data-categoryid="${product.category_id}" data-brandid="${product.brand_id}"
                    data-slug="${product.slug}" data-subcategoryid="${product.subcategory_id}"
                    data-productname="${product.product_name}" data-productquantity="${product.product_quantity}"
                    data-productregularprice="${product.product_regular_price}" data-productmrpprice="${product.product_mrp_price}"
                    data-productdescription="${product.product_description}" data-productimage="${product.product_image}"
                    data-productspecification="${product.product_specification}" data-productunit="${product.unit_value}"
                    data-productvalue="${product.product_value}"
                    data-bs-target="#editProductModal" class="btn btn-secondary edit_btn">Edit</button>
                <button class="btn btn-danger delete_btn" data-productid="${product.id}">Delete</button>
            </div>`),
        ]),
    }).forceRender();
}

// ============================================================
// FORM SUBMITS
// ============================================================

function addProductFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "products", method: "POST", dataType: "json",
        data: formdata, processData: false, contentType: false,
        success: function (response) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            $("#addProductForm")[0].reset();
            $("#addProductModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(response.products);
            Swal.fire("Added", "Records Added Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

function editProductFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updateProducts/" + $("#edit_product_id").val(), method: "POST", dataType: "json",
        data: formdata, processData: false, contentType: false,
        success: function (response) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            $("#editProductForm")[0].reset();
            $("#editProductModal").hide();
            $(".modal-backdrop").remove();
            $(".edit_product_remove_btn").trigger("click");
            document.body.style.overflowY = "scroll";
            gridjsReRender(response.products);
            Swal.fire("Updated", "Records Updated Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

$(function () {
    $(document).on("click", ".edit_btn", function () {
        const countryId = $(this).attr("data-countryid");
        const catId = $(this).attr("data-categoryid");
        const subCatId = $(this).attr("data-subcategoryid");

        const countryArray = countryId
            ? countryId.toString().split(',').map(s => s.trim())
            : [];

        // Load categories and brands using first country
        const firstCountryId = countryArray.length > 0 ? countryArray[0] : null;
        loadCategoriesByCountry(firstCountryId, 'edit_category_select');
        loadBrandsByCountry(firstCountryId, 'edit_brand_select');

        setTimeout(() => {
            $('#edit_country_select').val(countryArray).trigger('change.select2');
            $("#edit_category_select").val(catId);
            $("#edit_brand_select").val($(this).attr("data-brandid"));
            populateSubCategories($("#edit_subcategory_select"), catId, subCatId);
        }, 150);

        $(".edit_preview_image").attr("src", `images/${$(this).attr("data-productimage")}`);
        $("#edit_product_id").val($(this).attr("data-productid"));
        $("#edit_product_name").val($(this).attr("data-productname"));
        $("#edit_product_slug").val($(this).attr("data-slug"));
        $("#edit_product_description").val($(this).attr("data-productdescription"));
        $("#edit_product_specification").val($(this).attr("data-productspecification"));
    });
});

// ============================================================
// DELETE
// ============================================================

$(document).on("click", ".delete_btn", function () {
    const id = $(this).attr("data-productid");
    Swal.fire({
        title: "Are you sure?", text: "You won't be able to revert this!", icon: "warning",
        showCancelButton: true, confirmButtonText: "Yes, delete it!", cancelButtonText: "No, cancel!", reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "destroyProducts/" + id, method: "post", dataType: "json",
                success: function (response) {
                    gridjsReRender(response.products);
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
// IMAGE PREVIEWS
// ============================================================

$(function () {
    const backupHtml1 = $("#preview-container1").html();
    $("#add_varient_image").on("change", function () {
        const file = $(this)[0].files[0];
        if (!file || !file.type.match("image.*")) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = $("<img>").attr("src", e.target.result);
            const removeBtn = $("<button>").addClass("btn btn-danger product_remove_btn mt-2").text("Remove");
            $("#preview-container1").empty().append(img).append(removeBtn);
            removeBtn.on("click", function (e) {
                e.preventDefault();
                $("#preview-container1").html(backupHtml1);
                $("#add_varient_image").val("");
            });
        };
        reader.readAsDataURL(file);
    });
});

$(function () {
    const backupHtml = $("#preview-container").html();
    $("#add_product_image").on("change", function () {
        const file = $(this)[0].files[0];
        if (!file || !file.type.match("image.*")) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = $("<img>").attr("src", e.target.result);
            const removeBtn = $("<button>").addClass("btn btn-danger product_remove_btn mt-2").text("Remove");
            $("#preview-container").empty().append(img).append(removeBtn);
            removeBtn.on("click", function (e) {
                e.preventDefault();
                $("#preview-container").html(backupHtml);
                $("#add_product_image").val("");
            });
        };
        reader.readAsDataURL(file);
    });
});

$(function () {
    const backupHtml = $(".edit_preview-container").html();
    $("#edit_product_image").on("change", function () {
        const file = $(this)[0].files[0];
        if (!file || !file.type.match("image.*")) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = $("<img>").attr("src", e.target.result);
            const removeBtn = $("<button>").addClass("btn btn-danger edit_product_remove_btn mt-2").text("Remove");
            $(".edit_preview-container").empty().append(img).append(removeBtn);
            removeBtn.on("click", function (e) {
                e.preventDefault();
                $(".edit_preview-container").html(backupHtml);
                $("#edit_product_image").val("");
            });
        };
        reader.readAsDataURL(file);
    });
});

$(document).ready(function () {
    $("body").on("change", "input[id^='add_varient_image-']", function () {
        const inputId = $(this).attr("id");
        const targetId = $(this).data("container");
        const file = $(this)[0].files[0];
        if (!file || !file.type.match("image.*")) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = $("<img>").attr("src", e.target.result);
            const removeBtn = $("<button>").addClass("btn btn-danger product_remove_btn mt-2").text("Remove");
            $("#" + targetId).empty().append(img).append(removeBtn);
            removeBtn.on("click", function (e) {
                e.preventDefault();
                $("#" + targetId).html("");
                $("#" + inputId).val("");
            });
        };
        reader.readAsDataURL(file);
    });
});

// ============================================================
// ADD VARIANT CLONE
// ============================================================

$(document).ready(function () {
    $("#add-input").click(function () {
        const countryId = $("#add_country_select").val();
        const variantIndex = $(".dynamic-inputs1").length + 1;
        const imageCountIndex = $(".product_image_count").length + 1;

        const inputGroup = `
        <hr><br>
        <h5>Product Variant</h5>
        <div class="d-flex product_fields mt-3">
        <div class="row">
            <label for="add_varient_image-${variantIndex}" class="preview-container" id="preview-container-${variantIndex}">
                <div class="flex justify-content-center">
                    <div class="text-center"><i class="display-4 col-12 text-muted mdi mdi-cloud-upload"></i></div>
                    <div><span class="col-12">Upload Image</span></div>
                </div>
            </label>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Variant Image*(600 * 600)</label>
                    <input type="file" class="form-control image_el dropzone needsclick"
                        id="add_varient_image-${variantIndex}" accept="image/*"
                        name="Varient_image[]" required
                        data-var_button="add_varient_image-${variantIndex}"
                        data-container="preview-container-${variantIndex}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Variant Name(Optional)</label>
                    <input type="text" class="form-control" name="varient_name[]" placeholder="Variant Name">
                </div>
            </div>

            {{-- STOCK ENTRIES --}}
            {{-- <div class="col-md-12 stock-entries-wrapper">
                <h6>Stock Entries</h6>
                <input type="hidden" class="stock-entry-count" name="stock_entry_count[]" value="1">
                <div class="stock-entry-list">
                    <div class="stock-entry d-flex align-items-end gap-2 mb-2">
                        <div style="flex:1">
                            <label class="form-label">Choose Warehouse</label>
                            <select class="form-select variant-warehouse-select" name="warehouse_id[]">
                                <option value="" disabled selected>Select Warehouse</option>
                                ${buildWarehouseOptions(countryId)}
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
                            <input type="number" class="form-control stock-qty-input" name="product_quantity[]"
                                placeholder="Quantity" required min="0">
                        </div>
                        <div>
                            <button type="button" class="btn btn-success add-stock-entry">+</button>
                            <button type="button" class="btn btn-danger remove-stock-entry" style="display:none;">−</button>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">SKU (Auto-gen if empty)</label>
                    <input type="text" class="form-control" name="sku[]" placeholder="SKU-CODE">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Barcode</label>
                    <input type="text" class="form-control" name="barcode[]" placeholder="Barcode / EAN">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Product Unit*</label>
                    <select class="form-select" name="unit_id[]">
                        <option value="" selected>Select Units</option>
                        ${(window.units || []).map(u => `<option value="${u.id}">${u.unit_name} (${u.short_name})</option>`).join('')}
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Variant Value*</label>
                    <input type="text" class="form-control" name="product_value[]" placeholder="Product Value" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Product MRP Price*</label>
                    <input type="text" class="form-control" name="product_mrp_price[]" placeholder="MRP price" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Product Selling Price*</label>
                    <input type="text" class="form-control" name="product_offer_price[]" placeholder="Selling price" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Low Stock*</label>
                    <input type="text" class="form-control" name="low_stock[]" placeholder="Low stock" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Product GST</label>
                    <select class="form-select" name="product_gst[]">
                        <option value="" selected>Select GST</option>
                        <option value="0">0 (GST Included)</option>
                        <option value="5">5</option>
                        <option value="12">12</option>
                        <option value="18">18</option>
                        <option value="28">28</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-start align-items-center">
                <div class="mb-3">
                    <input type="checkbox" name="hot_deals[]" class="hot_value" value="0">
                    <label class="form-label">New Arrivals</label>
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-start align-items-center">
                <div class="mb-3 mt-4">
                    <input type="checkbox" name="popular_prod[]" class="pop_prod" value="0">
                    <label class="form-label">Best Seller</label>
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-start align-items-center">
                <div class="mb-3 mt-4">
                    <input type="checkbox" name="preorder[]" class="preorder" value="1">
                    <label class="form-label">Pre Order</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3 mt-4">
                    <textarea class="form-control" name="pre_note[]" placeholder="Pre Order Note" maxlength="300"></textarea>
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-start align-items-center">
                <div class="mb-3 mt-4">
                    <input type="checkbox" name="flash_sale[]" class="flash_sale_checkbox" value="1">
                    <label class="form-label">Flash Sale</label>
                </div>
            </div>
            <div class="col-md-3 flash_sale_date_container" style="display:none;">
                <div class="mb-3 mt-4">
                    <label class="form-label">Flash Sale End Date</label>
                    <input type="datetime-local" class="form-control flash_sale_date_input" name="flash_sale_date[]">
                </div>
            </div>

            <div class="">
                <h5>Product Thumb Images</h5>
                <input type="hidden" name="product_image_count[]"
                    id="product_image_count-${imageCountIndex}"
                    value="1" class="product_image_count">
                <div class="col-lg-12">
                    <div id="dynamic-inputs1-${variantIndex}" class="dynamic-inputs1">
                        <div class="d-flex product_fields1">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="mb-3">
                                        <label class="form-label">Product Image*(600 * 600)</label>
                                        <input type="file" class="form-control image_el dropzone needsclick"
                                            placeholder="Product Image" name="product_image1[]" required>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12 mt-4">
                                    <button class="btn btn-danger delete-input1" type="button">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 mt-3 mb-3">
                    <button class="btn btn-success add-input2" type="button"
                        data-target="dynamic-inputs1-${variantIndex}"
                        data-input="product_image_count-${imageCountIndex}">Add Another Images</button>
                </div>
                <hr>
            </div>

            <div class="col-lg-3 col-sm-12 mt-4">
                <button class="btn btn-danger delete-input" type="button">Delete Variant</button>
            </div>
        </div>
        </div>`;

        $("#dynamic-inputs").append(inputGroup);
    });

    $(document).on("click", ".delete-input", function () {
        $(this).closest(".product_fields").remove();
    });

    $(document).on("change", ".hot_value", function () {
        $(this).val($(this).prop("checked") ? 1 : 0);
    });

    $(document).on("change", ".pop_prod", function () {
        $(this).val($(this).prop("checked") ? 1 : 0);
    });
});

// ============================================================
// THUMBNAIL IMAGE CLONE
// ============================================================

$(document).ready(function () {
    $(".add-input1").click(function () {
        const $counter = $(".product_image_count").first();
        $counter.val(parseInt($counter.val()) + 1);
        $(".dynamic-inputs1").first().append(`
        <div class="d-flex product_fields1"><div class="row">
            <div class="col-lg-8"><div class="mb-3">
                <label class="form-label">Product Image*(600 * 600)</label>
                <input type="file" class="form-control thumbim image_el dropzone needsclick"
                    placeholder="Product Image" name="product_image1[]" required>
            </div></div>
            <div class="col-lg-4 col-sm-12 mt-4">
                <button class="btn btn-danger delete-input1" type="button">Delete</button>
            </div>
        </div></div>`);
    });

    $(document).on("click", ".delete-input1", function () {
        $(this).closest(".product_fields1").remove();
    });
});

$(document).ready(function () {
    $(document).on("click", ".add-input2", function () {
        const inputId = $(this).data("input");
        const targetId = $(this).data("target");
        const $counter = $("#" + inputId);
        $counter.val(parseInt($counter.val()) + 1);
        $("#" + targetId).append(`
        <div class="d-flex product_fields1"><div class="row">
            <div class="col-lg-8"><div class="mb-3">
                <label class="form-label">Product Image*(600 * 600)</label>
                <input type="file" class="form-control image_el dropzone needsclick"
                    placeholder="Product Image" name="product_image1[]" required>
            </div></div>
            <div class="col-lg-4 col-sm-12 mt-4">
                <button class="btn btn-danger delete-input1" type="button">Delete</button>
            </div>
        </div></div>`);
    });
});

// ============================================================
// SUBCATEGORY / FILTER / MISC
// ============================================================

const addValidator2 = new JustValidate("#productfilterForm", { validateBeforeSubmitting: true });
addValidator2
    .addField("#select_category_select", [{ rule: "required", errorMessage: "*Category is required" }])
    .onSuccess((event) => {
        $(".product_filter_btn").attr("disabled", true).html("Uploading.....");
        productfilterFormSubmit(event);
    });

function productfilterFormSubmit(event) {
    const formData = new FormData(event.target);
    $.ajax({
        url: "getproductfilter", method: "POST", dataType: "json",
        data: formData, processData: false, contentType: false,
        success: function (response) {
            $(".product_filter_btn").removeAttr("disabled").html("Get Report");
            gridjsReRender(response.products);
        },
        error: function (jqXHR, textStatus) {
            $(".product_filter_btn").removeAttr("disabled").html("Get Report");
            Swal.fire(textStatus.toUpperCase(), textStatus, "warning");
        },
    });
}

$(document).on("change", "#add_subcategory_select", function () {
    const id = $(this).val();
    const map = {
        "16": `<div class="subcate_append_wrapper"><input type="hidden" name="size_check" value="1"></div>`,
        "17": `<div class="subcate_append_wrapper"><input type="hidden" name="size_check" value="2"></div>`,
        "18": `<div class="subcate_append_wrapper"><input type="checkbox" name="size_check" value="3"></div>`,
        "19": `<div class="subcate_append_wrapper"><input type="checkbox" name="size_check" value="4"></div>`,
    };
    $(".subcate_size_append").html(map[id] || "");
});

$(document).on("input", "[name='low_stock[]']", function () {
    const lowStock = parseInt($(this).val()) || 0;
    if (lowStock <= 0) return;

    const $variantWrapper = $(this).closest(".product_fields, #dynamic-inputs > div");
    let totalStock = 0;
    $variantWrapper.find("input[name='product_quantity[]']").each(function () {
        totalStock += parseInt($(this).val()) || 0;
    });

    if (totalStock > 0 && lowStock >= totalStock) {
        alert("Low stock must be less than the total stock (" + totalStock + ").");
        $(this).val("");
    }
});

function populateSubCategories(select, categoryId, selectedId = null) {
    select.empty().append('<option value="" disabled selected>Select Sub Category</option>');
    if (window.subcategories && categoryId) {
        window.subcategories.filter(item => item.category_name == categoryId).forEach(item => {
            const isSelected = selectedId && item.id == selectedId ? 'selected' : '';
            select.append(`<option value="${item.id}" ${isSelected}>${item.subcategory_name}</option>`);
        });
    }
}

$(document).ready(function () {
    $("#add_category_select").change(function () { populateSubCategories($("#add_subcategory_select"), $(this).val()); });
    $("#edit_category_select").change(function () { populateSubCategories($("#edit_subcategory_select"), $(this).val()); });
    $("#bulk_category_select").change(function () { populateSubCategories($("#bulk_subcategory_select"), $(this).val()); });

    $("#bulkUploadForm").on("submit", function (e) {
        e.preventDefault();
        if (!this.checkValidity()) { this.classList.add('was-validated'); return; }
        const formData = new FormData(this);
        const submitBtn = $(".bulk_upload_submit_btn");
        submitBtn.attr("disabled", true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');
        $.ajax({
            url: "bulkUploadProducts", method: "POST", data: formData, processData: false, contentType: false,
            success: function (response) {
                submitBtn.attr("disabled", false).text("Start Bulk Upload");
                $("#bulkUploadModal").modal("hide");
                Swal.fire({ icon: 'success', title: 'Success', text: response.message || 'Products uploaded successfully!' });
                if (response.products) gridjsReRender(response.products);
            },
            error: function (xhr) {
                submitBtn.attr("disabled", false).text("Start Bulk Upload");
                Swal.fire({ icon: 'error', title: 'Upload Failed', text: xhr.responseJSON?.message || 'An error occurred.' });
            }
        });
    });
});

$(document).ready(function () {
    $('#preorderCheckbox').on('change', function () {
        if ($(this).is(':checked')) { $('#preorderNoteDiv').show(); }
        else { $('#preorderNoteDiv').hide(); $('#add_pre_note').val(""); }
    });

    $(document).on('change', '.flash_sale_checkbox', function () {
        const container = $(this).closest('.row').find('.flash_sale_date_container');
        if ($(this).is(':checked')) { container.show(); }
        else { container.hide(); container.find('.flash_sale_date_input').val(""); }
    });
});