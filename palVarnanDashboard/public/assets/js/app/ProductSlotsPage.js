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
    data: productSlots.map((productSlot, index) => {

        return [
            index + 1,



            gridjs.html(
                productSlot.product_varient.varient == 1
                    ? `<p>${productSlot.product.product_name} ${productSlot.product_varient.value}l</p>
            `
                    : productSlot.product_varient.varient == 2
                        ? `<p>${productSlot.product.product_name} ${productSlot.product_varient.value}ml</p>`
                        : productSlot.product_varient.varient == 3
                            ? `<p>${productSlot.product.product_name} ${productSlot.product_varient.value}g</p>`
                            : productSlot.product_varient.varient == 4
                                ? `<p>${productSlot.product.product_name} ${productSlot.product_varient.value}kg</p>`
                                : `<p>${productSlot.product.product_name} ${productSlot.product_varient.value}No's</p>`
            ),

            // productSlot.product.product_name,
            productSlot.product_varient.offer_price,
            productSlot.quantity,
            gridjs.html(
                `${productSlot.product_varient.offer_price * productSlot.quantity}`
            ),

            productSlot.order.customer.user_addresses[0].address_line_one,
            gridjs.html(
                productSlot.delivery_status == 0
                    ? `<div class="text-info" style="font-weight:bold">Billing</div>
`
                    : productSlot.delivery_status == 1
                        ? `<div class="text-secondary" style="font-weight:bold">Packing</div>`
                        : productSlot.delivery_status == 2
                            ? `<div class="text-success" style="font-weight:bold">Dispatched</div>`
                            : productSlot.delivery_status == 3

                                ? `<div class="text-danger" style="font-weight:bold">Out Of Delivery</div>`
                                : productSlot.delivery_status == 4
                                    ? `<div class="text-warning" style="font-weight:bold">Delivered</div>`
                                    : productSlot.delivery_status == 5
                                        ? `<div class="text-info" style="font-weight:bold">Cancel</div>`
                                        : `<div class="text-dark" style="font-weight:bold">Product Return</div>`

            ),






            gridjs.html(
                productSlot.is_cancelled == 0

                    ? ` <div class="btn btn-sm btn-danger">Not Cancel</div>`
                    : productSlot.is_cancelled == 1
                        ? `
                    <div class="btn btn-sm btn-danger">Cancelled</div>`
                        : `<button
                    data-productSlotid ="${productSlot.id}",
                    data-ogproductSlotid = "${productSlot.order_id}"
                    data-productname = "${productSlot.product.product_name}"
                    data-customername= "${productSlot.order.customer.name}"

                    data-deliverypersonid ="${productSlot.delivery_person_id}"
               class="btn btn-warning  btn-sm text-truncate ms-2 cancel_slot_btn">
                Cancel</button>`


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

function gridjsReRender(productSlots) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: productSlots.map((productSlot, index) => {
                return [
                    index + 1,



                    gridjs.html(
                        productSlot.product_varient.varient == 1
                            ? `<p>${productSlot.product.product_name} ${productSlot.product_varient.value}l</p>
                    `
                            : productSlot.product_varient.varient == 2
                                ? `<p>${productSlot.product.product_name} ${productSlot.product_varient.value}ml</p>`
                                : productSlot.product_varient.varient == 3
                                    ? `<p>${productSlot.product.product_name} ${productSlot.product_varient.value}g</p>`
                                    : productSlot.product_varient.varient == 4
                                        ? `<p>${productSlot.product.product_name} ${productSlot.product_varient.value}kg</p>`
                                        : `<p>${productSlot.product.product_name} ${productSlot.product_varient.value}No's</p>`
                    ),

                    // productSlot.product.product_name,
                    productSlot.product_varient.offer_price,
                    productSlot.quantity,
                    gridjs.html(
                        `${productSlot.product_varient.offer_price * productSlot.quantity}`
                    ),

                    productSlot.order.customer.user_addresses[0].address_line_one,
                    gridjs.html(
                        productSlot.delivery_status == 0
                            ? `<div class="text-info" style="font-weight:bold">Billing</div>
        `
                            : productSlot.delivery_status == 1
                                ? `<div class="text-secondary" style="font-weight:bold">Packing</div>`
                                : productSlot.delivery_status == 2
                                    ? `<div class="text-success" style="font-weight:bold">Dispatched</div>`
                                    : productSlot.delivery_status == 3

                                        ? `<div class="text-danger" style="font-weight:bold">Out Of Delivery</div>`
                                        : productSlot.delivery_status == 4
                                            ? `<div class="text-warning" style="font-weight:bold">Delivered</div>`
                                            : productSlot.delivery_status == 5
                                                ? `<div class="text-info" style="font-weight:bold">Cancel</div>`
                                                : `<div class="text-dark" style="font-weight:bold">Product Return</div>`

                    ),




                    gridjs.html(
                        productSlot.is_cancelled == 0
                            ? ` <div class="btn btn-sm btn-danger">Not Cancel</div>`
                            : productSlot.is_cancelled == 1
                                ? `
                            <div class="btn btn-sm btn-danger">Cancelled</div>`
                                : `<button
                            data-productSlotid ="${productSlot.id}",
                            data-ogproductSlotid = "${productSlot.order_id}"
                            data-productname = "${productSlot.product.product_name}"
                            data-customername= "${productSlot.order.customer.name}"

                            data-deliverypersonid ="${productSlot.delivery_person_id}"
                       class="btn btn-warning  btn-sm text-truncate ms-2 cancel_slot_btn">
                        Cancel</button>`


                    ),
                ];
            }),
        })
        .forceRender();
}

$(function () {
    $(document).on("click", ".cancel_slot_btn", function () {
        const id = $(this).attr("data-productSlotid");
        Swal.fire({
            title: "Are you sure want to cancel order?",
            text: "This current slot will be cancelled!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Cancel it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: location.origin + "/cancelProductSlot",
                    method: "post",
                    data: {
                        product_slot_id: id,
                    },
                    dataType: "json",
                    success: function (response) {
                        const updatedProductSlots = response.productSlots;
                        gridjsReRender(updatedProductSlots);
                        Swal.fire(
                            "Cancelled!",
                            "Slot Cancelled Successfully",
                            "success"
                        );
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
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
