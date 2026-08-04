const gridNew = new gridjs.Grid({
    columns: [
        "S.No",
        "Order ID",
        "Ordered Date",
        "Product",
        "Customer",
        "Area",
        "Status",
        {
            name: "AssignTo",
            sort: false,
        },
    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: milkOrders.map((milkOrder, index) => {
        return [
            index + 1,
            gridjs.html(
                `<a href="milkOrders/${milkOrder.order_id}">${milkOrder.order_id} </a>`
            ),
            milkOrder.date_ordered_on,
            milkOrder.product.product_name,
            milkOrder.customer.name,
            milkOrder.order_address.area.area_name,
            gridjs.html(
                milkOrder.delivery_person_id
                    ? `<div class="is_delivery_assigned">Assigned</div>
            `
                    : `<div class="is_delivery_not_assigned">Pending</div>
            `
            ),

            gridjs.html(
                milkOrder.delivery_person_id
                    ? `<div> <button data-bs-toggle="modal"
                    data-milkOrderid ="${milkOrder.id}",
                    data-ogmilkOrderid = "${milkOrder.order_id}"
                    data-orderedDate="${milkOrder.date_ordered_on}"
                    data-productname = "${milkOrder.product.product_name}"
                    data-customername= "${milkOrder.customer.name}"
                    data-areaname = "${milkOrder.order_address.area.area_name}"
                    data-areaid = "${milkOrder.order_address.area_id}"
                    data-deliverypersonid ="${milkOrder.delivery_person_id}"
                data-bs-target="#assignToModal"  class="btn btn-secondary btn-sm text-truncate ms-2 edit_btn ">Change</button>`
                    : `<div> <button data-bs-toggle="modal"
                    data-milkOrderid ="${milkOrder.id}",
                    data-ogmilkOrderid = "${milkOrder.order_id}"
                    data-orderedDate="${milkOrder.date_ordered_on}"
                    data-productname = "${milkOrder.product.product_name}"
                    data-customername= "${milkOrder.customer.name}"
                    data-areaname = "${milkOrder.order_address.area.area_name}"
                    data-areaid = "${milkOrder.order_address.area.id}"
                    data-deliverypersonid ="${milkOrder.delivery_person_id}"
                data-bs-target="#assignToModal"  class="btn btn-warning  btn-sm text-truncate ms-2 edit_btn ">
                Assign</button>`
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

function gridjsReRender(milkOrders) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: milkOrders.map((milkOrder, index) => {
                return [
                    index + 1,
                    gridjs.html(
                        `<a href="milkOrders/${milkOrder.order_id}">${milkOrder.order_id} </a>`
                    ),
                    milkOrder.date_ordered_on,
                    milkOrder.product.product_name,
                    milkOrder.customer.name,
                    milkOrder.order_address.area.area_name,
                    gridjs.html(
                        milkOrder.delivery_person_id
                            ? `<div class="is_delivery_assigned">Assigned</div>
                    `
                            : `<div class="is_delivery_not_assigned">Pending</div>
                    `
                    ),

                    gridjs.html(
                        milkOrder.delivery_person_id
                            ? `<div> <button data-bs-toggle="modal"
                            data-milkOrderid ="${milkOrder.id}",
                            data-ogmilkOrderid = "${milkOrder.order_id}"
                            data-orderedDate="${milkOrder.date_ordered_on}"
                            data-productname = "${milkOrder.product.product_name}"
                            data-customername= "${milkOrder.customer.name}"
                            data-areaname = "${milkOrder.order_address.area.area_name}"
                            data-areaid = "${milkOrder.order_address.area_id}"
                            data-deliverypersonid ="${milkOrder.delivery_person_id}"
                        data-bs-target="#assignToModal"  class="btn btn-secondary btn-sm text-truncate ms-2 edit_btn ">Change</button>`
                            : `<div> <button data-bs-toggle="modal"
                            data-milkOrderid ="${milkOrder.id}",
                            data-ogmilkOrderid = "${milkOrder.order_id}"
                            data-orderedDate="${milkOrder.date_ordered_on}"
                            data-productname = "${milkOrder.product.product_name}"
                            data-customername= "${milkOrder.customer.name}"
                            data-areaname = "${milkOrder.order_address.area.area_name}"
                            data-areaid = "${milkOrder.order_address.area.id}"
                            data-deliverypersonid ="${milkOrder.delivery_person_id}"
                        data-bs-target="#assignToModal"  class="btn btn-warning  btn-sm text-truncate ms-2 edit_btn ">
                        Assign</button>`
                    ),
                ];
            }),
        })
        .forceRender();
}

$(function () {
    $(document).on("click", ".edit_btn", function () {
        $("#customer_name_input").val($(this).attr("data-customername"));
        $("#customer_area_input").val($(this).attr("data-areaname"));
        $("#orderid_input").val($(this).attr("data-ogmilkOrderid"));
        const areaId = $(this).attr("data-areaid");
        const currentDeliveryPersonId = $(this).attr("data-deliverypersonid");

        $.ajax({
            type: "POST",
            url: "getAreaAssignedDelvieryPerson/" + areaId,
            data: {},
            success: function (response) {
                $("#delivery_par").html(response);

                if (currentDeliveryPersonId) {
                    $(
                        '#delivery_par option[value="' +
                            currentDeliveryPersonId +
                            '"]'
                    ).prop("selected", true);
                }
            },
            error: function (response) {
                console.log(response);
            },
        });
    });
});

const assignDeliveryValidator = new JustValidate("#assignDeliveryPartner", {
    validateBeforeSubmitting: true,
});

assignDeliveryValidator
    .addField(".delivery_id_input", [
        {
            rule: "required",
            errorMessage: "*Deliver Person field is required",
        },
    ])
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", "true");
        assignDeliveryPerson(event);
    });
function assignDeliveryPerson(event) {
    const formData = new FormData(event.target);
    $.ajax({
        type: "post",
        url: "milkOrderDeliveryAssign",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedMilkOrders = response.milkOrders;
            $("#assignDeliveryPartner")[0].reset();
            $("#assignToModal").hide();
            $(".modal-backdrop").remove();
            $(".add_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedMilkOrders);
            Swal.fire("Success", "Delivery Assigned Successfully", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".add_submit_btn").removeAttr("disabled");
            $(".add_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}
