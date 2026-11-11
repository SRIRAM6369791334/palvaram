const addValidator = new JustValidate("#addCategoriesForm", {
    validateBeforeSubmitting: true,
});
const editValidator = new JustValidate("#editCategoriesForm", {
    validateBeforeSubmitting: true,
});

let invalidAddCategoryImage = false;
let invalidEditCategoryImage = false;

// ─── Image Resolution Validation ─────────────────────────────────────────────

$("#add_categoryImage").on("change", function () {
    const file = this.files[0];
    if (file && file.type.startsWith("image/")) {
        let img = new Image();
        img.src = window.URL.createObjectURL(file);
        img.decode().then(() => {
            if (!(img.width === 512 && img.height === 512)) {
                invalidAddCategoryImage = true;
                $(".product_remove_btn").trigger("click");
                addValidator.showErrors({
                    "#add_categoryImage": "*Image resolution must be exactly 512 × 512",
                });
            } else {
                invalidAddCategoryImage = false;
            }
        });
    }
});

$("#edit_categoryImage").on("change", function () {
    const file = this.files[0];
    if (file && file.type.startsWith("image/")) {
        let img = new Image();
        img.src = window.URL.createObjectURL(file);
        img.decode().then(() => {
            if (!(img.width === 512 && img.height === 512)) {
                invalidEditCategoryImage = true;
                $(".edit_product_remove_btn").trigger("click");
                editValidator.showErrors({
                    "#edit_categoryImage": "*Image resolution must be exactly 512 × 512",
                });
            } else {
                invalidEditCategoryImage = false;
            }
        });
    }
});

// ─── Validators ───────────────────────────────────────────────────────────────

addValidator
    .addField("#add_categoriesname", [
        { rule: "required", errorMessage: "*Categories Name Field is required" },
        { rule: "minLength", value: 3, errorMessage: "*Categories Name should be at least 3 character long" },
        { rule: "maxLength", value: 50, errorMessage: "*Categories Name should be at Maximum 50 character long" },
    ])
    .addField("#add_categoryImage", [
        { rule: "minFilesCount", value: 1, errorMessage: "*Upload Category Image" },
        {
            rule: "files",
            value: { files: { extensions: ["jpeg", "jpg", "png", "webp"], maxSize: 10000000, minSize: 0 } },
            errorMessage: "*Unsupported file format (Only png, jpg, jpeg, webp) or file too large",
        },
    ])
    .onSuccess((event) => {
        if ($("#add_country_id").length) {
            const selected = $("#add_country_id").val();
            if (!selected || (Array.isArray(selected) && selected.length === 0)) {
                Swal.fire({
                    icon: "warning",
                    title: "Country Required",
                    text: "Please select at least one country for the gift category."
                });
                return false;
            }
        }
        if (invalidAddCategoryImage) {
            addValidator.showErrors({
                "#add_categoryImage": "*Image resolution must be exactly 512 × 512",
            });
            return;
        }
        $(".add_submit_btn").attr("disabled", true).html("Uploading...");
        addCategoriesFormSubmit(event);
    });

editValidator
    .addField("#edit_categoriesname", [
        { rule: "minLength", value: 3, errorMessage: "*Categories Name should be at least 3 character long" },
        { rule: "maxLength", value: 50, errorMessage: "*Categories Name should be at Maximum 50 character long" },
    ])
    .addField("#edit_categoryImage", [
        {
            rule: "files",
            value: { files: { extensions: ["jpeg", "jpg", "png", "webp"], maxSize: 10000000, minSize: 0 } },
            errorMessage: "*Unsupported file format (Only png, jpg, jpeg, webp) or file too large",
        },
    ])
    .onSuccess((event) => {
        if ($("#edit_country_id").length) {
            const selected = $("#edit_country_id").val();
            if (!selected || (Array.isArray(selected) && selected.length === 0)) {
                Swal.fire({
                    icon: "warning",
                    title: "Country Required",
                    text: "Please select at least one country for the gift category."
                });
                return false;
            }
        }
        if (invalidEditCategoryImage) {
            editValidator.showErrors({
                "#edit_categoryImage": "*Image resolution must be exactly 512 × 512",
            });
            return;
        }
        $(".edit_submit_btn").attr("disabled", true).html("Uploading...");
        editCategoriesFormSubmit(event);
    });

// ─── Country Helper ───────────────────────────────────────────────────────────

function resolveCountryNames(countryIdStr) {
    if (!countryIdStr) return '-';
    return countryIdStr.toString().split(',')
        .filter(id => !window.allowedCountryIds || window.allowedCountryIds.includes(parseInt(id)))
        .map(id => {
            const found = window.countries ? window.countries.find(c => c.id == id) : null;
            return found ? found.name : id;
        })
        .join(', ') || '-';
}

// ─── Grid.js ─────────────────────────────────────────────────────────────────

const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Country",
        "Category Name",
        "Category Image",
        { name: "Action", sort: false },
    ],
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: categories.map((cat, index) => buildRow(cat, index)),
    style: {
        table: { border: "1px solid #ccc" },
        th: { "background-color": "rgba(0, 0, 0, 0.1)", color: "#000", "border-bottom": "3px solid #ccc", "text-align": "center", "border-right": "0.5px solid #ccc" },
        td: { "text-align": "center", "border-right": "0.5px solid #ccc", "border-bottom": "0.5px solid #ccc" },
    },
});

gridNew.render(document.getElementById("table-gridjs"));

function buildRow(cat, index) {
    return [
        index + 1,
        resolveCountryNames(cat.country_id),
        cat.category_name,
        gridjs.html(`
            <div class="text-center">
                <img class="category_image_el" src="/images/${cat.category_image}" alt="category_image${index}" style="width:50px"/>
            </div>
        `),
        gridjs.html(
            cat.status == 1
                ? `<div>
                    <button data-bs-toggle="modal"
                        data-categoriesid="${cat.id}"
                        data-categoriesname="${cat.category_name}"
                        data-country="${cat.country_id}"
                        data-categoriesimage="${cat.category_image}"
                        data-bs-target="#editCategoriesModal"
                        class="btn btn-secondary edit_btn">Edit</button>
                   </div>`
                : `<div>
                    <button data-bs-toggle="modal"
                        data-categoriesid="${cat.id}"
                        data-categoriesname="${cat.category_name}"
                        data-country="${cat.country_id}"
                        data-categoriesimage="${cat.category_image}"
                        data-bs-target="#editCategoriesModal"
                        class="btn btn-secondary edit_btn">Edit</button>
                    <button class="btn btn-danger delete_btn" data-categoriesid="${cat.id}">Delete</button>
                   </div>`
        ),
    ];
}

function gridjsReRender(categories) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: categories.map((cat, index) => buildRow(cat, index)),
        })
        .forceRender();
}

// ─── AJAX Form Submissions ────────────────────────────────────────────────────

function parseGiftCategoryError(jqXHR, fallbackMsg) {
    if (jqXHR && jqXHR.responseJSON) {
        if (jqXHR.responseJSON.errors) {
            return Object.values(jqXHR.responseJSON.errors).flat().join("<br>");
        }
        if (jqXHR.responseJSON.message) {
            return jqXHR.responseJSON.message;
        }
    }
    return fallbackMsg || "Something went wrong. Please try again.";
}

function addCategoriesFormSubmit(e) {
    const formEl = document.getElementById("addCategoriesForm");
    const formdata = new FormData(formEl);
    $.ajax({
        url: giftUrls.store,
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            formEl.reset();
            if ($("#add_country_id").length) {
                $("#add_country_id").val(null).trigger("change");
            }
            $("#addCategoriesModal").modal('hide');
            $(".product_remove_btn").trigger("click");
            $(".modal-backdrop").remove();
            gridjsReRender(response.categories);
            Swal.fire({
                icon: "success",
                title: "Added",
                text: response.message || "Gift category added successfully."
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            const message = parseGiftCategoryError(jqXHR, "Failed to add gift category. Please try again.");
            Swal.fire({ icon: "error", title: "Error", html: message });
        },
    });
}

function editCategoriesFormSubmit(e) {
    const formEl = document.getElementById("editCategoriesForm");
    const formdata = new FormData(formEl);
    $.ajax({
        url: giftUrls.update + "/" + $("#edit_categories_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            formEl.reset();
            $("#editCategoriesModal").modal('hide');
            $(".modal-backdrop").remove();
            $(".edit_product_remove_btn").trigger("click");
            gridjsReRender(response.categories);
            Swal.fire({
                icon: "success",
                title: "Updated",
                text: response.message || "Gift category updated successfully."
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            const message = parseGiftCategoryError(jqXHR, "Failed to update gift category. Please try again.");
            Swal.fire({ icon: "error", title: "Error", html: message });
        },
    });
}

// Reset add modal state when opened
$('#addCategoriesModal').on('show.bs.modal', function () {
    const form = document.getElementById("addCategoriesForm");
    if (form) form.reset();
    if ($("#add_country_id").length) {
        $("#add_country_id").val(null).trigger("change");
    }
    $(".product_remove_btn").trigger("click");
});

// ─── Event Handlers ───────────────────────────────────────────────────────────

$(function () {
    // Edit button — populate modal + set select2 country
    $(document).on("click", ".edit_btn", function () {
        const imagePath = $(this).attr("data-categoriesimage");
        const country   = $(this).attr("data-country"); // e.g. "3" or "3,4"

        $("#edit_categories_id").val($(this).attr("data-categoriesid"));
        $("#edit_categoriesname").val($(this).attr("data-categoriesname"));
        $("#edit_country_id_hidden").val(country);

        // Set select2 multi-select with the stored country IDs
        const countryArray = country ? country.toString().split(',').map(s => s.trim()) : [];
        setTimeout(() => {
            $("#edit_country_id").val(countryArray).trigger("change");
        }, 200);

        $(".edit_preview_image").attr("src", `/images/${imagePath}`);
    });

    // Delete button
    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-categoriesid");
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
                        gridjsReRender(response.categories);
                        Swal.fire("Deleted!", "Records Deleted Successfully.", "success");
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        const message = parseGiftCategoryError(jqXHR, "Failed to delete gift category.");
                        Swal.fire({ icon: "error", title: "Delete Failed", html: message });
                    },
                });
            }
        });
    });

    // ─── Add image preview ────────────────────────────────────────────────────
    const backupHtml = $(".preview-container").html();
    $("#add_categoryImage").on("change", function () {
        var file = $(this)[0].files[0];
        if (file && file.type.match("image.*")) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = $("<img>").attr("src", e.target.result).css("max-width", "100px");
                var removeBtn = $("<button>").addClass("btn btn-danger product_remove_btn mt-2").text("Remove");
                $(".preview-container").empty().append(img).append(removeBtn);
                removeBtn.on("click", function (e) {
                    e.preventDefault();
                    $(".preview-container").html(backupHtml);
                    $("#add_categoryImage").val("");
                });
            };
            reader.readAsDataURL(file);
        }
    });

    // ─── Edit image preview ───────────────────────────────────────────────────
    const editBackupHtml = $(".edit_preview-container").html();
    $("#edit_categoryImage").on("change", function () {
        var file = $(this)[0].files[0];
        if (file && file.type.match("image.*")) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = $("<img>").attr("src", e.target.result).css("max-width", "100px");
                var removeBtn = $("<button>").addClass("btn btn-danger edit_product_remove_btn mt-2").text("Remove");
                $(".edit_preview-container").empty().append(img).append(removeBtn);
                removeBtn.on("click", function (e) {
                    e.preventDefault();
                    $(".edit_preview-container").html(editBackupHtml);
                    $("#edit_categoryImage").val("");
                });
            };
            reader.readAsDataURL(file);
        }
    });
});