// ==============================================================================
// Pal Varnan Dashboard - Banner Images Management Script
// Handles Web Banners (1920x600) & Offer Banners (1510x300)
// Prevents Multi-click / Spam submits, Isolates Previews, and Auto-cleans on close
// ==============================================================================

// Submitting lock flags to prevent double/multiple clicks
let isAddWebSubmitting = false;
let isEditWebSubmitting = false;
let isAddBannerSubmitting = false;
let isEditBannerSubmitting = false;

// Initialize JustValidate instances
const addValidator = new JustValidate("#addBannerImagesForm", {
    validateBeforeSubmitting: true,
});
const editValidator = new JustValidate("#editBannerImagesForm", {
    validateBeforeSubmitting: true,
});
const addValidator1 = new JustValidate("#addWebImagesForm", {
    validateBeforeSubmitting: true,
});
const editValidator1 = new JustValidate("#editWebImagesForm", {
    validateBeforeSubmitting: true,
});

// ------------------------------------------------------------------------------
// Web Banner Validation & Submissions (1920 x 600 px)
// ------------------------------------------------------------------------------

// Add Web Banner Validation Rules
addValidator1
    .addField("#add_webImageImage", [
        {
            rule: "minFilesCount",
            value: 1,
            errorMessage: "*Please upload a banner image",
        },
        {
            rule: "files",
            value: {
                files: {
                    extensions: ["jpeg", "jpg", "png", "webp"],
                    maxSize: 4194304, // 4MB
                },
            },
            errorMessage: "*Unsupported format or file exceeds 4MB",
        },
        {
            validator: (value, fields) => {
                return new Promise((resolve) => {
                    const fileInput = fields["#add_webImageImage"].elem;
                    const file = fileInput?.files?.[0];

                    if (!file || !file.type.startsWith("image/")) {
                        return resolve(false);
                    }

                    const img = new Image();
                    img.onload = () => {
                        const validSize = img.width === 1920 && img.height === 600;
                        resolve(validSize);
                    };
                    img.onerror = () => resolve(false);
                    img.src = URL.createObjectURL(file);
                });
            },
            errorMessage: "*Image size must be exactly 1920px × 600px",
        },
    ])
    .onSuccess((event) => {
        addWebImagesFormSubmit(event);
    });

// Edit Web Banner Validation Rules
editValidator1
    .addField("#edit_webImageImage", [
        {
            rule: "files",
            value: {
                files: {
                    extensions: ["jpeg", "jpg", "png", "webp"],
                    maxSize: 4194304, // 4MB
                },
            },
            errorMessage: "*Unsupported format or file exceeds 4MB",
        },
        {
            validator: (value, fields) => {
                return new Promise((resolve) => {
                    const fileInput = fields["#edit_webImageImage"].elem;
                    const file = fileInput?.files?.[0];

                    // Optional on edit: If no new file selected, existing image remains valid
                    if (!file || !file.type.startsWith("image/")) {
                        return resolve(true);
                    }

                    const img = new Image();
                    img.onload = () => {
                        const isValidSize = img.width === 1920 && img.height === 600;
                        resolve(isValidSize);
                    };
                    img.onerror = () => resolve(false);
                    img.src = URL.createObjectURL(file);
                });
            },
            errorMessage: "*Image size must be exactly 1920px × 600px",
        },
    ])
    .onSuccess((event) => {
        editWebImagesFormSubmit(event);
    });

// ------------------------------------------------------------------------------
// Add Web Banner File Change & Preview Handler
// ------------------------------------------------------------------------------
$("#add_webImageImage").on("change", function () {
    const fileInput = this;
    const file = fileInput.files && fileInput.files[0];

    // Reset preview state
    $("#add_web_preview_wrapper").hide();
    $("#add_web_preview_img").attr("src", "");

    if (!file) {
        return;
    }

    if (!file.type.startsWith("image/")) {
        Swal.fire({
            icon: "error",
            title: "Invalid File",
            text: "Please upload an image file (PNG, JPG, JPEG, WEBP).",
        });
        $(fileInput).val("");
        return;
    }

    if (file.size > 4 * 1024 * 1024) {
        Swal.fire({
            icon: "error",
            title: "File Too Large",
            text: "Image file size should be below 4MB.",
        });
        $(fileInput).val("");
        return;
    }

    const img = new Image();
    const objectUrl = URL.createObjectURL(file);

    img.onload = () => {
        if (img.width !== 1920 || img.height !== 600) {
            Swal.fire({
                icon: "error",
                title: "Invalid Image Size",
                text: `Image must be exactly 1920 x 600 pixels. (Current: ${img.width} x ${img.height} px)`,
            });
            // Wipe invalid file and hide preview
            $(fileInput).val("");
            $("#add_web_preview_wrapper").hide();
            $("#add_web_preview_img").attr("src", "");
            URL.revokeObjectURL(objectUrl);
            return;
        }

        // Show valid preview
        $("#add_web_preview_img").attr("src", objectUrl);
        $("#add_web_preview_wrapper").show();
        addValidator1.revalidateField("#add_webImageImage");
    };

    img.onerror = () => {
        Swal.fire({
            icon: "error",
            title: "Invalid Image",
            text: "Could not load the image. Please upload a valid image file.",
        });
        $(fileInput).val("");
        $("#add_web_preview_wrapper").hide();
        $("#add_web_preview_img").attr("src", "");
        URL.revokeObjectURL(objectUrl);
    };

    img.src = objectUrl;
});

// Remove button for Add Web Banner Preview
$(document).on("click", "#add_web_remove_btn", function (e) {
    e.preventDefault();
    $("#add_webImageImage").val("");
    $("#add_web_preview_wrapper").hide();
    $("#add_web_preview_img").attr("src", "");
    addValidator1.revalidateField("#add_webImageImage");
});

// ------------------------------------------------------------------------------
// Edit Web Banner File Change & Preview Handler
// ------------------------------------------------------------------------------
$("#edit_webImageImage").on("change", function () {
    const fileInput = this;
    const file = fileInput.files && fileInput.files[0];

    // Reset new preview wrapper
    $("#edit_web_new_preview_wrapper").hide();
    $("#edit_web_new_preview_img").attr("src", "");

    if (!file) {
        return;
    }

    if (!file.type.startsWith("image/")) {
        Swal.fire({
            icon: "error",
            title: "Invalid File",
            text: "Please upload an image file (PNG, JPG, JPEG, WEBP).",
        });
        $(fileInput).val("");
        return;
    }

    if (file.size > 4 * 1024 * 1024) {
        Swal.fire({
            icon: "error",
            title: "File Too Large",
            text: "Image file size should be below 4MB.",
        });
        $(fileInput).val("");
        return;
    }

    const img = new Image();
    const objectUrl = URL.createObjectURL(file);

    img.onload = () => {
        if (img.width !== 1920 || img.height !== 600) {
            Swal.fire({
                icon: "error",
                title: "Invalid Image Size",
                text: `Banner image must be exactly 1920 x 600 pixels. (Current: ${img.width} x ${img.height} px)`,
            });
            // Wipe invalid file immediately so it is never retained
            $(fileInput).val("");
            $("#edit_web_new_preview_wrapper").hide();
            $("#edit_web_new_preview_img").attr("src", "");
            URL.revokeObjectURL(objectUrl);
            return;
        }

        // Show valid new preview side-by-side with current image
        $("#edit_web_new_preview_img").attr("src", objectUrl);
        $("#edit_web_new_preview_wrapper").show();
        editValidator1.revalidateField("#edit_webImageImage");
    };

    img.onerror = () => {
        Swal.fire({
            icon: "error",
            title: "Invalid Image File",
            text: "Unable to load the image. Please upload a valid image file.",
        });
        $(fileInput).val("");
        $("#edit_web_new_preview_wrapper").hide();
        $("#edit_web_new_preview_img").attr("src", "");
        URL.revokeObjectURL(objectUrl);
    };

    img.src = objectUrl;
});

// Remove button for Edit Web Banner New Preview
$(document).on("click", "#edit_web_remove_btn", function (e) {
    e.preventDefault();
    $("#edit_webImageImage").val("");
    $("#edit_web_new_preview_wrapper").hide();
    $("#edit_web_new_preview_img").attr("src", "");
    editValidator1.revalidateField("#edit_webImageImage");
});

// ------------------------------------------------------------------------------
// Offer Banner Validation & Handlers (Mobile / Secondary Banners)
// ------------------------------------------------------------------------------
$("#add_bannerImageImage").on("change", function () {
    const file = this.files && this.files[0];
    if (file && file.type.startsWith("image/")) {
        const img = new Image();
        img.src = window.URL.createObjectURL(file);
        img.decode().then(() => {
            if (!(img.width <= 1510 && img.height <= 300)) {
                $(".product_remove_btn").trigger("click");
                addValidator.showErrors({
                    "#add_bannerImageImage": "*Image resolution should be below 1510 * 300",
                });
            }
        });
    }
});

addValidator
    .addField("#add_bannerImageImage", [
        {
            rule: "minFilesCount",
            value: 1,
            errorMessage: "*Upload BannerImage",
        },
        {
            rule: "files",
            value: {
                files: {
                    extensions: ["jpeg", "jpg", "png"],
                    maxSize: 40000000,
                    minSize: 0,
                },
            },
            errorMessage: "*Unsupported File Format or File size should be below 40MB",
        },
        {
            validator: (value, fields) => () => {
                const file = fields["#add_bannerImageImage"].elem?.files?.[0];
                if (file) {
                    const img = new Image();
                    img.src = window.URL.createObjectURL(file);
                    return new Promise((resolve) => {
                        img.decode().then(() => {
                            if (!(img.width <= 1510 && img.height <= 300)) {
                                $(".product_remove_btn").trigger("click");
                            }
                            resolve(img.width <= 1510 && img.height <= 300);
                        });
                    });
                }
                return Promise.resolve(true);
            },
            errorMessage: "*Image resolution should be below 1510 * 300",
        },
    ])
    .onSuccess((event) => {
        addBannerImagesFormSubmit(event);
    });

editValidator.onSuccess((event) => {
    editBannerImagesFormSubmit(event);
});

// ------------------------------------------------------------------------------
// GridJS Data Tables
// ------------------------------------------------------------------------------
const gridNew = new gridjs.Grid({
    columns: [
        { name: "Banner Image", sort: false },
        { name: "Action", sort: false },
    ],
    sort: true,
    search: false,
    data: (window.bannerImages || []).map((bannerImages, index) => {
        return [
            gridjs.html(
                `
                <div class="text-center sortable-row" data-id="${bannerImages.id}">
                    <img class="bannerImage_image_el gridImage" src="images/${bannerImages.banner_image}" alt="banner_image_${index}"/>
                </div>
                `
            ),
            gridjs.html(
                `<div>
                    <button data-bs-toggle="modal"
                        data-bannerImagesid="${bannerImages.id}"
                        data-bannerImagesimage="${bannerImages.banner_image}"
                        data-bs-target="#editBannerImagesModal"
                        class="btn btn-secondary edit_btn">Edit</button>
                    <button class="btn btn-danger delete_btn" data-bannerImagesid="${bannerImages.id}">Delete</button>
                </div>`
            ),
        ];
    }),
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

function gridjsReRender(bannerImages) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: bannerImages.map((bannerImages, index) => {
                return [
                    gridjs.html(
                        `
                        <div class="text-center sortable-row" data-id="${bannerImages.id}">
                            <img class="bannerImage_image_el gridImage" src="images/${bannerImages.banner_image}" alt="banner_image_${index}"/>
                        </div>
                        `
                    ),
                    gridjs.html(
                        `<div>
                            <button data-bs-toggle="modal"
                                data-bannerImagesid="${bannerImages.id}"
                                data-bannerImagesimage="${bannerImages.banner_image}"
                                data-bs-target="#editBannerImagesModal"
                                class="btn btn-secondary edit_btn">Edit</button>
                            <button class="btn btn-danger delete_btn" data-bannerImagesid="${bannerImages.id}">Delete</button>
                        </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

// Web Banner GridJS Table
const gridNew1 = new gridjs.Grid({
    columns: [
        { name: "Web Banner Image", sort: false },
        { name: "Action", sort: false },
    ],
    sort: true,
    search: false,
    data: (window.webbannerImages || []).map((webbannerImages, index) => {
        return [
            gridjs.html(
                `
                <div class="text-center sortable-row" data-id="${webbannerImages.id}">
                    <img class="bannerImage_image_el gridImage" src="images/${webbannerImages.image}" alt="web_banner_${index}"/>
                </div>
                `
            ),
            gridjs.html(
                `<div>
                    <button data-bs-toggle="modal"
                        data-webbannerImagesid="${webbannerImages.id}"
                        data-webbannerImagesimage="${webbannerImages.image}"
                        data-bs-target="#editWebImagesModal"
                        class="btn btn-secondary editweb_btn">Edit</button>
                    <button class="btn btn-danger deleteweb_btn" data-webbannerImagesid="${webbannerImages.id}">Delete</button>
                </div>`
            ),
        ];
    }),
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

gridNew1.render(document.getElementById("table1-gridjs"));

function gridjsReRender1(webbannerImages) {
    if (gridNew1) gridNew1.config.plugin.remove("pagination");
    if (gridNew1) gridNew1.config.plugin.remove("search");
    gridNew1
        .updateConfig({
            data: webbannerImages.map((webbannerImages, index) => {
                return [
                    gridjs.html(
                        `
                        <div class="text-center sortable-row" data-id="${webbannerImages.id}">
                            <img class="bannerImage_image_el gridImage" src="images/${webbannerImages.image}" alt="web_banner_${index}"/>
                        </div>
                        `
                    ),
                    gridjs.html(
                        `<div>
                            <button data-bs-toggle="modal"
                                data-webbannerImagesid="${webbannerImages.id}"
                                data-webbannerImagesimage="${webbannerImages.image}"
                                data-bs-target="#editWebImagesModal"
                                class="btn btn-secondary editweb_btn">Edit</button>
                            <button class="btn btn-danger deleteweb_btn" data-webbannerImagesid="${webbannerImages.id}">Delete</button>
                        </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

// ------------------------------------------------------------------------------
// AJAX Form Submissions (With Anti-Spam / Multi-Click Protection)
// ------------------------------------------------------------------------------

// Double-click interceptors on submit buttons
$(document).on("click", ".addweb_submit_btn", function (e) {
    if (isAddWebSubmitting) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }
});

$(document).on("click", ".editweb_submit_btn", function (e) {
    if (isEditWebSubmitting) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }
});

$(document).on("click", ".add_submit_btn", function (e) {
    if (isAddBannerSubmitting) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }
});

$(document).on("click", ".edit_submit_btn", function (e) {
    if (isEditBannerSubmitting) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }
});

// 1. Add Web Banner
function addWebImagesFormSubmit(e) {
    if (isAddWebSubmitting) return;
    isAddWebSubmitting = true;

    const $btn = $(".addweb_submit_btn");
    $btn.prop("disabled", true).addClass("disabled").html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Uploading...');

    const formdata = new FormData(e.target || e);
    $.ajax({
        url: "bannerwebImages",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") || window.csrfToken
        },
        success: function (response) {
            const updatedBannersImages = response.webbannerImages;
            $("#addWebImagesForm")[0].reset();
            $("#add_webImageImage").val("");
            $("#add_web_preview_wrapper").hide();
            $("#add_web_preview_img").attr("src", "");
            $("#addWebImagesModal").modal("hide");
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";

            gridjsReRender1(updatedBannersImages);
            Swal.fire("Added", "Records Added Successfully.", "success");
            renderSort();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ": " + errorThrown);
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
        complete: function () {
            isAddWebSubmitting = false;
            $btn.prop("disabled", false).removeClass("disabled").html("Submit");
        }
    });
}

// 2. Edit Web Banner
function editWebImagesFormSubmit(e) {
    if (isEditWebSubmitting) return;
    isEditWebSubmitting = true;

    const $btn = $(".editweb_submit_btn");
    $btn.prop("disabled", true).addClass("disabled").html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Updating...');

    const formdata = new FormData(e.target || e);
    $.ajax({
        url: "updatewebBannerImages/" + $("#edit_webImages_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") || window.csrfToken
        },
        success: function (response) {
            const updatedBannerImages = response.webbannerImages;
            $("#editWebImagesForm")[0].reset();
            $("#edit_webImageImage").val("");
            $("#edit_web_new_preview_wrapper").hide();
            $("#edit_web_new_preview_img").attr("src", "");
            $("#editWebImagesModal").modal("hide");
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";

            gridjsReRender1(updatedBannerImages);
            Swal.fire("Updated", "Records Updated Successfully.", "success");
            renderSort();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ": " + errorThrown);
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
        complete: function () {
            isEditWebSubmitting = false;
            $btn.prop("disabled", false).removeClass("disabled").html("Update");
        }
    });
}

// 3. Add Offer Banner
function addBannerImagesFormSubmit(e) {
    if (isAddBannerSubmitting) return;
    isAddBannerSubmitting = true;

    const $btn = $(".add_submit_btn");
    $btn.prop("disabled", true).addClass("disabled").html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Uploading...');

    const formdata = new FormData(e.target || e);
    $.ajax({
        url: "bannerImages",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") || window.csrfToken
        },
        success: function (response) {
            const updatedBannerImages = response.bannerImages;
            $("#addBannerImagesForm")[0].reset();
            $("#add_bannerImageImage").val("");
            $("#addBannerImagesModal").modal("hide");
            $(".product_remove_btn").trigger("click");
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";

            gridjsReRender(updatedBannerImages);
            Swal.fire("Added", "Records Added Successfully.", "success");
            renderSort();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ": " + errorThrown);
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
        complete: function () {
            isAddBannerSubmitting = false;
            $btn.prop("disabled", false).removeClass("disabled").html("Submit");
        }
    });
}

// 4. Edit Offer Banner
function editBannerImagesFormSubmit(e) {
    if (isEditBannerSubmitting) return;
    isEditBannerSubmitting = true;

    const $btn = $(".edit_submit_btn");
    $btn.prop("disabled", true).addClass("disabled").html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Updating...');

    const formdata = new FormData(e.target || e);
    $.ajax({
        url: "updateBannerImages/" + $("#edit_bannerImages_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") || window.csrfToken
        },
        success: function (response) {
            const updatedBannerImages = response.bannerImages;
            $("#editBannerImagesForm")[0].reset();
            $("#edit_bannerImageImage").val("");
            $("#editBannerImagesModal").modal("hide");
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";

            gridjsReRender(updatedBannerImages);
            $(".edit_product_remove_btn").trigger("click");
            Swal.fire("Updated", "Records Updated Successfully.", "success");
            renderSort();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ": " + errorThrown);
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
        complete: function () {
            isEditBannerSubmitting = false;
            $btn.prop("disabled", false).removeClass("disabled").html("Update");
        }
    });
}

// ------------------------------------------------------------------------------
// Document Ready & Modal Lifecycle Handlers
// ------------------------------------------------------------------------------
$(function () {
    // Password visibility toggle
    $(".bx-show").hide();
    $(".icon").on("click", function () {
        const parentEl = $(this).closest(".input-group");
        var input = parentEl.find("input");
        if (input.attr("type") === "password") {
            input.attr("type", "text");
            parentEl.find(".bx-hide").hide();
            parentEl.find(".bx-show").show();
        } else {
            input.attr("type", "password");
            parentEl.find(".bx-show").hide();
            parentEl.find(".bx-hide").show();
        }
    });

    // --------------------------------------------------------------------------
    // Opening Edit Web Banner Modal
    // --------------------------------------------------------------------------
    $(document).on("click", ".editweb_btn", function () {
        const id = $(this).attr("data-webbannerimagesid");
        const imagePath = $(this).attr("data-webbannerimagesimage");

        // Set ID and hidden input
        $("#edit_webImages_id").val(id);
        $("#existing_web_image").val(imagePath);

        // Display current active image in its dedicated container
        $("#edit_web_current_image").attr("src", `images/${imagePath}`);

        // Clean-slate reset for new image preview and input
        $("#edit_webImageImage").val("");
        $("#edit_web_new_preview_wrapper").hide();
        $("#edit_web_new_preview_img").attr("src", "");

        // Reset button state
        isEditWebSubmitting = false;
        $(".editweb_submit_btn").prop("disabled", false).removeClass("disabled").html("Update");

        // Clear previous validation error messages
        if (typeof editValidator1 !== "undefined") {
            editValidator1.clearErrors();
        }
    });

    // --------------------------------------------------------------------------
    // Modal Hidden / Close Event Cleanup (Ensures no lingering invalid images)
    // --------------------------------------------------------------------------
    $("#editWebImagesModal").on("hidden.bs.modal", function () {
        $("#edit_webImageImage").val("");
        $("#edit_web_new_preview_wrapper").hide();
        $("#edit_web_new_preview_img").attr("src", "");
        isEditWebSubmitting = false;
        $(".editweb_submit_btn").prop("disabled", false).removeClass("disabled").html("Update");

        if (typeof editValidator1 !== "undefined") {
            editValidator1.clearErrors();
        }
    });

    $("#addWebImagesModal").on("hidden.bs.modal", function () {
        $("#add_webImageImage").val("");
        $("#add_web_preview_wrapper").hide();
        $("#add_web_preview_img").attr("src", "");
        isAddWebSubmitting = false;
        $(".addweb_submit_btn").prop("disabled", false).removeClass("disabled").html("Submit");

        if (typeof addValidator1 !== "undefined") {
            addValidator1.clearErrors();
        }
    });

    // --------------------------------------------------------------------------
    // Offer Banner Modal Lifecycle
    // --------------------------------------------------------------------------
    $(document).on("click", ".edit_btn", function () {
        const imagePath = $(this).attr("data-bannerImagesimage");
        $("#edit_bannerImages_id").val($(this).attr("data-bannerImagesid"));
        $("#edit_bannerImagesname").val($(this).attr("data-bannerImagesname"));
        $("#edit_banner_preview_imageid").attr("src", `images/${imagePath}`);
        $("#edit_bannerImageImage").val("");

        isEditBannerSubmitting = false;
        $(".edit_submit_btn").prop("disabled", false).removeClass("disabled").html("Update");

        if (typeof editValidator !== "undefined") {
            editValidator.clearErrors();
        }
    });

    $("#editBannerImagesModal").on("hidden.bs.modal", function () {
        $("#edit_bannerImageImage").val("");
        isEditBannerSubmitting = false;
        $(".edit_submit_btn").prop("disabled", false).removeClass("disabled").html("Update");

        if (typeof editValidator !== "undefined") {
            editValidator.clearErrors();
        }
    });

    $("#addBannerImagesModal").on("hidden.bs.modal", function () {
        $("#add_bannerImageImage").val("");
        isAddBannerSubmitting = false;
        $(".add_submit_btn").prop("disabled", false).removeClass("disabled").html("Submit");

        if (typeof addValidator !== "undefined") {
            addValidator.clearErrors();
        }
    });

    // --------------------------------------------------------------------------
    // Delete Handlers
    // --------------------------------------------------------------------------
    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-bannerImagesid");
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
                    url: "destroyBannerImages/" + id,
                    method: "POST",
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") || window.csrfToken
                    },
                    success: function (response) {
                        const updatedBannerImages = response.bannerImages;
                        gridjsReRender(updatedBannerImages);
                        Swal.fire("Deleted!", "Records Deleted Successfully.", "success");
                        renderSort();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus + ": " + errorThrown);
                        Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
                    },
                });
            }
        });
    });

    $(document).on("click", ".deleteweb_btn", function () {
        const id = $(this).attr("data-webbannerImagesid");
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
                    url: "destroywebBannerImages/" + id,
                    method: "POST",
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") || window.csrfToken
                    },
                    success: function (response) {
                        const updatedBannerImages = response.webbannerImages;
                        gridjsReRender1(updatedBannerImages);
                        Swal.fire("Deleted!", "Records Deleted Successfully.", "success");
                        renderSort();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus + ": " + errorThrown);
                        Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
                    },
                });
            }
        });
    });
});

// ------------------------------------------------------------------------------
// Preview Container Handlers for Offer Banners
// ------------------------------------------------------------------------------
$(function () {
    const backupHtml = $(".preview-container").html();

    $("#add_bannerImageImage").on("change", function () {
        const file = this.files && this.files[0];
        if (file && file.type.match("image.*")) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = $("<img>").attr("src", e.target.result);
                const removeBtn = $("<button>")
                    .addClass("btn btn-danger product_remove_btn mt-2")
                    .text("Remove");

                $(".preview-container").empty().append(img).append(removeBtn);

                $(".product_remove_btn").on("click", function (ev) {
                    ev.preventDefault();
                    $(".preview-container").html(backupHtml);
                    $("#add_bannerImageImage").val("");
                });
            };
            reader.readAsDataURL(file);
        }
    });

    const editBackupHtml = $(".edit_preview-container").html();

    $("#edit_bannerImageImage").on("change", function () {
        const file = this.files && this.files[0];
        if (file && file.type.match("image.*")) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = $("<img>").attr("src", e.target.result);
                const removeBtn = $("<button>")
                    .addClass("btn btn-danger edit_product_remove_btn mt-2")
                    .text("Remove");

                $(".edit_preview-container").empty().append(img).append(removeBtn);

                removeBtn.on("click", function (ev) {
                    ev.preventDefault();
                    $(".edit_preview-container").html(editBackupHtml);
                    $("#edit_bannerImageImage").val("");
                });
            };
            reader.readAsDataURL(file);
        }
    });
});

// ------------------------------------------------------------------------------
// Drag & Drop Sortable Ordering
// ------------------------------------------------------------------------------
function renderSort() {
    const tbody = document.querySelector("tbody");
    if (!tbody) return;

    new Sortable(tbody, {
        handle: ".sortable-row",
        animation: 150,
        forceFallback: true,
        scroll: true,
        scrollSensitivity: 10,
        scrollSpeed: 100,
        onEnd: function () {
            const sortableRows = document.querySelectorAll(".sortable-row");
            const newOrder = Array.from(sortableRows).map((row) => row.dataset.id);

            $.ajax({
                type: "POST",
                url: "updateOrder",
                data: { order: newOrder },
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") || window.csrfToken
                },
                success: function (response) {
                    console.log("Order updated:", response);
                },
            });
        },
    });
}

renderSort();
