const gridNew = new gridjs.Grid({
    columns: [
        "S.No",
        "Order ID",
        "Cancelled Date",
        "Refund Status",
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
    data: productRefunds.map((productRefund, index) => {
        return [
            index + 1,
            gridjs.html(
                `${productRefund.order_id} `
            ),
            productRefund.created_at,
            gridjs.html(
                productRefund.refund_status
                    ? `<div class="is_delivery_assigned">Refunded</div>
            `
                    : `<div class="is_delivery_not_assigned">Pending</div>
            `
            ),
            gridjs.html(
                productRefund.refund_status
                    ? `<div class="is_delivery_assigned">Success</div>`
                    : `<div> <button data-bs-toggle="modal"
                    data-productRefundid ="${productRefund.id}"
                data-bs-target="#productRefundModal"  class="btn btn-warning  btn-sm text-truncate ms-2 product_refund_btn">
                Refund</button>`
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

function gridjsReRender(productRefunds) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: productRefunds.map((productRefund, index) => {
                return [
                    index + 1,
                    gridjs.html(
                        `${productRefund.order_id} `
                    ),
                    productRefund.created_at,
                    gridjs.html(
                        productRefund.refund_status
                            ? `<div class="is_delivery_assigned">Refunded</div>
                    `
                            : `<div class="is_delivery_not_assigned">Pending</div>
                    `
                    ),
                    gridjs.html(
                        productRefund.refund_status
                            ? `<div class="is_delivery_assigned">Success</div>`
                            : `<div> <button data-bs-toggle="modal"
                            data-productRefundid ="${productRefund.id}"
                        data-bs-target="#productRefundModal"  class="btn btn-warning  btn-sm text-truncate ms-2 product_refund_btn">
                        Refund</button>`
                    ),
                ];
            }),
        })
        .forceRender();
}

$(function () {
    $(document).on("click", ".product_refund_btn", function () {
        const refundId = $(this).attr("data-productRefundid");
        window.refundId = refundId;

        $.ajax({
            type: "POST",
            url: "getProductRefundDatas",
            data: {
                refund_id: refundId,
            },
            success: function (response) {
                $("#table_gridjs_modal").html("");
                $(".refund_btn").removeAttr("disabled");
                if (initalLoad) {
                    modalGrid(response);
                    initalLoad = 0;
                    return;
                }
                modalGridRerender(response);
            },
        });
    });

    $(document).on("click", ".refund_btn", function () {
        if (!refundId) return;
        $.ajax({
            type: "POST",
            url: "refundProductSlot",
            data: {
                refund_id: refundId,
            },
            success: function (response) {
                const updatedProductRefunds = response.productRefunds.map(
                    (item) => {
                        item.created_at = new Date(item.created_at)
                            .toLocaleDateString("en-IN", {
                                day: "numeric",
                                month: "short",
                                year: "numeric",
                            })
                            .split(" ")
                            .join("-");
                        return item;
                    }
                );
                $("#productRefundModal").hide();
                $(".modal-backdrop").remove();
                document.body.style.overflowY = "scroll";
                gridjsReRender(updatedProductRefunds);
                Swal.fire("Refunded", "Refund Status Successfull.", "success");
            },
        });
    });
});

let initalLoad = 1;

let gridModelNew;

function modalGrid(response) {
    gridModelNew = new gridjs.Grid({
        columns: [
            "S.No",
            "Customer Name",
            "Mobile Number",
            "Product",
            "Price",
            "Quantity",
            "Refund Amount",
        ],

        sort: 0,
        search: 0,
        data: [
            [
                1,
                response.product_order.customer.name,
                response.product_order.customer.phone_number,
                gridjs.html(
                    response.product_slot.product_varient.varient == 1

                  ? `${response.product_slot.product.product_name} <span>${response.product_slot.product_varient.value}</span><span>l</span>`
                  : response.product_slot.product_varient.varient == 2
                  ?`${response.product_slot.product.product_name} <span>${response.product_slot.product_varient.value}</span><span>ml</span>`
                  :response.product_slot.product_varient.varient == 3
                  ?`${response.product_slot.product.product_name} <span>${response.product_slot.product_varient.value}</span><span>g</span>`
                  :response.product_slot.product_varient.varient == 4
                  ?`${response.product_slot.product.product_name} <span>${response.product_slot.product_varient.value}</span><span>kg</span>`
                  :`${response.product_slot.product.product_name} <span>${response.product_slot.product_varient.value}</span><span>No's</span>`
                ),
                response.product_slot.product_varient.offer_price,
                response.product_slot.quantity,
                response.product_slot.product_varient.offer_price *
                    response.product_slot.quantity,
            ],
        ],
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
        className: {
            table: "mv_custom_grid_table",
        },
    });
    gridModelNew.render(document.getElementById("table_gridjs_modal"));
}

function modalGridRerender(response) {
    gridModelNew
        .updateConfig({
            data: [
                [
                    1,
                    response.product_order.customer.name,
                    response.product_order.customer.phone_number,
                    gridjs.html(
                        response.product_slot.product_varient.varient == 1

                      ? `${response.product_slot.product.product_name} <span>${response.product_slot.product_varient.value}</span><span>l</span>`
                      : response.product_slot.product_varient.varient == 2
                      ?`${response.product_slot.product.product_name} <span>${response.product_slot.product_varient.value}</span><span>ml</span>`
                      :response.product_slot.product_varient.varient == 3
                      ?`${response.product_slot.product.product_name} <span>${response.product_slot.product_varient.value}</span><span>g</span>`
                      :response.product_slot.product_varient.varient == 4
                      ?`${response.product_slot.product.product_name} <span>${response.product_slot.product_varient.value}</span><span>kg</span>`
                      :`${response.product_slot.product.product_name} <span>${response.product_slot.product_varient.value}</span><span>No's</span>`
                    ),
                    response.product_slot.product_varient.offer_price,
                    response.product_slot.quantity,
                    response.product_slot.product_varient.offer_price *
                        response.product_slot.quantity,
                ],
            ],
        })
        .forceRender();
}
