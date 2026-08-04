// ─── gift PAGE ────────────────────────────────────────────────────────────────

const addValidator = new JustValidate("#addGiftColorForm", { validateBeforeSubmitting: true });
const editValidator = new JustValidate("#editGiftColorForm", { validateBeforeSubmitting: true });

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

    .addField("#add_color",  [{ rule: "required", errorMessage: "*Color is required" }])
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", true).html("Saving...");
        addGiftColorFormSubmit(event);
    });

editValidator
    .addField("#edit_color", [{ rule: "required", errorMessage: "*Color is required" }])
    .onSuccess((event) => {
        $(".edit_submit_btn").attr("disabled", true).html("Updating...");
        editGiftColorFormSubmit(event);
    });

// ─── Grid.js Row Builder ──────────────────────────────────────────────────────

function buildRow(gift, index) {
    return [
        index + 1,
        resolveCountryName(gift.country_id),
       
        gift.color,
        gridjs.html(`
            <div class="d-flex gap-2 justify-content-center">
                <button
                    data-bs-toggle="modal"
                    data-bs-target="#editGiftColorModal"
                    data-giftid="${gift.id}"
                    data-countryid="${gift.country_id ?? ''}"
                   
                    data-color="${gift.color}"
                    class="btn btn-secondary btn-sm edit_btn">Edit</button>
                <button
                    class="btn btn-danger btn-sm delete_btn"
                    data-giftid="${gift.id}">Delete</button>
            </div>
        `),
    ];
}

// ─── Grid.js Init ─────────────────────────────────────────────────────────────

const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Country",
       
        "Color",
        { name: "Action", sort: false },
    ],
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: (window.gifts || []).map((gift, index) => buildRow(gift, index)),
    style: {
        table: { border: "1px solid #ccc" },
        th: { "background-color": "rgba(0,0,0,0.1)", color: "#000", "border-bottom": "3px solid #ccc", "text-align": "center", "border-right": "0.5px solid #ccc" },
        td: { "text-align": "center", "border-right": "0.5px solid #ccc", "border-bottom": "0.5px solid #ccc" },
    },
});

gridNew.render(document.getElementById("table-gridjs"));

function gridjsReRender(gifts) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew.updateConfig({
        data: gifts.map((gift, index) => buildRow(gift, index)),
    }).forceRender();
}

// ─── AJAX: Add gift ───────────────────────────────────────────────────────────

function addGiftColorFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: giftUrls.store,
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            $("#addGiftColorForm")[0].reset();
            $("#add_country_id").val(null).trigger("change"); // reset select2
            $("#addGiftColorModal").modal('hide');
            $(".modal-backdrop").remove();
            gridjsReRender(response.gifts);
            Swal.fire("Added", "Gift Color Added Successfully.", "success");
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

// ─── AJAX: Edit gift ──────────────────────────────────────────────────────────

function editGiftColorFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: giftUrls.update + "/" + $("#edit_gift_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            $("#editGiftColorForm")[0].reset();
            $("#edit_country_id").val(null).trigger("change"); // reset select2
            $("#editGiftColorModal").modal('hide');
            $(".modal-backdrop").remove();
            gridjsReRender(response.gifts);
            Swal.fire("Updated", "Gift Color Updated Successfully.", "success");
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

        $("#edit_gift_id").val($(this).attr("data-giftid"));

        $("#edit_color").val($(this).attr("data-color"));
        $("#edit_country_id_hidden").val(country);

        // Split comma-separated into array for select2 (same pattern as categories)
        const countryArray = country ? country.toString().split(',').map(s => s.trim()) : [];
        setTimeout(() => {
            $("#edit_country_id").val(countryArray).trigger("change");
        }, 200);
    });

    // Delete button
    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-giftid");
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
                    url: giftUrls.destroy + "/" + id,
                    method: "POST",
                    dataType: "json",
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        gridjsReRender(response.gifts);
                        Swal.fire("Deleted!", "Gift Color Deleted Successfully.", "success");
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
                    },
                });
            }
        });
    });

    // Auto-select single-country users after add modal opens
    $('#addGiftColorModal').on('shown.bs.modal', function () {
        if (window.allowedCountryIds && window.allowedCountryIds.length === 1) {
            setTimeout(() => {
                $("#add_country_id").val([window.allowedCountryIds[0].toString()]).trigger("change");
            }, 250);
        }
    });
});