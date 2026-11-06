// Validation & Format Helpers
function getSelectedCountry(prefix) {
    const el = $(`#${prefix}_country_id`);
    if (!el.length) return "india";

    const selected = el.find("option:selected");
    const name = (selected.text() || "").toLowerCase();
    const val = String(el.val() || "");
    if (name.includes("mexic") || val === "244") return "mexico";
    return "india";
}

function validatePhone(value, prefix) {
    if (!value) return false;
    const clean = String(value).replace(/[\s\-\(\)]/g, "");
    const country = getSelectedCountry(prefix);

    if (country === "mexico") {
        return /^(?:\+52|52)?[1-9]\d{9}$/.test(clean);
    }
    return /^(?:\+91|91)?[6-9]\d{9}$/.test(clean);
}

function validatePincode(value, prefix) {
    if (!value) return false;
    const clean = String(value).trim();
    const country = getSelectedCountry(prefix);

    if (country === "mexico") {
        return /^[0-9]{5}$/.test(clean);
    }
    return /^[1-9][0-9]{5}$/.test(clean);
}

// Online Pincode Lookup (India & Mexico)
let lookupTimeouts = {};

function handlePincodeInput(prefix) {
    const pincodeInput = $(`#${prefix}_pincode`);
    let pincode = pincodeInput.val().trim().replace(/[^0-9]/g, "");
    const country = getSelectedCountry(prefix);
    const maxLen = country === "mexico" ? 5 : 6;

    if (pincode.length > maxLen) {
        pincode = pincode.substring(0, maxLen);
    }
    pincodeInput.val(pincode);

    clearTimeout(lookupTimeouts[prefix]);

    if ((country === "india" && pincode.length === 6) || (country === "mexico" && pincode.length === 5)) {
        lookupTimeouts[prefix] = setTimeout(async () => {
            try {
                let response = await fetch(`warehouse/lookup-pincode?pincode=${encodeURIComponent(pincode)}&country=${encodeURIComponent(country)}`);
                let resData = await response.json();

                if (response.ok && resData.success && (resData.city || resData.state)) {
                    if (resData.city) $(`#${prefix}_city`).val(resData.city);
                    if (resData.state) $(`#${prefix}_state`).val(resData.state);
                    $(`#${prefix}_city`)[0]?.dispatchEvent(new Event('input', { bubbles: true }));
                    $(`#${prefix}_state`)[0]?.dispatchEvent(new Event('input', { bubbles: true }));
                    return;
                }

                // Client fallback
                if (country === "india") {
                    let directRes = await fetch(`https://api.postalpincode.in/pincode/${pincode}`);
                    let directData = await directRes.json();
                    if (Array.isArray(directData) && directData[0]?.Status === "Success" && directData[0]?.PostOffice?.length > 0) {
                        const po = directData[0].PostOffice[0];
                        const c = po.District || po.Block || po.Name || "";
                        const s = po.State || "";
                        if (c) $(`#${prefix}_city`).val(c);
                        if (s) $(`#${prefix}_state`).val(s);
                        $(`#${prefix}_city`)[0]?.dispatchEvent(new Event('input', { bubbles: true }));
                        $(`#${prefix}_state`)[0]?.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                } else if (country === "mexico") {
                    let zipRes = await fetch(`https://api.zippopotam.us/mx/${pincode}`);
                    if (zipRes.ok) {
                        let zipData = await zipRes.json();
                        if (zipData.places && zipData.places.length > 0) {
                            const c = zipData.places[0]["place name"] || "";
                            const s = zipData.places[0].state || "";
                            if (c) $(`#${prefix}_city`).val(c);
                            if (s) $(`#${prefix}_state`).val(s);
                            $(`#${prefix}_city`)[0]?.dispatchEvent(new Event('input', { bubbles: true }));
                            $(`#${prefix}_state`)[0]?.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }
                }
            } catch (e) {
                console.error("Pincode lookup error:", e);
            }
        }, 300);
    }
}

$(document).on("input paste", "#add_pincode", function () {
    handlePincodeInput("add");
});

$(document).on("input paste", "#edit_pincode", function () {
    handlePincodeInput("edit");
});

$(document).on("input paste", "#add_phone, #edit_phone", function () {
    let val = $(this).val().replace(/[^0-9+\-\s]/g, "");
    $(this).val(val);
});

// Form Validators
const addValidator = new JustValidate("#addWareHouseForm", {
    validateBeforeSubmitting: true,
});
const editValidator = new JustValidate("#editWareHouseForm", {
    validateBeforeSubmitting: true,
});

if ($("#add_country_id").length) {
    addValidator.addField("#add_country_id", [
        { rule: "required", errorMessage: "*Country is required" }
    ]);
}

addValidator
    .addField("#add_warehouse_name", [
        { rule: "required", errorMessage: "*Ware House is required" }
    ])
    .addField("#add_phone", [
        { rule: "required", errorMessage: "*Phone is required" },
        {
            validator: (value) => Boolean(validatePhone(value, "add")),
            errorMessage: "*Enter a valid 10-digit phone number"
        }
    ])
    .addField("#add_address", [
        { rule: "required", errorMessage: "*Address is required" }
    ])
    .addField("#add_pincode", [
        { rule: "required", errorMessage: "*Pincode is required" },
        {
            validator: (value) => Boolean(validatePincode(value, "add")),
            errorMessage: "*Invalid pincode (India: 6 digits, Mexico: 5 digits)"
        }
    ])
    .addField("#add_city", [
        { rule: "required", errorMessage: "*City is required" }
    ])
    .addField("#add_state", [
        { rule: "required", errorMessage: "*State is required" }
    ])
    .onSuccess((event) => {
        addWareHouseFormSubmit(event);
    });

if ($("#edit_country_id").length) {
    editValidator.addField("#edit_country_id", [
        { rule: "required", errorMessage: "*Country is required" }
    ]);
}

editValidator
    .addField("#edit_warehouse_name", [
        { rule: "required", errorMessage: "*Ware House is required" }
    ])
    .addField("#edit_phone", [
        { rule: "required", errorMessage: "*Phone is required" },
        {
            validator: (value) => Boolean(validatePhone(value, "edit")),
            errorMessage: "*Enter a valid 10-digit phone number"
        }
    ])
    .addField("#edit_address", [
        { rule: "required", errorMessage: "*Address is required" }
    ])
    .addField("#edit_pincode", [
        { rule: "required", errorMessage: "*Pincode is required" },
        {
            validator: (value) => Boolean(validatePincode(value, "edit")),
            errorMessage: "*Invalid pincode (India: 6 digits, Mexico: 5 digits)"
        }
    ])
    .addField("#edit_city", [
        { rule: "required", errorMessage: "*City is required" }
    ])
    .addField("#edit_state", [
        { rule: "required", errorMessage: "*State is required" }
    ])
    .onSuccess((event) => {
        updateWareHouseFormSubmit(event);
    });

// Original GridJS Table Layout & Styling
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

    data: (window.warehouse || []).map((wh, index) => {
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
                        data-name="${wh.warehouse_name || ''}"
                        data-phone="${wh.phone || ''}"
                        data-address="${wh.address || ''}"
                        data-pincode="${wh.pincode || ''}"
                        data-city="${wh.city || ''}"
                        data-state="${wh.state || ''}"
                        data-country="${wh.country_id || ''}">
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
        data: (data || []).map((wh, index) => {
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
                            data-name="${wh.warehouse_name || ''}"
                            data-phone="${wh.phone || ''}"
                            data-address="${wh.address || ''}"
                            data-pincode="${wh.pincode || ''}"
                            data-city="${wh.city || ''}"
                            data-state="${wh.state || ''}"
                            data-country="${wh.country_id || ''}">
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

let isAddSubmitting = false;
let isEditSubmitting = false;

function addWareHouseFormSubmit(e) {
    if (isAddSubmitting) return;
    isAddSubmitting = true;

    $(".addwarehouse_submit_btn").attr("disabled", true).html("Submit");

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
            $(".addwarehouse_submit_btn").removeAttr("disabled").html("Submit");
            isAddSubmitting = false;

            $("#addWareHouseForm")[0].reset();
            $("#addWareHouseModal").modal("hide");

            gridjsReRender(response.warehouse);
            Swal.fire("Success", "Warehouse added successfully.", "success");
        },

        error: function (xhr) {
            $(".addwarehouse_submit_btn").removeAttr("disabled").html("Submit");
            isAddSubmitting = false;

            let msg = xhr.responseJSON?.message || "Something went wrong";
            if (xhr.responseJSON?.errors) {
                const firstErrKey = Object.keys(xhr.responseJSON.errors)[0];
                msg = xhr.responseJSON.errors[firstErrKey][0];
            }

            Swal.fire("Error", msg, "error");
        }
    });
}

function updateWareHouseFormSubmit(e) {
    if (isEditSubmitting) return;
    isEditSubmitting = true;

    $(".editwarehouse_submit_btn").attr("disabled", true).html("Update");

    const formdata = new FormData(e.target);
    formdata.append('_token', window.csrfToken);

    const id = $("#edit_promotion_id").val() || $("#edit_warehouse_id").val();

    $.ajax({
        url: "warehouse/update/" + id,
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,

        success: function (response) {
            $(".editwarehouse_submit_btn").removeAttr("disabled").html("Update");
            isEditSubmitting = false;

            $("#editWareHouseForm")[0].reset();
            $("#editWareHouseModal").modal("hide");

            gridjsReRender(response.warehouse);
            Swal.fire("Success", "Warehouse updated successfully.", "success");
        },

        error: function (xhr) {
            $(".editwarehouse_submit_btn").removeAttr("disabled").html("Update");
            isEditSubmitting = false;

            let msg = xhr.responseJSON?.message || "Something went wrong";
            if (xhr.responseJSON?.errors) {
                const firstErrKey = Object.keys(xhr.responseJSON.errors)[0];
                msg = xhr.responseJSON.errors[firstErrKey][0];
            }

            Swal.fire("Error", msg, "error");
        }
    });
}

// Edit button click - populates all fields
$(document).on("click", ".edit_btn", function () {
    const id = $(this).data("id");
    const name = $(this).data("name");
    const phone = $(this).data("phone");
    const address = $(this).data("address");
    const pincode = $(this).data("pincode");
    const city = $(this).data("city");
    const state = $(this).data("state");
    const country = $(this).data("country");

    $("#edit_promotion_id").val(id);
    $("#edit_warehouse_name").val(name || "");
    $("#edit_phone").val(phone || "");
    $("#edit_address").val(address || "");
    $("#edit_pincode").val(pincode || "");
    $("#edit_city").val(city || "");
    $("#edit_state").val(state || "");

    if ($("#edit_country_id").length) {
        $("#edit_country_id").val(country).trigger("change");
    }
});

$(document).on("click", ".delete_btn", function () {
    const id = $(this).data("id");
    Swal.fire({
        title: "Delete this warehouse?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("warehouse/delete/" + id, { _token: window.csrfToken }, function (response) {
                gridjsReRender(response.warehouse);
                Swal.fire("Deleted!", "Warehouse deleted.", "success");
            }).fail(function (xhr) {
                Swal.fire("Error", xhr.responseJSON?.message || "Delete failed", "error");
            });
        }
    });
});

$("#addWareHouseModal").on("hidden.bs.modal", function () {
    $("#addWareHouseForm")[0].reset();
    $(".addwarehouse_submit_btn").removeAttr("disabled").html("Submit");
    isAddSubmitting = false;
    clearTimeout(lookupTimeouts["add"]);
});

$("#editWareHouseModal").on("hidden.bs.modal", function () {
    $("#editWareHouseForm")[0].reset();
    $(".editwarehouse_submit_btn").removeAttr("disabled").html("Update");
    isEditSubmitting = false;
    clearTimeout(lookupTimeouts["edit"]);
});

function renderSort() {
    if (typeof Sortable === "undefined") return;
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
