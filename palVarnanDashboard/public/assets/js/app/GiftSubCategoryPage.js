// ─── GIFT SUB CATEGORY PAGE ───────────────────────────────────────────────────

// ─── Validators ───────────────────────────────────────────────────────────────

$(document).on("click", ".addRowBtn", function () {
    let newRow = `
        <div class="row mb-2 subcategory-row">
            <div class="col-10">
                <input type="text" class="form-control subcategory-input"
                    name="subcategory_name[]" placeholder="Sub Category name" required>
            </div>
            <div class="col-2 d-flex">
                <button type="button" class="btn btn-success addRowBtn">+</button>
                <button type="button" class="btn btn-danger removeRowBtn ms-1">-</button>
            </div>
        </div>
    `;
    $("#subCategoryContainer").append(newRow);
});

$(document).on("click", ".removeRowBtn", function () {
    $(this).closest(".subcategory-row").remove();
});

const addValidator = new JustValidate("#addSubCategoriesForm", {
    validateBeforeSubmitting: true,
});

const editValidator = new JustValidate("#editSubCategoriesForm", {
    validateBeforeSubmitting: true,
});

// ─── Country-based Category Loader ───────────────────────────────────────────

function loadCategories(countryId, selectId) {
    const select = document.getElementById(selectId);
    select.innerHTML = '<option value="" disabled selected>Select Category</option>';

    let filtered = window.allCategories || [];

    if (countryId) {
        filtered = filtered.filter(cat => {
            const ids = cat.country_id
                ? cat.country_id.toString().split(',').map(id => parseInt(id.trim()))
                : [];
            return ids.includes(parseInt(countryId));
        });
    }

    filtered.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = cat.category_name;
        select.appendChild(option);
    });
}

// ─── On Page Load: auto-load for single-country users ────────────────────────

$(document).ready(function () {
    if (window.allowedCountryIds && window.allowedCountryIds.length === 1) {
        loadCategories(window.allowedCountryIds[0], 'add_categoryname');
        loadCategories(window.allowedCountryIds[0], 'edit_categoryname');
    }

    // ADD FORM — when country changes, reload category dropdown
    $(document).on("change", "#add_country_id", function () {
        const selectedCountry = $(this).val();
        $("#add_categoryname").val("").trigger("change"); // reset category
        loadCategories(selectedCountry, 'add_categoryname');
    });

    // EDIT FORM — when country changes, reload category dropdown
    $(document).on("change", "#edit_country_id", function () {
        const selectedCountry = $(this).val();
        $("#edit_categoryname").val("").trigger("change"); // reset category
        loadCategories(selectedCountry, 'edit_categoryname');
    });
});

// ─── Add Validator ────────────────────────────────────────────────────────────


addValidator
    .addField("#add_categoryname", [
        {
            rule: "required",
            errorMessage: "*Categories Name Field is required",
        },
    ])
    .onSuccess((event) => {

        let isValid = true;

        $(".subcategory-input").each(function () {
            if ($(this).val().trim() === "") {
                isValid = false;
                $(this).addClass("is-invalid");
            } else {
                $(this).removeClass("is-invalid");
            }
        });

        if (!isValid) {
            Swal.fire("Error", "All Sub Category fields are required", "warning");
            return;
        }

        $(".add_submit_btn").attr("disabled", "true");
        $(".add_submit_btn").html("Uploading.....");

        addSubCategoriesFormSubmit(event);
    });

// ─── Edit Validator ───────────────────────────────────────────────────────────

editValidator
    .addField("#edit_categoryname", [
        { rule: "required", errorMessage: "*Category selection is required" },
    ])
    .addField("#edit_subcategoriesname", [
        { rule: "required", errorMessage: "*Sub Category Name Field is required" },
        { rule: "minLength", value: 3, errorMessage: "*Sub Category Name should be at least 3 character long" },
        { rule: "maxLength", value: 50, errorMessage: "*Sub Category Name should be at Maximum 50 character long" },
    ])
    .onSuccess((event) => {
        $(".edit_submit_btn").attr("disabled", true).html("Updating...");
        editSubCategoriesFormSubmit(event);
    });

// ─── Country Name Helper ──────────────────────────────────────────────────────

function resolveCountryName(countryId) {
    if (!countryId) return '-';
    if (window.countries) {
        const found = window.countries.find(c => c.id == countryId);
        if (found) return found.name;
    }
    return countryId;
}

// ─── Grid.js Row Builder ──────────────────────────────────────────────────────

function buildRow(sub, index) {
    return [
        index + 1,
        resolveCountryName(sub.country_id),
        sub.category_display,
        sub.subcategory_name,
       
        gridjs.html(
            sub.status == 1
                ? `<div class="d-flex gap-2 justify-content-center">
                    <button data-bs-toggle="modal"
                        data-subcategoriesid="${sub.id}"
                        data-categoriesname="${sub.category_name}"
                        data-subcategoriesname="${sub.subcategory_name}"
                        data-country="${sub.country_id}"
                       
                        data-bs-target="#editSubCategoriesModal"
                        class="btn btn-secondary btn-sm edit_btn">Edit</button>
                   </div>`
                : `<div class="d-flex gap-2 justify-content-center">
                    <button data-bs-toggle="modal"
                        data-subcategoriesid="${sub.id}"
                        data-categoriesname="${sub.category_name}"
                        data-subcategoriesname="${sub.subcategory_name}"
                        data-country="${sub.country_id}"
                       
                        data-bs-target="#editSubCategoriesModal"
                        class="btn btn-secondary btn-sm edit_btn">Edit</button>
                    <button class="btn btn-danger btn-sm delete_btn"
                        data-subcategoriesid="${sub.id}">Delete</button>
                   </div>`
        ),
    ];
}

// ─── Grid.js Init ─────────────────────────────────────────────────────────────

const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Country",
        "Category Name",
        "Sub Category Name",
       
        { name: "Action", sort: false },
    ],
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: subcategories.map((sub, index) => buildRow(sub, index)),
    style: {
        table: { border: "1px solid #ccc" },
        th: { "background-color": "rgba(0, 0, 0, 0.1)", color: "#000", "border-bottom": "3px solid #ccc", "text-align": "center", "border-right": "0.5px solid #ccc" },
        td: { "text-align": "center", "border-right": "0.5px solid #ccc", "border-bottom": "0.5px solid #ccc" },
    },
});

gridNew.render(document.getElementById("table-gridjs"));

function gridjsReRender(subcategories) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: subcategories.map((sub, index) => buildRow(sub, index)),
        })
        .forceRender();
}

// ─── AJAX: Add Sub Category ───────────────────────────────────────────────────

function addSubCategoriesFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: giftUrls.store,
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            $("#addSubCategoriesForm")[0].reset();
            $("#subCategoryContainer").html(`
                <div class="row mb-2 subcategory-row">
                    <div class="col-10">
                        <input type="text" class="form-control subcategory-input"
                            name="subcategory_name[]" placeholder="Sub Category name" required>
                    </div>
                    <div class="col-2 d-flex">
                        <button type="button" class="btn btn-success addRowBtn">+</button>
                    </div>
                </div>
            `);
            $("#addSubCategoriesModal").modal('hide');
            $(".modal-backdrop").remove();
            gridjsReRender(response.subcategories);
            Swal.fire("Added", "Gift Sub Category Added Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            let message = errorThrown;
            if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                message = Object.values(jqXHR.responseJSON.errors).flat().join("<br>");
            }
            Swal.fire({ icon: "error", title: textStatus.toUpperCase(), html: message });
        },
    });
}

// ─── AJAX: Edit Sub Category ──────────────────────────────────────────────────

function editSubCategoriesFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: giftUrls.update + "/" + $("#edit_subcategories_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            $("#editSubCategoriesForm")[0].reset();
            $("#editSubCategoriesModal").modal('hide');
            $(".modal-backdrop").remove();
            $(".edit_product_remove_btn").trigger("click");
            gridjsReRender(response.subcategories);
            Swal.fire("Updated", "Gift Sub Category Updated Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            let message = errorThrown;
            if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                message = Object.values(jqXHR.responseJSON.errors).flat().join("<br>");
            }
            Swal.fire({ icon: "error", title: textStatus.toUpperCase(), html: message });
        },
    });
}

// ─── Event Handlers ───────────────────────────────────────────────────────────

$(function () {
    // Edit button — populate modal fields + load filtered categories
    $(document).on("click", ".edit_btn", function () {
     
        const country    = $(this).attr("data-country");
        const categoryId = $(this).attr("data-categoriesname"); // stores category ID

        $("#edit_subcategories_id").val($(this).attr("data-subcategoriesid"));
        $("#edit_subcategoriesname").val($(this).attr("data-subcategoriesname"));
        $("#edit_country_id_hidden").val(country);
        $("#edit_country_id").val(country).trigger("change");
     

        // Load gift categories filtered by country, then set the selected category
        loadCategories(country, 'edit_categoryname');
        setTimeout(() => {
            $("#edit_categoryname").val(categoryId);
        }, 100);
    });

    // Delete button
    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-subcategoriesid");
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
                        gridjsReRender(response.subcategories);
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