const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Category",
        "Product",
        "Name",
        // "Image",
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
    data: notififils.map((notifidata, index) => {
        return [
            index + 1,
            notifidata.cate_name,
            notifidata.pro_name,
            notifidata.title,
            // gridjs.html(
            //     `

            //     <img class="bannerImage_image_el gridImage" src="images/${notifidata.notification_image}"  alt ="categgory_image${index}"/>


            // `
            // ),
            gridjs.html(
                `<div> <button data-bs-toggle="modal"
                data-notifiid ="${notifidata.id}"
                data-notifitittle="${notifidata.title}"
                data-notificontent ="${notifidata.content}"
                data-cate="${notifidata.cate_id}"
                data-pro ="${notifidata.pro_id}"
                data-star ="${notifidata.star}"
                data-approval ="${notifidata.approval}"
                data-review ="${notifidata.review}"

                data-bs-target="#editNotificationModal"  class="btn btn-secondary edit_btn2 ">Edit</button><span>  </span><button class="btn btn-danger delete_btn" data-notifiid = ${notifidata.id} style="margin-left="5px">Delete</button>  </div>`
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


function gridjsReRender(notififils) {

    console.log(typeof notififils);
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: notififils.map((notifidata, index) => {
                return [
                    index + 1,
                    notifidata.cate_name,
                    notifidata.pro_name,
                    notifidata.title,
                    // gridjs.html(
                    //     `

                    //     <img class="bannerImage_image_el gridImage" src="images/${notifidata.notification_image}"  alt ="categgory_image${index}"/>


                    // `
                    // ),
                    gridjs.html(
                        `<div> <button data-bs-toggle="modal"
                        data-notifiid ="${notifidata.id}"
                        data-notifitittle="${notifidata.title}"
                        data-notificontent ="${notifidata.content}"
                         data-cate="${notifidata.cate_id}"
                data-pro ="${notifidata.pro_id}"
                data-star ="${notifidata.star}"
                data-approval ="${notifidata.approval}"
                data-review ="${notifidata.review}"
                        data-bs-target="#editNotificationModal"  class="btn btn-secondary edit_btn2 ">Edit</button><span>  </span><button class="btn btn-danger delete_btn" data-notifiid = ${notifidata.id} style="margin-left="5px">Delete</button>  </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

const addValidator = new JustValidate("#addNotificaForm", {
    validateBeforeSubmitting: true,
});
const addValidator1 = new JustValidate("#editNotificaForm", {
    validateBeforeSubmitting: true,
});
addValidator
    .addField("#add_category_select", [
        {
            rule: "required",
            errorMessage: "*Category Field is required",
        },

    ])
    .addField("#add_product_name", [
        {
            rule: "required",
            errorMessage: "*Product Name Field is required",
        },

    ])
    .addField("#add_tittle_name", [
        {
            rule: "required",
            errorMessage: "*Name Field is required",
        },
        {

            rule: 'minLength',
            value: 3,
            errorMessage: '*Name should be at Minimum 4 character',

        },

    ])
    .addField("#star_rating", [
        {
            rule: "required",
            errorMessage: "*Star Rating Field is required",
        },

    ])
    .addField("#add_product_review", [
        {
            rule: "required",
            errorMessage: "*Review Field is required",
        },
        {

            rule: 'minLength',
            value: 5,
            errorMessage: '*Review should be at Minimum 5 character',

        },
        {
            rule: 'maxLength',
            value: 600,
            errorMessage: '*Review should be at Maximum 600 character',

        },
    ])
    // .addField('#noti_product_image',[
    //     {
    //         rule: "required",
    //         errorMessage: "*Name Field is required",
    //     },
    // ])
    .onSuccess((event) => {
        $(".addNoti_submit_btn").attr("disabled", "true");
        $(".addNoti_submit_btn").html("Uploading.....");
        addNotificaFormSubmit(event);
    });

addValidator1
    .addField("#edit_category_select", [
        {
            rule: "required",
            errorMessage: "*Category Field is required",
        },

    ])
    .addField("#edit_product_name", [
        {
            rule: "required",
            errorMessage: "*Product Name Field is required",
        },

    ])
    .addField("#edit_title_name", [
        {
            rule: "required",
            errorMessage: "*Name Field is required",
        },
        {

            rule: 'minLength',
            value: 3,
            errorMessage: '*Name should be at Minimum 4 character',

        },

    ])
    .addField("#edit_product_review", [
        {
            rule: "required",
            errorMessage: "*Review Field is required",
        },
        {

            rule: 'minLength',
            value: 5,
            errorMessage: '*Review should be at Minimum 5 character',

        },
        {
            rule: 'maxLength',
            value: 600,
            errorMessage: '*Location should be at Maximum 600 character',

        },

    ])
    // .addField('#edit1_product_image',[
    //     {
    //         rule: "required",
    //         errorMessage: "*Title Field is required",
    //     },
    // ])
    .onSuccess((event) => {
        $(".editnoti_submit_btn").attr("disabled", "true");
        $(".editnoti_submit_btn").html("Uploading.....");
        editNotificaFormSubmit(event);
    });
function addNotificaFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "reviews",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".addNoti_submit_btn").removeAttr("disabled");
            $(".addNoti_submit_btn").html("Submit");

            const updatedNotifi = response.notififils;
            console.log({ updatedNotifi });

            $("#addNotificaForm")[0]?.reset();
            $("#addNotificaModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedNotifi);
            Swal.fire("Added", "Records Added Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".editnoti_submit_btn").removeAttr("disabled");
            $(".addNoti_submit_btn").removeAttr("disabled");
            $(".editnoti_submit_btn").html("Update");
            $(".addNoti_submit_btn").html("Submit");
            console.log(textStatus + ": " + errorThrown);

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}


$(document).on("click", ".edit_btn2", function () {
    $("#edit_notifi_id").val($(this).attr("data-notifiid"));
    $("#edit_title_name").val($(this).attr("data-notifitittle"));
    $("#edit_content_value").val($(this).attr("data-notificontent"));
    $("#edit_product_review").val($(this).attr("data-review"));

    const cateId = $(this).attr("data-cate");
    const proId = $(this).attr("data-pro");
    const editRating = $(this).attr("data-star");
    const approval = $(this).attr("data-approval");

    // ⭐ Star setup
    const editStars = document.querySelectorAll(".edit-star-rating .star");
    document.getElementById("edit_star").value = editRating;
    editStars.forEach(s => {
        s.classList.remove("bxs-star");
        s.classList.add("bx-star");
        s.style.color = "#ccc";
    });
    for (let i = 0; i < editRating; i++) {
        editStars[i].classList.remove("bx-star");
        editStars[i].classList.add("bxs-star");
        editStars[i].style.color = "#ffc107";
    }

    // ⭐ Approval checkbox
    $('#edit_approval_checkbox').prop('checked', approval == 1);

    // ⭐ Set category and defer product until loaded
    selectedProId = proId; // store temporarily
    $("#edit_category_select").val(cateId).trigger("change");
});

// $(document).on("click", ".edit_btn2", function () {
//     // Set ID, title, content
//     $("#edit_notifi_id").val($(this).attr("data-notifiid"));
//     $("#edit_title_name").val($(this).attr("data-notifitittle"));
//     $("#edit_content_value").val($(this).attr("data-notificontent"));
//     $("#edit_product_review").val($(this).attr("data-review"));
//     $("#edit_star").val($(this).attr("data-star"));

//     // Set selected category and product
//     const cateId = $(this).attr("data-cate");
//     const proId = $(this).attr("data-pro");
//     const editRating = $(this).attr("data-star");
//     const approval = $(this).attr("data-approval");

// const editStars = document.querySelectorAll(".edit-star-rating .star");
// document.getElementById("edit_star").value = editRating;

// // Reset stars
// editStars.forEach(s => {
//     s.classList.remove("bxs-star");
//     s.classList.add("bx-star");
//     s.style.color = "#ccc";
// });

// // Activate stars up to rating
// for (let i = 0; i < editRating; i++) {
//     editStars[i].classList.remove("bx-star");
//     editStars[i].classList.add("bxs-star");
//     editStars[i].style.color = "#ffc107";
// }


//     $("#edit_category_select").val(cateId).change(); // also trigger change if products are dependent

//    $("#edit_product_name").val(proId).change();


//      if (approval == 1) {
//         $('#edit_approval_checkbox').prop('checked', true);
//     } else {
//         $('#edit_approval_checkbox').prop('checked', false);
//     }
// });



function editNotificaFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updatenotifi/" + $("#edit_notifi_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedProduct = response.notififils;
            $("#editNotificaForm")[0].reset();
            $("#editNotificationModal").hide();
            $(".modal-backdrop").remove();
            $(".edit_product_remove_btn ").trigger("click");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedProduct);
            $(".editnoti_submit_btn").removeAttr("disabled");
            $(".editnoti_submit_btn").html("Update");
            Swal.fire("Updated", "Records Updated  Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".editnoti_submit_btn").removeAttr("disabled");
            $(".addNoti_submit_btn").removeAttr("disabled");
            $(".editnoti_submit_btn").html("Update");
            $(".addNoti_submit_btn").html("Submit");
            console.log(textStatus + ": " + errorThrown);

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

$(document).on("click", ".delete_btn", function () {
    const id = $(this).attr("data-notifiid");
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
                url: "destroynotifi/" + id,
                method: "post",
                dataType: "json",
                // success: function (response) {
                //     const updatedProduct = response.notififils;
                //     gridjsReRender(updatedProduct);
                //     Swal.fire(
                //         "Deleted!",
                //         "Records Deleted Successfully.",
                //         "success"
                //     );
                // },
                success: function (response) {
                    const updatedProduct = response.notification; // Correct key
                    gridjsReRender(updatedProduct);
                    Swal.fire(
                        "Deleted!",
                        "Records Deleted Successfully.",
                        "success"
                    );
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $(".editnoti_submit_btn").removeAttr("disabled");
                    $(".addNoti_submit_btn").removeAttr("disabled");
                    $(".editnoti_submit_btn").html("Update");
                    $(".addNoti_submit_btn").html("Submit");
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

$(document).on("change", "#add_category_select", function () {
    let categoryId = $(this).val();

    $("#add_product_name").empty().append(
        '<option value="" disabled selected>Loading products...</option>'
    );

    $.ajax({
        type: "GET",
        url: "/getproductsbycategory/" + categoryId,
        success: function (response) {
            $("#add_product_name").empty().append(
                '<option value="" disabled selected>Select Product</option>'
            );

            response.forEach((product) => {
                $("#add_product_name").append(
                    `<option value="${product.id}">${product.product_name}</option>`
                );
            });
        },
        error: function () {
            $("#add_product_name").empty().append(
                '<option value="" disabled selected>Failed to load products</option>'
            );
        },
    });
});


let selectedProId = null; // global scope or just outside $(document).ready

$("#edit_category_select").on("change", function () {
    const id = $(this).val();

    $.ajax({
        type: "GET",
        url: "getproductsbycategory/" + id,
        success: function (response) {
            $("#edit_product_name").empty().append('<option value="">Select Product</option>');
            response.forEach(product => {
                $("#edit_product_name").append(
                    `<option value="${product.id}">${product.product_name}</option>`
                );
            });

            // ✅ Only set product ID if one is stored for editing
            if (selectedProId) {
                $("#edit_product_name").val(selectedProId).change();
                selectedProId = null; // clear it after use
            }
        }
    });
});

// $("#edit_category_select").on("change", function () {
//     const id = $(this).val();

//     $.ajax({
//         type: "GET",
//         url: "getproductsbycategory/" + id,
//         success: function (response) {
//             $("#edit_product_name").empty().append('<option value="">Select Product</option>');
//             response.forEach(product => {
//                 $("#edit_product_name").append(
//                     `<option value="${product.id}">${product.product_name}</option>`
//                 );
//             });

//             // If product ID already exists (for edit), select it
//             const selectedProId = $(".edit_btn2").attr("data-pro");
//             if (selectedProId) {
//                 $("#edit_product_name").val(selectedProId);
//             }
//         }
//     });
// });


document.addEventListener("DOMContentLoaded", function () {
    const stars = document.querySelectorAll(".star-rating .star");
    const ratingInput = document.getElementById("star_rating");

    stars.forEach((star, index) => {
        star.addEventListener("click", () => {
            const rating = star.getAttribute("data-value");
            ratingInput.value = rating;

            stars.forEach(s => {
                s.classList.remove("bxs-star");
                s.classList.add("bx-star");
                s.style.color = "#ccc";
            });

            for (let i = 0; i < rating; i++) {
                stars[i].classList.remove("bx-star");
                stars[i].classList.add("bxs-star");
                stars[i].style.color = "#ffc107";
            }
        });
    });
});



document.querySelectorAll(".edit-star-rating .star").forEach((star, index, stars) => {
    star.addEventListener("click", () => {
        const rating = star.getAttribute("data-value");
        document.getElementById("edit_star").value = rating;

        stars.forEach(s => {
            s.classList.remove("bxs-star");
            s.classList.add("bx-star");
            s.style.color = "#ccc";
        });

        for (let i = 0; i < rating; i++) {
            stars[i].classList.remove("bx-star");
            stars[i].classList.add("bxs-star");
            stars[i].style.color = "#ffc107";
        }
    });
});
