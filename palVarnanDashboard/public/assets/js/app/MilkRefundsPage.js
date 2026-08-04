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
    data: milkRefunds.map((milkRefund, index) => {
        return [
            index + 1,
            gridjs.html(
                `<a href="milkRefunds/${milkRefund.order_id}" target="_blank">${milkRefund.order_id} </a>`
            ),
            milkRefund.created_at,
            gridjs.html(
                milkRefund.refund_status
                    ? `<div class="is_delivery_assigned">Refunded</div>
            `
                    : `<div class="is_delivery_not_assigned">Pending</div>
            `
            ),
            gridjs.html(
                milkRefund.refund_status
                    ? `<div class="is_delivery_assigned">Success</div>`
                    : `<div> <button data-bs-toggle="modal"
                    data-milkRefundid ="${milkRefund.id}"
                data-bs-target="#milkRefundModal"  class="btn btn-warning  btn-sm text-truncate ms-2 milk_refund_btn">
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

function gridjsReRender(milkRefunds) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: milkRefunds.map((milkRefund, index) => {
                return [
                    index + 1,
                    gridjs.html(
                        `<a href="milkRefunds/${milkRefund.order_id}" target="_blank">${milkRefund.order_id} </a>`
                    ),
                    milkRefund.created_at,
                    gridjs.html(
                        milkRefund.refund_status
                            ? `<div class="is_delivery_assigned">Refunded</div>
                    `
                            : `<div class="is_delivery_not_assigned">Pending</div>
                    `
                    ),
                    gridjs.html(
                        milkRefund.refund_status
                            ? `<div class="is_delivery_assigned">Success</div>`
                            : `<div> <button data-bs-toggle="modal"
                            data-milkRefundid ="${milkRefund.id}"
                        data-bs-target="#milkRefundModal"  class="btn btn-warning  btn-sm text-truncate ms-2 milk_refund_btn">
                        Refund</button>`
                    ),
                ];
            }),
        })
        .forceRender();
}

$(function () {
    $(document).on("click", ".milk_refund_btn", function () {
        const refundId = $(this).attr("data-milkRefundid");
        window.refundId = refundId;

        $.ajax({
            type: "POST",
            url: "getMilkRefundDatas",
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
            url: "refundMilkSlot",
            data: {
                refund_id: refundId,
            },
            success: function (response) {
                const updatedMilkRefunds = response.milkRefunds.map((item) => {
                    item.created_at = new Date(item.created_at)
                        .toLocaleDateString("en-IN", {
                            day: "numeric",
                            month: "short",
                            year: "numeric",
                        })
                        .split(" ")
                        .join("-");
                    return item;
                });
                $("#milkRefundModal").hide();
                $(".modal-backdrop").remove();
                document.body.style.overflowY = "scroll";
                gridjsReRender(updatedMilkRefunds);
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
                response.milk_order.customer.name,
                response.milk_order.customer.phone_number,
                response.milk_order.product.product_name,
                response.milk_order.product.product_mrp_price,
                response.milk_order.quantity,
                response.milk_order.product.product_mrp_price *
                    response.milk_order.quantity,
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
                    response.milk_order.customer.name,
                    response.milk_order.customer.phone_number,
                    response.milk_order.product.product_name,
                    response.milk_order.product.product_mrp_price,
                    response.milk_order.quantity,
                    response.milk_order.product.product_mrp_price *
                        response.milk_order.quantity,
                ],
            ],
        })
        .forceRender();
}
