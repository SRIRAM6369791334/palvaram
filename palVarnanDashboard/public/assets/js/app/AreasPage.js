const addValidator = new JustValidate("#addAreaForm", {
    validateBeforeSubmitting: true,
});
const editValidator = new JustValidate("#editAreaForm", {
    validateBeforeSubmitting: true,
});

const addDeliveryPersonValidator = new JustValidate("#addDeliveryPersonForm", {
    validateBeforeSubmitting: true,
});

const deleteDeliveryPersonValidator = new JustValidate(
    "#deleteDeliveryPersonForm",
    {
        validateBeforeSubmitting: true,
    }
);

deleteDeliveryPersonValidator
    .addField("#delete_assign_area_id", [
        {
            rule: "required",
            errorMessage: "*Area Name Field is required",
        },
    ])
    .onSuccess((event) => {
        $(".deleteDeliverySubmitBtn").attr("disabled", "true");
        deleteDeliveryFormSubmit(event);
    });

addDeliveryPersonValidator
    .addField("#assign_area_id", [
        {
            rule: "required",
            errorMessage: "*Area Name Field is required",
        },
    ])
    .addField("#add_delivery_persons_multiple", [
        {
            rule: "required",
            errorMessage: "*Choose Any Delivery Person",
        },
    ])
    .onSuccess((event) => {
        $(".addDeliverySubmitBtn").attr("disabled", "true");
        addDeliveryFormSubmit(event);
    });

function checkArea() {
    const areaName = $("#add_areaname").val();
    if (!areaName) {
        addValidator.showErrors({
            "#add_areaname": "*Area Name Field is required",
        });
        return new Promise((resolve) => {
            resolve(false);
        });
    }

    return new Promise((resolve, reject) => {
        $.ajax({
            type: "post",
            url: "checkAreaValidation",
            data: {
                area_name: areaName,
            },
            success: function (response) {
                if (response == "0") {
                    resolve(true);
                } else {
                    addValidator.showErrors({
                        "#add_areaname": "*Area Name Already Exists",
                    });

                    resolve(false);
                }
            },
            error: function (response) {
                reject("something went wrong");
            },
        });
    });
}

$("#add_areaname").on("change", checkArea);

addValidator
    .addField("#add_areaname", [
        {
            rule: "required",
            errorMessage: "*Area Name Field is required",
        },
        {
            validator: () => checkArea,
            errorMessage: "*Area Name already Exists",
        },
    ])
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", "true");
        addAreaFormSubmit(event);
    });

editValidator
    .addField("#edit_areaname", [
        {
            rule: "required",
            errorMessage: "*Area Name Field is required",
        },
    ])
    .addField("#edit_pincode_input", [
        {
            rule: "required",
            errorMessage: "*Pincode Field is required",
        },
        {
            rule: "minLength",
            value: 6,
            errorMessage: "*Pincode Should be  6 digits",
        },
        {
            rule: "maxLength",
            value: 6,
            errorMessage: "*Pincode Should be  6 digits",
        },
    ])
    .onSuccess((event) => {
        editAreaFormSubmit(event);
    });

// <button class="btn  btn-dark btn-outline-secondary remove_dp_btn" title="Remove Delivery Partner from  this area">Remove DP</button>
const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Pincode",
        "Area Name",
        "Delivery Assigned",
        "Assign Delivery Persons to Area",
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
    data: areas.map((area, index) => {
        return [
            index + 1,
            area.area_pincode,
            area.area_name,
            gridjs.html(
                `

                    <div class="dropdown">
                <btn class="me-3 dropdown-toggle hidden-arrow position-relative pe-none" href="#" id="navbarDropdownMenuLink"
                role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-truck-fast-outline delivery_assign_truck_icon"></i>
                    <span class="badge rounded-pill badge-notification
                    ${area.area_assigns_count ? "bg-primary" : "bg-danger"}
                    notification_count_el">${area.area_assigns_count}</span>
                </btn>

            </div>
                    `
            ),
            gridjs.html(`
            <div>
            <button
            data-bs-toggle="modal"
            data-bs-target="#addDeliveryPersonModal"
            data-areaid ="${area.id}"
            data-areaname="${area.area_name}"
            data-areapincode = "${area.area_pincode}"
            class="btn btn-purple assign_dp_btn" title="Assign Delivery Partner to this area">
            <i class="bx bxs-user-plus"></i>
            </button>

            <button
            data-bs-toggle="modal"
            data-bs-target="#deleteDeliveryPersonModal"
            data-areaid ="${area.id}"
            data-areaname="${area.area_name}"
            data-areapincode = "${area.area_pincode}"
            class="btn btn-danger delete_dp_btn" title="Remove  Delivery Partner from this area">
            <i class="bx bxs-user-minus"></i>
            </button>
            </div>
            `),
            gridjs.html(
                `<div> <button data-bs-toggle="modal"
                data-areaid ="${area.id}"
                data-areaname="${area.area_name}"
                data-areapincode = "${area.area_pincode}"
              data-bs-target="#editAreaModal"  class="btn btn-secondary edit_btn">Edit</button> <button class="btn btn-danger delete_btn" data-areaid = ${area.id}>Delete</button>
              </div>`
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

function gridjsReRender(areas) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: areas.map((area, index) => {
                return [
                    index + 1,
                    area.area_pincode,
                    area.area_name,
                    gridjs.html(
                        `

                            <div class="dropdown">
                        <btn class="me-3 dropdown-toggle hidden-arrow position-relative pe-none" href="#" id="navbarDropdownMenuLink"
                        role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-truck-fast-outline delivery_assign_truck_icon"></i>
                            <span class="badge rounded-pill badge-notification
                            ${
                                area.area_assigns_count
                                    ? "bg-primary"
                                    : "bg-danger"
                            }
                            notification_count_el">${
                                area.area_assigns_count
                            }</span>
                        </btn>

                    </div>
                            `
                    ),
                    gridjs.html(`
                    <div>
                    <button
                    data-bs-toggle="modal"
                    data-bs-target="#addDeliveryPersonModal"
                    data-areaid ="${area.id}"
                    data-areaname="${area.area_name}"
                    data-areapincode = "${area.area_pincode}"
                    class="btn btn-purple assign_dp_btn" title="Assign Delivery Partner to this area">
                    <i class="bx bxs-user-plus"></i>
                    </button>

                    <button
                    data-bs-toggle="modal"
                    data-bs-target="#deleteDeliveryPersonModal"
                    data-areaid ="${area.id}"
                    data-areaname="${area.area_name}"
                    data-areapincode = "${area.area_pincode}"
                    class="btn btn-danger delete_dp_btn" title="Remove  Delivery Partner from this area">
                    <i class="bx bxs-user-minus"></i>
                    </button>
                    </div>
                    `),
                    gridjs.html(
                        `<div> <button data-bs-toggle="modal"
                        data-areaid ="${area.id}"
                        data-areaname="${area.area_name}"
                        data-areapincode = "${area.area_pincode}"
                      data-bs-target="#editAreaModal"  class="btn btn-secondary edit_btn">Edit</button> <button class="btn btn-danger delete_btn" data-areaid = ${area.id}>Delete</button>
                      </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

function addAreaFormSubmit(e) {
    const formdata = new FormData(e.target);
    const otherNameValue = $("#other_area_name").val();
    const pincode = $("#pincode_input").val();

    console.log(pincode);

    if (otherNameValue) {
        formdata.append("area_name", otherNameValue);
    }
    formdata.append("area_pincode", pincode);

    // nav
    $.ajax({
        url: "areas",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedAreas = response.areas;
            $("#addAreaForm")[0].reset();
            $("#addAreaModal").hide();
            $(".modal-backdrop").remove();
            $(".area_name_container").hide();
            $(".edit_submit_btn").removeAttr("disabled");
            $(".add_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedAreas);
            Swal.fire("Added", "Records Added Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn").removeAttr("disabled");
            $(".add_submit_btn").removeAttr("disabled");
            $(".edit_submit_btn").html("Update");
            $(".add_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

function addDeliveryFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "assignDeliveryPerson",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updateddata = response.data;
            $(e.target)[0].reset();
            $("#addDeliveryPersonModal").hide();
            $(".modal-backdrop").remove();

            $(".addDeliverySubmitBtn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updateddata);
            Swal.fire("Added", "Records Added Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".addDeliverySubmitBtn").removeAttr("disabled");
            $(".add_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

function deleteDeliveryFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "deleteDeliveryPerson",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updateddata = response.data;
            $(e.target)[0].reset();
            $("#deleteDeliveryPersonModal").hide();
            $(".modal-backdrop").remove();
            $(".deleteDeliverySubmitBtn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updateddata);
            Swal.fire("Updated", "Records Updated Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".deleteDeliverySubmitBtn").removeAttr("disabled");
            $(".add_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

function editAreaFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updateArea/" + $("#edit_area_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedAreas = response.areas;
            $("#editAreaForm")[0].reset();
            $("#editAreaModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedAreas);
            Swal.fire("Updated", "Records Updated  Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn").removeAttr("disabled");
            $(".add_submit_btn").removeAttr("disabled");
            $(".edit_submit_btn").html("Update");
            $(".add_submit_btn").html("Submit");

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

$(function () {
    $(".bx-show").hide();
    $(".icon").on("click", function () {
        const parentEl = $(this).closest(".input-group");
        let input = $(this).closest(".input-group").find("input");
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
        $("#edit_area_id").val($(this).attr("data-areaid"));
        $("#edit_areaname").val($(this).attr("data-areaname"));
        $("#edit_pincode_input").val($(this).attr("data-areapincode"));

        //    come
    });

    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-areaid");
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
                    url: "destroyArea/" + id,
                    method: "post",
                    dataType: "json",
                    success: function (response) {
                        const updatedAreas = response.areas;
                        gridjsReRender(updatedAreas);
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

    let addchoicesForm = new Choices("#add_delivery_persons_multiple", {
        removeItemButton: !0,
    });

    let deletechoicesForm = new Choices("#delete_delivery_persons_multiple", {
        removeItemButton: !0,
    });

    $(document).on("click", ".assign_dp_btn", function () {
        const areaId = $(this).attr("data-areaid");
        const areaName = $(this).attr("data-areaname");

        $.ajax({
            url: "fetchAreaDeliveryPartners/" + areaId,
            method: "post",
            dataType: "json",
            success: function (response) {
                const optionsHtml = response.data;
                $("#assign_area_id").val(areaId);
                $("#assign_area_name").val(areaName);

                addchoicesForm?.destroy();
                $("#add_delivery_persons_multiple").html(optionsHtml);
                addchoicesForm = new Choices("#add_delivery_persons_multiple", {
                    removeItemButton: !0,
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    title: textStatus.toUpperCase(),
                    html: jqXHR.responseText,
                    icon: "warning",
                });
            },
        });
    });

    $(document).on("click", ".delete_dp_btn", function () {
        const areaId = $(this).attr("data-areaid");
        const areaName = $(this).attr("data-areaname");
        $(".deleteDeliverySubmitBtn").removeAttr("disabled");
        $.ajax({
            url: "deleteAreaDeliveryPartners/" + areaId,
            method: "post",
            dataType: "json",
            success: function (response) {
                const optionsHtml = response.data;
                if (optionsHtml == "") {
                    $(".deleteDeliverySubmitBtn").attr("disabled", "true");
                }
                $("#delete_assign_area_id").val(areaId);
                $("#delete_assign_area_name").val(areaName);
                deletechoicesForm?.destroy();
                $("#delete_delivery_persons_multiple").html(optionsHtml);
                deletechoicesForm = new Choices(
                    "#delete_delivery_persons_multiple",
                    {
                        removeItemButton: !0,
                    }
                );
            },
            error: function (jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    title: textStatus.toUpperCase(),
                    html: jqXHR.responseText,
                    icon: "warning",
                });
            },
        });
    });
});

// Area city dropdown functionality
let areaChoice;
function areaOptionInit() {
    areaChoice = new Choices($("#add_areaname")[0], {
        shouldSort: false,
    });
}
$(".area_loading").hide();

$("#pincode_input").on("input", function (event) {
    const input = this.value;
    const numericInput = input?.replace(/\D/g, "");
    this.value = numericInput;
});

$("#edit_pincode_input").on("input", function (event) {
    const input = this.value;
    const numericInput = input?.replace(/[^0-9]/g, "");
    this.value = numericInput;
});

$("#pincode_input").on("input", _.debounce(ajaxGetArea, 300));

function ajaxGetArea(e) {
    if (e) e.preventDefault();
    const pincode = $("#pincode_input").val();

    if (!pincode || pincode.length < 6) {
        $(".area_name_container").hide();
        return;
    }

    $(".area_loading").show();
    $(".area_name_container").hide();

    $.ajax({
        type: "post",
        url: "getPincodeAreas",
        data: {
            pincode,
        },
        success: function (response) {
            $(".area_loading").hide();
            $(".area_name_container").show();

            if (areaChoice) {
                areaChoice.destroy();
                $("#add_areaname").html(response);
                areaOptionInit();
                return;
            }

            $("#add_areaname").html(response);
            areaOptionInit();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".area_loading").hide();
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

$("#pincodeSeachForm").on("submit", function (e) {
    ajaxGetArea(e);
});

$(".other_area_container").hide();

$("#add_areaname").on("change", function () {
    const selectedValue = this.value;
    $("#other_area_name").val("");
    if (!selectedValue || selectedValue != "others") {
        $(".other_area_container").hide();
        return;
    }

    $(".other_area_container").show();
});
//
