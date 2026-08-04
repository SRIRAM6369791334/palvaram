const gridNew = new gridjs.Grid({
    columns: [
        "S.No",

        "Product",
        "Price",
        "Product Qut",
        "Amount",
        "Area",
        "Delivery Status",
        {
            name: "Cancel Slot",
            sort: false,
        },
    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: productSlotss.map((productSlots, index) => {
        return [
            index + 1,


            gridjs.html(
                productSlots.product_varient.varient==1
                    ? `<p>${productSlots.product.product_name} ${ productSlots.product_varient.value}l</p>
            `
            : productSlots.product_varient.varient==2
            ? `<p>${productSlots.product.product_name} ${ productSlots.product_varient.value}ml</p>`
            : productSlots.product_varient.varient==3
            ? `<p>${productSlots.product.product_name} ${ productSlots.product_varient.value}g</p>`
            : productSlots.product_varient.varient==4
            ?`<p>${productSlots.product.product_name} ${ productSlots.product_varient.value}kg</p>`
            :`<p>${productSlots.product.product_name} ${ productSlots.product_varient.value}No's</p>`
            ),

            productSlots.product_varient.offer_price,
            // productSlots.product.product_name,
            productSlots.quantity,

            gridjs.html(
                `${productSlots.product_varient.offer_price * productSlots.quantity}`
            ),

            productSlots.order.customer.user_addresses[0].address_line_one,
            gridjs.html(
                productSlots.delivery_status == 0
                ? `<div class="text-info" style="font-weight:bold">Billing</div>
        `
        : productSlots.delivery_status == 1
        ? `<div class="text-secondary" style="font-weight:bold">Packing</div>`
        : productSlots.delivery_status == 2
        ? `<div class="text-success" style="font-weight:bold">Dispatched</div>`
        : productSlots.delivery_status == 3

        ?`<div class="text-danger" style="font-weight:bold">Out Of Delivery</div>`
        :productSlots.delivery_status == 4
        ?`<div class="text-warning" style="font-weight:bold">Delivered</div>`
        :productSlots.delivery_status == 5
        ?`<div class="text-info" style="font-weight:bold">Cancel</div>`
        :`<div class="text-dark" style="font-weight:bold">Product Return</div>`

            ),

            gridjs.html(
                productSlots.is_cancelled == 0
                    ? `<div class="btn btn-sm btn-primary">Not Cancelled</div>`
                    : `<div class="btn btn-sm btn-warning">Cancelled</div>`

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

function gridjsReRender(productSlotss) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: productSlotss.map((productSlots, index) => {
                return [
                    index + 1,


                    gridjs.html(
                        productSlots.product_varient.varient==1
                            ? `<p>${productSlots.product.product_name} ${ productSlots.product_varient.value}l</p>
                    `
                    : productSlots.product_varient.varient==2
                    ? `<p>${productSlots.product.product_name} ${ productSlots.product_varient.value}ml</p>`
                    : productSlots.product_varient.varient==3
                    ? `<p>${productSlots.product.product_name} ${ productSlots.product_varient.value}g</p>`
                    : productSlots.product_varient.varient==4
                    ?`<p>${productSlots.product.product_name} ${ productSlots.product_varient.value}kg</p>`
                    :`<p>${productSlots.product.product_name} ${ productSlots.product_varient.value}No's</p>`
                    ),

                    productSlots.product_varient.offer_price,
                    // productSlots.product.product_name,
                    productSlots.quantity,

                    gridjs.html(
                        `${productSlots.product_varient.offer_price * productSlots.quantity}`
                    ),

                    productSlots.order.customer.user_addresses[0].address_line_one,
                    gridjs.html(
                        productSlots.delivery_status == 0
                        ? `<div class="text-info" style="font-weight:bold">Billing</div>
                `
                : productSlots.delivery_status == 1
                ? `<div class="text-secondary" style="font-weight:bold">Packing</div>`
                : productSlots.delivery_status == 2
                ? `<div class="text-success" style="font-weight:bold">Dispatched</div>`
                : productSlots.delivery_status == 3

                ?`<div class="text-danger" style="font-weight:bold">Out Of Delivery</div>`
                :productSlots.delivery_status == 4
                ?`<div class="text-warning" style="font-weight:bold">Delivered</div>`
                :productSlots.delivery_status == 5
                ?`<div class="text-info" style="font-weight:bold">Cancel</div>`
                :`<div class="text-dark" style="font-weight:bold">Product Return</div>`


                    ),

                    gridjs.html(
                        productSlots.is_cancelled == 0
                            ? `<div class="btn btn-sm btn-primary">Not Cancelled</div>`
                            : `<div class="btn btn-sm btn-warning">Cancelled</div>`

                    ),
                ];
            }),
        })
        .forceRender();
}
