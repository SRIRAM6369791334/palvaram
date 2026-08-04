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
        "Delivery Agent",
        {
            name: "Status",
            sort: false,
        },
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
    data: productDeliverys.map((productDelivery, index) => {
        return [
            index + 1,
            productDelivery.country?.name ?? "-",
            // gridjs.html(
            //     `<a href="productOrders/${productDelivery.order_id}">${productDelivery.order_id} </a>`
            // ),
            productDelivery.order_id,
            productDelivery.date_ordered_on,
            productDelivery.order_address ? productDelivery.order_address.firstname : (productDelivery.billing_name || 'N/A'),
            // productDelivery.order_address.address_line_one,
            // gridjs.html(
            //     productDelivery.payment_status == 1
            //     ?`<div class="text-success" style="font-weight:bold">PAID</div>`
            //     :`<div class="text-warning" style="font-weight:bold">COD</div>`
            // ),
            productDelivery.payment_status,
            gridjs.html(
                productDelivery.delivery_status == 0
                    ? `<div class="text-info" style="font-weight:bold">Pending</div>
            `
                    : productDelivery.delivery_status == 1
                        ? `<div class="text-primary" style="font-weight:bold">Packing</div>`
                        : productDelivery.delivery_status == 2
                            ? `<div class="text-warning" style="font-weight:bold">Dispatched</div>`
                            : productDelivery.delivery_status == 3

                                ? `<div class="text-success" style="font-weight:bold">Out for Delivery</div>`
                                : `<div class="text-success" style="font-weight:bold">Delivered</div>`

            ),
            productDelivery.delivery_person_name ? `${productDelivery.delivery_person_name} (${productDelivery.delivery_person_phone})` : 'N/A',
            gridjs.html(
                productDelivery.payment_status == 1
                    ? `<div> <button data-bs-toggle="modal"
                    data-milkOrderid1 ="${productDelivery.id}",
                    data-ogmilkOrderid1 = "${productDelivery.order_id}"
                    data-orderedDate1="${productDelivery.date_ordered_on}"
                    data-custnum= "${productDelivery.customer ? productDelivery.customer.phone_number : (productDelivery.billing_phone || 'N/A')}"


                    data-customername1= "${productDelivery.order_address ? productDelivery.order_address.firstname : (productDelivery.billing_name || 'N/A')}"
                    data-customerid1= "${productDelivery.customer ? productDelivery.customer.user_id : 'N/A'}"


                    data-deliverypersonid1 ="${productDelivery.delivery_person_id}"
                data-bs-target="#DeliveryModal"  class="btn btn-secondary edit_btns2 ">Status</button>`
                    : `<div> <button data-bs-toggle="modal"
                    data-milkOrderid1 ="${productDelivery.id}",
                    data-ogmilkOrderid1 = "${productDelivery.order_id}"
                    data-orderedDate1="${productDelivery.date_ordered_on}"
                    data-custnum= "${productDelivery.customer ? productDelivery.customer.phone_number : (productDelivery.billing_phone || 'N/A')}"


                    data-customername1= "${productDelivery.order_address ? productDelivery.order_address.firstname : (productDelivery.billing_name || 'N/A')}"
                    data-customerid1= "${productDelivery.customer ? productDelivery.customer.user_id : 'N/A'}"
                    data-codamt ="${productDelivery.total_amount}"


                    data-deliverypersonid1 ="${productDelivery.delivery_person_id}"
                data-bs-target="#CollectModal"  class="btn btn-secondary edit_btns3 ">Collect</button>`
            ),
            gridjs.html(`
            <a href="viewProductdetail/${productDelivery.order_id}" target="_blank">
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

function gridjsReRender(productDeliverys) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: productDeliverys.map((productDelivery, index) => {
                return [
                    index + 1,
                    // gridjs.html(
                    //     `<a href="productOrders/${productDelivery.order_id}">${productDelivery.order_id} </a>`
                    // ),
                    productDelivery.order_id,
                    productDelivery.date_ordered_on,
                    productDelivery.order_address ? productDelivery.order_address.firstname : (productDelivery.billing_name || 'N/A'),
                    // productDelivery.order_address.address_line_one,
                    // gridjs.html(
                    //     productDelivery.payment_status == 1
                    //     ?`<div class="text-success" style="font-weight:bold">PAID</div>`
                    //     :`<div class="text-warning" style="font-weight:bold">COD</div>`
                    // ),
                    productDelivery.payment_status,
                    gridjs.html(
                        productDelivery.delivery_status == 0
                            ? `<div class="text-info" style="font-weight:bold">Pending</div>
                    `
                            : productDelivery.delivery_status == 1
                                ? `<div class="text-primary" style="font-weight:bold">Packing</div>`
                                : productDelivery.delivery_status == 2
                                    ? `<div class="text-warning" style="font-weight:bold">Dispatched</div>`
                                    : productDelivery.delivery_status == 3

                                        ? `<div class="text-success" style="font-weight:bold">Out for Delivery</div>`
                                        : `<div class="text-success" style="font-weight:bold">Delivered</div>`
                    ),
                    productDelivery.delivery_person_name ? `${productDelivery.delivery_person_name} (${productDelivery.delivery_person_phone})` : 'N/A',

                    gridjs.html(
                        productDelivery.payment_status == 1
                            ? `<div> <button data-bs-toggle="modal"
                            data-milkOrderid1 ="${productDelivery.id}",
                            data-ogmilkOrderid1 = "${productDelivery.order_id}"
                            data-orderedDate1="${productDelivery.date_ordered_on}"
                            data-custnum= "${productDelivery.customer ? productDelivery.customer.phone_number : (productDelivery.billing_phone || 'N/A')}"


                            data-customername1= "${productDelivery.order_address ? productDelivery.order_address.firstname : (productDelivery.billing_name || 'N/A')}"
                            data-customerid1= "${productDelivery.customer ? productDelivery.customer.user_id : 'N/A'}"



                            data-deliverypersonid1 ="${productDelivery.delivery_person_id}"
                        data-bs-target="#DeliveryModal"  class="btn btn-secondary edit_btns2 ">Status</button>`
                            : `<div> <button data-bs-toggle="modal"
                            data-milkOrderid1 ="${productDelivery.id}",
                            data-ogmilkOrderid1 = "${productDelivery.order_id}"
                            data-orderedDate1="${productDelivery.date_ordered_on}"
                            data-custnum= "${productDelivery.customer ? productDelivery.customer.phone_number : (productDelivery.billing_phone || 'N/A')}"


                            data-customername1= "${productDelivery.order_address ? productDelivery.order_address.firstname : (productDelivery.billing_name || 'N/A')}"
                            data-customerid1= "${productDelivery.customer ? productDelivery.customer.user_id : 'N/A'}"
                            data-codamt ="${productDelivery.total_amount}"


                            data-deliverypersonid1 ="${productDelivery.delivery_person_id}"
                        data-bs-target="#CollectModal"  class="btn btn-secondary edit_btns3 ">Collect</button>`
                    ),
                    gridjs.html(`
                    <a href="viewProductdetail/${productDelivery.order_id}" target="_blank">
                    <button type="button" class="btn btn-primary btn-sm text-truncate ms-2"  data-bs-target="#viewOrdersModal"><i class="bx bx-message-square-dots me-1"></i>

                    View</button>
                    </a>
                    `),
                ];
            }),
        })
        .forceRender();
}

$(function () {
    $(document).on("click", ".edit_btns2", function () {

        $("#customer_name_input2").val($(this).attr("data-customername1"));
        $("#order_id_input2").val($(this).attr("data-ogmilkOrderid1"));
        $("#cusiddelive").val($(this).attr("data-customerid1"));
        $('#cusnumer').val($(this).attr("data-custnum"));
    });
});

$(function () {
    $(document).on("click", ".edit_btns3", function () {
        console.log("lkajsdkl");

        $("#customer_name_input21").val($(this).attr("data-customername1"));
        $("#order_id_input21").val($(this).attr("data-ogmilkOrderid1"));
        $("#cusiddelive1").val($(this).attr("data-customerid1"));
        $("#cod_input21").val($(this).attr("data-codamt"));
        $('#cusnumer1').val($(this).attr("data-custnum"));
    });
});

$(function () {
    $(document).ready(function () {
        $("#codamt").hide();

        $("#add_status_select21").change(function () {
            var selectedStatus = $(this).val();

            if (selectedStatus == '4') {
                $('#codamt').show();
            } else {
                $('#codamt').hide();
            }
        });
    });
});

const assignDeliveryValidator12 = new JustValidate("#collectstatus3", {
    validateBeforeSubmitting: true,
});

assignDeliveryValidator12
    .addField("#add_status_select21", [
        {
            rule: "required",
            errorMessage: "*Status field is required",
        },
    ])
    .onSuccess((event) => {
        $(".collectdelivery_submit_btn").attr("disabled", "true");
        collectstatus3submit(event);
    });

const assignDeliveryValidator = new JustValidate("#changestatus3", {
    validateBeforeSubmitting: true,
});

assignDeliveryValidator
    .addField("#add_status_select2", [
        {
            rule: "required",
            errorMessage: "*Status field is required",
        },
    ])
    .onSuccess((event) => {
        $(".adddelivery_submit_btn").attr("disabled", "true");
        changestatus3submit(event);
    });
function changestatus3submit(event) {
    const formData = new FormData(event.target);
    $.ajax({
        type: "post",
        url: "updatestatusdelivery",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedproductDeliverys = response.productDeliverys;
            $("#changestatus3")[0].reset();
            $("#DeliveryModal").modal('hide');
            $(".modal-backdrop").remove();
            $(".adddelivery_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedproductDeliverys);
            Swal.fire("Success", "Status Change Successfully", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".adddelivery_submit_btn").removeAttr("disabled");
            $(".adddelivery_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

function collectstatus3submit(event) {
    const formData = new FormData(event.target);
    $.ajax({
        type: "post",
        url: "collectstatusdelivery",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedproductDeliverys = response.productDeliverys;
            $("#collectstatus3")[0].reset();
            $("#CollectModal").modal('hide');
            $(".modal-backdrop").remove();
            $(".collectdelivery_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedproductDeliverys);
            Swal.fire("Success", "Status Change Successfully", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".collectdelivery_submit_btn").removeAttr("disabled");
            $(".collectdelivery_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}


