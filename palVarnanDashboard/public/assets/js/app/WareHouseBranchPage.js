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

    data: window.branches.map((br, index) => {
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
                        data-phone="${br.phone}"
                        data-address="${br.address}"
                        data-pincode="${br.pincode}"
                        data-city="${br.city}"
                        data-state="${br.state}"
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
        data: data.map((br, index) => {
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
                            data-phone="${br.phone}"
                            data-address="${br.address}"
                            data-pincode="${br.pincode}"
                            data-city="${br.city}"
                            data-state="${br.state}"
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


$("#addBranchForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
        url: "/warehouse-branch/store",
        type: "POST",
        data: {
            _token: window.csrfToken,
            country_id: $("#add_country_id").val(),
            warehouse_id: $("#add_warehouse_id").val(),
            branch_name: $("#add_branch_name").val(),
        },
        success: function (res) {
            gridjsReRender(res.branches);

            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: res.message,
                timer: 2000,
                showConfirmButton: false
            });

            $("#addBranchModal").modal("hide");
        },
        error: function (err) {
            console.log(err.responseText);
        }
    });
});

$(document).on("click", ".edit_btn", function () {

    let id = $(this).data("id");
    let name = $(this).data("name");
    let country = $(this).data("country");
    let warehouse = $(this).data("warehouse");

    $("#edit_branch_id").val(id);
    $("#edit_branch_name").val(name);
    $("#edit_country_id").val(country);

    // Load warehouses based on country
    $.ajax({
        url: "/get-warehouses/" + country,
        type: "GET",
        success: function (data) {

            let options = '<option value="">Select Warehouse</option>';

            data.forEach(function (w) {
                options += `<option value="${w.id}">${w.warehouse_name}</option>`;
            });

            $("#edit_warehouse_id").html(options);

            // set selected warehouse AFTER loading
            $("#edit_warehouse_id").val(warehouse);
        }
    });
});

$("#editBranchForm").on("submit", function (e) {
    e.preventDefault();

    let id = $("#edit_branch_id").val();

    $.ajax({
        url: "/warehouse-branch/update/" + id,
        type: "POST",
        data: {
            _token: window.csrfToken,
            country_id: $("#edit_country_id").val(),
            warehouse_id: $("#edit_warehouse_id").val(),
            branch_name: $("#edit_branch_name").val(),
        },
        success: function (res) {
            gridjsReRender(res.branches);

            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: res.message,
                timer: 2000,
                showConfirmButton: false
            });

            $("#editBranchModal").modal("hide");
        },
        error: function (err) {
            console.log(err.responseText);
        }
    });
});

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

                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function (err) {
                    console.log(err.responseText);
                }
            });

        }
    });
});


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