// ─── UNIT PAGE ────────────────────────────────────────────────────────────────

const addValidator = new JustValidate("#addUnitForm", { validateBeforeSubmitting: true });
const editValidator = new JustValidate("#editUnitForm", { validateBeforeSubmitting: true });

// ─── Country Name Helper (multi, same as categories) ─────────────────────────

function resolveCountryName(countryId) {
    if (!countryId) return '-';
    const ids = countryId.toString().split(',').filter(id =>
        !window.allowedCountryIds || window.allowedCountryIds.includes(parseInt(id))
    );
    return ids.map(id => {
        const found = (window.countries || []).find(c => c.id == id);
        return found ? found.name : id;
    }).join(', ') || '-';
}

// ─── On Page Load: auto-select single-country users ──────────────────────────

$(document).ready(function () {
    if (window.allowedCountryIds && window.allowedCountryIds.length === 1) {
        const countryId = window.allowedCountryIds[0].toString();
        // Will be set via select2 after modal opens
    }
});

// ─── Validators ───────────────────────────────────────────────────────────────

addValidator
    .addField("#add_country_id",  [{ rule: "required", errorMessage: "*Country is required" }])
    .addField("#add_unit_name",   [{ rule: "required", errorMessage: "*Unit Name is required" }])
    .addField("#add_short_name",  [{ rule: "required", errorMessage: "*Short Name is required" }])
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", true).html("Saving...");
        addUnitFormSubmit(event);
    });

editValidator
    .addField("#edit_unit_name",  [{ rule: "required", errorMessage: "*Unit Name is required" }])
    .addField("#edit_short_name", [{ rule: "required", errorMessage: "*Short Name is required" }])
    .onSuccess((event) => {
        $(".edit_submit_btn").attr("disabled", true).html("Updating...");
        editUnitFormSubmit(event);
    });

// ─── Grid.js Row Builder ──────────────────────────────────────────────────────

function buildRow(unit, index) {
    return [
        index + 1,
        resolveCountryName(unit.country_id),
        unit.unit_name,
        unit.short_name,
        gridjs.html(`
            <div class="d-flex gap-2 justify-content-center">
                <button
                    data-bs-toggle="modal"
                    data-bs-target="#editUnitModal"
                    data-unitid="${unit.id}"
                    data-countryid="${unit.country_id ?? ''}"
                    data-unitname="${unit.unit_name}"
                    data-shortname="${unit.short_name}"
                    class="btn btn-secondary btn-sm edit_btn">Edit</button>
                <button
                    class="btn btn-danger btn-sm delete_btn"
                    data-unitid="${unit.id}">Delete</button>
            </div>
        `),
    ];
}

// ─── Grid.js Init ─────────────────────────────────────────────────────────────

const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Country",
        "Unit Name",
        "Short Name",
        { name: "Action", sort: false },
    ],
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: (window.units || []).map((unit, index) => buildRow(unit, index)),
    style: {
        table: { border: "1px solid #ccc" },
        th: { "background-color": "rgba(0,0,0,0.1)", color: "#000", "border-bottom": "3px solid #ccc", "text-align": "center", "border-right": "0.5px solid #ccc" },
        td: { "text-align": "center", "border-right": "0.5px solid #ccc", "border-bottom": "0.5px solid #ccc" },
    },
});

gridNew.render(document.getElementById("table-gridjs"));

function gridjsReRender(units) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew.updateConfig({
        data: units.map((unit, index) => buildRow(unit, index)),
    }).forceRender();
}

// ─── AJAX: Add Unit ───────────────────────────────────────────────────────────

function addUnitFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: unitUrls.store,
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            $("#addUnitForm")[0].reset();
            $("#add_country_id").val(null).trigger("change"); // reset select2
            $("#addUnitModal").modal('hide');
            $(".modal-backdrop").remove();
            gridjsReRender(response.units);
            Swal.fire("Added", "Unit Added Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            let message = errorThrown;
            if (jqXHR.responseJSON?.errors) {
                message = Object.values(jqXHR.responseJSON.errors).flat().join("<br>");
            }
            Swal.fire({ icon: "error", title: textStatus.toUpperCase(), html: message });
        },
    });
}

// ─── AJAX: Edit Unit ──────────────────────────────────────────────────────────

function editUnitFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: unitUrls.update + "/" + $("#edit_unit_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            $("#editUnitForm")[0].reset();
            $("#edit_country_id").val(null).trigger("change"); // reset select2
            $("#editUnitModal").modal('hide');
            $(".modal-backdrop").remove();
            gridjsReRender(response.units);
            Swal.fire("Updated", "Unit Updated Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            let message = errorThrown;
            if (jqXHR.responseJSON?.errors) {
                message = Object.values(jqXHR.responseJSON.errors).flat().join("<br>");
            }
            Swal.fire({ icon: "error", title: textStatus.toUpperCase(), html: message });
        },
    });
}

// ─── Event Handlers ───────────────────────────────────────────────────────────

$(function () {
    // Edit button — populate modal with multi-country (same as category page)
    $(document).on("click", ".edit_btn", function () {
        const country = $(this).attr("data-countryid"); // e.g. "1,2" or "1"

        $("#edit_unit_id").val($(this).attr("data-unitid"));
        $("#edit_unit_name").val($(this).attr("data-unitname"));
        $("#edit_short_name").val($(this).attr("data-shortname"));
        $("#edit_country_id_hidden").val(country);

        // Split comma-separated into array for select2 (same pattern as categories)
        const countryArray = country ? country.toString().split(',').map(s => s.trim()) : [];
        setTimeout(() => {
            $("#edit_country_id").val(countryArray).trigger("change");
        }, 200);
    });

    // Delete button
    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-unitid");
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
                    url: unitUrls.destroy + "/" + id,
                    method: "POST",
                    dataType: "json",
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        gridjsReRender(response.units);
                        Swal.fire("Deleted!", "Unit Deleted Successfully.", "success");
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
                    },
                });
            }
        });
    });

    // Auto-select single-country users after add modal opens
    $('#addUnitModal').on('shown.bs.modal', function () {
        if (window.allowedCountryIds && window.allowedCountryIds.length === 1) {
            setTimeout(() => {
                $("#add_country_id").val([window.allowedCountryIds[0].toString()]).trigger("change");
            }, 250);
        }
    });
});