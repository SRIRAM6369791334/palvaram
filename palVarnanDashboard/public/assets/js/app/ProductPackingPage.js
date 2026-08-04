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
        {
            name: "Status",
            sort: false,
        },
        {
            name: "Action",
            sort: false,
        },
        // {
        //     name: "Refund",
        //     sort: false,
        // }
    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: productPackings.map((productPacking, index) => {
        return [
            index + 1,
            productPacking.country?.name ?? "-",
            // gridjs.html(
            //     `<a href="productOrders/${productPacking.order_id}">${productPacking.order_id} </a>`
            // ),
            productPacking.order_id,
            productPacking.date_ordered_on,
            productPacking.order_address ? productPacking.order_address.firstname : (productPacking.billing_name || 'N/A'),
            // productPacking.order_address.address_line_one,
            productPacking.payment_status,
            // gridjs.html(
            //     productPacking.payment_status == 1
            //     ?`<div class="text-success" style="font-weight:bold">PAID</div>`
            //     :`<div class="text-warning" style="font-weight:bold">COD</div>`
            // ),
            gridjs.html(
                productPacking.delivery_status == 0
                    ? `<div class="text-info" style="font-weight:bold">Pending</div>
            `
                    : productPacking.delivery_status == 1
                        ? `<div class="text-primary" style="font-weight:bold">Packing</div>`
                        : productPacking.delivery_status == 2
                            ? `<div class="text-warning" style="font-weight:bold">Dispatched</div>`
                            : `<div class="text-success" style="font-weight:bold">Delivery</div>`
            ),

            gridjs.html(
                productPacking.delivery_status == 2
                    ? `<div> <button data-bs-toggle="modal"
                    data-milkOrderid1 ="${productPacking.id}",
                    data-ogmilkOrderid1 = "${productPacking.order_id}"
                    data-orderedDate1="${productPacking.date_ordered_on}"

                    data-customername1= "${productPacking.order_address ? productPacking.order_address.firstname : (productPacking.billing_name || 'N/A')}"
                    data-customerid1= "${productPacking.customer ? productPacking.customer.user_id : 'N/A'}"


                data-bs-target="#PackingModal"  class="btn btn-secondary edit_btns ">Changed</button>`
                    : `<div> <button data-bs-toggle="modal"
                    data-milkOrderid1 ="${productPacking.id}",
                    data-ogmilkOrderid1 = "${productPacking.order_id}"
                    data-orderedDate1="${productPacking.date_ordered_on}"

                    data-customername1= "${productPacking.order_address ? productPacking.order_address.firstname : (productPacking.billing_name || 'N/A')}"
                    data-customerid1= "${productPacking.customer ? productPacking.customer.user_id : 'N/A'}"
                    data-cusnum= "${productPacking.customer ? productPacking.customer.phone_number : (productPacking.billing_phone || 'N/A')}"

                    data-deliverypersonid1 ="${productPacking.delivery_person_id}"
                data-bs-target="#PackingModal"  class="btn btn-warning edit_btns ">Status</button>`
            ),
            gridjs.html(`
            <a href="viewProductdetail/${productPacking.order_id}" target="_blank">
            <button type="button" class="btn btn-primary btn-sm text-truncate ms-2"  data-bs-target="#viewOrdersModal"><i class="bx bx-message-square-dots me-1"></i>

            View</button>
            </a>
            `),

            //     gridjs.html(
            //         productPacking.is_cancelled == 0

            //        ?`<p class="text-success">Not Cancelled</p>`
            //        : productPacking.is_cancelled == 2
            //        ?`<div> <button data-bs-toggle="modal"
            //        data-milkOrderid ="${productPacking.id}",
            //        data-ogmilkOrderid = "${productPacking.order_id}"
            //        data-orderedDate="${productPacking.date_ordered_on}"

            //        data-customername= "${productPacking.customer.name}"
            //        data-customerid= "${productPacking.customer.user_id}"
            //        data-refund = "${productPacking.cancel_reason}"


            //    data-bs-target="#Refund1Modal"  class="btn btn-secondary edit_btn2 ">Request</button>`
            //    :`<p class="text-danger">Product Cancel</p>`
            //     )
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

function gridjsReRender(productPackings) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: productPackings.map((productPacking, index) => {
                return [
                    index + 1,
                    productPacking.country?.name ?? "-",
                    // gridjs.html(
                    //     `<a href="productOrders/${productPacking.order_id}">${productPacking.order_id} </a>`
                    // ),
                    // productPacking.order_id,
                    productPacking.order_id,
                    productPacking.date_ordered_on,
                    productPacking.order_address ? productPacking.order_address.firstname : (productPacking.billing_name || 'N/A'),
                    // productPacking.order_address.address_line_one,
                    // gridjs.html(
                    //     productPacking.payment_status == 1
                    //     ?`<div class="text-success" style="font-weight:bold">PAID</div>`
                    //     :`<div class="text-warning" style="font-weight:bold">COD</div>`
                    // ),
                    productPacking.payment_status,
                    gridjs.html(
                        productPacking.delivery_status == 0
                            ? `<div class="text-info" style="font-weight:bold">Pending</div>
                    `
                            : productPacking.delivery_status == 1
                                ? `<div class="text-primary" style="font-weight:bold">Packing</div>`
                                : productPacking.delivery_status == 2
                                    ? `<div class="text-warning" style="font-weight:bold">Dispatched</div>`
                                    : `<div class="text-success" style="font-weight:bold">Delivery</div>`
                    ),

                    gridjs.html(
                        productPacking.delivery_status == 2
                            ? `<div> <button data-bs-toggle="modal"
                            data-milkOrderid1 ="${productPacking.id}",
                            data-ogmilkOrderid1 = "${productPacking.order_id}"
                            data-orderedDate1="${productPacking.date_ordered_on}"


                            data-customername1= "${productPacking.order_address ? productPacking.order_address.firstname : (productPacking.billing_name || 'N/A')}"
                            data-customerid1= "${productPacking.customer ? productPacking.customer.user_id : 'N/A'}"

                            data-deliverypersonid1 ="${productPacking.delivery_person_id}"
                        data-bs-target="#PackingModal"  class="btn btn-secondary edit_btns ">Change</button>`
                            : `<div> <button data-bs-toggle="modal"
                            data-milkOrderid1 ="${productPacking.id}",
                            data-ogmilkOrderid1 = "${productPacking.order_id}"
                            data-orderedDate1="${productPacking.date_ordered_on}"

                            data-customername1= "${productPacking.order_address ? productPacking.order_address.firstname : (productPacking.billing_name || 'N/A')}"
                            data-customerid1= "${productPacking.customer ? productPacking.customer.user_id : 'N/A'}"
                            data-cusnum= "${productPacking.customer ? productPacking.customer.phone_number : (productPacking.billing_phone || 'N/A')}"

                            data-deliverypersonid1 ="${productPacking.delivery_person_id}"
                        data-bs-target="#PackingModal"  class="btn btn-warning edit_btns ">Assign</button>`
                    ),
                    gridjs.html(`
                    <a href="viewProductdetail/${productPacking.order_id}" target="_blank">
                    <button type="button" class="btn btn-primary btn-sm text-truncate ms-2"  data-bs-target="#viewOrdersModal"><i class="bx bx-message-square-dots me-1"></i>

                    View</button>
                    </a>
                    `),
                    //     gridjs.html(
                    //         productPacking.is_cancelled == 0

                    //        ?`<p class="text-success">Not Cancelled</p>`
                    //        : productPacking.is_cancelled == 2
                    //        ?`<div> <button data-bs-toggle="modal"
                    //        data-milkOrderid ="${productPacking.id}",
                    //        data-ogmilkOrderid = "${productPacking.order_id}"
                    //        data-orderedDate="${productPacking.date_ordered_on}"

                    //        data-customername= "${productPacking.customer.name}"
                    //        data-customerid= "${productPacking.customer.user_id}"
                    //        data-refund = "${productPacking.cancel_reason}"


                    //    data-bs-target="#Refund1Modal"  class="btn btn-secondary edit_btn2 ">Request</button>`
                    //    :`<p class="text-danger">Product Cancel</p>`
                    //     )
                ];
            }),
        })
        .forceRender();
}

$(function () {
    $(document).on("click", ".edit_btns", function () {
        console.log("hai");
        $("#customer_name_input1").val($(this).attr("data-customername1"));
        $("#order_id_input1").val($(this).attr("data-ogmilkOrderid1"));
        $("#cusid").val($(this).attr("data-customerid1"));
        $('#cusnum').val($(this).attr("data-cusnum"));
    });
});

$(function () {
    $(document).on("click", ".edit_btn2", function () {
        $("#customer_name_input2").val($(this).attr("data-customername"));
        $("#order_id_input2").val($(this).attr("data-ogmilkOrderid"));
        $("#cusid2").val($(this).attr("data-customerid"));
        $('#cusnum').val($(this).attr("data-cusnum"));
        $("#customer_resons_input2").val($(this).attr("data-refund"));
    });
});

const assignDeliveryValidator = new JustValidate("#changestatus1", {
    validateBeforeSubmitting: true,
});

const assignDeliveryValidator1 = new JustValidate("#changestatus2", {
    validateBeforeSubmitting: true,
});

assignDeliveryValidator
    .addField("#add_status_select1", [
        {
            rule: "required",
            errorMessage: "*Status field is required",
        },
    ])
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", "true");
        changestatus1submit(event);
    });

assignDeliveryValidator1
    .onSuccess((event) => {
        $(".reson2_submit_btn").attr("disabled", "true");
        changestatus2submit(event);
    });
function changestatus1submit(event) {
    const formData = new FormData(event.target);
    $.ajax({
        type: "post",
        url: "updatestatupacking",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedproductPackings = response.productPackings;
            $("#changestatus1")[0].reset();
            $("#PackingModal").hide();
            $(".modal-backdrop").remove();
            $(".add_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedproductPackings);
            Swal.fire("Success", "Status Change Successfully", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".add_submit_btn").removeAttr("disabled");
            $(".add_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}


function changestatus2submit(event) {
    const formData = new FormData(event.target);
    $.ajax({
        type: "post",
        url: "updaterefund1",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedProductOrders = response.productPackings;
            $("#changestatus2")[0].reset();
            $("#Refund1ModalLabel").hide();
            $(".modal-backdrop").remove();
            $(".reson1_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedProductOrders);
            Swal.fire("Success", "Status Change Successfully", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".reson1_submit_btn").removeAttr("disabled");
            $(".reson1_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}
