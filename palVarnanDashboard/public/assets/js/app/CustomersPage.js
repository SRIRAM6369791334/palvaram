const addValidator = new JustValidate("#addUserForm", {
    validateBeforeSubmitting: true,
});

const editValidator = new JustValidate("#editUserForm", {
    validateBeforeSubmitting: true,
});

// const addressValidator = new JustValidate("#addAddressForm", {
//     validateBeforeSubmitting: true,
// });

// addressValidator
// .addField("#address_line_one", [
//     {
//         rule: "required",
//         errorMessage: "*Address  Field is required",
//     },


// ])
// .addField("#address_line_two", [
//     {
//         rule: "required",
//         errorMessage: "*Address Field is required",
//     },

// ])
// .addField("#address_type_select", [
//     {
//         rule: "required",
//         errorMessage: "*Address Field is required",
//     },
// ])
// .addField("#address_type_select", [
//     {
//         rule: "required",
//         errorMessage: "*address type Field is required",
//     },

// ])
// .addField("#pin_code_type",[
//     {
//         rule: "required",
//         errorMessage: "*pincode Field is required",
//     },

//     {
//         rule: "maxLength",
//         value: 6,
//         errorMessage: "*Pincode invalid number",
//     },
// ])
// .addField("#city_input",[
//     {
//         rule: "required",
//         errorMessage: "*City Field is required",
//     },


// ])
// .onSuccess((event) => {
//     $(".address_submit_btn").attr("disabled", "true");
//     $(".addvari_submit_btn").html("Uploading.....");

//     addAddressFormSubmit(event);
// });

// user add
addValidator
    .addField("#add_username", [
        {
            rule: "required",
            errorMessage: "*User Name Field is required",
        },
        {
            rule: "minLength",
            value: 3,
            errorMessage: "*Should more than 3 characters",
        },
        {
            rule: "maxLength",
            value: 15,
            errorMessage: "*Should not more than 15 characters",
        },

    ])
    .addField("#add_email", [
        {
            rule: "required",
            errorMessage: "*Email Field is required",
        },
        {
            rule: "email",
            errorMessage: "*Must be valid Email Format(ex:- john@gmail.com)",
        },
    ])
    .addField("#add_password", [
        {
            rule: "required",
            errorMessage: "*Password Field is required",
        },
    ])
    .addField("#add_phone_number", [
        {
            rule: "required",
            errorMessage: "*Phone Number Field is required",
        },
        {
            rule: "minLength",
            value: 10,
            errorMessage: "*Invalid Phone number",
        },
        {
            rule: "maxLength",
            value: 10,
            errorMessage: "*Invalid Phone number",
        },
    ])
    .addField("#add_guest_select", [
        {
            rule: "required",
            errorMessage: "*User Field is required",
        },
    ])
    .addField("#address_line_one", [
        {
            rule: "required",
            errorMessage: "*Address  Field is required",
        },


    ])
    .addField("#address_line_two", [
        {
            rule: "required",
            errorMessage: "*Address Field is required",
        },

    ])
    .addField("#address_type_select", [
        {
            rule: "required",
            errorMessage: "*Address Field is required",
        },
    ])
    .addField(".pincode_type", [
        {
            rule: "required",
            errorMessage: "*pincode Field is required",
        },

        {
            rule: "maxLength",
            value: 6,
            errorMessage: "*Pincode invalid number",
        },
    ])
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", "true");
        addUserFormSubmit(event);
    });

editValidator
    .addField("#edit_username", [
        {
            rule: "required",
            errorMessage: "*User Name Field is required",
        },
        {
            rule: "minLength",
            value: 3,
            errorMessage: "*Should more than 3 characters",
        },
        {
            rule: "maxLength",
            value: 15,
            errorMessage: "*Should not more than 15 characters",
        },
    ])
    .addField("#edit_email", [
        {
            rule: "required",
            errorMessage: "*Email Field is required",
        },
        {
            rule: "email",
            errorMessage: "*Must be valid Email Format(ex:- john@gmail.com)",
        },
    ])
    .addField("#edit_phone_number", [
        {
            rule: "required",
            errorMessage: "*Phone Number Field is required",
        },
        {
            rule: "minLength",
            value: 10,
            errorMessage: "*Invalid Phone number",
        },
        {
            rule: "maxLength",
            value: 10,
            errorMessage: "*Invalid Phone number",
        },
    ])
    .addField("#edit_guest_select", [
        {
            rule: "required",
            errorMessage: "*User Field is required",
        },
    ])
    .addField("#edit_address_line_one", [
        {
            rule: "required",
            errorMessage: "*Address  Field is required",
        },


    ])
    .addField("#edit_address_line_two", [
        {
            rule: "required",
            errorMessage: "*Address Field is required",
        },

    ])
    .addField("#edit_address_type_select", [
        {
            rule: "required",
            errorMessage: "*Address Field is required",
        },
    ])
    .addField(".pincode_type1", [
        {
            rule: "required",
            errorMessage: "*pincode Field is required",
        },

        {
            rule: "maxLength",
            value: 6,
            errorMessage: "*Pincode invalid number",
        },
    ])


    .onSuccess((event) => {
        editUserFormSubmit(event);
    });

const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Country",
        "Name",

        // "login",
        "Phone Number",
        // "Date",
        // {
        //     name: "Order Manually",
        //     sort: false,
        // },
        // {
        //     name: "Action",
        //     sort: false,
        // },
        // {
        //     name: "Address",
        //     sort: false,
        // },

    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: users.map((user, index) => {
        const dateObject = new Date(user.created_at);
        const day = dateObject.getDate().toString().padStart(2, '0');
        const month = (dateObject.getMonth() + 1).toString().padStart(2, '0'); // Note: Month is zero-based
        const year = dateObject.getFullYear();

        const formattedDate = `${day}-${month}-${year}`;
        return [      
            index + 1,
            user.country_name ?? "-",
            gridjs.html(
                `<a href="customers/${user.user_id || user.id}" >${user.name} </a>`
            ),

            // `${
            //     user.address_line_one
            // }`,
            //   gridjs.html(
            //     user.from_app === 0
            //     ?`<div class="text-warning" style="font-weight:bold">Web Site</div>`
            //     :`<div class="text-success" style="font-weight:bold">App</div>`

            // ),

            gridjs.html(
                user.phone_number ? `<a href="tel:+91${user.phone_number}">+91${user.phone_number}</a>` : user.phone ? `<a href="tel:+91${user.phone}">+91${user.phone}</a>` : '-'
            ),
            // formattedDate,

            // gridjs.html(
            //     `<div> <button data-bs-toggle="modal"
            //     data-userid ="${user.id}"
            //     data-orguserid = "${user.user_id || user.id}"
            //     data-username="${user.name}"
            //     data-email ="${user.email}" data-phonenumber="${user.phone_number}" data-bs-target="#manualUserModal"  class="btn btn-dark manual_edit_btn">
            //     <i class="bx bxs-shopping-bag"></i>
            //     </button></div>`
            // ),

            // gridjs.html(
            //     `<div>

            //         <button data-bs-toggle="modal"
            //         data-userid ="${user.id}"
            //         data-orguserid = "${user.user_id || user.id}"
            //         data-username="${user.name}"
            //         data-email ="${user.email}" data-phonenumber="${user.phone_number}"
            //         data-user ="${user.is_guest_user}"
            //         data-added1 ="${user.address_line_one}"
            //         data-added2 ="${user.address_line_two}"
            //         data-land ="${user.landmark}"
            //         data-seletype ="${user.address_type_id}"
            //         data-pincode ="${user.pincode}"
            //         data-type ="${user.address_type_id}"
            //         data-city ="${user.city}"
            //         data-areaname ="${user.area_name}"
            //         data-sate ="${user.state}"
            //         class="btn btn-secondary edit_btn " data-bs-target="#editUserModal">Edit</button> <button class="btn btn-danger delete_btn" data-userid = ${user.id}>Delete</button>
            //      </div>`
            // ),

            // gridjs.html(
            //     `<div class="text-center">

            //         <button data-bs-toggle="modal" data-bs-target="#addAddressModal"
            //         data-userid ="${user.id}"
            //         data-orguserid = "${user.user_id || user.id}"class="btn btn-success add_addre_btn"><i class='bx bx-home'></i></button>
            //      </div>`
            // ),



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

// assign button click function

gridNew.render(document.getElementById("table-gridjs"));

function gridjsReRender(users) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: users.map((user, index) => {
                const dateObject = new Date(user.created_at);
                const day = dateObject.getDate().toString().padStart(2, '0');
                const month = (dateObject.getMonth() + 1).toString().padStart(2, '0'); // Note: Month is zero-based
                const year = dateObject.getFullYear();

                const formattedDate = `${day}-${month}-${year}`;
                return [      
                    index + 1,
                    user.country_name ?? "-",
                    gridjs.html(
                        `<a href="customers/${user.user_id || user.id}" >${user.name} </a>`
                    ),
                    gridjs.html(
                        user.phone_number ? `<a href="tel:+91${user.phone_number}">+91${user.phone_number}</a>` : user.phone ? `<a href="tel:+91${user.phone}">+91${user.phone}</a>` : '-'
                    ),
                ];
            }),
        })
        .forceRender();
}
$(document).on("click", ".add_addre_btn", function () {
    const catId = $(this).attr("data-categoryid");

    $("#user_dataid").val($(this).attr("data-orguserid"));
    $("#user_valueid").val($(this).attr("data-userid"));
});


// function addAddressFormSubmit(e) {
//     const formdata = new FormData(e.target);
//     $.ajax({
//         url: "addaddressvalue",
//         method: "POST",
//         dataType: "json",
//         data: formdata,
//         processData: false,
//         contentType: false,
//         success: function (response) {
//             $(".address_submit_btn").removeAttr("disabled");
//             $(".address_submit_btn").html("Submit");

//             const updatedUsers = response.users;
//             $("#addAddressForm")[0].reset();
//             $("#addAddressModal").hide();
//             $(".modal-backdrop").remove();
//             document.body.style.overflowY = "scroll";

//             console.log(updatedUsers);

//             gridjsReRender(updatedProduct);
//             Swal.fire("Added", "Records Added Successfully.", "success");
//         },
//         error: function (jqXHR, textStatus, errorThrown) {
//             $(".address_submit_btn").removeAttr("disabled");
//             $(".address_submit_btn").removeAttr("disabled");
//             $(".address_submit_btn").html("Update");
//             $(".address_submit_btn").html("Submit");
//             console.log(textStatus + ": " + errorThrown);

//             Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
//         },
//     });
// }

function addUserFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "customers",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedUsers = response.users;
            $("#addUserForm")[0].reset();
            $("#addUserModal").hide();
            $(".modal-backdrop").remove();
            $(".edit_submit_btn").removeAttr("disabled");
            $(".add_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedUsers);
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

function editUserFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updateUser/" + $("#edit_user_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedUsers = response.users;
            $("#editUserForm")[0].reset();
            $("#editUserModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedUsers);
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

const phoneNumberInput = document.getElementById("add_phone_number");
const editphoneNumberInput = document.getElementById("edit_phone_number");

// Prevent non-numeric input
phoneNumberInput.addEventListener("input", function (event) {
    const input = event.target.value;
    const numericInput = input.replace(/[^0-9]/g, "");
    phoneNumberInput.value = numericInput;
});
editphoneNumberInput.addEventListener("input", function (event) {
    const input = event.target.value;
    const numericInput = input.replace(/[^0-9]/g, "");
    editphoneNumberInput.value = numericInput;
});

// show hide product while manual entry
$(function () {
    $(".selected_product").hide();
    $(".other_selected_product").hide();
    $("#add_product_select").on("change", function () {
        const productId = this.value;

        if (!productId) {
            $(".selected_product").hide();
            return;
        }

        $.ajax({
            type: "post",
            url: "getProductDetail",
            data: {
                product_id: productId,
            },
            success: function (response) {
                $(".selected_product").slideDown();
                const {
                    product_name,
                    product_regular_price,
                    product_mrp_price,
                    product_image,
                } = response;

                $(".manual_product_name").html(product_name);
                $(".manual_regular_price").html("₹" + product_regular_price);
                $(".manual_mrp_price").html("₹" + product_mrp_price);
                $(".manual_card_image").attr("src", `/images/${product_image}`);
            },
        });
    });

    $("#add_other_product_select").on("change", function () {
        const productId = this.value;

        if (!productId) {
            $(".other_selected_product").hide();
            return;
        }

        $.ajax({
            type: "post",
            url: "getProductDetail",
            data: {
                product_id: productId,
            },
            success: function (response) {
                $(".other_selected_product").slideDown();
                const {
                    product_name,
                    product_regular_price,
                    product_mrp_price,
                    product_image,
                } = response;

                $(".other_manual_product_name").html(product_name);
                $(".other_manual_regular_price").html(
                    "₹" + product_regular_price
                );
                $(".other_manual_mrp_price").html("₹" + product_mrp_price);
                $(".other_manual_card_image").attr(
                    "src",
                    `/images/${product_image}`
                );
            },
        });
    });
});

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
        const catId = $(this).attr("data-areaname");
        $("#edit_user_id").val($(this).attr("data-userid"));
        $("#edit_username").val($(this).attr("data-username"));
        $("#edit_email").val($(this).attr("data-email"));
        $("#edit_phone_number").val($(this).attr("data-phonenumber"));
        $("#edit_password").val("");
        $("#edit_address_line_one").val($(this).attr("data-added1"));
        $("#edit_address_line_two").val($(this).attr("data-added2"));
        $("#edit_landmark").val($(this).attr("data-land"));
        $("#edit_address_type_select").val($(this).attr("data-type"));
        $('#edit_guest_select').val($(this).attr("data-user"))


        $("#pin_code_type1").val($(this).attr("data-pincode"));
        // $("#city_input1").val($(this).attr("data-areaname"));

        $("#city_input1")
            .find(`option[value="${catId}"]`)
            .prop("selected", true);
        $("#pin_district1").val($(this).attr("data-city"));

        $("#pin_state1").val($(this).attr("data-sate"));
    });

    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-userid");
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
                    url: "destroyUser/" + id,
                    method: "post",
                    dataType: "json",
                    success: function (response) {
                        const updatedUsers = response.users;
                        gridjsReRender(updatedUsers);
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

    $(document).on("click", ".manual_edit_btn", function () {
        window.currentUserId = $(this).attr("data-orguserid");
    });

    let firstLoad = 0;
    $(document).on("click", ".add_btn_top_el", function () {
        setTimeout(() => {
            if (firstLoad != 0) return;
            $("#add_username").val("");
            $("#add_password").val("");
            $("#addUserForm  .just-validate-error-label").hide();
            firstLoad++;
        }, 100);
    });
});

// Manual Order Date Picker Functions
const monthlyDatePicker = flatpickr(".date_picker_from_date", {
    minDate: "today",
    altInput: true,
    altFormat: "j F, Y",
    dateFormat: "Y-m-d",
});

const oneTimerDatePicker = flatpickr(".date_picker_one_time", {
    minDate: "today",
    altInput: true,
    altFormat: "j F, Y",
    dateFormat: "Y-m-d",
});

const oneTimerDatePickerNew = flatpickr(".date_picker_one_time_new", {
    minDate: "today",
    altInput: true,
    altFormat: "j F, Y",
    dateFormat: "Y-m-d",
});

const customizedDatePicker = flatpickr(".datepicker-customized", {
    mode: "multiple",
    dateFormat: "Y-m-d",
    minDate: "today",
    altInput: true,
    altFormat: "j F, Y",
    dateFormat: "Y-m-d",
});

$(function () {
    $(".date-input-container").on("click", function () {
        const currentInput = $(this).find("input");
        $(currentInput).trigger("focus");
    });

    $(".date_picker_one_time").on("change", function () {
        $("#add_product_quantity").val("");
        const inputValue = this.value;

        if (!inputValue) return;
        $("#add_product_quantity").val(1);
    });

    $(".plan_select").on("change", function () {
        $("#add_product_quantity").val("");
        $(".date_picker_from_date").val("");
        $(".date_picker_to_date").val("");
        $(".date_picker_one_time").val("");
        $(".datepicker-customized").val("");
        const inputValue = this.value;
        if (!inputValue) return;
        $(".show_el").hide();
        $(`[ data-plan-type="${inputValue}"]`).show();
        if (inputValue == "1") {
            manualValidator
                .addField(".date_picker_from_date", [
                    {
                        rule: "required",
                        errorMessage: "*From date field is required",
                    },
                ])
                .addField(".date_picker_to_date", [
                    {
                        rule: "required",
                        errorMessage: "*To date field is required",
                    },
                ]);
        } else {
            Object.entries(manualValidator.fields).forEach((item, value) => {
                if (item[1].elem.classList.contains("date_picker_from_date")) {
                    manualValidator.removeField(".date_picker_from_date");
                    manualValidator.removeField(".date_picker_to_date");
                }
            });
        }

        if (inputValue == "2") {
            manualValidator.addField(".date_picker_one_time", [
                {
                    rule: "required",
                    errorMessage: "*Date field is required",
                },
            ]);
        } else {
            Object.entries(manualValidator.fields).forEach((item, value) => {
                if (item[1].elem.classList.contains("date_picker_one_time")) {
                    manualValidator.removeField(".date_picker_one_time");
                }
            });
        }

        if (inputValue == "3") {
            manualValidator.addField(".datepicker-customized", [
                {
                    rule: "required",
                    errorMessage: "*Date field is required",
                },
            ]);
        } else {
            Object.entries(manualValidator.fields).forEach((item, value) => {
                if (item[1].elem.classList.contains("datepicker-customized")) {
                    manualValidator.removeField(".datepicker-customized");
                }
            });
        }
    });

    $(".datepicker-customized").on("change", function () {
        $("#add_product_quantity").val("");
        const inputValue = this.value;
        if (!inputValue) return;
        const selectedDateCounts = inputValue.split(",").length;
        $("#add_product_quantity").val(selectedDateCounts);
    });

    $(".date_picker_from_date").on("change", function () {
        $("#add_product_quantity").val("");
        const inputValue = this.value;
        if (!inputValue) return;

        // Get the value of the selected date in milliseconds
        const fromDate = new Date($(this).val()).getTime();

        // Add 30 days in milliseconds
        const toDate = fromDate + 29 * 24 * 60 * 60 * 1000;

        // Create a new date object with the updated date and format it as yyyy-mm-dd
        const toDateFormatted = new Date(toDate).toISOString().substring(0, 10);

        // Set the updated date as the value of the ".date_picker_to_date" input field
        $(".date_picker_to_date").val(toDateFormatted);

        // window.toDatePickr = toDatePickr;

        $("#add_product_quantity").val(30);
    });
});

const manualValidator = new JustValidate("#manualOrderForm", {
    validateBeforeSubmitting: true,
});

manualValidator
    .addField("#add_product_select", [
        {
            rule: "required",
            errorMessage: "*Select Product Field",
        },
    ])
    .addField(".plan_select", [
        {
            rule: "required",
            errorMessage: "*Select Plan Type",
        },
    ])
    .addField("#add_product_quantity", [
        {
            rule: "required",
            errorMessage: "*Choose product quantity field",
        },
    ])
    .onSuccess((event) => {
        $(".manual_submit_btn").attr("disabled", "true");
        manualOrderSubmit(event);
    });

const manualProductValidator = new JustValidate("#manualProductOrderForm", {
    validateBeforeSubmitting: true,
});

manualProductValidator
    .addField(".other_category_select", [
        {
            rule: "required",
            errorMessage: "*Select Field",
        },
    ])
    .addField(".other_product_select", [
        {
            rule: "required",
            errorMessage: "*Select Field",
        },
    ])
    .addField(".other_product_quantity", [
        {
            rule: "required",
            errorMessage: "*Select Field",
        },
    ])

    .onSuccess((event) => {
        $(".manual_submit_btn").attr("disabled", "true");
        otherManualOrderSubmit(event);
    });
function otherManualOrderSubmit(event) {
    const product_id = $("#add_other_product_select").val();
    const quantity = $("#other_product_quantity").val();
    const selecttype = $("#other_select_type").val();



    const formData = new FormData(event.target);
    formData.append("user_id", currentUserId);
    $.ajax({
        type: "post",
        url: "createProductSubscription",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $("#manualProductOrderForm")[0].reset();
            $("#manualUserModal").hide();
            $(".modal-backdrop").remove();
            $(".other_selected_product").hide();
            $(".manual_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            Swal.fire("Added", "Order Created Successfully", "success");
            oneTimerDatePickerNew.clear();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".manual_submit_btn").removeAttr("disabled");
            $(".manual_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

function manualOrderSubmit(event) {
    const plan_type = $(".plan_select").val();
    const product_id = $("#add_product_select").val();
    const monthly_from_date = $(".date_picker_from_date").val();
    const monthly_to_date = $(".date_picker_to_date").val();
    const one_time_date = $(".date_picker_one_time").val();
    const customized_date = $(".datepicker-customized").val();

    const data = {};

    if (plan_type == 1) {
        data["plan_type"] = plan_type;
        data["quantity"] = 1;
        data["user_id"] = currentUserId;
        data["product_id"] = product_id;
        data["from_date"] = monthly_from_date;
        data["to_date"] = monthly_to_date;
    }

    if (plan_type == 2) {
        data["plan_type"] = plan_type;
        data["quantity"] = 1;
        data["user_id"] = currentUserId;
        data["product_id"] = product_id;
        data["from_date"] = one_time_date;
    }

    if (plan_type == 3) {
        const datesArray = customized_date.split(", ").sort();
        data["from_date"] = datesArray[0];
        data["to_date"] = datesArray[datesArray.length - 1];
        data["plan_type"] = plan_type;
        data["quantity"] = 1;
        data["user_id"] = currentUserId;
        data["product_id"] = product_id;
        data["selected_dates"] = datesArray;
    }
    $.ajax({
        type: "post",
        url: "createMilkSubscription",
        data: data,
        success: function (response) {
            $("#manualOrderForm")[0].reset();
            $("#manualUserModal").hide();
            $(".modal-backdrop").remove();
            $(".selected_product").hide();
            $(".manual_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            Swal.fire("Added", "Order Created Successfully", "success");
            $(".show_el").hide();
            monthlyDatePicker.clear();
            oneTimerDatePicker.clear();
            customizedDatePicker.clear();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".manual_submit_btn").removeAttr("disabled");
            $(".manual_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

$("#manualOrderForm").hide();
$("#manualProductOrderForm").hide();
$("#add_category_select").on("change", function () {
    const isCowMilk = this.value == milkProductId;
    const categoryid = this.value;

    $(".pr_alert").alert("close");

    if (isCowMilk) {
        $("#manualProductOrderForm").hide();
        $("#manualOrderForm").show();
        $(".selected_product").hide();
        $(this).closest(".modal-dialog").removeClass("modal-lg");
        $(this).closest(".modal-dialog").addClass("modal-md");

        $.ajax({
            type: "post",
            url: "getProductsOptions",
            data: {
                category_id: categoryid,
            },
            success: function (response) {
                if (isCowMilk) {
                    $("#add_product_select").html(response);
                    return;
                }
            },
            error: function (response) {
                console.log(response);
            },
        });
    }

    if (!isCowMilk) {
        $("#manualOrderForm").hide();
        $("#manualProductOrderForm").show();
        $(".other_selected_product").hide();

        $(this).closest(".modal-dialog").removeClass("modal-md");
        $(this).closest(".modal-dialog").addClass("modal-lg");
    }
});

$(document).on("change", ".other_category_select", function () {
    const categoryid = this.value;
    const currentProductEl = $(this)
        .closest(".product_row")
        .find(".other_product_select");

    const optionsHtml = `<option value="">Loading...</option>`;

    $(currentProductEl).html(optionsHtml);

    $.ajax({
        type: "post",
        url: "getProductsOptions",
        data: {
            category_id: categoryid,
        },
        success: function (response) {
            $(currentProductEl).html(response);
        },
        error: function (response) {
            console.log(response);
        },
    });
});

$(document).on("change", ".other_product_select", function () {
    const productid = this.value;
    const currentProductEl = $(this)
        .closest(".product_row")
        .find(".other_product_varient");

    const optionsHtml = `<option value="">Loading...</option>`;

    $(currentProductEl).html(optionsHtml);

    $.ajax({
        type: "post",
        url: "getProductsverentOptions",
        data: {
            product_id: productid,
        },
        success: function (response) {
            $(currentProductEl).html(response);
        },
        error: function (response) {
            console.log(response);
        },
    });
});

$(document).on("change", ".other_product_varient", function () {
    const productvarid = this.value;
    var parentrow = $(this).closest('.row');
    var showqty = parentrow.find('.other_product_quantity');
    var hiddenqty = parentrow.find('.hidden_qty');


    $.ajax({
        type: "post",
        url: "getProductsverentqty",
        data: {
            id: productvarid,
        },
        success: function (response) {
            // $(currentProductEl).html(response);
            parentrow.find('.other_product_quantity').val(response['product_qty']);

            var quantity = parseFloat(response['product_qty']);
            showqty.val(quantity);
            hiddenqty.val(quantity);

            showqty.on('input', function () {
                var qty = parseFloat(showqty.val()) || 0;
                var availableQty = parseFloat(hiddenqty.val()) || 0;

                if (qty > availableQty) {
                    parentrow.find('.qty_error').html('Quantity Cannot be exceeded than' + availableQty);
                    // alert('Input quantity cannot exceed available quantity (' +
                    //     availableQty +
                    //     ').');
                    showqty.val(availableQty);
                } else { }
            });


        },
        error: function (response) {
            console.log(response);
        },
    });


});




$(function () {
    const deleteBtn = `
    <div class="col-md-1">
        <button type="button" class="btn btn-danger btn-sm delete_col_btn"><i
                class="fa fa-trash"></i></button>
    </div>
    `;
    const productRowHtml = $(".product_row");
    const productRowWithDeleteBtn = productRowHtml.clone().append(deleteBtn);
    let rowCount = 0;

    $(document).on("click", ".add_col_btn", function () {
        const newProductRow = productRowWithDeleteBtn.clone();
        newProductRow
            .find(".other_category_select")
            .addClass("category_select_" + rowCount)
            .attr("data-custom-validation", "validate");
        newProductRow
            .find(".other_product_select")
            .addClass("product_select_" + rowCount)
            .attr("data-custom-validation", "validate");
        newProductRow
            .find(".other_product_quantity")
            .addClass("product_quantity_" + rowCount)
            .attr("data-custom-validation", "validate");

        const categoryClass = ".category_select_" + rowCount;
        const productSelect = ".product_select_" + rowCount;
        const productQuantity = ".product_quantity_" + rowCount;

        $(".group_container").append(newProductRow);
        rowCount++;

        // manualProductValidator
        manualProductValidator.addField(categoryClass, [
            {
                rule: "required",
                errorMessage: "*Select  Field",
            },
        ]);
        manualProductValidator.addField(productSelect, [
            {
                rule: "required",
                errorMessage: "*Select  Field",
            },
        ]);
        manualProductValidator.addField(productQuantity, [
            {
                rule: "required",
                errorMessage: "*Select  Field",
            },
        ]);
    });

    $(document).on("click", ".delete_col_btn", function () {
        $(this)
            .closest(".product_row")
            .find(`[data-custom-validation="validate"]`)
            .each(function () {
                let classes = $(this).attr("class").split(" ").filter(Boolean);
                manualProductValidator.removeField("." + classes[2]);
            });

        $(this).closest(".row").remove();
    });
});


// date filter functions

$(".cs_from_date").on("change", function () {
    const selectedDate = this.value;
    if (!selectedDate) return;
    $(".cs_to_date").removeAttr("disabled");
    $(".cs_to_date").attr("min", selectedDate);

})

$('#myusername').on("change", function () {
    console.log("gyvtfvvyg");
    $('[type="search"]').val($(this).val());
    $('[type="search"]').trigger("input");

})

const fromleadin = document.getElementById('from-date2');
const todayleadin = new Date().toISOString().split('T')[0];
fromleadin.setAttribute('max', todayleadin);

fromleadin.addEventListener('change', function () {
    if (this.value > todayleadin) {
        this.value = '';
        alert('Please select a date in the past or present.');
    }
}); const toinputle = document.getElementById('to-date2');
const todayinputs = new Date().toISOString().split('T')[0];
toinputle.setAttribute('max', todayinputs);

toinputle.addEventListener('change', function () {
    if (this.value > todayinputs) {
        this.value = '';
        alert('Please select a date in the past or present.');
    }
});

const addValidators = new JustValidate("#customerForm", {
    validateBeforeSubmitting: true,
});

addValidators

    .addField(".fromdate", [
        {
            rule: "required",
            errorMessage: "*From Date Field is required",
        },
    ])

    .addField(".todate", [
        {
            rule: "required",
            errorMessage: "*To Date Field is required",
        },
    ])
    .onSuccess((event) => {
        $(".user_submit_btn").attr("disabled", "true");
        $(".user_submit_btn").html("Uploading.....");
        customerFormSubmit(event);
    });


function customerFormSubmit(event) {
    const formData = new FormData(event.target);
    $.ajax({
        url: "getcustomersummery",
        method: "POST",
        dataType: "json",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".user_submit_btn").removeAttr("disabled");
            $(".user_submit_btn").html("Get Report");
            const updatedProduct = response.users;
            gridjsReRender(updatedProduct);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".user_submit_btn").removeAttr("disabled");
            $(".user_submit_btn").html("Get Report");
            Swal.fire(textStatus.toUpperCase(), textStatus, "warning");
        },
    });
}


// state city function ajax

$('.custid').on('change', function () {
    var custid = $(this).val();

    // $('.produ1').hide();
    // $('.product2').show();


    $('.cityname').empty();
    $('.cityname').append(
        `<option value="0" disabled selected>Processing...</option>`);

    $.ajax({
        type: 'GET',
        url: 'Getcitys/' + custid,
        dataType: 'JSON',
        success: function (response) {

            $('.cityname').empty();
            $('.cityname').append(
                `<option value="0" disabled selected>Select Product</option>`
            );

            response.forEach(element => {
                $('.cityname').append(
                    `<option value="${element['id']}">${element['name']}</option>`
                );
            });
        }
    });
});


$(document).ready(function () {
    $('#pin_code_type').on('input', function () {
        var pinCode = $(this).val();
        if (pinCode.length === 6) {
            // Make AJAX request to Laravel backend
            $.ajax({
                url: '/get-address-details',
                method: 'GET',
                data: { pincode: pinCode },
                success: function (response) {
                    // Clear existing options in the dropdown
                    $('#city_input').empty();

                    var district = response[0].match(/District: (.*?),/)[1];
                    var state = response[0].match(/State: (.*)/)[1];

                    $('#pin_district').val(district);
                    $('#pin_state').val(state);

                    // Iterate through each city in the response and add it to the dropdown
                    response.forEach(function (cityDetails) {
                        // Extract city, district, and state
                        var cityMatch = cityDetails.match(/City: (.*?),/);


                        var city = cityMatch ? cityMatch[1] : '';

                        // Append a new option to the dropdown
                        $('#city_input').append($('<option>', {
                            value: city,
                            text: city
                        }));
                    });
                },
                error: function (error) {
                    console.error('Error fetching address details:', error);
                }
            });
        }
    });
});
// edit manual entry

$(document).ready(function () {
    $('#pin_code_type1').on('input', function () {
        var pinCode = $(this).val();
        if (pinCode.length === 6) {
            // Make AJAX request to Laravel backend
            $.ajax({
                url: '/get-address-details1',
                method: 'GET',
                data: { pincode: pinCode },
                success: function (response) {
                    // Clear existing options in the dropdown
                    $('#city_input1').empty();

                    var district = response[0].match(/District: (.*?),/)[1];
                    var state = response[0].match(/State: (.*)/)[1];

                    $('#pin_district1').val(district);
                    $('#pin_state1').val(state);

                    // Iterate through each city in the response and add it to the dropdown
                    response.forEach(function (cityDetails) {
                        // Extract city, district, and state
                        var cityMatch = cityDetails.match(/City: (.*?),/);


                        var city = cityMatch ? cityMatch[1] : '';

                        // Append a new option to the dropdown
                        $('#city_input1').append($('<option>', {
                            value: city,
                            text: city
                        }));
                    });
                },
                error: function (error) {
                    console.error('Error fetching address details:', error);
                }
            });
        }
    });
});
