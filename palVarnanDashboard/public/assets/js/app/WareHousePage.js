// WareHousePage.js - Complete validation and online pincode lookup

// --- Country & Format Helpers ---
function getSelectedCountry(prefix) {
    const el = $(`#${prefix}_country_id`);
    if (!el.length) return "india";

    if (el.is("select")) {
        const selected = el.find("option:selected");
        const name = (selected.data("name") || selected.text() || "").toLowerCase();
        const val = String(el.val() || "");
        if (name.includes("mexic") || val === "244") return "mexico";
        return "india";
    } else {
        const name = String(el.data("name") || "").toLowerCase();
        const val = String(el.val() || "");
        if (name.includes("mexic") || val === "244") return "mexico";
        return "india";
    }
}

function validatePhone(value, prefix) {
    if (!value) return false;
    const clean = String(value).replace(/[\s\-\(\)]/g, "");
    const country = getSelectedCountry(prefix);

    if (country === "mexico") {
        // Mexico: 10 digits starting with 1-9 (or +52 prefix)
        return /^(?:\+52|52)?[1-9]\d{9}$/.test(clean);
    }
    // India: 10 digits starting with 6, 7, 8, or 9 (or +91 prefix)
    return /^(?:\+91|91)?[6-9]\d{9}$/.test(clean);
}

function validatePincode(value, prefix) {
    if (!value) return false;
    const clean = String(value).trim();
    const country = getSelectedCountry(prefix);

    if (country === "mexico") {
        // Mexico postal codes are 5 digits (01000 - 99999)
        return /^[0-9]{5}$/.test(clean);
    }
    // India postal codes are 6 digits (100000 - 999999, cannot start with 0)
    return /^[1-9][0-9]{5}$/.test(clean);
}

// --- Online Pincode Lookup (India & Mexico) ---
let lookupTimeouts = {};

function handlePincodeInput(prefix) {
    const pincodeInput = $(`#${prefix}_pincode`);
    const statusEl = $(`#${prefix}_pincode_status`);
    let pincode = pincodeInput.val().trim();
    const country = getSelectedCountry(prefix);

    // Sanitize input to numbers only
    pincode = pincode.replace(/[^0-9]/g, "");
    const maxLen = country === "mexico" ? 5 : 6;
    if (pincode.length > maxLen) {
        pincode = pincode.substring(0, maxLen);
    }
    pincodeInput.val(pincode);

    clearTimeout(lookupTimeouts[prefix]);

    if (!pincode) {
        statusEl.html("");
        return;
    }

    // Trigger lookup when exact length reached
    if ((country === "india" && pincode.length === 6) || (country === "mexico" && pincode.length === 5)) {
        statusEl.html('<span class="text-primary"><span class="spinner-border spinner-border-sm me-1"></span> Fetching location online...</span>');
        lookupTimeouts[prefix] = setTimeout(() => {
            fetchLocation(pincode, country, prefix);
        }, 300);
    } else {
        const remaining = maxLen - pincode.length;
        if (remaining > 0) {
            statusEl.html(`<span class="text-muted small">Enter ${remaining} more digit(s) for ${country === "mexico" ? "Mexico (5 digits)" : "India (6 digits)"}</span>`);
        }
    }
}

async function fetchLocation(pincode, country, prefix) {
    const statusEl = $(`#${prefix}_pincode_status`);
    const cityInput = $(`#${prefix}_city`);
    const stateInput = $(`#${prefix}_state`);

    try {
        // 1. Try our internal server endpoint first (handles proxying, caching, fallback)
        let response = await fetch(`warehouse/lookup-pincode?pincode=${encodeURIComponent(pincode)}&country=${encodeURIComponent(country)}`);
        let resData = await response.json();

        if (response.ok && resData.success && (resData.city || resData.state)) {
            applyLocationData(resData.city, resData.state, prefix, statusEl);
            return;
        }

        // 2. Direct client-side fallback if server lookup didn't succeed
        if (country === "india") {
            try {
                let directRes = await fetch(`https://api.postalpincode.in/pincode/${pincode}`);
                let directData = await directRes.json();
                if (Array.isArray(directData) && directData[0]?.Status === "Success" && directData[0]?.PostOffice?.length > 0) {
                    const po = directData[0].PostOffice[0];
                    const c = po.District || po.Block || po.Name || "";
                    const s = po.State || "";
                    if (c || s) {
                        applyLocationData(c, s, prefix, statusEl);
                        return;
                    }
                }
            } catch (e) {}

            try {
                let zipRes = await fetch(`https://api.zippopotam.us/in/${pincode}`);
                if (zipRes.ok) {
                    let zipData = await zipRes.json();
                    if (zipData.places && zipData.places.length > 0) {
                        const c = zipData.places[0]["place name"] || "";
                        const s = zipData.places[0].state || "";
                        if (c || s) {
                            applyLocationData(c, s, prefix, statusEl);
                            return;
                        }
                    }
                }
            } catch (e) {}
        } else if (country === "mexico") {
            try {
                let zipRes = await fetch(`https://api.zippopotam.us/mx/${pincode}`);
                if (zipRes.ok) {
                    let zipData = await zipRes.json();
                    if (zipData.places && zipData.places.length > 0) {
                        const c = zipData.places[0]["place name"] || "";
                        const s = zipData.places[0].state || "";
                        if (c || s) {
                            applyLocationData(c, s, prefix, statusEl);
                            return;
                        }
                    }
                }
            } catch (e) {}
        }

        statusEl.html('<span class="text-warning"><i class="mdi mdi-information-outline me-1"></i> Location not found online. Please enter City & State manually.</span>');
    } catch (err) {
        console.error("Postal API error:", err);
        statusEl.html('<span class="text-muted"><i class="mdi mdi-information-outline me-1"></i> Could not reach location service. Please enter City & State manually.</span>');
    }
}

function applyLocationData(city, state, prefix, statusEl) {
    const cityInput = $(`#${prefix}_city`);
    const stateInput = $(`#${prefix}_state`);

    if (city) cityInput.val(city);
    if (state) stateInput.val(state);

    statusEl.html(`<span class="text-success"><i class="mdi mdi-check-circle me-1"></i> Auto-filled: <strong>${city ? city + ', ' : ''}${state || ''}</strong></span>`);

    // Flash background to indicate auto-fill
    cityInput.addClass("is-valid");
    stateInput.addClass("is-valid");
    setTimeout(() => {
        cityInput.removeClass("is-valid");
        stateInput.removeClass("is-valid");
    }, 2000);

    // Revalidate JustValidate
    cityInput[0]?.dispatchEvent(new Event('input', { bubbles: true }));
    stateInput[0]?.dispatchEvent(new Event('input', { bubbles: true }));
}

// Attach event listeners for real-time input and country change
$(document).on("input paste", "#add_pincode", function () {
    handlePincodeInput("add");
});

$(document).on("input paste", "#edit_pincode", function () {
    handlePincodeInput("edit");
});

$(document).on("input paste", "#add_phone, #edit_phone", function () {
    // Only allow numbers, +, -, and spaces
    let val = $(this).val().replace(/[^0-9+\-\s]/g, "");
    $(this).val(val);
});

$(document).on("change", "#add_country_id", function () {
    const country = getSelectedCountry("add");
    if (country === "mexico") {
        $("#add_phone").attr("placeholder", "10-digit phone (e.g. 55 1234 5678)");
        $("#add_pincode").attr("placeholder", "5-digit Postal Code (e.g. 01000)").attr("maxlength", "5");
    } else {
        $("#add_phone").attr("placeholder", "10-digit mobile (e.g. 9876543210)");
        $("#add_pincode").attr("placeholder", "6-digit Pincode (e.g. 600001)").attr("maxlength", "6");
    }
    $("#add_pincode_status").html("");
    if ($("#add_pincode").val()) {
        handlePincodeInput("add");
    }
});

$(document).on("change", "#edit_country_id", function () {
    const country = getSelectedCountry("edit");
    if (country === "mexico") {
        $("#edit_phone").attr("placeholder", "10-digit phone (e.g. 55 1234 5678)");
        $("#edit_pincode").attr("placeholder", "5-digit Postal Code (e.g. 01000)").attr("maxlength", "5");
    } else {
        $("#edit_phone").attr("placeholder", "10-digit mobile (e.g. 9876543210)");
        $("#edit_pincode").attr("placeholder", "6-digit Pincode (e.g. 600001)").attr("maxlength", "6");
    }
    $("#edit_pincode_status").html("");
    if ($("#edit_pincode").val()) {
        handlePincodeInput("edit");
    }
});

// --- JustValidate Setup ---
const addValidator = new JustValidate("#addWareHouseForm", {
    validateBeforeSubmitting: true,
});

const editValidator = new JustValidate("#editWareHouseForm", {
    validateBeforeSubmitting: true,
});

// Add Form Rules
if ($("#add_country_id").is("select")) {
    addValidator.addField("#add_country_id", [
        { rule: "required", errorMessage: "*Country is required" }
    ]);
}

addValidator
    .addField("#add_warehouse_name", [
        { rule: "required", errorMessage: "*Warehouse Name is required" },
        { rule: "minLength", value: 2, errorMessage: "*Must be at least 2 characters" },
        { rule: "maxLength", value: 100, errorMessage: "*Cannot exceed 100 characters" }
    ])
    .addField("#add_phone", [
        { rule: "required", errorMessage: "*Phone Number is required" },
        {
            validator: (value) => Boolean(validatePhone(value, "add")),
            errorMessage: "*Enter a valid 10-digit mobile number"
        }
    ])
    .addField("#add_address", [
        { rule: "required", errorMessage: "*Address is required" },
        { rule: "minLength", value: 3, errorMessage: "*Address must be at least 3 characters" }
    ])
    .addField("#add_pincode", [
        { rule: "required", errorMessage: "*Pincode / Postal Code is required" },
        {
            validator: (value) => Boolean(validatePincode(value, "add")),
            errorMessage: "*Invalid postal code (India: 6 digits, Mexico: 5 digits)"
        }
    ])
    .addField("#add_city", [
        { rule: "required", errorMessage: "*City is required" },
        { rule: "minLength", value: 2, errorMessage: "*City must be at least 2 characters" }
    ])
    .addField("#add_state", [
        { rule: "required", errorMessage: "*State is required" },
        { rule: "minLength", value: 2, errorMessage: "*State must be at least 2 characters" }
    ])
    .onSuccess((event) => {
        addWareHouseFormSubmit(event);
    });

// Edit Form Rules
if ($("#edit_country_id").is("select")) {
    editValidator.addField("#edit_country_id", [
        { rule: "required", errorMessage: "*Country is required" }
    ]);
}

editValidator
    .addField("#edit_warehouse_name", [
        { rule: "required", errorMessage: "*Warehouse Name is required" },
        { rule: "minLength", value: 2, errorMessage: "*Must be at least 2 characters" },
        { rule: "maxLength", value: 100, errorMessage: "*Cannot exceed 100 characters" }
    ])
    .addField("#edit_phone", [
        { rule: "required", errorMessage: "*Phone Number is required" },
        {
            validator: (value) => Boolean(validatePhone(value, "edit")),
            errorMessage: "*Enter a valid 10-digit mobile number"
        }
    ])
    .addField("#edit_address", [
        { rule: "required", errorMessage: "*Address is required" },
        { rule: "minLength", value: 3, errorMessage: "*Address must be at least 3 characters" }
    ])
    .addField("#edit_pincode", [
        { rule: "required", errorMessage: "*Pincode / Postal Code is required" },
        {
            validator: (value) => Boolean(validatePincode(value, "edit")),
            errorMessage: "*Invalid postal code (India: 6 digits, Mexico: 5 digits)"
        }
    ])
    .addField("#edit_city", [
        { rule: "required", errorMessage: "*City is required" },
        { rule: "minLength", value: 2, errorMessage: "*City must be at least 2 characters" }
    ])
    .addField("#edit_state", [
        { rule: "required", errorMessage: "*State is required" },
        { rule: "minLength", value: 2, errorMessage: "*State must be at least 2 characters" }
    ])
    .onSuccess((event) => {
        updateWareHouseFormSubmit(event);
    });

// --- GridJS Table ---
const gridNew = new gridjs.Grid({
    columns: [
        { name: "S.NO", sort: true },
        { name: "Warehouse Name", sort: true },
        { name: "Country", sort: true },
        { name: "Phone", sort: false },
        { name: "Pincode", sort: false },
        { name: "City & State", sort: false },
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
            gridjs.html(`<strong>${wh.warehouse_name || '-'}</strong>`),
            gridjs.html(`<span class="badge bg-soft-primary text-primary">${wh.country?.name || '-'}</span>`),
            gridjs.html(`${wh.phone || '-'}`),
            gridjs.html(`${wh.pincode || '-'}`),
            gridjs.html(`${wh.city ? wh.city + ', ' : ''}${wh.state || '-'}`),
            gridjs.html(`
                <div class="d-flex justify-content-center">
                    <button class="btn btn-secondary btn-sm edit_btn me-2"
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
                        <i class="mdi mdi-pencil me-1"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm delete_btn"
                        data-id="${wh.id}">
                        <i class="mdi mdi-trash-can me-1"></i> Delete
                    </button>
                </div>
            `),
        ];
    }),

    style: {
        table: { border: "1px solid #e2e8f0" },
        th: {
            "background-color": "#f8fafc",
            color: "#475569",
            "border-bottom": "2px solid #e2e8f0",
            "text-align": "center",
        },
        td: {
            "text-align": "center",
            "border-bottom": "1px solid #e2e8f0",
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
                gridjs.html(`<span class="badge bg-soft-primary text-primary">${wh.country?.name || '-'}</span>`),
                gridjs.html(`${wh.phone || '-'}`),
                gridjs.html(`${wh.pincode || '-'}`),
                gridjs.html(`${wh.city ? wh.city + ', ' : ''}${wh.state || '-'}`),
                gridjs.html(`
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-secondary btn-sm edit_btn me-2"
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
                            <i class="mdi mdi-pencil me-1"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm delete_btn"
                            data-id="${wh.id}">
                            <i class="mdi mdi-trash-can me-1"></i> Delete
                        </button>
                    </div>
                `),
            ];
        }),
    }).forceRender();
}

// --- Submit Actions with Anti-Multi-Click Protection ---
let isAddSubmitting = false;
let isEditSubmitting = false;

function addWareHouseFormSubmit(e) {
    if (isAddSubmitting) return;
    isAddSubmitting = true;

    $(".addwarehouse_submit_btn").attr("disabled", true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

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
            $("#add_pincode_status").html("");
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

    $(".editwarehouse_submit_btn").attr("disabled", true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...');

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
            $(".editwarehouse_submit_btn").removeAttr("disabled").html("Update");
            isEditSubmitting = false;

            $("#editWareHouseForm")[0].reset();
            $("#edit_pincode_status").html("");
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

// --- Edit Button Population ---
$(document).on("click", ".edit_btn", function () {
    const id = $(this).data("id");
    const name = $(this).data("name");
    const phone = $(this).data("phone");
    const address = $(this).data("address");
    const pincode = $(this).data("pincode");
    const city = $(this).data("city");
    const state = $(this).data("state");
    const country = $(this).data("country");

    $("#edit_warehouse_id").val(id);
    $("#edit_warehouse_name").val(name || "");
    $("#edit_phone").val(phone || "");
    $("#edit_address").val(address || "");
    $("#edit_pincode").val(pincode || "");
    $("#edit_city").val(city || "");
    $("#edit_state").val(state || "");
    $("#edit_pincode_status").html("");

    if ($("#edit_country_id").length) {
        $("#edit_country_id").val(country).trigger("change");
    }
});

// --- Delete Button ---
$(document).on("click", ".delete_btn", function () {
    const id = $(this).data("id");
    Swal.fire({
        title: "Delete this warehouse?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
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

// --- Modal Close / Reset Handlers ---
$("#addWareHouseModal").on("hidden.bs.modal", function () {
    $("#addWareHouseForm")[0].reset();
    $("#add_pincode_status").html("");
    $(".addwarehouse_submit_btn").removeAttr("disabled").html("Submit");
    isAddSubmitting = false;
    clearTimeout(lookupTimeouts["add"]);
});

$("#editWareHouseModal").on("hidden.bs.modal", function () {
    $("#editWareHouseForm")[0].reset();
    $("#edit_pincode_status").html("");
    $(".editwarehouse_submit_btn").removeAttr("disabled").html("Update");
    isEditSubmitting = false;
    clearTimeout(lookupTimeouts["edit"]);
});

// --- Sortable Handler ---
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
