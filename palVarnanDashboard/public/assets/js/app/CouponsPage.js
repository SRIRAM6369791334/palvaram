const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Code Name",
        "Minimum Purchase Amount",
        "Discount Type",
        "Discount Value",
        "Start Date",
        "End Date",
        "Status",
        {
            name: "Action",
            sort: false,
        },
        // {
        //     name: "Action",
        //     sort: false,
        // },
    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: coupons.map((coupons, index) => {
        return [
            index + 1,
            coupons.codename,
            coupons.mini_amt,
            gridjs.html(
                coupons.discounttype == 1
                    ? `<p style="position: relative;
                top: 10px;">Rs</p>`
                    : `<p style="position: relative;
                top: 10px;">%</p>`
            ),
            coupons.discount,
            coupons.start_date,
            coupons.end_date,


            gridjs.html(
                `<div class="form-check form-switch d-flex justify-content-center">
                    <input class="form-check-input status_toggle" type="checkbox" 
                        role="switch"
                        data-couponid="${coupons.id}"
                        ${coupons.coupon_status == 1 ? 'checked' : ''}
                        style="cursor:pointer; width:40px; height:20px;">
                </div>`
            ),

            gridjs.html(
                `<div> <button data-bs-toggle="modal"
                data-couponid ="${coupons.id}"
                data-couponname="${coupons.codename}"
                data-minimumamt="${coupons.mini_amt}"
                data-coupondistype ="${coupons.discounttype}"
                data-couponvalue="${coupons.discount}"
                data-couponstart="${coupons.start_date}"
                data-couponend="${coupons.end_date}"


                data-bs-target="#editcouponModal"  class="btn btn-secondary edit_btn1 ">Edit</button> 
                <button 
                data-couponid ="${coupons.id}" class="btn btn-secondary delete_btn ">Delete</button>
                </div>`
            ),
            // gridjs.html(
            //     coupons.default_id == 0 ?
            //     `<button class="btn btn-danger delete_btn" data-couponid = ${coupons.id}>Delete</button>`
            //     :``

            // )
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

function gridjsReRender(coupons) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: coupons.map((coupons, index) => {
                return [
                    index + 1,
                    coupons.codename,
                    coupons.mini_amt,
                    gridjs.html(
                        coupons.discounttype == 1
                            ? `<p style="position: relative;
                        top: 10px;">Rs</p>`
                            : `<p style="position: relative;
                        top: 10px;">%</p>`
                    ),

                    coupons.discount,
                    coupons.start_date,
                    coupons.end_date,

                    gridjs.html(
                        `<div class="form-check form-switch d-flex justify-content-center">
                            <input class="form-check-input status_toggle" type="checkbox" 
                                role="switch"
                                data-couponid="${coupons.id}"
                                ${coupons.coupon_status == 1 ? 'checked' : ''}
                                style="cursor:pointer; width:40px; height:20px;">
                        </div>`
                    ),
                    gridjs.html(
                        `<div> <button data-bs-toggle="modal"
                        data-couponid ="${coupons.id}"
                        data-couponname="${coupons.codename}"
                        data-minimumamt="${coupons.mini_amt}"
                        data-coupondistype ="${coupons.discounttype}"
                        data-couponvalue="${coupons.discount}"
                        data-couponstart="${coupons.start_date}"
                        data-couponend="${coupons.end_date}"
                        data-bs-target="#editcouponModal data"  class="btn btn-secondary edit_btn1 ">Edit</button>  
                        <button 
                        data-couponid ="${coupons.id}" class="btn btn-secondary delete_btn ">Delete</button>
                        </div>`
                    ),
                    // gridjs.html(
                    //     coupons.default_id == 0 ?
                    //     `<button class="btn btn-danger delete_btn" data-couponid = ${coupons.id}>Delete</button>`
                    //     :``

                    // )
                ];
            }),
        })
        .forceRender();
}

const addValidator = new JustValidate("#addCouponForm", {
    validateBeforeSubmitting: true,
});
const addValidator1 = new JustValidate("#editCouponForm", {
    validateBeforeSubmitting: true,
});

addValidator
    .addField("#add_coupon_name", [
        {
            rule: "required",
            errorMessage: "*Coupon Code Field is required",
        },
        {
            rule: "minLength",
            value: 3,
            errorMessage: "*Coupon Code should be at Minimum 4 character",
        },
    ])
    .addField("#add_discount_select", [
        {
            rule: "required",
            errorMessage: "*Discount Field is required",
        },
    ])
    .addField("#add_discount_value", [
        {
            rule: "required",
            errorMessage: "*Discount Value Field is required",
        },
    ])
    .addField("#add_start_date", [
        {
            rule: "required",
            errorMessage: "*Date Field is required",
        },
    ])
    .addField("#add_end_date", [
        {
            rule: "required",
            errorMessage: "*Date Field is required",
        },
    ])
    .onSuccess((event) => {
        $(".add_submit_btn2").attr("disabled", "true");
        $(".add_submit_btn2").html("Uploading.....");
        addCouponFormSubmit(event);
    });

// edit

addValidator1
    .addField("#edit_coupon_name", [
        {
            rule: "required",
            errorMessage: "*Coupon Code Field is required",
        },
        {
            rule: "minLength",
            value: 3,
            errorMessage: "*Coupon Code should be at Minimum 4 character",
        },
    ])
    .addField("#edit_discount_select", [
        {
            rule: "required",
            errorMessage: "*Discount Field is required",
        },
    ])
    .addField("#edit_discount_value", [
        {
            rule: "required",
            errorMessage: "*Discount Value Field is required",
        },
    ])
    .addField("#edit_start_date", [
        {
            rule: "required",
            errorMessage: "*Date Field is required",
        },
    ])
    .addField("#edit_end_date", [
        {
            rule: "required",
            errorMessage: "*Date Field is required",
        },
    ])
    .onSuccess((event) => {
        $(".edit_submit_btn1").attr("disabled", "true");
        $(".edit_submit_btn1").html("Uploading.....");
        editCouponFormSubmit(event);
    });

// function addCouponFormSubmit(e) {
//     const formdata = new FormData(e.target);
//     $.ajax({
//         url: "coupons",
//         method: "POST",
//         dataType: "json",
//         data: formdata,
//         processData: false,
//         contentType: false,
//         success: function (response) {
//             $(".add_submit_btn2").removeAttr("disabled");
//             $(".add_submit_btn2").html("Submit");

//             const updatedCoupon = response.coupons;
//             $("#addCouponForm")[0]?.reset();
//             $("#addProductModal").hide();
//             $(".modal-backdrop").remove();
//             document.body.style.overflowY = "scroll";

//             console.log(updatedCoupon);

//             gridjsReRender(updatedCoupon);
//             Swal.fire("Added", "Records Added Successfully.", "success");
//         },
//         error: function (jqXHR, textStatus, errorThrown) {
//             $(".edit_submit_btn1").removeAttr("disabled");
//             $(".add_submit_btn2").removeAttr("disabled");
//             $(".edit_submit_btn1").html("Update");
//             $(".add_submit_btn2").html("Submit");
//             console.log(textStatus + ": " + errorThrown);

//             Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
//         },
//     });
// }

$(document).on("click", ".edit_btn1", function () {
    $("#edit_coupon_id").val($(this).attr("data-couponid"));
    $("#edit_coupon_name").val($(this).attr("data-couponname"));
    $("#edit_mini_amt").val($(this).attr("data-minimumamt"));
    $("#edit_discount_select").val($(this).attr("data-coupondistype"));
    $("#edit_discount_value").val($(this).attr("data-couponvalue"));
    $("#edit_start_date").val($(this).attr("data-couponstart"));
    $("#edit_end_date").val($(this).attr("data-couponend"));
});

function editCouponFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updatecoupon/" + $("#edit_coupon_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedProduct = response.coupons;
            $("#editCouponForm")[0].reset();
            $("#editcouponModal").hide();
            $(".modal-backdrop").remove();

            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedProduct);
            $(".edit_submit_btn1").removeAttr("disabled");
            $(".edit_submit_btn1").html("Update");
            Swal.fire(
                "Updated",
                "Records Updatedsss  Successfully.",
                "success"
            );
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn1").removeAttr("disabled");
            $(".add_submit_btn2").removeAttr("disabled");
            $(".edit_submit_btn1").html("Update");
            $(".add_submit_btn2").html("Submit");
            console.log(textStatus + ": " + errorThrown);

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

$(document).on("click", ".delete_btn", function () {
    const id = $(this).attr("data-couponid");
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
                url: "destroycoupon/" + id,
                method: "post",
                dataType: "json",
                success: function (response) {
                    const updatedProduct = response.coupons;
                    gridjsReRender(updatedProduct);
                    Swal.fire(
                        "Deleted!",
                        "Records Deleted Successfully.",
                        "success"
                    );
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $(".edit_submit_btn1").removeAttr("disabled");
                    $(".add_submit_btn2").removeAttr("disabled");
                    $(".edit_submit_btn1").html("Update");
                    $(".add_submit_btn2").html("Submit");
                    console.log(textStatus + ": " + errorThrown);

                    Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
                },
            });
        }
    });
});

$(document).ready(function () {
    // Get the current date in ISO format
    const today = new Date().toISOString().split("T")[0];

    // Set the minimum date for the date input field
    $("#add_start_date").attr("min", today);

    $("#add_start_date").on("change", function () {
        // Get the selected date value
        const selectedDate = $(this).val();

        // Set the minimum date for the end date input field to the selected date
        $("#add_end_date").attr("min", selectedDate);
    });
});

function addCouponFormSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    $.ajax({
        url: "coupons",
        method: "POST",
        dataType: "json",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn2").removeAttr("disabled").text("Submit");

            form.reset();

            $("#addProductModal").modal("hide");
            $("body").removeClass("modal-open");
            $(".modal-backdrop").remove();

            gridjsReRender(response.coupons);

            Swal.fire("Added", "Record Added Successfully.", "success");
        },
        error: function (xhr) {
            $(".add_submit_btn2").removeAttr("disabled").text("Submit");

            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = "Please correct the following errors:\n";

                Object.keys(errors).forEach(function (key) {
                    errorMessage += errors[key] + "\n";
                });

                Swal.fire({
                    title: "Coupon Already Exist",
                    // text: errorMessage,
                    icon: "error",
                }).then((result) => {
                    if (result.value) {
                        form.reset();
                    }
                });
            } else {
                Swal.fire({
                    title: "Error",
                    text: "An unexpected error occurred. Please try again.",
                    icon: "error",
                }).then((result) => {
                    if (result.value) {
                        form.reset();
                    }
                });
            }
        },
    });
}
$(document).ready(function () {
    $(document).on("change", "#add_discount_select", function () {
        $("#add_discount_value").val("");
    });
    $(document).on("input", "#add_discount_value", function () {
        var discounttype = parseInt($("#add_discount_select").val());
        var discountInput = $("#add_discount_value");

        if (discounttype === 1) {
            discountInput.off("input");
            discountInput.on("input", function () {
                discountInput.attr(
                    "oninput",
                    "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                );
            });
        } else if (discounttype === 2) {
            // discountInput.attr("oninput", "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)");
            discountInput.off("input");
            discountInput.on("input", function () {
                discountInput.attr(
                    "oninput",
                    "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3); if(parseInt(this.value) >= 100) this.value = '100';"
                );
            });
        } else {
            // discountInput.removeAttr("oninput");
            discountInput.off("input");
        }
    });
});

$(document).on("change", ".status_toggle", function () {
    const id = $(this).attr("data-couponid");
    const checkbox = $(this);

    $.ajax({
        url: "togglecouponstatus/" + id,
        method: "POST",
        dataType: "json",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            gridjsReRender(response.coupons);
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Status updated successfully",
                showConfirmButton: false,
                timer: 1500,
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // Revert toggle if failed
            checkbox.prop("checked", !checkbox.prop("checked"));
            Swal.fire("Error", "Could not update status.", "error");
        },
    });
});