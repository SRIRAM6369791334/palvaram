// ============ Validation & Format Helpers ============
function getSelectedCountry(prefix) {
    const el = $(`#${prefix}_country_id`);
    if (!el.length) return "india";

    const selected = el.find("option:selected");
    const name = (selected.text() || "").toLowerCase();
    const val = String(el.val() || "");
    if (name.includes("mexic") || val === "244") return "mexico";
    if (val === "" || val === "0") return "unknown";
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
    if (country === "unknown") {
        return /^[1-9][0-9]{5}$/.test(clean) || /^[0-9]{5}$/.test(clean);
    }
    return /^[1-9][0-9]{5}$/.test(clean);
}

// ============ Online Pincode Lookup (India & Mexico) ============
let lookupTimeouts = {};

function handlePincodeInput(prefix) {
    const pincodeInput = $(`#${prefix}_pincode`);
    let pincode = pincodeInput.val().trim().replace(/[^0-9]/g, "");
    const country = getSelectedCountry(prefix);

    let maxLen = 6;
    if (country === "mexico") maxLen = 5;
    if (country === "unknown") maxLen = 6;

    if (pincode.length > maxLen) {
        pincode = pincode.substring(0, maxLen);
    }
    pincodeInput.val(pincode);

    clearTimeout(lookupTimeouts[prefix]);

    let shouldLookup = false;
    let lookupCountry = country;

    if (country === "india" && pincode.length === 6) {
        shouldLookup = true;
    } else if (country === "mexico" && pincode.length === 5) {
        shouldLookup = true;
    } else if (country === "unknown") {
        if (pincode.length === 6) {
            shouldLookup = true;
            lookupCountry = "india";
        } else if (pincode.length === 5) {
            shouldLookup = true;
            lookupCountry = "mexico";
        }
    }

    if (shouldLookup) {
        lookupTimeouts[prefix] = setTimeout(async () => {
            function fillFields(city, state) {
                if (city) $(`#${prefix}_city`).val(city);
                if (state) $(`#${prefix}_state`).val(state);
                $(`#${prefix}_city`)[0]?.dispatchEvent(new Event('input', { bubbles: true }));
                $(`#${prefix}_state`)[0]?.dispatchEvent(new Event('input', { bubbles: true }));
            }

            // 1) Direct public APIs first (CORS enabled, no auth needed)
            try {
                if (lookupCountry === "mexico") {
                    let padded = pincode.padStart(5, '0');
                    let zipRes = await fetch(`https://api.zippopotam.us/mx/${padded}`);
                    if (zipRes.ok) {
                        let zipData = await zipRes.json();
                        if (zipData.places && zipData.places.length > 0) {
                            fillFields(zipData.places[0]["place name"] || "", zipData.places[0].state || "");
                            return;
                        }
                    }
                } else if (lookupCountry === "india") {
                    let directRes = await fetch(`https://api.postalpincode.in/pincode/${pincode}`);
                    let directData = await directRes.json();
                    if (Array.isArray(directData) && directData[0]?.Status === "Success" && directData[0]?.PostOffice?.length > 0) {
                        const po = directData[0].PostOffice[0];
                        fillFields(po.District || po.Block || po.Name || "", po.State || "");
                        return;
                    }
                    let zipRes = await fetch(`https://api.zippopotam.us/in/${pincode}`);
                    if (zipRes.ok) {
                        let zipData = await zipRes.json();
                        if (zipData.places && zipData.places.length > 0) {
                            fillFields(zipData.places[0]["place name"] || "", zipData.places[0].state || "");
                            return;
                        }
                    }
                }
            } catch (e1) {
                console.warn("Direct API lookup failed:", e1);
            }

            // 2) Fallback: server-side proxy
            try {
                let response = await fetch(`/warehouse/lookup-pincode?pincode=${encodeURIComponent(pincode)}&country=${encodeURIComponent(lookupCountry)}`);
                if (response.ok) {
                    let resData = await response.json();
                    if (resData.success && (resData.city || resData.state)) {
                        fillFields(resData.city || "", resData.state || "");
                    }
                }
            } catch (e2) {
                console.error("Server proxy lookup also failed:", e2);
            }
        }, 300);
    }
}

// ============ Re-evaluate pincode on country change ============
function bindCountryChangeForPincode(selector) {
    const el = $(selector);
    if (!el.length) return;
    const prefix = el.attr("id").startsWith("add") ? "add" : "edit";
    el.on("select2:select change", function () {
        const pincodeVal = $(`#${prefix}_pincode`).val()?.trim();
        if (pincodeVal && pincodeVal.length >= 5) {
            handlePincodeInput(prefix);
        }
    });
}
bindCountryChangeForPincode("#add_country_id");
bindCountryChangeForPincode("#edit_country_id");
$(document).ready(function () {
    setTimeout(function () {
        bindCountryChangeForPincode("#add_country_id");
        bindCountryChangeForPincode("#edit_country_id");
    }, 500);
});

// ============ Block letters in Phone fields ============
$(document).on("keydown", "#add_phone, #edit_phone", function (e) {
    if (
        e.key === 'Backspace' || e.key === 'Delete' || e.key === 'Tab' ||
        e.key === 'Escape' || e.key === 'Enter' ||
        e.key === 'ArrowLeft' || e.key === 'ArrowRight' ||
        e.key === 'ArrowUp' || e.key === 'ArrowDown' ||
        e.key === 'Home' || e.key === 'End' ||
        e.ctrlKey || e.metaKey || e.altKey
    ) {
        return;
    }
    if (e.key === '+' && this.selectionStart === 0 && !this.value.includes('+')) {
        return;
    }
    if (e.key.length === 1 && !/^[0-9]$/.test(e.key)) {
        e.preventDefault();
    }
});

$(document).on("beforeinput", "#add_phone, #edit_phone", function (e) {
    const data = e.originalEvent?.data;
    if (data && !/^[0-9+]+$/.test(data)) {
        e.preventDefault();
    }
});

// ============ Block letters in Pincode fields ============
$(document).on("keydown", "#add_pincode, #edit_pincode", function (e) {
    if (
        e.key === 'Backspace' || e.key === 'Delete' || e.key === 'Tab' ||
        e.key === 'Escape' || e.key === 'Enter' ||
        e.key === 'ArrowLeft' || e.key === 'ArrowRight' ||
        e.key === 'ArrowUp' || e.key === 'ArrowDown' ||
        e.key === 'Home' || e.key === 'End' ||
        e.ctrlKey || e.metaKey || e.altKey
    ) {
        return;
    }
    if (e.key.length === 1 && !/^[0-9]$/.test(e.key)) {
        e.preventDefault();
    }
});

$(document).on("beforeinput", "#add_pincode, #edit_pincode", function (e) {
    const data = e.originalEvent?.data;
    if (data && !/^[0-9]+$/.test(data)) {
        e.preventDefault();
    }
});

// Pincode input triggers lookup
$(document).on("input paste", "#add_pincode", function () {
    handlePincodeInput("add");
});

$(document).on("input paste", "#edit_pincode", function () {
    handlePincodeInput("edit");
});

// Sanitize phone on paste
$(document).on("input paste", "#add_phone, #edit_phone", function () {
    const hasPlus = this.value.startsWith('+');
    let digits = this.value.replace(/[^0-9]/g, '');
    $(this).val((hasPlus ? '+' : '') + digits);
});

// ============ JustValidate ============
const addValidator = new JustValidate("#addBranchForm", {
    validateBeforeSubmitting: true,
});

addValidator
    .addField("#add_country_id", [
        { rule: "required", errorMessage: "*Country is required" }
    ])
    .addField("#add_warehouse_id", [
        { rule: "required", errorMessage: "*Warehouse is required" }
    ])
    .addField("#add_branch_name", [
        { rule: "required", errorMessage: "*Store Name is required" }
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
        addBranchFormSubmit(event);
    });

const editValidator = new JustValidate("#editBranchForm", {
    validateBeforeSubmitting: true,
});

editValidator
    .addField("#edit_country_id", [
        { rule: "required", errorMessage: "*Country is required" }
    ])
    .addField("#edit_warehouse_id", [
        { rule: "required", errorMessage: "*Warehouse is required" }
    ])
    .addField("#edit_branch_name", [
        { rule: "required", errorMessage: "*Store Name is required" }
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
        updateBranchFormSubmit(event);
    });

// ============ GridJS Table ============
const gridNew = new gridjs.Grid({
    columns: [
        { name: "S.NO", sort: true },
        { name: "Country", sort: true },
        { name: "Warehouse", sort: true },
        { name: "Store Name", sort: true },
        { name: "Action", sort: false },
    ],

    sort: true,
    search: true,
    pagination: {
        limit: 10,
    },

    data: (window.branches || []).map((br, index) => {
        return [
            index + 1,
            gridjs.html(`${br.country?.name || '-'}`),
            gridjs.html(`${br.warehouse?.warehouse_name || '-'}`),
            gridjs.html(`<strong>${br.branch_name || '-'}</strong>`),
            gridjs.html(`
                <div>
                    <button class="btn btn-secondary edit_btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editBranchModal"
                        data-id="${br.id}"
                        data-name="${br.branch_name}"
                        data-phone="${br.phone || ''}"
                        data-address="${br.address || ''}"
                        data-pincode="${br.pincode || ''}"
                        data-city="${br.city || ''}"
                        data-state="${br.state || ''}"
                        data-country="${br.country_id}"
                        data-warehouse="${br.warehouse_id}">
                        Edit
                    </button>

                    <button class="btn btn-danger delete_btn"
                        data-id="${br.id}">
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
        data: (data || []).map((br, index) => {
            return [
                index + 1,
                gridjs.html(`${br.country?.name || '-'}`),
                gridjs.html(`${br.warehouse?.warehouse_name || '-'}`),
                gridjs.html(`<strong>${br.branch_name || '-'}</strong>`),
                gridjs.html(`
                    <div>
                        <button class="btn btn-secondary edit_btn"
                            data-bs-toggle="modal"
                            data-bs-target="#editBranchModal"
                            data-id="${br.id}"
                            data-name="${br.branch_name}"
                            data-phone="${br.phone || ''}"
                            data-address="${br.address || ''}"
                            data-pincode="${br.pincode || ''}"
                            data-city="${br.city || ''}"
                            data-state="${br.state || ''}"
                            data-country="${br.country_id}"
                            data-warehouse="${br.warehouse_id}">
                            Edit
                        </button>

                        <button class="btn btn-danger delete_btn"
                            data-id="${br.id}">
                            Delete
                        </button>
                    </div>
                `),
            ];
        }),
    }).forceRender();
}

// ============ Anti-multi-click & Submit ============
let isAddSubmitting = false;
let isEditSubmitting = false;

function addBranchFormSubmit(e) {
    if (isAddSubmitting) return;
    isAddSubmitting = true;
    $(".addbranch_submit_btn").attr("disabled", true);

    $.ajax({
        url: "/warehouse-branch/store",
        type: "POST",
        data: {
            _token: window.csrfToken,
            country_id: $("#add_country_id").val(),
            warehouse_id: $("#add_warehouse_id").val(),
            branch_name: $("#add_branch_name").val(),
            phone: $("#add_phone").val(),
            address: $("#add_address").val(),
            pincode: $("#add_pincode").val(),
            city: $("#add_city").val(),
            state: $("#add_state").val(),
        },
        success: function (res) {
            $(".addbranch_submit_btn").removeAttr("disabled");
            isAddSubmitting = false;

            $("#addBranchForm")[0].reset();
            $("#addBranchModal").modal("hide");

            gridjsReRender(res.branches);
            Swal.fire("Success", res.message || "Store added successfully.", "success");
        },
        error: function (xhr) {
            $(".addbranch_submit_btn").removeAttr("disabled");
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

function updateBranchFormSubmit(e) {
    if (isEditSubmitting) return;
    isEditSubmitting = true;
    $(".editbranch_submit_btn").attr("disabled", true);

    let id = $("#edit_branch_id").val();

    $.ajax({
        url: "/warehouse-branch/update/" + id,
        type: "POST",
        data: {
            _token: window.csrfToken,
            country_id: $("#edit_country_id").val(),
            warehouse_id: $("#edit_warehouse_id").val(),
            branch_name: $("#edit_branch_name").val(),
            phone: $("#edit_phone").val(),
            address: $("#edit_address").val(),
            pincode: $("#edit_pincode").val(),
            city: $("#edit_city").val(),
            state: $("#edit_state").val(),
        },
        success: function (res) {
            $(".editbranch_submit_btn").removeAttr("disabled");
            isEditSubmitting = false;

            $("#editBranchForm")[0].reset();
            $("#editBranchModal").modal("hide");

            gridjsReRender(res.branches);
            Swal.fire("Success", res.message || "Store updated successfully.", "success");
        },
        error: function (xhr) {
            $(".editbranch_submit_btn").removeAttr("disabled");
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

// ============ Edit button - populate ALL fields ============
$(document).on("click", ".edit_btn", function () {
    const id = $(this).data("id");
    const name = $(this).data("name");
    const phone = $(this).data("phone");
    const address = $(this).data("address");
    const pincode = $(this).data("pincode");
    const city = $(this).data("city");
    const state = $(this).data("state");
    const country = $(this).data("country");
    const warehouse = $(this).data("warehouse");

    $("#edit_branch_id").val(id);
    $("#edit_branch_name").val(name || "");
    $("#edit_phone").val(phone || "");
    $("#edit_address").val(address || "");
    $("#edit_pincode").val(pincode || "");
    $("#edit_city").val(city || "");
    $("#edit_state").val(state || "");

    if ($("#edit_country_id").length) {
        $("#edit_country_id").val(country).trigger("change");
    }

    // Load warehouses based on country, then set selected warehouse
    $.ajax({
        url: "/get-warehouses/" + country,
        type: "GET",
        success: function (data) {
            let options = '<option value="">Select Warehouse</option>';
            data.forEach(function (w) {
                options += `<option value="${w.id}">${w.warehouse_name}</option>`;
            });
            $("#edit_warehouse_id").html(options);
            $("#edit_warehouse_id").val(warehouse);
        }
    });
});

// ============ Delete ============
$(document).on("click", ".delete_btn", function () {
    let id = $(this).data("id");

    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this Store",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/warehouse-branch/delete/" + id,
                type: "POST",
                data: {
                    _token: window.csrfToken,
                },
                success: function (res) {
                    gridjsReRender(res.branches);
                    Swal.fire("Deleted!", res.message || "Store deleted.", "success");
                },
                error: function (xhr) {
                    Swal.fire("Error", xhr.responseJSON?.message || "Delete failed", "error");
                }
            });
        }
    });
});

// ============ Country → Warehouse dropdown cascade ============
$("#add_country_id").on("change", function () {
    let country_id = $(this).val();
    $("#add_warehouse_id").html('<option value="">Loading...</option>');

    $.ajax({
        url: "/get-warehouses/" + country_id,
        type: "GET",
        success: function (data) {
            let options = '<option value="">Select Warehouse</option>';
            data.forEach(function (w) {
                options += `<option value="${w.id}">${w.warehouse_name}</option>`;
            });
            $("#add_warehouse_id").html(options);
        }
    });
});

$("#edit_country_id").on("change", function () {
    let country_id = $(this).val();
    $("#edit_warehouse_id").html('<option value="">Loading...</option>');

    $.ajax({
        url: "/get-warehouses/" + country_id,
        type: "GET",
        success: function (data) {
            let options = '<option value="">Select Warehouse</option>';
            data.forEach(function (w) {
                options += `<option value="${w.id}">${w.warehouse_name}</option>`;
            });
            $("#edit_warehouse_id").html(options);
        }
    });
});

// ============ Modal reset ============
$("#addBranchModal").on("hidden.bs.modal", function () {
    $("#addBranchForm")[0].reset();
    $(".addbranch_submit_btn").removeAttr("disabled");
    isAddSubmitting = false;
    clearTimeout(lookupTimeouts["add"]);
});

$("#editBranchModal").on("hidden.bs.modal", function () {
    $("#editBranchForm")[0].reset();
    $(".editbranch_submit_btn").removeAttr("disabled");
    isEditSubmitting = false;
    clearTimeout(lookupTimeouts["edit"]);
});
