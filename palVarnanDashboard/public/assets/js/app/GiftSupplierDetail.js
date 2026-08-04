const addValidator = new JustValidate("#addSupplierForm", { validateBeforeSubmitting: true });
const editValidator = new JustValidate("#editSupplierForm", { validateBeforeSubmitting: true });

addValidator
    .addField("#add_suppliername", [
        { rule: "required", errorMessage: "*Supplier Name is required" },
        { rule: "maxLength", value: 50, errorMessage: "*Maximum 50 characters" },
    ])
    .addField("#add_shop",     [{ rule: "required", errorMessage: "*Shop is required" }])
    .addField("#add_location", [{ rule: "required", errorMessage: "*Location is required" }])
    .addField("#add_phone",    [{ rule: "required", errorMessage: "*Phone is required" }])
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", true).html("Submitting...");
        addSupplierFormSubmit(event);
    });

editValidator
    .addField("#edit_suppliername", [
        { rule: "required", errorMessage: "*Supplier Name is required" },
        { rule: "maxLength", value: 50, errorMessage: "*Maximum 50 characters" },
    ])
    .addField("#edit_shop",     [{ rule: "required", errorMessage: "*Shop is required" }])
    .addField("#edit_location", [{ rule: "required", errorMessage: "*Location is required" }])
    .addField("#edit_phone",    [{ rule: "required", errorMessage: "*Phone number is required" }])
    .onSuccess((event) => {
        $(".edit_submit_btn").attr("disabled", true).html("Updating...");
        editSupplierFormSubmit(event);
    });

function formatCountryNames(countryId) {
    if (!countryId) return "-";
    return countryId.toString().split(",")
        .filter(id => !window.allowedCountryIds || window.allowedCountryIds.includes(parseInt(id)))
        .map(id => {
            const found = window.countries ? window.countries.find(c => c.id == id) : null;
            return found ? found.name : id;
        })
        .join(", ") || "-";
}

function buildRow(supplier, index) {
    return [
        index + 1,
        formatCountryNames(supplier.country_id),
        supplier.name,
        supplier.shop,
        supplier.location,
        supplier.phone,
        gridjs.html(`
            <div>
                <button data-bs-toggle="modal"
                    data-supplierid="${supplier.id}"
                    data-suppliername="${supplier.name}"
                    data-shop="${supplier.shop}"
                    data-location="${supplier.location}"
                    data-phone="${supplier.phone}"
                    data-country="${supplier.country_id ?? ''}"
                    data-bs-target="#editSupplierModal"
                    class="btn btn-secondary edit_btn">Edit</button>
                <button class="btn btn-danger delete_btn" data-supplierid="${supplier.id}">Delete</button>
            </div>
        `),
    ];
}

const gridNew = new gridjs.Grid({
    columns: ["S.NO", "Country", "Supplier Name", "Shop Name", "Location", "Phone Number", { name: "Action", sort: false }],
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: window.suppliers.map((s, i) => buildRow(s, i)),
    style: {
        table: { border: "1px solid #ccc" },
        th: { "background-color": "rgba(0,0,0,0.1)", color: "#000", "border-bottom": "3px solid #ccc", "text-align": "center", "border-right": "0.5px solid #ccc" },
        td: { "text-align": "center", "border-right": "0.5px solid #ccc", "border-bottom": "0.5px solid #ccc" },
    },
});

gridNew.render(document.getElementById("table-gridjs"));

function gridjsReRender(updatedSuppliers) {
    gridNew.updateConfig({
        data: updatedSuppliers.map((s, i) => buildRow(s, i)),
    }).forceRender();
}

function addSupplierFormSubmit(e) {
    const formdata = new FormData(e.target);
    formdata.delete("country_id");
    formdata.append("country_id", $("#add_country_id").val() || "");

    $.ajax({
        url: window.giftSupplierUrls.store,
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            $("#addSupplierForm")[0].reset();
            if ($("#add_country_id").hasClass("select2-hidden-accessible")) {
                $("#add_country_id").val(null).trigger("change");
            }
            $("#addSupplierModal").modal("hide");
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(response.suppliers);
            Swal.fire("Added", response.message, "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

function editSupplierFormSubmit(e) {
    const formdata = new FormData(e.target);
    formdata.delete("country_id");
    formdata.append("country_id", $("#edit_country_id").val() || "");

    $.ajax({
        url: window.giftSupplierUrls.update + "/" + $("#edit_supplier_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            $("#editSupplierForm")[0].reset();
            $("#editSupplierModal").modal("hide");
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(response.suppliers);
            Swal.fire("Updated", response.message, "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

$(function () {
    $(document).on("click", ".edit_btn", function () {
        $("#edit_supplier_id").val($(this).attr("data-supplierid"));
        $("#edit_suppliername").val($(this).attr("data-suppliername"));
        $("#edit_shop").val($(this).attr("data-shop"));
        $("#edit_location").val($(this).attr("data-location"));
        $("#edit_phone").val($(this).attr("data-phone"));

        const country = $(this).attr("data-country");
        setTimeout(() => {
            $("#edit_country_id").val(country ? country.toString().split(",")[0].trim() : "").trigger("change");
        }, 200);
    });

    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-supplierid");
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
                    url: window.giftSupplierUrls.destroy + "/" + id,
                    method: "POST",
                    dataType: "json",
                    data: { _token: $('meta[name="csrf-token"]').attr("content") },
                    success: function (response) {
                        gridjsReRender(response.suppliers);
                        Swal.fire("Deleted!", response.message, "success");
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
                    },
                });
            }
        });
    });
});