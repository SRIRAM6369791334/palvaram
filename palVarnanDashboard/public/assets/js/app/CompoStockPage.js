const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Category Name",
        "Product Name",

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
    data: combostocks.map((combostocks, index) => {

        return [
            index + 1,
            combostocks.category_name,
            gridjs.html(
                combostocks.varient == 1
                ? `<p>${combostocks.productname} ${combostocks.value} l</p>
        `
        :combostocks.varient == 2
        ? `<p>${combostocks.productname} ${combostocks.value} ml</p>`
        : combostocks.varient == 3
        ? `<p>${combostocks.productname} ${combostocks.value} g</p>`
        : combostocks.varient == 4
        ?`<p>${combostocks.productname} ${combostocks.value} kg</p>`
        :`<p>${combostocks.productname} ${combostocks.value} No's</p>`
            ),



            combostocks.availablestock,
            combostocks.salestock,
            gridjs.html(
                `<div> <button data-bs-toggle="modal"
                data-productid ="${combostocks.id}"

                data-bs-target="#editProductModal"  class="btn btn-success edit_btn ">Add</button>   <button data-bs-toggle="modal"
                data-productid1 ="${combostocks.id}"
                data-availa ="${combostocks.availablestock}"

                data-bs-target="#updateProductModal"  class="btn btn-secondary edit_btn1 ">Reduce</button></div>`

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

function gridjsReRender(combostocks) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: combostocks.map((combostocks, index) => {
                return [
                    index + 1,
                    combostocks.category_name,
                    gridjs.html(
                        combostocks.varient == 1
                        ? `<p>${combostocks.productname} ${combostocks.value} l</p>
                `
                :combostocks.varient == 2
                ? `<p>${combostocks.productname} ${combostocks.value} ml</p>`
                : combostocks.varient == 3
                ? `<p>${combostocks.productname} ${combostocks.value} g</p>`
                : combostocks.varient == 4
                ?`<p>${combostocks.productname} ${combostocks.value} kg</p>`
                :`<p>${combostocks.productname} ${combostocks.value} No's</p>`
                    ),

                    combostocks.availablestock,
                    combostocks.salestock,
                    gridjs.html(
                        `<div> <button data-bs-toggle="modal"
                        data-productid ="${combostocks.id}"

                        data-bs-target="#editProductModal"  class="btn btn-success edit_btn ">Add</button>   <button data-bs-toggle="modal"
                        data-productid1 ="${combostocks.id}"
                        data-availa ="${combostocks.availablestock}"

                        data-bs-target="#updateProductModal"  class="btn btn-secondary edit_btn1 ">Reduce</button></div>`

                    ),
                ];
            }),
        })
        .forceRender();
}

const addValidator = new JustValidate("#addStockForm", {
    validateBeforeSubmitting: true,
});
const addValidator1 = new JustValidate("#addStockForm1", {
    validateBeforeSubmitting: true,
});
addValidator
    .addField("#stock_quantity", [
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
        $(".add_submit_btn").attr("disabled", "true");
        $(".add_submit_btn").html("Uploading.....");
        addStockFormSubmit(event);
    });

    addValidator1
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
        },
        {
            validator: (value) => {
              const ava =  $("#availa_quantity1").val();
              if (parseInt(value) < parseInt(ava)) {
                return true;
              } else {
                return false;
              }
            },
            errorMessage: "Should not be above Available stock",
          },

    ])

    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", "true");
        $(".add_submit_btn").html("Uploading.....");
        addStockForm1Submit(event);
    });


    $(document).on("click", ".edit_btn", function () {
        $("#productid").val($(this).attr("data-productid"));
    });
    $(document).on("click", ".edit_btn1", function () {
        $("#productid1").val($(this).attr("data-productid1"));
        $("#availa_quantity1").val($(this).attr("data-availa"));
    });


    function addStockFormSubmit(e) {
        const formdata = new FormData(e.target);
        $.ajax({
            url: "updateStack1",
            method: "POST",
            dataType: "json",
            data: formdata,
            processData: false,
            contentType: false,
            success: function (response) {
                const updatedProduct = response.combostocks;
                $("#addStockForm")[0].reset();
                $("#editProductModal").hide();
                $(".modal-backdrop").remove();

                document.body.style.overflowY = "scroll";
                gridjsReRender(updatedProduct);
                $(".add_submit_btn").removeAttr("disabled");
                $(".edit_submit_btn").html("Update");
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


    function addStockForm1Submit(e) {
        const formdata = new FormData(e.target);
        $.ajax({
            url: "reduceStock1",
            method: "POST",
            dataType: "json",
            data: formdata,
            processData: false,
            contentType: false,
            success: function (response) {
                const updatedProduct = response.combostocks;
                $("#addStockForm1")[0].reset();
                $("#updateProductModal").hide();
                $(".modal-backdrop").remove();

                document.body.style.overflowY = "scroll";
                gridjsReRender(updatedProduct);
                $(".add_submit_btn").removeAttr("disabled");
                $(".edit1_submit_btn").html("Update");
                Swal.fire("Updated", "Records Updated  Successfully.", "success");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".edit1_submit_btn").removeAttr("disabled");
                $(".add_submit_btn").removeAttr("disabled");
                $(".edit1_submit_btn").html("Update");
                $(".add_submit_btn").html("Submit");
                console.log(textStatus + ": " + errorThrown);

                Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
            },
        });
    }
