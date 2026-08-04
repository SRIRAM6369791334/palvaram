const addValidator = new JustValidate("#addPromotionForm", {
    validateBeforeSubmitting: true,
});
const editValidator = new JustValidate("#editPromotionForm", {
    validateBeforeSubmitting: true,
});

let invalidAddImage = false;
let invalidEditImage = false;

if ($("#add_country_id").length) {
addValidator
    .addField("#add_country_id", [{ rule: "required", errorMessage: "*Country is required" }])
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
                if (invalidAddImage) return false;
                return new Promise((resolve) => {
                    const file = fields["#add_bg_image"].elem.files[0];
                    if (!file || !file.type.match(/^image\//)) return resolve(false);

                    const img = new Image();
                    img.onload = () => {
                        resolve(img.width === 597 && img.height === 354);
                    };
                    img.onerror = () => resolve(false);
                    img.src = URL.createObjectURL(file);
                });
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
                    if (invalidEditImage) return false;

                    const file = fields["#edit_bg_image"]?.elem?.files?.[0];

                    if (!file) return true;

                    if (!file.type.match(/^image\//)) return false;

                    return new Promise((resolve) => {
                        const img = new Image();
                        img.onload = () => resolve(img.width === 597 && img.height === 354);
                        img.onerror = () => resolve(false);
                        img.src = URL.createObjectURL(file);
                    });
                },
                errorMessage: "*Image resolution must be exactly 597 × 354 px",
            }
        ])
        .onSuccess((event) => {
            submitForm(event, "update");
        });

    // Add country validation only if dropdown exists (super admin)
    if ($("#edit_country_id").length) {
        editValidator.addField("#edit_country_id", [{ rule: "required", errorMessage: "*Country is required" }]);
    }
}

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
    data: promotions.map((promo, index) => {
        return [
            index + 1,
            gridjs.html(`<div class="text-center sortable-row" data-id="${promo.id}">
                <img class="gridImage" src="images/${promo.bg_image}" style="width:100px; height:auto; border-radius:8px;"/>
            </div>`),
            gridjs.html(`<strong>Country:</strong> ${promo.country?.name || '-'}<br><strong>Badge:</strong> ${promo.badge_text || '-'}<br><strong>Title:</strong> ${promo.main_title || '-'}<br><strong>Highlight:</strong> ${promo.highlight_text || '-'}<br><strong>Link:</strong> ${promo.link_url || '-'}`),
            gridjs.html(`<div> 
                <button data-bs-toggle="modal" data-bs-target="#editPromotionModal" class="btn btn-secondary edit_btn" 
                    data-id="${promo.id}" data-badge="${promo.badge_text}" data-title="${promo.main_title}" data-country="${promo.country_id}" 
                    data-highlight="${promo.highlight_text}" data-link="${promo.link_url}" data-image="${promo.bg_image}">Edit</button> 
                 
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
        data: data.map((promo, index) => {
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
                        data-badge="${promo.badge_text}"
                        data-title="${promo.main_title}"
                        data-country="${promo.country_id}"
                        data-highlight="${promo.highlight_text}"
                        data-link="${promo.link_url}"
                        data-image="${promo.bg_image}">Edit</button>
                </div>`),
            ];
        }),
    }).forceRender();

    // ✅ Re-init sort after re-render
    setTimeout(renderSort, 300);
}

function submitForm(e, type) {
    e.preventDefault();

    const formdata = new FormData(e.target);
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
            }, 400); // slight delay so modal finishes hiding

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
            } else {
                errorMessage += ": " + xhr.statusText;
            }
            Swal.fire("Error", errorMessage, "error");
        }
    });
}

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
    $("#edit_preview_image").attr("src", "images/" + image).show();
    invalidEditImage = false; // reset flag on open
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

function renderSort() {
    new Sortable(document.querySelector("tbody"), {
        handle: ".sortable-row",
        animation: 150,
        onEnd: function () {
            const newOrder = Array.from(document.querySelectorAll(".sortable-row")).map(row => row.dataset.id);
            $.post("update-home-promotions-order", { order: newOrder, _token: window.csrfToken });
        },
    });
}

renderSort();

// Image Preview Logic
let originalAddPreview = $(".preview-container").html();
let originalEditPreview = $(".edit_preview-container").html();

$("#add_bg_image").on("change", function () {
    var file = $(this)[0].files[0];
    if (file && file.type.match("image.*")) {
        var imgCheck = new Image();
        imgCheck.onload = function () {
            if (imgCheck.width !== 597 || imgCheck.height !== 354) {
                invalidAddImage = true;
                $("#add_bg_image").val(""); // Need to clear it so we don't accidentally submit it later if validation gets removed
                $(".preview-container").html(originalAddPreview);
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Image Size',
                    text: 'Image resolution must be exactly 597 × 354 px!',
                });
                addValidator.showErrors({
                    "#add_bg_image": "*Image resolution must be exactly	597 × 354 px!"
                });
                return;
            }

            invalidAddImage = false;
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = $("<img>").attr("src", e.target.result).css({ "max-width": "100%", "height": "auto", "border-radius": "8px" });
                var removeBtn = $("<button>").addClass("btn btn-danger product_remove_btn mt-2").text("Remove");
                $(".preview-container").empty().append(img).append(removeBtn);
                $(".product_remove_btn").on("click", function (e) {
                    e.preventDefault();
                    $(".preview-container").html(originalAddPreview);
                    $("#add_bg_image").val("");
                });
            };
            reader.readAsDataURL(file);
        };
        imgCheck.src = URL.createObjectURL(file);
    }
});

$("#edit_bg_image").on("change", function () {
    var file = $(this)[0].files[0];
    if (file && file.type.match("image.*")) {
        var imgCheck = new Image();
        imgCheck.onload = function () {
            if (imgCheck.width !== 597 || imgCheck.height !== 354) {
                invalidEditImage = true;
                $("#edit_bg_image").val(""); // Clear it so it doesn't get submitted
                $(".edit_preview-container").html(originalEditPreview);
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Image Size',
                    text: 'Image resolution must be exactly 597 × 354 px!',
                });
                editValidator.showErrors({
                    "#edit_bg_image": "*Image resolution must be exactly	597 × 354 px!"
                });
                return;
            }

            invalidEditImage = false;
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = $("<img>").attr("src", e.target.result).css({ "max-width": "100%", "height": "auto", "border-radius": "8px" });
                var removeBtn = $("<button>").addClass("btn btn-danger edit_product_remove_btn mt-2").text("Remove");
                $(".edit_preview-container").empty().append(img).append(removeBtn);
                $(".edit_product_remove_btn").on("click", function (e) {
                    e.preventDefault();
                    $(".edit_preview-container").html(originalEditPreview);
                    $("#edit_bg_image").val("");
                });
            };
            reader.readAsDataURL(file);
        };
        imgCheck.src = URL.createObjectURL(file);
    }
});
