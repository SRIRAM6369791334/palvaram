// SUB CATEGORY PAGE


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

// ADD FORM VALIDATION
const addValidator = new JustValidate("#addSubCategoriesForm", {
    validateBeforeSubmitting: true,
});

const editValidator = new JustValidate("#editSubCategoriesForm", {
    validateBeforeSubmitting: true,
});

// Add this function at the top of the file
function loadCategories(countryId, selectId) {
    const select = document.getElementById(selectId);
    select.innerHTML = '<option value="" disabled selected>Select Category</option>';

    let filtered = window.allCategories;

    if (countryId) {
        filtered = window.allCategories.filter(cat => {
            const ids = cat.country_id ? cat.country_id.toString().split(',').map(id => parseInt(id.trim())) : [];
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

// For single country users (no dropdown visible), load categories on page load
$(document).ready(function() {
    if (window.allowedCountryIds && window.allowedCountryIds.length === 1) {
        loadCategories(window.allowedCountryIds[0], 'add_categoryname');
        loadCategories(window.allowedCountryIds[0], 'edit_categoryname');
    }
});

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

        addSubCategoriesForm(event);
    });

// EDIT FORM VALIDATION

editValidator
    .addField("#edit_categoryname", [
        {
            rule: "required",
            errorMessage: "*Categories Name Field is required",
        },
    ])
    .addField("#edit_subcategoriesname", [
        {
            rule: "required",
            errorMessage: "*Sub Categories Name Field is required",
        },
        {
            rule: "minLength",
            value: 3,
            errorMessage:
                "*Sub Categories Name should be at least 3 character long",
        },
        {
            rule: "maxLength",
            value: 50,
            errorMessage:
                "*Sub Categories Name should be at Maximum 50 character long",
        },
    ])
    .onSuccess((event) => {
        $(".edit_submit_btn").attr("disabled", "true");
        $(".edit_submit_btn").html("Uploading.....");
        editSubCategoriesFormSubmit(event);
    });

// SUB CATEGORY GRID TABLE

const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Country",
        "Category Name",
        "Subcategory Name",
        {
            name: "Action",
            sort: false,
        },
    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: subcate.map((subcate, index) => {
        // return [index + 1, subcate.subcategory_name, subcate.category_name];
        return [
            index + 1,
            subcate.country?.name,
            subcate.category_display,
            subcate.subcategory_name, gridjs.html(
                subcate.status == 1
                    ? `<div> <button data-bs-toggle="modal"
                data-subcategoriesid ="${subcate.id}"
                data-categoriesname="${subcate.category_name}"
                data-subcategoriesname="${subcate.subcategory_name}" data-country="${subcate.country_id}" 
                data-subcategoriesimage ="${subcate.subcategory_image}" data-bs-target="#editSubCategoriesModal"  class="btn btn-secondary edit_btn ">Edit</button>

                </div>`
                    : `<div> <button data-bs-toggle="modal"
                data-subcategoriesid ="${subcate.id}"
                data-categoriesname="${subcate.category_name}"
                data-subcategoriesname="${subcate.subcategory_name}" data-country="${subcate.country_id}" 
                data-subcategoriesimage ="${subcate.subcategory_image}" data-bs-target="#editSubCategoriesModal"  class="btn btn-secondary edit_btn ">Edit</button>

                <button class="btn btn-danger delete_btn" data-subcategoriesid = ${subcate.id}>Delete</button> </div>`
            ),
        ];
    }),
    style: {
        table: {
            border: "1px solid #ccc",
        },
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

function gridjsReRender(subcate) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: subcate.map((subcate, index) => {
                // return [
                //     index + 1,
                //     subcate.subcategory_name,
                //     subcate.category_name,
                // ];
                return [
                    index + 1,
                    subcate.country?.name,
                    subcate.category_display,
                    subcate.subcategory_name, gridjs.html(
                        subcate.status == 1
                            ? `<div> <button data-bs-toggle="modal"
                        data-subcategoriesid ="${subcate.id}"
                        data-categoriesname="${subcate.category_name}"
                        data-subcategoriesname="${subcate.subcategory_name}" data-country="${subcate.country_id}" 
                        data-subcategoriesimage ="${subcate.subcategory_image}" data-bs-target="#editSubCategoriesModal"  class="btn btn-secondary edit_btn ">Edit</button>

                        </div>`
                            : `<div> <button data-bs-toggle="modal"
                        data-subcategoriesid ="${subcate.id}"
                        data-categoriesname="${subcate.category_name}" 
                        data-subcategoriesname="${subcate.subcategory_name}" data-country="${subcate.country_id}" 
                        data-subcategoriesimage ="${subcate.subcategory_image}" data-bs-target="#editSubCategoriesModal"  class="btn btn-secondary edit_btn ">Edit</button>

                        <button class="btn btn-danger delete_btn" data-subcategoriesid = ${subcate.id}>Delete</button> </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

// ADD SUB CATEGORY FORM SUBMISSION

function addSubCategoriesForm(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "subcategories",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn").removeAttr("disabled");
            $(".add_submit_btn").html("Submit");
            const updatedSubCategories = response.subcategories;
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
            $("#addSubCategoriesModal").hide();
            $(".product_remove_btn").trigger("click");
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";

            gridjsReRender(updatedSubCategories);
            Swal.fire("Added", "Records Added Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn").removeAttr("disabled");
            $(".add_submit_btn").removeAttr("disabled");
            $(".edit_submit_btn").html("Update");
            $(".add_submit_btn").html("Submit");
            console.log(textStatus + ": " + errorThrown);

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

// EDIT SUB CATEGORY

function editSubCategoriesFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updateSubCategories/" + $("#edit_subcategories_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedSubCategories = response.subcategories;
            $("#editSubCategoriesForm")[0].reset();
            $("#editSubCategoriesModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedSubCategories);
            $(".edit_submit_btn").removeAttr("disabled");
            $(".edit_submit_btn").html("Update");
            $(".edit_product_remove_btn").trigger("click");
            Swal.fire("Updated", "Records Updated  Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn").removeAttr("disabled");
            $(".add_submit_btn").removeAttr("disabled");
            $(".edit_submit_btn").html("Update");
            $(".add_submit_btn").html("Submit");
            console.log(textStatus + ": " + errorThrown);

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

$(function () {
    $(".bx-show").hide();
    $(".icon").on("click", function () {
        const parentEl = $(this).closest(".input-group");
        var input = $(this).closest(".input-group").find("input");
        if (input.attr("type") === "password") {
            input.attr("type", "text");
            $(parentEl).find(".bx-hide").hide();
            $(parentEl).find(".bx-show").show();
        } else {
            input.attr("type", "password");
            $(parentEl).find(".bx-show").hide();
            $(parentEl).find(".bx-hide").show();
        }
    });

    // APPENDING VALUES TO EDIT FORM

$(document).on("click", ".edit_btn", function () {
    const imagePath = $(this).attr("data-subcategoriesimage");
    const country   = $(this).attr("data-country");
    const categoryId = $(this).attr("data-categoriesname");

    $("#edit_subcategories_id").val($(this).attr("data-subcategoriesid"));
    $("#edit_country_id_hidden").val(country);
    $("#edit_country_id").val(country);
    $("#edit_subcategoriesname").val($(this).attr("data-subcategoriesname"));
    $(".edit_preview_image").attr("src", `images/${imagePath}`);

    // Load categories for this country then set the value
    loadCategories(country, 'edit_categoryname');
    setTimeout(() => {
        $("#edit_categoryname").val(categoryId);
    }, 100);
});

    // SUB CATEGORY DELETION

    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-subcategoriesid");
        Swal.fire({
            title: "Are you sure?",
            text: "If this subcategory has products, they will also be soft-deleted. You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "destroySubCategories/" + id,
                    method: "post",
                    dataType: "json",
                    success: function (response) {
                        const updatedSubCategories = response.subcategories;
                        gridjsReRender(updatedSubCategories);
                        Swal.fire(
                            "Deleted!",
                            "Records Deleted Successfully.",
                            "success"
                        );
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $(".edit_submit_btn").removeAttr("disabled");
                        $(".add_submit_btn").removeAttr("disabled");
                        $(".edit_submit_btn").html("Update");
                        $(".add_submit_btn").html("Submit");
                        console.log(textStatus + ": " + errorThrown);

                        Swal.fire(
                            textStatus.toUpperCase(),
                            errorThrown,
                            "warning"
                        );
                    },
                });
            }
        });
    });
});



// Form submission
$("#editSubCategoriesForm").on("submit", function (e) {
    // Validate form fields here if needed

    // Proceed with form submission
    return true;
});
// $(function () {
//     const backupHtml = $(".edit_preview-container").html();

//     // Listen for changes to the input field
//     $("#edit_subcategoryImage").on("change", function () {
//         // Get the selected file
//         var file = $(this)[0].files[0];

//         // Check if the file is an image
//         if (file.type.match("image.*")) {
//             // Create a new FileReader object
//             var reader = new FileReader();

//             // Set up the FileReader to load the image
//             reader.onload = function (e) {
//                 // Create a new image element
//                 var img = $("<img>").attr("src", e.target.result);

//                 // Create a remove button
//                 var removeBtn = $("<button>")
//                     .addClass("btn btn-danger edit_product_remove_btn mt-2")
//                     .text("Remove");

//                 // Add the image and remove button to the preview container
//                 $(".edit_preview-container")
//                     .empty()
//                     .append(img)
//                     .append(removeBtn);

//                 // Listen for clicks on the remove button
//                 removeBtn.on("click", function (e) {
//                     e.preventDefault();

//                     // Remove the image from the preview container
//                     $(".edit_preview-container").html(backupHtml);
//                     // Clear the input field
//                     $("#edit_subcategoryImage").val("");
//                 });
//             };

//             // Read the selected file as a data URL
//             reader.readAsDataURL(file);
//         }
//     });
// });