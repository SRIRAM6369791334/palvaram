// ==============================================================================
// Pal Varnan Dashboard - Home Promotions (4 Banners) Script
// Resolves Sortable dependency, JustValidate boolean returns, and Multi-submit spam
// ==============================================================================

const addValidator = new JustValidate("#addPromotionForm", {
    validateBeforeSubmitting: true,
});
const editValidator = new JustValidate("#editPromotionForm", {
    validateBeforeSubmitting: true,
});

let invalidAddImage = false;
let invalidEditImage = false;
let isSubmittingPromo = false;

// ------------------------------------------------------------------------------
// Form Validation Setup
// ------------------------------------------------------------------------------
if ($("#add_country_id").length) {
    addValidator.addField("#add_country_id", [{ rule: "required", errorMessage: "*Country is required" }]);
}

if ($("#addPromotionForm").length) {
    addValidator
        .addField("#add_badge_text", [{ rule: "required", errorMessage: "*Badge Text is required" }])
        .addField("#add_main_title", [{ rule: "required", errorMessage: "*Main Title is required" }])
        .addField("#add_highlight_text", [{ rule: "required", errorMessage: "*Highlight Text is required" }])
        .addField("#add_link_url", [{ rule: "required", errorMessage: "*Link URL is required" }])
        .addField("#add_bg_image", [
            {
                rule: "minFilesCount",
                value: 1,
                errorMessage: "*Upload Background Image",
            },
            {
                validator: (value, fields) => {
                    const file = fields["#add_bg_image"]?.elem?.files?.[0];
                    if (!file) return false;
                    return !invalidAddImage;
                },
                errorMessage: "*Image resolution must be exactly 597 × 354 px",
            }
        ])
        .onSuccess((event) => {
            submitForm(event, "store");
        });
}

if ($("#editPromotionForm").length) {
    editValidator
        .addField("#edit_badge_text", [{ rule: "required", errorMessage: "*Badge Text is required" }])
        .addField("#edit_main_title", [{ rule: "required", errorMessage: "*Main Title is required" }])
        .addField("#edit_highlight_text", [{ rule: "required", errorMessage: "*Highlight Text is required" }])
        .addField("#edit_link_url", [{ rule: "required", errorMessage: "*Link URL is required" }])
        .addField("#edit_bg_image", [
            {
                validator: (value, fields) => {
                    const file = fields["#edit_bg_image"]?.elem?.files?.[0];
                    if (!file) return true; // Optional when editing
                    return !invalidEditImage;
                },
                errorMessage: "*Image resolution must be exactly 597 × 354 px",
            }
        ])
        .onSuccess((event) => {
            submitForm(event, "update");
        });

    if ($("#edit_country_id").length) {
        editValidator.addField("#edit_country_id", [{ rule: "required", errorMessage: "*Country is required" }]);
    }
}

// ------------------------------------------------------------------------------
// GridJS Data Table
// ------------------------------------------------------------------------------
const gridNew = new gridjs.Grid({
    columns: [
        { name: "S.NO", sort: true },
        { name: "Image", sort: false },
        { name: "Text Details", sort: false },
        { name: "Action", sort: false },
    ],
    sort: true,
    search: true,
    pagination: {
        limit: 10,
    },
    data: (window.promotions || []).map((promo, index) => {
        return [
            index + 1,
            gridjs.html(`<div class="text-center sortable-row" data-id="${promo.id}">
                <img class="gridImage" src="images/${promo.bg_image}" style="width:100px; height:auto; border-radius:8px;"/>
            </div>`),
            gridjs.html(`<strong>Country:</strong> ${promo.country?.name || '-'}<br><strong>Badge:</strong> ${promo.badge_text || '-'}<br><strong>Title:</strong> ${promo.main_title || '-'}<br><strong>Highlight:</strong> ${promo.highlight_text || '-'}<br><strong>Link:</strong> ${promo.link_url || '-'}`),
            gridjs.html(`<div> 
                <button data-bs-toggle="modal" data-bs-target="#editPromotionModal" class="btn btn-secondary edit_btn" 
                    data-id="${promo.id}" data-badge="${promo.badge_text || ''}" data-title="${promo.main_title || ''}" data-country="${promo.country_id || ''}" 
                    data-highlight="${promo.highlight_text || ''}" data-link="${promo.link_url || ''}" data-image="${promo.bg_image || ''}">Edit</button> 
            </div>`),
        ];
    }),
    style: {
        table: { border: "1px solid #ccc" },
        th: {
            "background-color": "rgba(0, 0, 0, 0.1)",
            color: "#000",
            "border-bottom": "3px solid #ccc",
            "text-align": "center",
            "border-right": "0.5px solid #ccc"
        },
        td: {
            "text-align": "center",
            "border-right": "0.5px solid #ccc",
            "border-bottom": "0.5px solid #ccc"
        },
    },
});

gridNew.render(document.getElementById("table-gridjs"));

function gridjsReRender(data) {
    gridNew.updateConfig({
        data: (data || []).map((promo, index) => {
            return [
                index + 1,
                gridjs.html(`<div class="text-center sortable-row" data-id="${promo.id}">
                    <img class="gridImage" src="images/${promo.bg_image}" style="width:100px; height:auto; border-radius:8px;"/>
                </div>`),
                gridjs.html(`<strong>Country:</strong> ${promo.country?.name || '-'}<br>
                    <strong>Badge:</strong> ${promo.badge_text || '-'}<br>
                    <strong>Title:</strong> ${promo.main_title || '-'}<br>
                    <strong>Highlight:</strong> ${promo.highlight_text || '-'}<br>
                    <strong>Link:</strong> ${promo.link_url || '-'}`),
                gridjs.html(`<div>
                    <button data-bs-toggle="modal" data-bs-target="#editPromotionModal" class="btn btn-secondary edit_btn"
                        data-id="${promo.id}"
                        data-badge="${promo.badge_text || ''}"
                        data-title="${promo.main_title || ''}"
                        data-country="${promo.country_id || ''}"
                        data-highlight="${promo.highlight_text || ''}"
                        data-link="${promo.link_url || ''}"
                        data-image="${promo.bg_image || ''}">Edit</button>
                </div>`),
            ];
        }),
    }).forceRender();

    setTimeout(renderSort, 300);
}

// ------------------------------------------------------------------------------
// Form Submission with Anti-Multi-Click Protection
// ------------------------------------------------------------------------------
$(document).on("click", ".edit_promo_submit_btn, .add_promo_submit_btn", function (e) {
    if (isSubmittingPromo) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }
});

function submitForm(e, type) {
    if (isSubmittingPromo) return;
    isSubmittingPromo = true;

    const $btn = type === "store" ? $(".add_promo_submit_btn") : $(".edit_promo_submit_btn");
    const originalText = type === "store" ? "Submit" : "Update";
    $btn.prop("disabled", true).addClass("disabled").html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...');

    const formdata = new FormData(e.target || e);
    formdata.append('_token', window.csrfToken);

    const url = type === "store"
        ? "home-promotions"
        : "update-home-promotions/" + $("#edit_promotion_id").val();

    $.ajax({
        url: url,
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": window.csrfToken
        },
        success: function (response) {
            if (response.promotions) {
                gridjsReRender(response.promotions);
            }

            $("#addPromotionModal").modal('hide');
            $("#editPromotionModal").modal('hide');

            setTimeout(function () {
                $("#addPromotionForm")[0].reset();
                $("#editPromotionForm")[0].reset();

                invalidAddImage = false;
                invalidEditImage = false;

                if ($(".preview-container").length) {
                    $(".preview-container").html(originalAddPreview);
                }
                if ($(".edit_preview-container").length) {
                    $(".edit_preview-container").html(originalEditPreview);
                }
            }, 300);

            Swal.fire({
                icon: "success",
                title: "Success",
                text: response.message,
                timer: 1500,
                showConfirmButton: false
            });
        },
        error: function (xhr) {
            console.error("Error submitting form:", xhr.responseText);
            let errorMessage = "Something went wrong";
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                const firstKey = Object.keys(errors)[0];
                errorMessage = errors[firstKey][0];
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            } else {
                errorMessage += ": " + xhr.statusText;
            }
            Swal.fire("Error", errorMessage, "error");
        },
        complete: function () {
            isSubmittingPromo = false;
            $btn.prop("disabled", false).removeClass("disabled").html(originalText);
        }
    });
}

// ------------------------------------------------------------------------------
// Edit Button & Lifecycle
// ------------------------------------------------------------------------------
$(document).on("click", ".edit_btn", function () {
    const id = $(this).data("id");
    const badge = $(this).data("badge");
    const title = $(this).data("title");
    const highlight = $(this).data("highlight");
    const link = $(this).data("link");
    const image = $(this).data("image");
    const country = $(this).data("country");

    $("#edit_promotion_id").val(id);
    $("#edit_badge_text").val(badge);
    $("#edit_main_title").val(title);
    $("#edit_highlight_text").val(highlight);
    $("#edit_link_url").val(link);
    $("#edit_country_id").val(country);
    $("#edit_country_id_hidden").val(country);

    // Clean reset of new image and preview
    $("#edit_bg_image").val("");
    invalidEditImage = false;
    isSubmittingPromo = false;
    $(".edit_promo_submit_btn").prop("disabled", false).removeClass("disabled").html("Update");

    if ($(".edit_preview-container").length) {
        $(".edit_preview-container").html(originalEditPreview);
    }

    if (image) {
        $("#edit_preview_image").attr("src", "images/" + image).show();
    } else {
        $("#edit_preview_image").hide();
    }

    if (typeof editValidator !== "undefined") {
        editValidator.clearErrors();
    }
});

// Modal Close Handlers (Ensures no invalid lingering images)
$("#editPromotionModal").on("hidden.bs.modal", function () {
    $("#edit_bg_image").val("");
    invalidEditImage = false;
    isSubmittingPromo = false;
    $(".edit_promo_submit_btn").prop("disabled", false).removeClass("disabled").html("Update");

    if ($(".edit_preview-container").length) {
        $(".edit_preview-container").html(originalEditPreview);
    }
    if (typeof editValidator !== "undefined") {
        editValidator.clearErrors();
    }
});

$("#addPromotionModal").on("hidden.bs.modal", function () {
    $("#add_bg_image").val("");
    invalidAddImage = false;
    isSubmittingPromo = false;
    $(".add_promo_submit_btn").prop("disabled", false).removeClass("disabled").html("Submit");

    if ($(".preview-container").length) {
        $(".preview-container").html(originalAddPreview);
    }
    if (typeof addValidator !== "undefined") {
        addValidator.clearErrors();
    }
});

$(document).on("click", ".delete_btn", function () {
    const id = $(this).data("id");
    Swal.fire({
        title: "Delete this promotion?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("destroy-home-promotions/" + id, { _token: window.csrfToken }, function (response) {
                gridjsReRender(response.promotions);
                Swal.fire("Deleted!", "Promotion deleted.", "success");
            });
        }
    });
});

// ------------------------------------------------------------------------------
// Drag & Drop Sortable Ordering
// ------------------------------------------------------------------------------
function renderSort() {
    if (typeof Sortable === "undefined") {
        console.warn("Sortable library not loaded yet.");
        return;
    }

    const tbody = document.querySelector("tbody");
    if (!tbody) return;

    new Sortable(tbody, {
        handle: ".sortable-row",
        animation: 150,
        onEnd: function () {
            const newOrder = Array.from(document.querySelectorAll(".sortable-row")).map(row => row.dataset.id);
            $.post("update-home-promotions-order", { order: newOrder, _token: window.csrfToken });
        },
    });
}

// Initialize sortable after DOM is fully ready
$(function () {
    renderSort();
});

// ------------------------------------------------------------------------------
// Image Preview & Dimension Validation Handlers
// ------------------------------------------------------------------------------
const originalAddPreview = $(".preview-container").html();
const originalEditPreview = $(".edit_preview-container").html();

$("#add_bg_image").on("change", function () {
    const file = this.files && this.files[0];
    if (file && file.type.match("image.*")) {
        const imgCheck = new Image();
        const objectUrl = URL.createObjectURL(file);

        imgCheck.onload = function () {
            if (imgCheck.width !== 597 || imgCheck.height !== 354) {
                invalidAddImage = true;
                $("#add_bg_image").val("");
                $(".preview-container").html(originalAddPreview);
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Image Size',
                    text: `Image resolution must be exactly 597 × 354 px! (Selected: ${imgCheck.width} × ${imgCheck.height} px)`,
                });
                addValidator.showErrors({
                    "#add_bg_image": "*Image resolution must be exactly 597 × 354 px!"
                });
                URL.revokeObjectURL(objectUrl);
                return;
            }

            invalidAddImage = false;
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = $("<img>").attr("src", e.target.result).css({ "max-width": "100%", "height": "auto", "border-radius": "8px" });
                const removeBtn = $("<button>").addClass("btn btn-danger product_remove_btn mt-2").text("Remove");
                $(".preview-container").empty().append(img).append(removeBtn);
                $(".product_remove_btn").on("click", function (ev) {
                    ev.preventDefault();
                    $(".preview-container").html(originalAddPreview);
                    $("#add_bg_image").val("");
                    invalidAddImage = false;
                    addValidator.revalidateField("#add_bg_image");
                });
            };
            reader.readAsDataURL(file);
            addValidator.revalidateField("#add_bg_image");
        };

        imgCheck.onerror = function () {
            invalidAddImage = true;
            $("#add_bg_image").val("");
            $(".preview-container").html(originalAddPreview);
            Swal.fire({
                icon: 'error',
                title: 'Invalid Image',
                text: 'Could not load image file.',
            });
            URL.revokeObjectURL(objectUrl);
        };

        imgCheck.src = objectUrl;
    }
});

$("#edit_bg_image").on("change", function () {
    const file = this.files && this.files[0];
    if (file && file.type.match("image.*")) {
        const imgCheck = new Image();
        const objectUrl = URL.createObjectURL(file);

        imgCheck.onload = function () {
            if (imgCheck.width !== 597 || imgCheck.height !== 354) {
                invalidEditImage = true;
                $("#edit_bg_image").val("");
                $(".edit_preview-container").html(originalEditPreview);
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Image Size',
                    text: `Image resolution must be exactly 597 × 354 px! (Selected: ${imgCheck.width} × ${imgCheck.height} px)`,
                });
                editValidator.showErrors({
                    "#edit_bg_image": "*Image resolution must be exactly 597 × 354 px!"
                });
                URL.revokeObjectURL(objectUrl);
                return;
            }

            invalidEditImage = false;
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = $("<img>").attr("src", e.target.result).css({ "max-width": "100%", "height": "auto", "border-radius": "8px" });
                const removeBtn = $("<button>").addClass("btn btn-danger edit_product_remove_btn mt-2").text("Remove");
                $(".edit_preview-container").empty().append(img).append(removeBtn);
                $(".edit_product_remove_btn").on("click", function (ev) {
                    ev.preventDefault();
                    $(".edit_preview-container").html(originalEditPreview);
                    $("#edit_bg_image").val("");
                    invalidEditImage = false;
                    editValidator.revalidateField("#edit_bg_image");
                });
            };
            reader.readAsDataURL(file);
            editValidator.revalidateField("#edit_bg_image");
        };

        imgCheck.onerror = function () {
            invalidEditImage = true;
            $("#edit_bg_image").val("");
            $(".edit_preview-container").html(originalEditPreview);
            Swal.fire({
                icon: 'error',
                title: 'Invalid Image',
                text: 'Could not load image file.',
            });
            URL.revokeObjectURL(objectUrl);
        };

        imgCheck.src = objectUrl;
    }
});
