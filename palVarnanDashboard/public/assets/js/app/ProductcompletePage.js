const gridNew = new gridjs.Grid({
    columns: [
        "S.No",
        "Country",
        "Order ID",
        "Ordered Date",
        "Name",
        // "Addesss",
        "Payment Status",
        "Delivery Status",
        "Delivered Date",
        "Delivery Agent",

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
    data: productcompletes.map((productcomplete, index) => {
        return [
            index + 1,
            productcomplete.country?.name ?? "-",
            // gridjs.html(
            //     `<a href="productOrders/${productcomplete.order_id}">${productcomplete.order_id} </a>`
            // ),
            productcomplete.order_id,
            productcomplete.date_ordered_on,
            productcomplete.order_address ? productcomplete.order_address.firstname : (productcomplete.billing_name || 'N/A'),
            // productcomplete.order_address.address_line_one,
            // gridjs.html(
            //     productcomplete.payment_status == 1
            //     ?`<div class="text-success" style="font-weight:bold">PAID</div>`
            //     :`<div class="text-warning" style="font-weight:bold">COD (PAID)</div>`
            // ),
            productcomplete.payment_status,
            gridjs.html(
                productcomplete.delivery_status == 0
                    ? `<div class="text-info" style="font-weight:bold">Pending</div>
            `
                    : productcomplete.delivery_status == 1
                        ? `<div class="text-primary" style="font-weight:bold">Packing</div>`
                        : productcomplete.delivery_status == 2
                            ? `<div class="text-warning" style="font-weight:bold">Dispatched</div>`
                            : productcomplete.delivery_status == 3

                                ? `<div class="text-success" style="font-weight:bold">Out for Delivery</div>`
                                : `<div class="text-success" style="font-weight:bold">Delivered</div>`
            ),
            productcomplete.updated_at ? new Date(productcomplete.updated_at).toLocaleDateString('en-GB') : productcomplete.date_ordered_on,
            productcomplete.delivery_person_name ? `${productcomplete.delivery_person_name} (${productcomplete.delivery_person_phone})` : 'N/A',


            gridjs.html(`
            <a href="viewProductdetail/${productcomplete.order_id}" target="_blank">
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

function gridjsReRender(productcompletes) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: productcompletes.map((productcomplete, index) => {
                return [
                    index + 1,
                    productcomplete.country?.name ?? "-",
                    // gridjs.html(
                    //     `<a href="productOrders/${productcomplete.order_id}">${productcomplete.order_id} </a>`
                    // ),
                    productcomplete.order_id,
                    productcomplete.date_ordered_on,
                    productcomplete.order_address ? productcomplete.order_address.firstname : (productcomplete.billing_name || 'N/A'),
                    // productcomplete.order_address.address_line_one,
                    // gridjs.html(
                    //     productcomplete.payment_status == 1
                    //     ?`<div class="text-success" style="font-weight:bold">PAID</div>`
                    //     :`<div class="text-warning" style="font-weight:bold">COD (PAID)</div>`
                    // ),
                    productcomplete.payment_status,
                    gridjs.html(
                        productcomplete.delivery_status == 0
                            ? `<div class="text-info" style="font-weight:bold">Pending</div>
                    `
                            : productcomplete.delivery_status == 1
                                ? `<div class="text-primary" style="font-weight:bold">Packing</div>`
                                : productcomplete.delivery_status == 2
                                    ? `<div class="text-warning" style="font-weight:bold">Dispatched</div>`
                                    : productcomplete.delivery_status == 3

                                        ? `<div class="text-success" style="font-weight:bold">Out for Delivery</div>`
                                        : `<div class="text-success" style="font-weight:bold">Delivered</div>`
                    ),
                    productcomplete.updated_at ? new Date(productcomplete.updated_at).toLocaleDateString('en-GB') : productcomplete.date_ordered_on,
                    productcomplete.delivery_person_name ? `${productcomplete.delivery_person_name} (${productcomplete.delivery_person_phone})` : 'N/A',


                    gridjs.html(`
                    <a href="viewProductdetail/${productcomplete.order_id}" target="_blank">
                    <button type="button" class="btn btn-primary btn-sm text-truncate ms-2"  data-bs-target="#viewOrdersModal"><i class="bx bx-message-square-dots me-1"></i>

                    View</button>
                    </a>
                    `),
                ];
            }),
        })
        .forceRender();
}


