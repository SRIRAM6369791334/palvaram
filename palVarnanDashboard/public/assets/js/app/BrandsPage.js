const addValidator = new JustValidate("#addBrandForm", {
    validateBeforeSubmitting: true,
});
const editValidator = new JustValidate("#editBrandForm", {
    validateBeforeSubmitting: true,
});

addValidator
    .addField("#add_brand_name", [
        {
            rule: "required",
            errorMessage: "Brand Name is required",
        },
        {
            rule: "minLength",
            value: 2,
            errorMessage: "Name should be at least 2 characters",
        },
    ])
    .onSuccess((event) => {
        if ($("#add_country_id").length) {
            const selected = $("#add_country_id").val();
            if (!selected || (Array.isArray(selected) && selected.length === 0)) {
                Swal.fire({
                    icon: "warning",
                    title: "Country Required",
                    text: "Please select at least one country for the brand."
                });
                return false;
            }
        }
        $(".add_submit_btn").attr("disabled", true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
        addBrandFormSubmit(event);
    });

editValidator
    .addField("#edit_brand_name", [
        {
            rule: "required",
            errorMessage: "Brand Name is required",
        },
        {
            rule: "minLength",
            value: 2,
            errorMessage: "Name should be at least 2 characters",
        },
    ])
    .onSuccess((event) => {
        if ($("#edit_country_id").length) {
            const selected = $("#edit_country_id").val();
            if (!selected || (Array.isArray(selected) && selected.length === 0)) {
                Swal.fire({
                    icon: "warning",
                    title: "Country Required",
                    text: "Please select at least one country for the brand."
                });
                return false;
            }
        }
        $(".edit_submit_btn").attr("disabled", true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...');
        editBrandFormSubmit(event);
    });

const gridBrands = new gridjs.Grid({
    columns: [
        "S.NO",
        "Country",
        "Brand Name",
        {
            name: "Action",
            sort: false,
        },
    ],
    pagination: {
        limit: 10,
    },
    sort: true,
    search: true,
    data: brandsData.map((brand, index) => {
        return [
            index + 1,
            brand.country_id ? brand.country_id.toString().split(',')
                .filter(id => !window.allowedCountryIds || window.allowedCountryIds.includes(parseInt(id)))
                .map(id => {
                    const found = window.countries ? window.countries.find(c => c.id == id) : null;
                    return found ? found.name : id;
                }).join(', ') : '-',
            brand.brand_name,
            gridjs.html(`
                <div class="d-flex justify-content-center">
                    <button data-bs-toggle="modal" 
                        data-brandid="${brand.id}" 
                        data-brandname="${brand.brand_name}" data-country="${brand.country_id}"
                        data-brandimage="${brand.brand_image}" 
                        data-bs-target="#editBrandModal" 
                        class="btn btn-secondary edit_btn edit_brand_trigger">Edit</button>
                    <button class="btn btn-danger delete_btn delete_brand_trigger ms-2" data-brandid="${brand.id}">Delete</button>
                </div>
            `),
        ];
    }),
    style: {
        table: { border: "1px solid #e2e8f0" },
        th: { backgroundColor: "#f8fafc", color: "#475569", borderBottom: "2px solid #e2e8f0", textAlign: "center" },
        td: { textAlign: "center", borderBottom: "1px solid #e2e8f0" },
    },
});

gridBrands.render(document.getElementById("table-gridjs"));

function gridjsReRender(brands) {
    gridBrands.updateConfig({
        data: brands.map((brand, index) => {
            return [
                index + 1,
                brand.country_id ? brand.country_id.toString().split(',')
                    .filter(id => !window.allowedCountryIds || window.allowedCountryIds.includes(parseInt(id)))
                    .map(id => {
                        const found = window.countries ? window.countries.find(c => c.id == id) : null;
                        return found ? found.name : id;
                    }).join(', ') : '-',
                brand.brand_name,
                gridjs.html(`
                    <div class="d-flex justify-content-center">
                        <button data-bs-toggle="modal" 
                            data-brandid="${brand.id}" 
                            data-brandname="${brand.brand_name}" data-country="${brand.country_id}"
                            data-brandimage="${brand.brand_image}" 
                            data-bs-target="#editBrandModal" 
                            class="btn btn-secondary edit_btn edit_brand_trigger">Edit</button>
                        <button class="btn btn-danger delete_btn delete_brand_trigger ms-2" data-brandid="${brand.id}">Delete</button>
                    </div>
                `),
            ];
        }),
    }).forceRender();
}

function parseAjaxError(jqXHR, fallbackMsg) {
    if (jqXHR && jqXHR.responseJSON) {
        if (jqXHR.responseJSON.errors) {
            return Object.values(jqXHR.responseJSON.errors).flat().join('<br>');
        }
        if (jqXHR.responseJSON.message) {
            return jqXHR.responseJSON.message;
        }
    }
    return fallbackMsg || "Something went wrong. Please try again.";
}

function addBrandFormSubmit(e) {
    const formEl = document.getElementById("addBrandForm");
    const formdata = new FormData(formEl);
    
    $.ajax({
        url: "brands",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn").removeAttr("disabled").html("Create Brand");
            formEl.reset();
            if ($("#add_country_id").length) {
                $("#add_country_id").val(null).trigger('change');
            }
            $("#addBrandModal").modal('hide');
            $("#brand-add-preview-container").html('<div class="flex justify-content-center"><div class="text-center"><i class="display-4 col-12 text-muted mdi mdi-cloud-upload"></i></div><div><span class="col-12 text-muted">Upload Image</span></div></div>');
            gridjsReRender(response.brands);
            Swal.fire({
                icon: "success",
                title: "Success",
                text: response.message || "Brand added successfully"
            });
        },
        error: function (jqXHR) {
            $(".add_submit_btn").removeAttr("disabled").html("Create Brand");
            const errorHtml = parseAjaxError(jqXHR, "Failed to add brand. Please try again.");
            Swal.fire({
                icon: "error",
                title: "Error",
                html: errorHtml
            });
        },
    });
}

function editBrandFormSubmit(e) {
    const formEl = document.getElementById("editBrandForm");
    const formdata = new FormData(formEl);
    const id = $("#edit_brand_id").val();
    
    $.ajax({
        url: "updateBrands/" + id,
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update Brand");
            $("#editBrandModal").modal('hide');
            gridjsReRender(response.brands);
            Swal.fire({
                icon: "success",
                title: "Updated",
                text: response.message || "Brand updated successfully"
            });
        },
        error: function (jqXHR) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update Brand");
            const errorHtml = parseAjaxError(jqXHR, "Update failed. Please try again.");
            Swal.fire({
                icon: "error",
                title: "Error",
                html: errorHtml
            });
        },
    });
}

// Reset add modal state when opened
$('#addBrandModal').on('show.bs.modal', function () {
    const form = document.getElementById("addBrandForm");
    if (form) form.reset();
    if ($("#add_country_id").length) {
        $("#add_country_id").val(null).trigger('change');
    }
    $("#brand-add-preview-container").html('<div class="flex justify-content-center"><div class="text-center"><i class="display-4 col-12 text-muted mdi mdi-cloud-upload"></i></div><div><span class="col-12 text-muted">Upload Image</span></div></div>');
});

// Preview Image logic
$(document).on("change", "#add_brand_image", function () {
    const file = this.files[0];
    if (file) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $("#brand-add-preview-container").html(`<img src="${e.target.result}" style="max-height: 150px; border-radius:8px;">`);
        };
        reader.readAsDataURL(file);
    }
});

$(document).on("click", ".edit_brand_trigger", function () {
    const id      = $(this).attr("data-brandid");
    const name    = $(this).attr("data-brandname");
    const image   = $(this).attr("data-brandimage");
    const country = $(this).attr("data-country");

    $("#edit_brand_id").val(id);
    $("#edit_brand_name").val(name);
    $("#edit_country_id_hidden").val(country);

    const countryArray = country ? country.toString().split(',').map(s => s.trim()) : [];
    setTimeout(() => {
        $("#edit_country_id").val(countryArray).trigger("change");
    }, 200);

    $(".edit_preview_image").attr("src", image ? `images/${image}` : 'images/brand_images/default.png');
});

$(document).on("click", ".delete_brand_trigger", function () {
    const id = $(this).attr("data-brandid");
    Swal.fire({
        title: "Delete Brand?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "destroyBrands/" + id,
                method: "POST",
                dataType: "json",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: function (response) {
                    gridjsReRender(response.brands);
                    Swal.fire("Deleted", response.message || "Brand removed successfully", "success");
                },
                error: function (jqXHR) {
                    const errorHtml = parseAjaxError(jqXHR, "Failed to delete brand.");
                    Swal.fire({
                        icon: "error",
                        title: "Delete Failed",
                        html: errorHtml
                    });
                }
            });
        }
    });
});
