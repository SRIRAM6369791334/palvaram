const gridNew = new gridjs.Grid({
    columns: [
        "S.No",
        "Date To Deliver",
        "Product",
        "Area",
        "Delivery Status",
        "AssignTo",
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
    data: milkSlots.map((milkSlot, index) => {
        return [
            index + 1,
            milkSlot.delivery_date,
            milkSlot.order.product.product_name,
            milkSlot.order.customer.area.area_name,
            gridjs.html(
                milkSlot.delivery_status == 0
                    ? `<div class="btn btn-sm btn-info">Pending</div>`
                    : milkSlot.delivery_status == 1
                    ? `<div class="btn btn-sm btn-primary">Not Assigned</div>`
                    : `<div class="btn btn-sm btn-danger">Cancelled</div>`
            ),
            gridjs.html(
                milkSlot.delivery_person_id
                    ? `<div> <button
                    data-milkSlotid ="${milkSlot.id}",
                    data-ogmilkSlotid = "${milkSlot.order_id}"
                    data-productname = "${milkSlot.order.product.product_name}"
                    data-customername= "${milkSlot.order.customer.name}"
                    data-areaname = "${milkSlot.order.customer.area.area_name}"
                    data-areaid = "${milkSlot.customer.area.id}"
                    data-deliverypersonid ="${milkSlot.delivery_person_id}"
                data-bs-target="#assignToModal"  class="btn btn-secondary btn-sm text-truncate ms-2 edit_btn ">Change</button>`
                    : `<button
                    data-milkSlotid ="${milkSlot.id}",
                    data-ogmilkSlotid = "${milkSlot.order_id}"
                    data-productname = "${milkSlot.order.product.product_name}"
                    data-customername= "${milkSlot.order.customer.name}"
                    data-areaname = "${milkSlot.order.customer.area.area_name}"
                    data-areaid = "${milkSlot.order.customer.area.id}"
                    data-deliverypersonid ="${milkSlot.delivery_person_id}"
                data-bs-target="#assignToModal"  class="btn btn-warning  btn-sm text-truncate ms-2 edit_btn ">
                Assign</button>`
            ),
            gridjs.html(
                milkSlot.is_cancelled
                    ? `
                    <div class="btn btn-sm btn-danger">Cancelled</div>
                `
                    : `<button
            data-milkSlotid ="${milkSlot.id}",
            data-ogmilkSlotid = "${milkSlot.order_id}"
            data-productname = "${milkSlot.order.product.product_name}"
            data-customername= "${milkSlot.order.customer.name}"
            data-areaname = "${milkSlot.order.customer.area.area_name}"
            data-areaid = "${milkSlot.order.customer.area.id}"
            data-deliverypersonid ="${milkSlot.delivery_person_id}"
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

function gridjsReRender(milkSlots) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: milkSlots.map((milkSlot, index) => {
                return [
                    index + 1,
                    milkSlot.delivery_date,
                    milkSlot.order.product.product_name,
                    milkSlot.order.customer.area.area_name,
                    gridjs.html(
                        milkSlot.delivery_status == 0
                            ? `<div class="btn btn-sm btn-info">Pending</div>`
                            : milkSlot.delivery_status == 1
                            ? `<div class="btn btn-sm btn-primary">Not Assigned</div>`
                            : `<div class="btn btn-sm btn-danger">Cancelled</div>`
                    ),
                    // gridjs.html(
                    //     milkSlot.delivery_person_id
                    //         ? `<div> <button
                    //         data-milkSlotid ="${milkSlot.id}",
                    //         data-ogmilkSlotid = "${milkSlot.order_id}"
                    //         data-productname = "${milkSlot.order.product.product_name}"
                    //         data-customername= "${milkSlot.order.customer.name}"
                    //         data-areaname = "${milkSlot.order.customer.area.area_name}"
                    //         data-areaid = "${milkSlot.customer.area.id}"
                    //         data-deliverypersonid ="${milkSlot.delivery_person_id}"
                    //     data-bs-target="#assignToModal"  class="btn btn-secondary btn-sm text-truncate ms-2 edit_btn ">Change</button>`
                    //         : `<button
                    //         data-milkSlotid ="${milkSlot.id}",
                    //         data-ogmilkSlotid = "${milkSlot.order_id}"
                    //         data-productname = "${milkSlot.order.product.product_name}"
                    //         data-customername= "${milkSlot.order.customer.name}"
                    //         data-areaname = "${milkSlot.order.customer.area.area_name}"
                    //         data-areaid = "${milkSlot.order.customer.area.id}"
                    //         data-deliverypersonid ="${milkSlot.delivery_person_id}"
                    //     data-bs-target="#assignToModal"  class="btn btn-warning  btn-sm text-truncate ms-2 edit_btn ">
                    //     Assign</button>`
                    // ),
                    gridjs.html(
                        milkSlot.is_cancelled
                            ? `
                            <div class="btn btn-sm btn-danger">Cancelled</div>

                        `
                            : `<button
                    data-milkSlotid ="${milkSlot.id}",
                    data-ogmilkSlotid = "${milkSlot.order_id}"
                    data-productname = "${milkSlot.order.product.product_name}"
                    data-customername= "${milkSlot.order.customer.name}"
                    data-areaname = "${milkSlot.order.customer.area.area_name}"
                    data-areaid = "${milkSlot.order.customer.area.id}"
                    data-deliverypersonid ="${milkSlot.delivery_person_id}"
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
        const id = $(this).attr("data-milkslotid");
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
                    url: location.origin + "/cancelMilkSlot",
                    method: "post",
                    data: {
                        milk_slot_id: id,
                    },
                    dataType: "json",
                    success: function (response) {
                        const updatedMilkSlots = response.milkSlots;
                        gridjsReRender(updatedMilkSlots);
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
