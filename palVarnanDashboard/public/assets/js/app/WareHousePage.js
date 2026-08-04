const addValidator = new JustValidate("#addWareHouseForm", {
    validateBeforeSubmitting: true,
});
const editValidator = new JustValidate("#editWareHouseForm", {
    validateBeforeSubmitting: true,
});

let invalidAddImage = false;
let invalidEditImage = false;

if ($("#add_country_id").length) {
addValidator
    .addField("#add_country_id", [
        { rule: "required", errorMessage: "*Country is required" }
    ])
    .addField("#add_warehouse_name", [
        { rule: "required", errorMessage: "*Ware House is required" }
    ])
    .addField("#add_phone", [
        {
            rule: "required",
            errorMessage: "*Phone is required",
        },
    ])
    .addField("#add_address", [
        {
            rule: "required",
            errorMessage: "*Address is required",
        },
    ])
    .addField("#add_pincode", [
        {
            rule: "required",
            errorMessage: "*Pincode is required",
        },
    ])
    .addField("#add_city", [
        {
            rule: "required",
            errorMessage: "*City is required",
        },
    ])
    .addField("#add_state", [
        {
            rule: "required",
            errorMessage: "*State is required",
        },
    ])
    .onSuccess((event) => {
        addWareHouseFormSubmit(event);
    });
}

if ($("#edit_country_id").length) {
editValidator
    .addField("#edit_country_id", [
        { rule: "required", errorMessage: "*Country is required" }
    ])
    .addField("#edit_warehouse_name", [
        { rule: "required", errorMessage: "*Ware House is required" }
    ])
    .addField("#edit_phone", [
        {
            rule: "required",
            errorMessage: "*Phone is required",
        },
    ])
    .addField("#edit_address", [
        {
            rule: "required",
            errorMessage: "*Address is required",
        },
    ])
    .addField("#edit_pincode", [
        {
            rule: "required",
            errorMessage: "*Pincode is required",
        },
    ])
    .addField("#edit_city", [
        {
            rule: "required",
            errorMessage: "*City is required",
        },
    ])
    .addField("#edit_state", [
        {
            rule: "required",
            errorMessage: "*State is required",
        },
    ])
    .onSuccess((event) => {
        updateWareHouseFormSubmit(event);
    });
}

const gridNew = new gridjs.Grid({
    columns: [
        { name: "S.NO", sort: true },
        { name: "Warehouse Name", sort: true },
        { name: "Country", sort: true },
        { name: "Action", sort: false },
    ],

    sort: true,
    search: true,
    pagination: {
        limit: 10,
    },

    data: window.warehouse.map((wh, index) => {
        return [
            index + 1,

            // Warehouse Name
            gridjs.html(`<strong>${wh.warehouse_name || '-'}</strong>`),

            // Country Name (from relation)
            gridjs.html(`${wh.country?.name || '-'}`),

            // Action buttons
            gridjs.html(`
                <div>
                    <button class="btn btn-secondary edit_btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editWareHouseModal"
                        data-id="${wh.id}"
                        data-name="${wh.warehouse_name}"
                        data-phone="${wh.phone}"
                        data-address="${wh.address}"
                        data-pincode="${wh.pincode}"
                        data-city="${wh.city}"
                        data-state="${wh.state}"
                        data-country="${wh.country_id}">
                        Edit
                    </button>

                    <button class="btn btn-danger delete_btn"
                        data-id="${wh.id}">
                        Delete
                    </button>
                </div>
            `),
        ];
    }),

    style: {
        table: { border: "1px solid #ccc" },
        th: {
            "background-color": "rgba(0,0,0,0.1)",
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
        data: data.map((wh, index) => {
            return [
                index + 1,
                gridjs.html(`<strong>${wh.warehouse_name || '-'}</strong>`),
                gridjs.html(`${wh.country?.name || '-'}`),
                gridjs.html(`
                    <div>
                        <button class="btn btn-secondary edit_btn"
                            data-bs-toggle="modal"
                            data-bs-target="#editWareHouseModal"
                            data-id="${wh.id}"
                            data-name="${wh.warehouse_name}"
                            data-phone="${wh.phone}"
                            data-address="${wh.address}"
                            data-pincode="${wh.pincode}"
                            data-city="${wh.city}"
                            data-state="${wh.state}"
                            data-country="${wh.country_id}">
                            Edit
                        </button>

                        <button class="btn btn-danger delete_btn"
                            data-id="${wh.id}">
                            Delete
                        </button>
                    </div>
                `),
            ];
        }),
    }).forceRender();
}


function addWareHouseFormSubmit(e) {
    const formdata = new FormData(e.target);
    formdata.append('_token', window.csrfToken);

    $.ajax({
        url: "warehouse/store",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,

        success: function (response) {

            $(".addwarehouse_submit_btn").removeAttr("disabled");
            $(".addwarehouse_submit_btn").html("Submit");

            $("#addWareHouseForm")[0].reset();

            $("#addWareHouseModal").modal("hide");

            gridjsReRender(response.warehouse);

            Swal.fire("Success", "Warehouse added successfully.", "success");
        },

        error: function (xhr) {

            $(".addwarehouse_submit_btn").removeAttr("disabled");
            $(".addwarehouse_submit_btn").html("Submit");

            let msg = "Something went wrong";

            if (xhr.responseJSON?.message) {
                msg = xhr.responseJSON.message;
            }

            Swal.fire("Error", msg, "error");
        }
    });
}

$(document).on("click", ".edit_btn", function () {
    const id = $(this).data("id");
    const name = $(this).data("name");
    const country = $(this).data("country");

    // set values in modal
    $("#edit_warehouse_id").val(id); // (you can rename this to edit_warehouse_id if needed)
    $("#edit_warehouse_name").val(name);
    $("#edit_country_id").val(country);
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
            $.post("warehouse/delete/" + id, { _token: window.csrfToken }, function (response) {
                gridjsReRender(response.warehouse);
                Swal.fire("Deleted!", "Warehouse deleted.", "success");
            });
        }
    });
});

function renderSort() {
    const table = document.querySelector("#table-gridjs tbody");
    if (!table) return;

    new Sortable(table, {
        animation: 150,
        handle: ".sortable-row",
        onEnd: function () {
            const newOrder = Array.from(document.querySelectorAll(".sortable-row"))
                .map(row => row.dataset.id);

            $.post("warehouse/update-order", {
                order: newOrder,
                _token: window.csrfToken
            });
        },
    });
}

renderSort();

function updateWareHouseFormSubmit(e) {
    const formdata = new FormData(e.target);
    formdata.append('_token', window.csrfToken);

    const id = $("#edit_warehouse_id").val();

    $.ajax({
        url: "warehouse/update/" + id,
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,

        success: function (response) {

            $(".addwarehouse_submit_btn, .editwarehouse_submit_btn")
                .removeAttr("disabled")
                .html("Submit");

            $("#editWareHouseForm")[0].reset();
            $("#editWareHouseModal").modal("hide");

            gridjsReRender(response.warehouse);

            Swal.fire("Success", "Warehouse updated successfully.", "success");
        },

        error: function (xhr) {

            let msg = xhr.responseJSON?.message || "Something went wrong";

            Swal.fire("Error", msg, "error");
        }
    });
}