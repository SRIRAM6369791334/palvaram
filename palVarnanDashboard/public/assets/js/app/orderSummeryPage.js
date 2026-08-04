const gridNew = new gridjs.Grid({
    columns: [
        "S.No",
        "Country", 
        "Order ID",
        "Ordered Date",
        "Amount",
        "Name",
        "Addesss",
        "Payment Status",
        "Delivery Status",
        {
            name: "Status",
            sort: false,
        },

    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: ordersummerys.map((ordersummery, index) => {
        return [
            index + 1,
            gridjs.html(
                `<a href="ordersummerys/${ordersummery.order_id}">${ordersummery.order_id} </a>`
            ),
            ordersummery.country_name ?? '-',
            ordersummery.date_ordered_on,
            ordersummery.total_amount,
            ordersummery.customer.name,
            ordersummery.order_address.address_line_one,
            gridjs.html(
                ordersummery.payment_status == 1
                ?`<div class="text-success" style="font-weight:bold">PAID</div>`
                :ordersummery.payment_status == 2
                ?`<div class="text-info" style="font-weight:bold">COD</div>`
                :`<div class="text-warning" style="font-weight:bold">COD (PAID)</div>`
            ),
            gridjs.html(
                ordersummery.delivery_status == 0
                    ? `<div class="text-info" style="font-weight:bold">Billing</div>
            `
            : ordersummery.delivery_status == 1
            ? `<div class="text-secondary" style="font-weight:bold">Packing</div>`
            : ordersummery.delivery_status == 2
            ? `<div class="text-success" style="font-weight:bold">Dispatched</div>`
            : ordersummery.delivery_status == 3

            ?`<div class="text-danger" style="font-weight:bold">Out Of Delivery</div>`
            :ordersummery.delivery_status == 4
            ?`<div class="text-warning" style="font-weight:bold">Delivered</div>`
            :ordersummery.delivery_status == 5
            ?`<div class="text-info" style="font-weight:bold">Cancel</div>`
            :`<div class="text-dark" style="font-weight:bold">Product Return</div>`


            ),


            gridjs.html(`
            <a href="viewProductdetail/${ordersummery.order_id}" target="_blank">
            <button type="button" class="btn btn-primary btn-sm text-truncate ms-2"  data-bs-target="#viewOrdersModal"><i class="bx bx-message-square-dots me-1"></i>

            View</button>
            </a>
            `),
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

function gridjsReRender(ordersummerys) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: ordersummerys.map((ordersummery, index) => {
                return [
                    index + 1,
                    gridjs.html(
                        `<a href="ordersummerys/${ordersummery.order_id}">${ordersummery.order_id} </a>`
                    ),
                    ordersummery.date_ordered_on,
                    ordersummery.customer.name,
                    ordersummery.total_amount,
                    ordersummery.order_address.address_line_one,
                    gridjs.html(
                        ordersummery.payment_status == 1
                        ?`<div class="text-success" style="font-weight:bold">PAID</div>`
                        :ordersummery.payment_status == 2
                        ?`<div class="text-info" style="font-weight:bold">COD</div>`
                        :`<div class="text-warning" style="font-weight:bold">COD (PAID)</div>`
                    ),
                    gridjs.html(
                        ordersummery.delivery_status == 0
                            ? `<div class="text-info" style="font-weight:bold">Billing</div>
                    `
                    : ordersummery.delivery_status == 1
                    ? `<div class="text-secondary" style="font-weight:bold">Packing</div>`
                    : ordersummery.delivery_status == 2
                    ? `<div class="text-success" style="font-weight:bold">Dispatched</div>`
                    : ordersummery.delivery_status == 3

                    ?`<div class="text-danger" style="font-weight:bold">Out Of Delivery</div>`
                    :ordersummery.delivery_status == 4
                    ?`<div class="text-warning" style="font-weight:bold">Delivered</div>`
                    :ordersummery.delivery_status == 5
                    ?`<div class="text-info" style="font-weight:bold">Cancel</div>`
                    :`<div class="text-dark" style="font-weight:bold">Product Return</div>`


                    ),


                    gridjs.html(`
                    <a href="viewProductdetail/${ordersummery.order_id}" target="_blank">
                    <button type="button" class="btn btn-primary btn-sm text-truncate ms-2"  data-bs-target="#viewOrdersModal"><i class="bx bx-message-square-dots me-1"></i>

                    View</button>
                    </a>
                    `),
                ];
            }),
        })
        .forceRender();
}

const addValidator = new JustValidate("#ordersummeryForm", {
    validateBeforeSubmitting: true,
});

addValidator
    .addField("#select_delivery", [
        {
            rule: "required",
            errorMessage: "*Delivery Status is required",
        },
    ])
    .addField(".fromdate", [
        {
            rule: "required",
            errorMessage: "*From Date Field is required",
        },
    ])

    .addField(".todate", [
        {
            rule: "required",
            errorMessage: "*To Date Field is required",
        },
    ])
    .onSuccess((event) => {
        $(".order_submit_btn").attr("disabled", "true");
        $(".order_submit_btn").html("Uploading.....");
        ordersummeryFormSubmit(event);
    });

    function ordersummeryFormSubmit(event) {
        const formData = new FormData(event.target);
         $.ajax({
            url: "getoversummery",
            method: "POST",
            dataType: "json",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".order_submit_btn").removeAttr("disabled");
                $(".order_submit_btn").html("Get Report");
                const updatedProduct = response.ordersummerys;
                gridjsReRender(updatedProduct);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".order_submit_btn").removeAttr("disabled");
                $(".order_submit_btn").html("Get Report");
                Swal.fire(textStatus.toUpperCase(), textStatus, "warning");
            },
        });
    }




