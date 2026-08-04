const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Category Name",
        "Product Name",
        // "Overall Stock",
        "Available Stock",
        "Sale Stock",
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
    data: lowstocks.map((lowstocks, index) => {

        return [
            index + 1,
            lowstocks.category_name,
            gridjs.html(
                lowstocks.varient == 1
                ? `<p>${lowstocks.productname} ${lowstocks.value} l</p>
        `
        :lowstocks.varient == 2
        ? `<p>${lowstocks.productname} ${lowstocks.value} ml</p>`
        : lowstocks.varient == 3
        ? `<p>${lowstocks.productname} ${lowstocks.value} g</p>`
        : lowstocks.varient == 4
        ?`<p>${lowstocks.productname} ${lowstocks.value} kg</p>`
        :`<p>${lowstocks.productname} ${lowstocks.value} No's</p>`
            ),

            // lowstocks.overallstock,

            lowstocks.availablestock,
            lowstocks.salestock,
            gridjs.html(
                `<div> <button data-bs-toggle="modal"
                data-productid ="${lowstocks.id}"

                data-bs-target="#editstockModal"  class="btn btn-secondary edit_btn ">Add Qty</button>  </div>`
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

function gridjsReRender(lowstocks) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: lowstocks.map((lowstocks, index) => {
                return [
                    index + 1,
                    lowstocks.category_name,
                    gridjs.html(
                        lowstocks.varient == 1
                        ? `<p>${lowstocks.productname} ${lowstocks.value} l</p>
                `
                :lowstocks.varient == 2
                ? `<p>${lowstocks.productname} ${lowstocks.value} ml</p>`
                : lowstocks.varient == 3
                ? `<p>${lowstocks.productname} ${lowstocks.value} g</p>`
                : lowstocks.varient == 4
                ?`<p>${lowstocks.productname} ${lowstocks.value} kg</p>`
                :`<p>${lowstocks.productname} ${lowstocks.value} No's</p>`
                    ),
                    // lowstocks.overallstock,
                    lowstocks.availablestock,
                    lowstocks.salestock,
                    gridjs.html(
                        `<div> <button data-bs-toggle="modal"
                        data-productid ="${lowstocks.id}"
                         data-bs-target="#editstockModal data"  class="btn btn-secondary edit_btn ">Add Qty</button>  </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

const addValidator = new JustValidate("#addStockLowForm", {
    validateBeforeSubmitting: true,
});
addValidator
    .addField("#stock_quantity1", [
        {
            rule: "required",
            errorMessage: "*Stock Field is required",
        },
        {

            rule: 'maxLength',
            value: 5,
            errorMessage: '*Stock should be at Maximum 5 character',

        },
        {
            rule: 'customRegexp',
            value:  /^[0-9]+$/,
            errorMessage: '* Stock only number'
        }
    ])

    .onSuccess((event) => {
        $(".addstock_submit_btn").attr("disabled", "true");
        $(".addstock_submit_btn").html("Uploading.....");
        addStockLowFormSubmit(event);
    });


    $(document).on("click", ".edit_btn", function () {
        $("#productid").val($(this).attr("data-productid"));
    });


    function addStockLowFormSubmit(e) {
        const formdata = new FormData(e.target);
        $.ajax({
            url: "updateStacklow",
            method: "POST",
            dataType: "json",
            data: formdata,
            processData: false,
            contentType: false,
            success: function (response) {
                const updatedProduct = response.lowstocks;
                $("#addStockLowForm")[0].reset();
                $("#editstockModal").hide();
                $(".modal-backdrop").remove();

                document.body.style.overflowY = "scroll";
                gridjsReRender(updatedProduct);
                $(".add_submit_btn").removeAttr("disabled");
                $(".addstock_submit_btn").html("Update");
               Swal.fire({
                title: "Updated",
                text: "Records updated successfully.",
                icon: "success",
                timer: 1200,
                showConfirmButton: false,
                willClose: () => {
                    location.reload(); // Reload after alert closes
                }
            });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".addstock_submit_btn").removeAttr("disabled");
                $(".add_submit_btn").removeAttr("disabled");
                $(".addstock_submit_btn").html("Update");
                $(".add_submit_btn").html("Submit");
                console.log(textStatus + ": " + errorThrown);

                Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
            },
        });
    }
