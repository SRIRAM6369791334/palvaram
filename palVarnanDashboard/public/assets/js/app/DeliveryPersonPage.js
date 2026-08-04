const addValidator = new JustValidate("#addDeliveryPersonForm", {
    validateBeforeSubmitting: true,
});
const editValidator = new JustValidate("#editDeliveryPersonForm", {
    validateBeforeSubmitting: true,
});

addValidator
    .addField("#add_deliveryPersonname", [
        {
            rule: "required",
            errorMessage: "*DeliveryPerson Name Field is required",
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
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", "true");
        $(".add_submit_btn").html("Uploading.....");

        addDeliveryPersonFormSubmit(event);
    });

editValidator
    .addField("#edit_deliveryPersonname", [
        {
            rule: "required",
            errorMessage: "*DeliveryPerson Name Field is required",
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
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", "true");
        $(".add_submit_btn").html("Uploading.....");
        editDeliveryPersonFormSubmit(event);
    });

const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "ID",
        "DeliveryPersonname",
        "Phone Number",
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
    data: deliveryPersons.map((deliveryPerson, index) => {
        return [
            index + 1,
            deliveryPerson.delivery_person_id,
            deliveryPerson.name,
            deliveryPerson.phone_number,
            gridjs.html(
                `<div> <button data-bs-toggle="modal"
                data-deliveryPersonid ="${deliveryPerson.id}"
                data-deliveryPersonname="${deliveryPerson.name}"
                data-email ="${deliveryPerson.email}" data-phonenumber="${deliveryPerson.phone_number}" data-bs-target="#editDeliveryPersonModal"  class="btn btn-secondary edit_btn ">Edit</button> <button class="btn btn-danger delete_btn" data-deliveryPersonid = ${deliveryPerson.id}>Delete</button> </div>`
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

function gridjsReRender(deliveryPersons) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: deliveryPersons.map((deliveryPerson, index) => {
                return [
                    index + 1,
                    deliveryPerson.delivery_person_id,
                    deliveryPerson.name,
                    deliveryPerson.phone_number,
                    gridjs.html(
                        `<div> <button data-bs-toggle="modal"
                        data-deliveryPersonid ="${deliveryPerson.id}"
                        data-deliveryPersonname="${deliveryPerson.name}"
                        data-email ="${deliveryPerson.email}" data-phonenumber="${deliveryPerson.phone_number}" data-bs-target="#editDeliveryPersonModal"  class="btn btn-secondary edit_btn ">Edit</button> <button class="btn btn-danger delete_btn" data-deliveryPersonid = ${deliveryPerson.id}>Delete</button> </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

function addDeliveryPersonFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "deliveryPersons",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedDeliveryPersons = response.deliveryPersons;
            $("#addDeliveryPersonForm")[0].reset();
            $("#addDeliveryPersonModal").hide();
            $(".modal-backdrop").remove();
            $(".add_submit_btn").removeAttr("disabled");
            $(".add_submit_btn").html("Submit");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedDeliveryPersons);
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

function editDeliveryPersonFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updateDeliveryPerson/" + $("#edit_deliveryPerson_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedDeliveryPersons = response.deliveryPersons;
            $("#editDeliveryPersonForm")[0].reset();
            $("#editDeliveryPersonModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            $(".edit_submit_btn").removeAttr("disabled");
            $(".edit_submit_btn").html("Submit");
            gridjsReRender(updatedDeliveryPersons);
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
        $("#edit_deliveryPerson_id").val($(this).attr("data-deliveryPersonid"));
        $("#edit_deliveryPersonname").val(
            $(this).attr("data-deliveryPersonname")
        );
        $("#edit_email").val($(this).attr("data-email"));
        $("#edit_phone_number").val($(this).attr("data-phonenumber"));
        $("#edit_password").val("");
    });

    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-deliveryPersonid");
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
                    url: "destroyDeliveryPerson/" + id,
                    method: "post",
                    dataType: "json",
                    success: function (response) {
                        const updatedDeliveryPersons = response.deliveryPersons;
                        gridjsReRender(updatedDeliveryPersons);
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

    let firstLoad = 0;
    $(document).on("click", ".add_btn_top_el", function () {
        setTimeout(() => {
            if (firstLoad != 0) return;
            $("#add_email").val("");
            $("#add_password").val("");
            firstLoad++;
        }, 500);
    });
});
