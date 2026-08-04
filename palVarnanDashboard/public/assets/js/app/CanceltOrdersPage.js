const gridNew = new gridjs.Grid({
    columns: [
        "S.No",
        "Order Id",
        "User Id",
        "Amount",
        // "cancel reason",
        "Status",
        // {
        //     name: "Cancel Product",
        //     sort: false,
        // },
    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: requests.map((CancelOrder, index) => {
        return [
            index + 1,

            CancelOrder.order_id,
            CancelOrder.user_id,
            CancelOrder.total_amount,

            // gridjs.html(
            //     CancelOrder.product_varient.varient == 1
            //         ? `<p>${CancelOrder.product.product_name} ${CancelOrder.product_varient.value}l</p>
            // `
            //         : CancelOrder.product_varient.varient == 2
            //         ? `<p>${CancelOrder.product.product_name} ${CancelOrder.product_varient.value}ml</p>`
            //         : CancelOrder.product_varient.varient == 3
            //         ? `<p>${CancelOrder.product.product_name} ${CancelOrder.product_varient.value}g</p>`
            //         : CancelOrder.product_varient.varient == 4
            //         ? `<p>${CancelOrder.product.product_name} ${CancelOrder.product_varient.value}kg</p>`
            //         : `<p>${CancelOrder.product.product_name} ${CancelOrder.product_varient.value}No's</p>`
            // ),

            // CancelOrder.product.product_name,

            // gridjs.html(
            //     `${
            //         CancelOrder.product_varient.offer_price *
            //         CancelOrder.quantity
            //     }`
            // ),

            // CancelOrder.cancel_reason,
            // gridjs.html(
            //     CancelOrder.delivery_status == 0
            //         ? `<div class="text-info" style="font-weight:bold">Billing</div>`
            //         : CancelOrder.delivery_status == 1
            //         ? `<div class="text-primary" style="font-weight:bold">Packing</div>`
            //         : CancelOrder.delivery_status == 2
            //         ? `<div class="text-warning" style="font-weight:bold">Dispatched</div>`
            //         : CancelOrder.delivery_status == 3
            //         ? `<div class="text-success" style="font-weight:bold">Out Of Delivery</div>`
            //         : CancelOrder.delivery_status == 4
            //         ? `<div class="text-success" style="font-weight:bold">Delivered</div>`
            //         : `<div class="text-success" style="font-weight:bold">Cancel</div>`
            // ),

            gridjs.html(
                CancelOrder.is_cancelled == 0
                    ? ` <div class="btn btn-sm btn-danger">Not Cancel</div>`
                    : CancelOrder.is_cancelled == 1
                    ? `
                    <div class="btn btn-sm btn-danger">Cancelled</div>`
                    : `<button
                    data-CancelOrderid ="${CancelOrder.id}",
                    data-ogCancelOrderid = "${CancelOrder.order_id}"
                    

                    
               class="btn btn-warning  btn-sm text-truncate ms-2 cancel_slot_btn">
                Approve</button>`
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

function gridjsReRender(pendindRequests) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: pendindRequests.map((CancelOrder, index) => {
                return [
                    index + 1,

                    CancelOrder.order_id,
                    CancelOrder.user_id,
                    CancelOrder.total_amount,
                    // gridjs.html(
                    //     CancelOrder.product_varient.varient == 1
                    //         ? `<p>${CancelOrder.product.product_name} ${CancelOrder.product_varient.value}l</p>
                    // `
                    //         : CancelOrder.product_varient.varient == 2
                    //         ? `<p>${CancelOrder.product.product_name} ${CancelOrder.product_varient.value}ml</p>`
                    //         : CancelOrder.product_varient.varient == 3
                    //         ? `<p>${CancelOrder.product.product_name} ${CancelOrder.product_varient.value}g</p>`
                    //         : CancelOrder.product_varient.varient == 4
                    //         ? `<p>${CancelOrder.product.product_name} ${CancelOrder.product_varient.value}kg</p>`
                    //         : `<p>${CancelOrder.product.product_name} ${CancelOrder.product_varient.value}No's</p>`
                    // ),

                    // CancelOrder.product.product_name,

                    // gridjs.html(
                    //     `${
                    //         CancelOrder.product_varient.offer_price *
                    //         CancelOrder.quantity
                    //     }`
                    // ),

                    // CancelOrder.cancel_reason,
                    // gridjs.html(
                    //     CancelOrder.delivery_status == 0
                    //         ? `<div class="text-info" style="font-weight:bold">Billing</div>`
                    //         : CancelOrder.delivery_status == 1
                    //         ? `<div class="text-primary" style="font-weight:bold">Packing</div>`
                    //         : CancelOrder.delivery_status == 2
                    //         ? `<div class="text-warning" style="font-weight:bold">Dispatched</div>`
                    //         : CancelOrder.delivery_status == 3
                    //         ? `<div class="text-success" style="font-weight:bold">Out Of Delivery</div>`
                    //         : CancelOrder.delivery_status == 4
                    //         ? `<div class="text-success" style="font-weight:bold">Delivered</div>`
                    //         : `<div class="text-success" style="font-weight:bold">Cancel</div>`
                    // ),

                    gridjs.html(
                        CancelOrder.is_cancelled == 0
                            ? ` <div class="btn btn-sm btn-danger">Not Cancel</div>`
                            : CancelOrder.is_cancelled == 1
                            ? `
                            <div class="btn btn-sm btn-danger">Cancelled</div>`
                            : `<button
                            data-CancelOrderid ="${CancelOrder.id}",
                            data-ogCancelOrderid = "${CancelOrder.order_id}"
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
        const id = $(this).attr("data-ogCancelOrderid");
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
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });
                $.ajax({
                    url: "/approverequest",
                    method: "post",
                    data: {
                        order_id: id,
                    },
                    dataType: "json",
                    success: function (response) {
                        const updatedCancelOrders = response.pendingRequests;
                        gridjsReRender(updatedCancelOrders);
                        Swal.fire(
                            "Cancelled!",
                            "Order Cancelled Successfully",
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
// data-productname = "${CancelOrder.product.product_name}"
// data-customername= "${CancelOrder.order.customer.name}"
// data-deliverypersonid ="${CancelOrder.delivery_person_id}"

// data-productname = "${CancelOrder.product.product_name}"
// data-customername= "${CancelOrder.order.customer.name}"

// data-deliverypersonid ="${CancelOrder.delivery_person_id}"
