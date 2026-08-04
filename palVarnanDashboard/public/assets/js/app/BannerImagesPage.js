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
// web banner
$("#add_webImageImage").on("change", function () {
    const fileInput = this;
    const file = fileInput.files[0];

    if (file && file.type.startsWith("image/")) {
        const img = new Image();

        img.onload = () => {
            if (img.width !== 1920 || img.height !== 600) {
                // Show SweetAlert
                Swal.fire({
                    icon: "error",
                    title: "Invalid Image Size",
                    text: "Image must be exactly 1920 x 600 pixels.",
                });

                // Clear the file input
                $(fileInput).val("");
            }
        };

        img.onerror = () => {
            Swal.fire({
                icon: "error",
                title: "Invalid Image",
                text: "Could not load the image. Please upload a valid image file.",
            });
            $(fileInput).val("");
        };

        img.src = URL.createObjectURL(file);
    }
});

$("#edit_webImageImage").on("change", function () {
    const fileInput = this;
    const file = fileInput.files[0];

    if (file && file.type.startsWith("image/")) {
        const img = new Image();

        img.onload = () => {
            if (img.width !== 1920 || img.height !== 600) {
                Swal.fire({
                    icon: "error",
                    title: "Invalid Image Size",
                    text: "Banner image must be exactly 1920 x 600 pixels.",
                });

                // Clear the input field
                $(fileInput).val("");
            }
        };

        img.onerror = () => {
            Swal.fire({
                icon: "error",
                title: "Invalid Image File",
                text: "Unable to load the image. Please upload a valid image file.",
            });

            // Clear the input field
            $(fileInput).val("");
        };

        img.src = URL.createObjectURL(file);
    }
});

// $("#edit_webImageImage").on("change", function () {
//     const file = this.files[0];
//     if (file && file.type.startsWith("image/")) {
//         const img = new Image();
//         img.src = URL.createObjectURL(file);
//         img.onload = () => {
//             if (!(img.width === 1920 && img.height === 600)) {
//                 $(".edit_product_remove_btn").trigger("click"); // optional
//                 editValidator1.showErrors({
//                     "#edit_webImageImage":
//                         "*Image size must be exactly 1920px × 600px",
//                 });
//             }
//         };
//     }
// });
editValidator1
    .addField("#edit_webImageImage", [
        {
            rule: "files",
            value: {
                files: {
                    extensions: ["jpeg", "jpg", "png"],
                    maxSize: 1000000,
                },
            },
            errorMessage: "*Unsupported format or file too large",
        },
        {
            validator: (value, fields) => {
                return new Promise((resolve) => {
                    const fileInput = fields["#edit_webImageImage"].elem;
                    const file = fileInput?.files?.[0];

                    // ✅ No new image uploaded? Allow submit (existing image retained)
                    if (!file || !file.type.startsWith("image/")) return resolve(true);

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
        $(".editweb_submit_btn").attr("disabled", true).html("Uploading.....");
        editWebImagesFormSubmit(event); // ✅ Will only run if validation passes
    });
// editValidator1
//     .addField("#edit_webImageImage", [
//         {
//             rule: "files",
//             value: {
//                 files: {
//                     extensions: ["jpeg", "jpg", "png", "webp"],
//                     maxSize: 1000000, // 1MB max
//                 },
//             },
//             errorMessage: "*Unsupported format or file too large",
//         },
//         {
//             validator: (value, fields) => {
//                 return new Promise((resolve) => {
//                     const fileInput = fields["#edit_webImageImage"].elem;
//                     const file = fileInput.files[0];

//                     // ✅ Allow submission if no image selected
//                     if (!file) return resolve(true);

//                     const reader = new FileReader();

//                     reader.onload = function (e) {
//                         const img = new Image();
//                         img.src = e.target.result;

//                         img.onload = () => {
//                             const isExact = img.width === 1920 && img.height === 600;
//                             resolve(isExact);
//                         };

//                         img.onerror = () => resolve(false);
//                     };

//                     reader.readAsDataURL(file);
//                 });
//             },
//             errorMessage: "*Image size must be exactly 1920px × 600px",
//         },
//     ])
//     .onSuccess((event) => {
//         $(".editweb_submit_btn").attr("disabled", true).html("Updating...");
//         editWebImagesFormSubmit(event); // ✅ Submits only if all validation passes
//     });


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
                    extensions: ["jpeg", "jpg", "png"],
                    maxSize: 1000000, // 1 MB
                },
            },
            errorMessage: "*Unsupported format or file too large",
        },
        {
            validator: (value, fields) => {
                return new Promise((resolve) => {
                    const fileInput = fields["#add_webImageImage"].elem;
                    const file = fileInput?.files?.[0];

                    if (!file || !file.type.startsWith("image/")) {
                        resolve(false);
                        return;
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
        // Only called when all validation passed
        $(".addweb_submit_btn").attr("disabled", true).html("Uploading.....");
        addWebImagesFormSubmit(event); // your submit function
    });

// end webbanner

$("#add_bannerImageImage").on("change", function () {
    const file = this.files[0];
    if (file && file.type.startsWith("image/")) {
        let img = new Image();
        img.src = window.URL.createObjectURL(file);

        img.decode().then(() => {
            if (!(img.width <= 1510 && img.height <= 300)) {
                console.log("hi");
                $(".product_remove_btn").trigger("click");
                addValidator.showErrors({
                    "#add_bannerImageImage":
                        "*Image resolution should be below 600 * 600",
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
            errorMessage:
                "*Unsupported File Format  or File size should below 50KB",
        },
        {
            validator: (value, fields) => () => {
                const file = fields["#add_bannerImageImage"].elem.files[0];
                if (file) {
                    let img = new Image();
                    img.src = window.URL.createObjectURL(file);

                    return new Promise((resolve, reject) => {
                        img.decode().then(() => {
                            if (!(img.width <= 1510 && img.height <= 300)) {
                                $(".product_remove_btn").trigger("click");
                            }
                            resolve(img.width <= 1496 && img.height <= 300);
                        });
                    });
                }

                return new Promise((resolve, reject) => {
                    resolve(true);
                });
            },
            errorMessage: "*Image resolution should be below 600 * 600",
            // make sure to set the "required" property to true
        },
    ])
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", "true");
        $(".add_submit_btn").html("Uploading.....");
        addBannerImagesFormSubmit(event);
    });

editValidator.onSuccess((event) => {
    $(".edit_submit_btn").attr("disabled", "true");
    $(".edit_submit_btn").html("Uploading.....");
    editBannerImagesFormSubmit(event);
});

const gridNew = new gridjs.Grid({
    columns: [
        // { name: "S.NO", sort: false },
        { name: "Banner Image", sort: false },
        {
            name: "Action",
            sort: false,
        },
    ],
    // pagination: {
    //     limit: 10,
    // },
    sort: !0,
    search: 0,
    data: bannerImages.map((bannerImages, index) => {
        return [
            // index + 1,
            // bannerImages.bannerImage_name,
            gridjs.html(
                `
                <div class="text-center sortable-row" data-id="${bannerImages.id}">
                <img class="bannerImage_image_el gridImage" src="images/${bannerImages.banner_image}"  alt ="categgory_image${index}"/>
            </div>

            `
            ),
            gridjs.html(
                `<div> <button data-bs-toggle="modal"
                data-bannerImagesid ="${bannerImages.id}"
                data-bannerImagesimage ="${bannerImages.banner_image}"  data-bs-target="#editBannerImagesModal"  class="btn btn-secondary edit_btn ">Edit</button> <button class="btn btn-danger delete_btn" data-bannerImagesid = ${bannerImages.id}>Delete</button> </div>`
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

// gridNew.render(document.getElementById("table-gridjs"));

function gridjsReRender(bannerImages) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: bannerImages.map((bannerImages, index) => {
                return [
                    // index + 1,
                    // bannerImages.bannerImage_name,
                    gridjs.html(
                        `
                        <div class="text-center sortable-row" data-id="${bannerImages.id}">
                        <img class="bannerImage_image_el gridImage" src="images/${bannerImages.banner_image}"  alt ="categgory_image${index}"/>
                    </div>

                    `
                    ),
                    gridjs.html(
                        `<div> <button data-bs-toggle="modal"
                        data-bannerImagesid ="${bannerImages.id}"
                        data-bannerImagesimage ="${bannerImages.banner_image}"  data-bs-target="#editBannerImagesModal"  class="btn btn-secondary edit_btn ">Edit</button> <button class="btn btn-danger delete_btn" data-bannerImagesid = ${bannerImages.id}>Delete</button> </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

// web banner
const gridNew1 = new gridjs.Grid({
    columns: [
        // { name: "S.NO", sort: false },
        { name: "Web Banner Image", sort: false },
        {
            name: "Action",
            sort: false,
        },
    ],
    // pagination: {
    //     limit: 10,
    // },
    sort: !0,
    search: 0,
    data: webbannerImages.map((webbannerImages, index) => {
        return [
            // index + 1,
            // bannerImages.bannerImage_name,
            gridjs.html(
                `
                <div class="text-center sortable-row" data-id="${webbannerImages.id}">
                <img class="bannerImage_image_el gridImage" src="images/${webbannerImages.image}"  alt ="categgory_image${index}"/>
            </div>

            `
            ),
            gridjs.html(
                `<div> <button data-bs-toggle="modal"
                data-webbannerImagesid ="${webbannerImages.id}"
                data-webbannerImagesimage ="${webbannerImages.image}"  data-bs-target="#editWebImagesModal"  class="btn btn-secondary editweb_btn ">Edit</button> <button class="btn btn-danger deleteweb_btn" data-webbannerImagesid = ${webbannerImages.id}>Delete</button> </div>`
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

gridNew1.render(document.getElementById("table1-gridjs"));

function gridjsReRender1(webbannerImages) {
    if (gridNew1) gridNew1.config.plugin.remove("pagination");
    if (gridNew1) gridNew1.config.plugin.remove("search");
    gridNew1
        .updateConfig({
            data: webbannerImages.map((webbannerImages, index) => {
                return [
                    // index + 1,
                    // bannerImages.bannerImage_name,
                    gridjs.html(
                        `
                        <div class="text-center sortable-row" data-id="${webbannerImages.id}">
                        <img class="bannerImage_image_el gridImage" src="images/${webbannerImages.image}"  alt ="categgory_image${index}"/>
                    </div>

                    `
                    ),
                    gridjs.html(
                        `<div> <button data-bs-toggle="modal"
                        data-webbannerImagesid ="${webbannerImages.id}"
                        data-webbannerImagesimage ="${webbannerImages.image}"  data-bs-target="#editWebImagesModal"  class="btn btn-secondary editweb_btn ">Edit</button> <button class="btn btn-danger deleteweb_btn" data-webbannerImagesid = ${webbannerImages.id}>Delete</button> </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

// add web banner

function addWebImagesFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "bannerwebImages",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".addweb_submit_btn").removeAttr("disabled");
            $(".addweb_submit_btn").html("Submit");
            const updatedBannersImages = response.webbannerImages;
            $("#addWebImagesForm")[0].reset();
            $("#addWebImagesModal").hide();
            $(".product_remove_btn").trigger("click");
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";

            gridjsReRender1(updatedBannersImages);
            Swal.fire("Added", "Records Added Successfully.", "success");
            renderSort();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".editweb_submit_btn").removeAttr("disabled");
            $(".addweb_submit_btn").removeAttr("disabled");
            $(".editweb_submit_btn").html("Update");
            $(".addweb_submit_btn").html("Submit");
            console.log(textStatus + ": " + errorThrown);

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

function addBannerImagesFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "bannerImages",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn").removeAttr("disabled");
            $(".add_submit_btn").html("Submit");
            const updatedBannerImages = response.bannerImages;
            $("#addBannerImagesForm")[0].reset();
            $("#addBannerImagesModal").hide();
            $(".product_remove_btn").trigger("click");
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";

            gridjsReRender(updatedBannerImages);
            Swal.fire("Added", "Records Added Successfully.", "success");
            renderSort();
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

function editBannerImagesFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updateBannerImages/" + $("#edit_bannerImages_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedBannerImages = response.bannerImages;
            $("#editBannerImagesForm")[0].reset();
            $("#editBannerImagesModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedBannerImages);
            $(".edit_submit_btn").removeAttr("disabled");
            $(".edit_submit_btn").html("Update");
            $(".edit_product_remove_btn").trigger("click");
            Swal.fire("Updated", "Records Updated  Successfully.", "success");
            renderSort();
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

// edit web banner
function editWebImagesFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updatewebBannerImages/" + $("#edit_webImages_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedBannerImages = response.webbannerImages;
            $("#editWebImagesForm")[0].reset();
            $("#editWebImagesModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender1(updatedBannerImages);
            $(".editweb_submit_btn").removeAttr("disabled");
            $(".editweb_submit_btn").html("Update");
            $(".edit_product_remove_btn").trigger("click");
            Swal.fire("Updated", "Records Updated  Successfully.", "success");
            renderSort();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".editweb_submit_btn").removeAttr("disabled");
            $(".addweb_submit_btn").removeAttr("disabled");
            $(".editweb_submit_btn").html("Update");
            $(".addweb_submit_btn").html("Submit");
            console.log(textStatus + ": " + errorThrown);

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

// end web banner

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

    $(document).on("click", ".edit_btn", function () {
        const imagePath = $(this).attr("data-bannerImagesimage");
        console.log(imagePath);
        $("#edit_bannerImages_id").val($(this).attr("data-bannerImagesid"));
        $("#edit_bannerImagesname").val($(this).attr("data-bannerImagesname"));
        $(".edit_preview_image").attr("src", `images/${imagePath}`);
    });

    $(document).on("click", ".editweb_btn", function () {
    const imagePath = $(this).attr("data-webbannerimagesimage");

    $(".edit_preview_image").attr("src", `images/${imagePath}`);
    $("#edit_webImages_id").val($(this).attr("data-webbannerImagesid"));

    // Save current image path (will be used on submit if no new file uploaded)
    $("#existing_web_image").val(imagePath);

    // Clear file input
    $("#edit_webImageImage").val("");
});

$("#edit_webImageImage").on("change", function () {
    editValidator1.revalidateField("#edit_webImageImage");
});




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
                    method: "post",
                    dataType: "json",
                    success: function (response) {
                        const updatedBannerImages = response.bannerImages;
                        gridjsReRender(updatedBannerImages);
                        Swal.fire(
                            "Deleted!",
                            "Records Deleted Successfully.",
                            "success"
                        );

                        renderSort();
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

    // web delete functions
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
                    method: "post",
                    dataType: "json",
                    success: function (response) {
                        const updatedBannerImages = response.webbannerImages;
                        gridjsReRender1(updatedBannerImages);
                        Swal.fire(
                            "Deleted!",
                            "Records Deleted Successfully.",
                            "success"
                        );

                        renderSort();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $(".editweb_submit_btn").removeAttr("disabled");
                        $(".addweb_submit_btn").removeAttr("disabled");
                        $(".editweb_submit_btn").html("Update");
                        $(".addweb_submit_btn").html("Submit");
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

$(function () {
    const backupHtml = $(".preview-container").html();

    // Listen for changes to the input field
    $("#add_bannerImageImage").on("change", function () {
        // Get the selected file
        var file = $(this)[0].files[0];

        // Check if the file is an image
        if (file.type.match("image.*")) {
            // Create a new FileReader object
            var reader = new FileReader();

            // Set up the FileReader to load the image
            reader.onload = function (e) {
                // Create a new image element
                var img = $("<img>").attr("src", e.target.result);

                // Create a remove button
                var removeBtn = $("<button>")
                    .addClass("btn btn-danger product_remove_btn mt-2")
                    .text("Remove");

                // Add the image and remove button to the preview container
                $(".preview-container").empty().append(img).append(removeBtn);

                // Listen for clicks on the remove button
                $('.product_remove_btn').on("click", function (e) {
                    e.preventDefault();

                    // Remove the image from the preview container
                    $(".preview-container").html(backupHtml);
                    // Clear the input field
                    $("#add_bannerImageImage").val("");
                });
            };

            // Read the selected file as a data URL
            reader.readAsDataURL(file);
        }
    });
});

$(function () {
    const backupHtml = $(".preview-container").html();

    // Listen for changes to the input field
    $("#add_webImageImage").on("change", function () {
        // Get the selected file
        var file = $(this)[0].files[0];

        // Check if the file is an image
        if (file.type.match("image.*")) {
            // Create a new FileReader object
            var reader = new FileReader();

            // Set up the FileReader to load the image
            reader.onload = function (e) {
                // Create a new image element
                var img = $("<img>").attr("src", e.target.result);

                // Create a remove button
                var removeBtn = $("<button>")
                    .addClass("btn btn-danger product_remove_btn mt-2")
                    .text("Remove");

                // Add the image and remove button to the preview container
                $(".preview-container").empty().append(img).append(removeBtn);

                // Listen for clicks on the remove button
                removeBtn.on("click", function (e) {
                    e.preventDefault();

                    // Remove the image from the preview container
                    $(".preview-container").html(backupHtml);
                    // Clear the input field
                    $("#add_webImageImage").val("");
                });
            };

            // Read the selected file as a data URL
            reader.readAsDataURL(file);
        }
    });
});

$(function () {
    const backupHtml = $(".edit_preview-container").html();

    // Listen for changes to the input field
    $("#edit_bannerImageImage").on("change", function () {
        // Get the selected file
        var file = $(this)[0].files[0];

        // Check if the file is an image
        if (file.type.match("image.*")) {
            // Create a new FileReader object
            var reader = new FileReader();

            // Set up the FileReader to load the image
            reader.onload = function (e) {
                // Create a new image element
                var img = $("<img>").attr("src", e.target.result);

                // Create a remove button
                var removeBtn = $("<button>")
                    .addClass("btn btn-danger edit_product_remove_btn mt-2")
                    .text("Remove");

                // Add the image and remove button to the preview container
                $(".edit_preview-container")
                    .empty()
                    .append(img)
                    .append(removeBtn);

                // Listen for clicks on the remove button
                removeBtn.on("click", function (e) {
                    e.preventDefault();

                    // Remove the image from the preview container
                    $(".edit_preview-container").html(backupHtml);
                    // Clear the input field
                    $("#edit_bannerImageImage").val("");
                });
            };

            // Read the selected file as a data URL
            reader.readAsDataURL(file);
        }
    });
});

// edit banner
$(function () {
    const backupHtml = $(".edit_preview-container").html();

    // Listen for changes to the input field
    $("#edit_webImageImage").on("change", function () {
        // Get the selected file
        var file = $(this)[0].files[0];

        // Check if the file is an image
        if (file.type.match("image.*")) {
            // Create a new FileReader object
            var reader = new FileReader();

            // Set up the FileReader to load the image
            reader.onload = function (e) {
                // Create a new image element
                var img = $("<img>").attr("src", e.target.result);

                // Create a remove button
                var removeBtn = $("<button>")
                    .addClass("btn btn-danger edit_product_remove_btn mt-2")
                    .text("Remove");

                // Add the image and remove button to the preview container
                $(".edit_preview-container")
                    .empty()
                    .append(img)
                    .append(removeBtn);

                // Listen for clicks on the remove button
                removeBtn.on("click", function (e) {
                    e.preventDefault();

                    // Remove the image from the preview container
                    $(".edit_preview-container").html(backupHtml);
                    // Clear the input field
                    $("#edit_webImageImage").val("");
                });
            };

            // Read the selected file as a data URL
            reader.readAsDataURL(file);
        }
    });
});
//

function renderSort() {
    new Sortable(document.querySelector("tbody"), {
        handle: ".sortable-row",
        animation: 150,
        forceFallback: true,
        scroll: true,
        scrollSensitivity: 10,
        scrollSpeed: 100,
        onEnd: function (evt) {
            const sortableRows = document.querySelectorAll(".sortable-row");
            const newOrder = Array.from(sortableRows).map(
                (row, index) => row.dataset.id
            );

            $.ajax({
                type: "post",
                url: "updateOrder",
                data: { order: newOrder },
                dataType: "dataType",
                success: function (response) {
                    console.log(response);
                },
            });
        },
    });
}

renderSort();
