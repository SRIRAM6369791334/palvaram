// VALIDATION INITIALIZATION
const addValidator = new JustValidate("#addDealsForm", {
    validateBeforeSubmitting: true,
});

const editValidator = new JustValidate("#editDealsForm", {
    validateBeforeSubmitting: true,
});

// ADD fORM VALIDATION
addValidator
    .addField("#add_productname", [
        {
            rule: "required",
            errorMessage: "*Product Name Field is required",
        },
    ])
    .addField("#add_offervalue", [
        {
            rule: "required",
            errorMessage: "*Offer Value Field is required",
        },
        // {
        //     rule: "minLength",
        //     value: 3,
        //     errorMessage: "*Offer Value should be at least 3 character long",
        // },
        {
            rule: "maxLength",
            value: 3,
            errorMessage: "*Offer Value should be at Maximum 3 character long",
        },
    ])
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", "true");
        $(".add_submit_btn").html("Uploading.....");
        addDealsForm(event);
    });

//EDIT FORM VALIDATION
editValidator
    .addField("#edit_productname", [
        {
            rule: "required",
            errorMessage: "*Product Name Field is required",
        },
    ])
    .addField("#edit_offervalue", [
        {
            rule: "required",
            errorMessage: "*Offer Value Field is required",
        },
        // {
        //     rule: "minLength",
        //     value: 3,
        //     errorMessage:
        //         "*Sub Categories Name should be at least 3 character long",
        // },
        // {
        //     rule: "maxLength",
        //     value: 50,
        //     errorMessage:
        //         "*Sub Categories Name should be at Maximum 50 character long",
        // },
    ])
    .onSuccess((event) => {
        $(".edit_submit_btn").attr("disabled", "true");
        $(".edit_submit_btn").html("Uploading.....");
        editDealsForm(event);
    });

// GRID JS TABLE
const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Product ID",
        "Product Name",
        "Offer Percentage",

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
    data: subcate.map((deals, index) => {
        return [
            index + 1,
            deals.product_id,
            deals.product_name,
            deals.offer_value,

            gridjs.html(
                subcate.status == 1
                    ? `<div> <button data-bs-toggle="modal"
                data-dealsid ="${deals.id}"
                data-productVarintID ="${deals.variant_id}"
                data-productname="${deals.product_name}"
                data-offervalue="${deals.offer_value}"
                data-bs-target="#editFormModal"  class="btn btn-secondary edit_btn ">Edit</button>

                </div>`
                    : `<div> <button data-bs-toggle="modal"
                data-dealsid ="${deals.id}"
                data-productVarintID ="${deals.variant_id}"
                data-productname="${deals.product_name}"
                data-offervalue="${deals.offer_value}"
                data-bs-target="#editFormModal"  class="btn btn-secondary edit_btn ">Edit</button>

                <button class="btn btn-danger delete_btn" data-dealsid = ${deals.id}>Delete</button> </div>`
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

function gridjsReRender(subcate) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: subcate.map((deals, index) => {
                return [
                    index + 1,
                    deals.product_id,
                    deals.product_name,
                    deals.offer_value,

                    gridjs.html(
                        subcate.status == 1
                            ? `<div> <button data-bs-toggle="modal"
                                data-dealsid ="${deals.id}"
                                data-productVarintID ="${deals.variant_id}"
                                data-productname="${deals.product_name}"
                                data-offervalue="${deals.offer_value}"
                                data-bs-target="#editFormModal"  class="btn btn-secondary edit_btn ">Edit</button>

                                </div>`
                            : `<div> <button data-bs-toggle="modal"
                                data-dealsid ="${deals.id}"
                                data-productVarintID ="${deals.variant_id}"
                                data-productname="${deals.product_name}"
                                data-offervalue="${deals.offer_value}"
                                data-bs-target="#editFormModal"  class="btn btn-secondary edit_btn ">Edit</button>

                                <button class="btn btn-danger delete_btn" data-dealsid = ${deals.id}>Delete</button> </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

// ADD DEALS FORM SUBMITION
function addDealsForm(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "todaydeals",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn").removeAttr("disabled");
            $(".add_submit_btn").html("Submit");
            const updatedTodayDeals = response.deals;
            $("#addDealsForm")[0].reset();
            $("#addtoadyDealsModal").hide();
            $(".product_remove_btn").trigger("click");
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";

            gridjsReRender(updatedTodayDeals);
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

// EDIT DEALS FORM SUBMISSION
// APPENDING VALUES TO EDIT FORM

$(document).on("click", ".edit_btn", function () {
    $("#edit_deals_id").val($(this).attr("data-dealsid"));
    $("#edit_product_id").val($(this).attr("data-productid"));
    $("#edit_productname").val($(this).attr("data-productVarintID"));
    $("#edit_offervalue").val($(this).attr("data-offervalue"));
});

function editDealsForm(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updatetodaydeals/" + $("#edit_deals_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedTodayDeals = response.deals;
            $("#editDealsForm")[0].reset();
            $("#editFormModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedTodayDeals);
            $(".edit_submit_btn").removeAttr("disabled");
            $(".edit_submit_btn").html("Update");
            $(".edit_product_remove_btn").trigger("click");
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

// DEALS DELETION

$(function () {
    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-dealsid");
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
                    url: "destroytodaydeals/" + id,
                    method: "post",
                    dataType: "json",
                    success: function (response) {
                        const updatedTodayDeals = response.deals;
                        gridjsReRender(updatedTodayDeals);
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
});

$("#add_offervalue").on("input", function () {
    $("#add_offervalue").attr(
        "oninput",
        "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3); if(parseInt(this.value) >= 100) this.value = '100';"
    );
});