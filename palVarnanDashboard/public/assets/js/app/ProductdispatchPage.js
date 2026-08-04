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
        // ,{
        //     name: "Refund",
        //     sort: false,
        // }
    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: productDispaths.map((productDispath, index) => {
        return [
            index + 1,
            productDispath.country?.name ?? "-",
            // gridjs.html(
            //     `<a href="productOrders/${productDispath.order_id}">${productDispath.order_id} </a>`
            // ),
            productDispath.order_id,
            productDispath.date_ordered_on,
            productDispath.order_address ? productDispath.order_address.firstname : (productDispath.billing_name || 'N/A'),
            // productDispath.order_address.address_line_one,
            // gridjs.html(
            //     productDispath.payment_status == 1
            //     ?`<div class="text-success" style="font-weight:bold">PAID</div>`
            //     :`<div class="text-warning" style="font-weight:bold">COD</div>`
            // ),
            productDispath.payment_status,
            gridjs.html(
                productDispath.delivery_status == 0
                    ? `<div class="text-info" style="font-weight:bold">Pending</div>
            `
                    : productDispath.delivery_status == 1
                        ? `<div class="text-primary" style="font-weight:bold">Packing</div>`
                        : productDispath.delivery_status == 2
                            ? `<div class="text-success" style="font-weight:bold">Dispatched</div>`
                            : `<div class="text-success" style="font-weight:bold">Out Of Delivery Dispatched</div>`
            ),

            gridjs.html(
                `<div> <button data-bs-toggle="modal"
                    data-milkOrderid1 ="${productDispath.id}",
                    data-ogmilkOrderid1 = "${productDispath.order_id}"
                    data-orderedDate1="${productDispath.date_ordered_on}"

                    data-customername1= "${productDispath.order_address ? productDispath.order_address.firstname : (productDispath.billing_name || 'N/A')}"
                    data-customerid1= "${productDispath.customer ? productDispath.customer.user_id : 'N/A'}"
                    data-cusnum= "${productDispath.customer ? productDispath.customer.phone_number : (productDispath.billing_phone || 'N/A')}"

                    data-deliverypersonid1 ="${productDispath.delivery_person_id}"
                data-bs-target="#DispatchModal"  class="btn btn-warning edit_btns1 ">Status</button>`
            ),
            gridjs.html(`
            <a href="viewProductdetail/${productDispath.order_id}" target="_blank">
            <button type="button" class="btn btn-primary btn-sm text-truncate ms-2"  data-bs-target="#viewOrdersModal"><i class="bx bx-message-square-dots me-1"></i>

            View</button>
            </a>
            `),
            //     gridjs.html(
            //         productDispath.is_cancelled == 0

            //        ?`<p class="text-success">Not Cancelled</p>`
            //        : productDispath.is_cancelled == 2
            //        ?`<div> <button data-bs-toggle="modal"
            //        data-milkOrderid ="${productDispath.id}",
            //        data-ogmilkOrderid = "${productDispath.order_id}"
            //        data-orderedDate="${productDispath.date_ordered_on}"

            //        data-customername= "${productDispath.customer.name}"
            //        data-customerid= "${productDispath.customer.user_id}"
            //        data-refund = "${productDispath.cancel_reason}"


            //    data-bs-target="#Refund3Modal"  class="btn btn-info edit_btn3 ">Request</button>`
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

function gridjsReRender(productDispaths) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: productDispaths.map((productDispath, index) => {
                return [
                    index + 1,
                    productDispath.country?.name ?? "-",
                    // gridjs.html(
                    //     `<a href="productOrders/${productDispath.order_id}">${productDispath.order_id} </a>`
                    // ),
                    productDispath.order_id,
                    productDispath.date_ordered_on,
                    productDispath.order_address ? productDispath.order_address.firstname : (productDispath.billing_name || 'N/A'),
                    // productDispath.order_address.address_line_one,
                    // gridjs.html(
                    //     productDispath.payment_status == 1
                    //     ?`<div class="text-success" style="font-weight:bold">PAID</div>`
                    //     :`<div class="text-warning" style="font-weight:bold">COD</div>`
                    // ),
                    productDispath.payment_status,
                    gridjs.html(
                        productDispath.delivery_status == 0
                            ? `<div class="text-info" style="font-weight:bold">Pending</div>
                    `
                            : productDispath.delivery_status == 1
                                ? `<div class="text-primary" style="font-weight:bold">Packing</div>`
                                : productDispath.delivery_status == 2
                                    ? `<div class="text-success" style="font-weight:bold">Dispatched</div>`
                                    : `<div class="text-success" style="font-weight:bold">Out Of Delivery Dispatched</div>`
                    ),

                    gridjs.html(
                        productDispath.delivery_status == 2
                            ? `<div> <button data-bs-toggle="modal"
                            data-milkOrderid1 ="${productDispath.id}",
                            data-ogmilkOrderid1 = "${productDispath.order_id}"
                            data-orderedDate1="${productDispath.date_ordered_on}"


                            data-customername1= "${productDispath.order_address.firstname}"
                            data-customerid1= "${productDispath.customer.user_id}"

                            data-deliverypersonid1 ="${productDispath.delivery_person_id}"
                        data-bs-target="#DispatchModal"  class="btn btn-secondary edit_btns1 ">Change</button>`
                            : `<div> <button data-bs-toggle="modal"
                            data-milkOrderid1 ="${productDispath.id}",
                            data-ogmilkOrderid1 = "${productDispath.order_id}"
                            data-orderedDate1="${productDispath.date_ordered_on}"

                            data-customername1= "${productDispath.order_address ? productDispath.order_address.firstname : (productDispath.billing_name || 'N/A')}"
                            data-customerid1= "${productDispath.customer ? productDispath.customer.user_id : 'N/A'}"
                            data-cusnum= "${productDispath.customer ? productDispath.customer.phone_number : (productDispath.billing_phone || 'N/A')}"

                            data-deliverypersonid1 ="${productDispath.delivery_person_id}"
                        data-bs-target="#DispatchModal"  class="btn btn-warning edit_btns1 ">Assign</button>`
                    ),
                    gridjs.html(`
                    <a href="viewProductdetail/${productDispath.order_id}" target="_blank">
                    <button type="button" class="btn btn-primary btn-sm text-truncate ms-2"  data-bs-target="#viewOrdersModal"><i class="bx bx-message-square-dots me-1"></i>

                    View</button>
                    </a>
                    `),
                    //     gridjs.html(
                    //         productDispath.is_cancelled == 0

                    //        ?`<p class="text-success">Not Cancelled</p>`
                    //        : productDispath.is_cancelled == 2
                    //        ?`<div> <button data-bs-toggle="modal"
                    //        data-milkOrderid ="${productDispath.id}",
                    //        data-ogmilkOrderid = "${productDispath.order_id}"
                    //        data-orderedDate="${productDispath.date_ordered_on}"

                    //        data-customername= "${productDispath.customer.name}"
                    //        data-customerid= "${productDispath.customer.user_id}"
                    //        data-refund = "${productDispath.cancel_reason}"


                    //    data-bs-target="#Refund3Modal"  class="btn btn-info edit_btn3 ">Request</button>`
                    //    :`<p class="text-danger">Product Cancel</p>`
                    //     )
                ];
            }),
        })
        .forceRender();
}

$(function () {
    $(document).on("click", ".edit_btns1", function () {

        $("#customer_name_input2").val($(this).attr("data-customername1"));
        $("#order_id_input2").val($(this).attr("data-ogmilkOrderid1"));
        $("#cusiddiapac").val($(this).attr("data-customerid1"));
        $('#cusnum').val($(this).attr("data-cusnum"));
    });
});

$(function () {
    $(document).on("click", ".edit_btn3", function () {
        $("#customer_name_input3").val($(this).attr("data-customername"));
        $("#order_id_input3").val($(this).attr("data-ogmilkOrderid"));
        $("#cusid3").val($(this).attr("data-customerid"));
        $("#customer_resons_input3").val($(this).attr("data-refund"));
    });
});

const assignDeliveryValidator = new JustValidate("#changestatus2", {
    validateBeforeSubmitting: true,
});
const assignDeliveryValidator1 = new JustValidate("#changestatus3", {
    validateBeforeSubmitting: true,
});

assignDeliveryValidator
    .addField("#add_status_select2", [
        {
            rule: "required",
            errorMessage: "*Status field is required",
        },
    ])
    .addField("#delivery_person_name", [
        {
            rule: "required",
            errorMessage: "*Delivery Person Name is required",
        },
    ])
    .addField("#delivery_person_phone", [
        {
            rule: "required",
            errorMessage: "*Delivery Person Phone is required",
        },
        {
            rule: "number",
            errorMessage: "*Phone must be a number",
        },
        {
            rule: "minLength",
            value: 10,
            errorMessage: "*Phone must be at least 10 digits",
        }
    ])
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", "true");
        changestatus1submit(event);
    });

assignDeliveryValidator1
    .onSuccess((event) => {
        $(".reson3_submit_btn").attr("disabled", "true");
        changestatus3submit(event);
    });
function changestatus1submit(event) {
    const formData = new FormData(event.target);
    $.ajax({
        type: "post",
        url: "updatestatusdispatch",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedproductDispaths = response.productDispaths;
            $("#changestatus2")[0].reset();
            $("#DispatchModal").modal('hide');
            $(".modal-backdrop").remove();
            $(".adddispatch_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedproductDispaths);
            Swal.fire("Success", "Status Change Successfully", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".adddispatch_submit_btn").removeAttr("disabled");
            $(".adddispatch_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

function changestatus3submit(event) {
    const formData = new FormData(event.target);
    $.ajax({
        type: "post",
        url: "updaterefund2",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedProductOrders = response.productDispaths;
            $("#changestatus3")[0].reset();
            $("#Refund3Modal").modal('hide');
            $(".modal-backdrop").remove();
            $(".reson3_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedProductOrders);
            Swal.fire("Success", "Status Change Successfully", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".reson3_submit_btn").removeAttr("disabled");
            $(".reson3_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}
