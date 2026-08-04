const addValidator = new JustValidate("#addOfferImagesForm", {
    validateBeforeSubmitting: true,
});
const editValidator = new JustValidate("#editOfferImagesForm", {
    validateBeforeSubmitting: true,
});

$("#add_offerImageImage").on("change", function () {
    const file = this.files[0];
    if (file && file.type.startsWith("image/")) {
        let img = new Image();
        img.src = window.URL.createObjectURL(file);

        img.decode().then(() => {
            if (!(img.width <= 600 && img.height <= 600)) {
                console.log("hi");
                $(".product_remove_btn").trigger("click");
                addValidator.showErrors({
                    "#add_offerImageImage":
                        "*Image resolution should be below 600 * 600",
                });
            }
        });
    }
});

addValidator
    .addField("#add_offerImageImage", [
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
                const file = fields["#add_offerImageImage"].elem.files[0];
                if (file) {
                    let img = new Image();
                    img.src = window.URL.createObjectURL(file);

                    return new Promise((resolve, reject) => {
                        img.decode().then(() => {
                            if (!(img.width <= 600 && img.height <= 600)) {
                                $(".product_remove_btn").trigger("click");
                            }
                            resolve(img.width <= 600 && img.height <= 600);
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
        $(".add_submit_btn3").attr("disabled", "true");
        $(".add_submit_btn3").html("Uploading.....");
        addOfferImagesFormSubmit(event);
    });

editValidator.onSuccess((event) => {
    $(".edit_submit_btn4").attr("disabled", "true");
    $(".edit_submit_btn4").html("Uploading.....");
    editOfferImagesFormSubmit(event);
});

const gridNew = new gridjs.Grid({
    columns: [
        // { name: "S.NO", sort: false },
        { name: "Offer Image", sort: false },
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
    data: offerImages.map((offerImages, index) => {
        return [
            // index + 1,
            // bannerImages.bannerImage_name,
            gridjs.html(
                `
                <div class="text-center sortable-row" data-id="${offerImages.id}">
                <img class="bannerImage_image_el gridImage" src="images/${offerImages.offer_image}"  alt ="categgory_image${index}"/>
            </div>

            `
            ),
            gridjs.html(
                `<div> <button data-bs-toggle="modal"
                data-bannerImagesid ="${offerImages.id}"
                data-bannerImagesimage ="${offerImages.offer_image}"  data-bs-target="#editBannerImagesModal"  class="btn btn-secondary edit_btn ">Edit</button> <button class="btn btn-danger delete_btn" data-bannerImagesid = ${offerImages.id}>Delete</button> </div>`
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

function gridjsReRender(offerImages) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: offerImages.map((offerImages, index) => {
                return [
                    // index + 1,
                    // bannerImages.bannerImage_name,
                    gridjs.html(
                        `
                        <div class="text-center sortable-row" data-id="${offerImages.id}">
                        <img class="bannerImage_image_el gridImage" src="images/${offerImages.offer_image}"  alt ="categgory_image${index}"/>
                    </div>

                    `
                    ),
                    gridjs.html(
                        `<div> <button data-bs-toggle="modal"
                        data-bannerImagesid ="${offerImages.id}"
                        data-bannerImagesimage ="${offerImages.offer_image}"  data-bs-target="#editBannerImagesModal"  class="btn btn-secondary edit_btn ">Edit</button> <button class="btn btn-danger delete_btn" data-bannerImagesid = ${offerImages.id}>Delete</button> </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

function addOfferImagesFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "offerImagess",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn3").removeAttr("disabled");
            $(".add_submit_btn3").html("Submit");
            const updatedBannerImages = response.offerImages;
            $("#addOfferImagesForm")[0].reset();
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

function editOfferImagesFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updateOfferImages/" + $("#edit_offerImages_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedBannerImages = response.offerImages;
            $("#editOfferImagesFormSubmit")[0].reset();
            $("#editBannerImagesModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedBannerImages);
            $(".edit_submit_btn4").removeAttr("disabled");
            $(".edit_submit_btn4").html("Update");
            $(".edit_product_remove_btn").trigger("click");
            Swal.fire("Updated", "Records Updated  Successfully.", "success");
            renderSort();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn4").removeAttr("disabled");
            $(".add_submit_btn3").removeAttr("disabled");
            $(".edit_submit_btn4").html("Update");
            $(".add_submit_btn3").html("Submit");
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

    $(document).on("click", ".edit_btn", function () {
        const imagePath = $(this).attr("data-bannerImagesimage");
        console.log(imagePath);
        $("#edit_bannerImages_id").val($(this).attr("data-bannerImagesid"));
        $("#edit_bannerImagesname").val($(this).attr("data-bannerImagesname"));
        $(".edit_preview_image").attr("src", `images/${imagePath}`);
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
                    url: "destroyOfferImages/" + id,
                    method: "post",
                    dataType: "json",
                    success: function (response) {
                        const updatedBannerImages = response.offerImages;
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
                removeBtn.on("click", function (e) {
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
