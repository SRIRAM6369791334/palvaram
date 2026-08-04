var todayDate = new Date().toISOString().split("T")[0];
const gridNew = new gridjs.Grid({
    columns: [
        "S.No",
        "Order ID",
        "User ID",
        "status",
        "Delivered Date",
        "Eligibility",
        "Action",
        // {
        //     name: "Status",
        //     sort: false,
        // },
        // {
        //     name: "Action",
        //     sort: false,
        // },
    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: requests.map((requests, index) => {
        return [
            index + 1,
            gridjs.html(
                `<a href="productOrders/${requests.order_id}">${requests.order_id} </a>`
            ),
            requests.user_id,
            requests.status,
            requests.delivered_date,
            gridjs.html(
                requests.return_approval_date < todayDate
                    ? `<div class="text-warning" style="font-weight:bold">NOT ELIGIBLE</div>`
                    : `<div class="text-success" style="font-weight:bold">ELIGIBLE</div>`
            ),

            gridjs.html(
                requests.return_approval_date < todayDate
                    ? `<div> <button 
                    data-milkOrderid1 ="${requests.id}",
                    data-ogmilkOrderid1 = "${requests.order_id}"
                    data-orderedDate1="${requests.delivered_date}"
                class="btn btn-secondary edit_btns2 rejection_but">Reject</button>`
                    : `<div> <button
                    data-milkOrderid1 ="${requests.id}",
                    data-ogmilkOrderid1 = "${requests.order_id}"
                    data-orderedDate1="${requests.delivered_date}"
                class="btn btn-danger edit_btns2 approv_but">Approve</button>
                <button 
                    data-milkOrderid1 ="${requests.id}",
                    data-ogmilkOrderid1 = "${requests.order_id}"
                    data-orderedDate1="${requests.delivered_date}"
                class="btn btn-secondary edit_btns2 rejection_but">Reject</button>
                </div>`
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

function gridjsReRender(requests) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: requests.map((requests, index) => {
                return [
                    index + 1,
                    gridjs.html(
                        `<a href="productOrders/${requests.order_id}">${requests.order_id} </a>`
                    ),
                    requests.user_id,
                    requests.status,
                    requests.delivered_date,
                    gridjs.html(
                        requests.return_approval_date < todayDate
                            ? `<div class="text-warning" style="font-weight:bold">NOT ELIGIBLE</div>`
                            : `<div class="text-success" style="font-weight:bold">ELIGIBLE</div>`
                    ),

                    gridjs.html(
                        requests.return_approval_date < todayDate
                            ? `<div> <button 
                            data-milkOrderid1 ="${requests.id}",
                            data-ogmilkOrderid1 = "${requests.order_id}"
                            data-orderedDate1="${requests.delivered_date}"
                        class="btn btn-secondary edit_btns2 rejection_but">Reject</button>`
                            : `<div> <button
                            data-milkOrderid1 ="${requests.id}",
                            data-ogmilkOrderid1 = "${requests.order_id}"
                            data-orderedDate1="${requests.delivered_date}"
                        class="btn btn-danger edit_btns2 approv_but">Approve</button>
                        <button 
                            data-milkOrderid1 ="${requests.id}",
                            data-ogmilkOrderid1 = "${requests.order_id}"
                            data-orderedDate1="${requests.delivered_date}"
                        class="btn btn-secondary edit_btns2 rejection_but">Reject</button>
                        </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

$(function () {
    $(document).on("click", ".edit_btns2", function () {
        $("#customer_name_input6").val($(this).attr("data-customername1"));
        $("#order_id_input6").val($(this).attr("data-ogmilkOrderid1"));
        $("#cusiddelive1").val($(this).attr("data-customerid1"));
        $("#numcus1").val($(this).attr("data-numcus"));
    });
});

$(function () {
    $(document).on("click", ".collectedit_btns", function () {
        $("#customer_name_input61").val($(this).attr("data-customername1"));
        $("#order_id_input61").val($(this).attr("data-ogmilkOrderid1"));
        $("#cusiddelive16").val($(this).attr("data-customerid1"));
        $("#cod_input61").val($(this).attr("data-codamt"));
        $("#numcus16").val($(this).attr("data-numcus"));
    });
});

$(function () {
    $(document).ready(function () {
        $("#codamt1").hide();

        $("#add_status_select61").change(function () {
            var selectedStatus = $(this).val();

            if (selectedStatus == "4") {
                $("#codamt1").show();
            } else {
                $("#codamt1").hide();
            }
        });
    });
});

const assigncollectValidator = new JustValidate("#collectstatus61", {
    validateBeforeSubmitting: true,
});

assigncollectValidator
    .addField("#add_status_select61", [
        {
            rule: "required",
            errorMessage: "*Status field is required",
        },
    ])
    .onSuccess((event) => {
        $(".collect_submit_btn").attr("disabled", "true");
        collectstatus61submit(event);
    });

const assignDeliveryValidator = new JustValidate("#changestatus6", {
    validateBeforeSubmitting: true,
});

assignDeliveryValidator
    .addField("#add_status_select6", [
        {
            rule: "required",
            errorMessage: "*Status field is required",
        },
    ])
    .onSuccess((event) => {
        $(".return_submit_btn").attr("disabled", "true");
        changestatus6submit(event);
    });
function changestatus6submit(event) {
    const formData = new FormData(event.target);
    $.ajax({
        type: "post",
        url: "updatereturnpro",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedproductReturns = response.productReturns;
            $("#changestatus6")[0].reset();
            $("#ReturnModal").hide();
            $(".modal-backdrop").remove();
            $(".return_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedproductReturns);
            Swal.fire("Success", "Status Change Successfully", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".return_submit_btn").removeAttr("disabled");
            $(".return_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

// cod

function collectstatus61submit(event) {
    const formData = new FormData(event.target);
    $.ajax({
        type: "post",
        url: "collectreturn",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedproductReturns = response.productReturns;
            $("#collectstatus61")[0].reset();
            $("#collectedModal").hide();
            $(".modal-backdrop").remove();
            $(".collect_submit_btn").removeAttr("disabled");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedproductReturns);
            Swal.fire("Success", "Status Change Successfully", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".collect_submit_btn").removeAttr("disabled");
            $(".collect_submit_btn").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

// RETURN APPROVE

$(document).ready(function () {
    // $("#cancellationtxt").hide();
    $(document).on("click", ".approv_but", function () {
        var order = $(this).data("order");
        var user = $(this).data("user");
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-danger ms-2",
                cancelButton: "btn btn-success",
            },
            buttonsStyling: false,
        });
        swalWithBootstrapButtons
            .fire({
                title: "Are you sure?",
                text: "You want to Approve the Return ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Approve it!",
                cancelButtonText: "No, Go Back",
                reverseButtons: true,
            })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    });

                    $.ajax({
                        url: "/request_return",
                        method: "post",
                        data: {
                            order_id: order,
                            user_id: user,
                        },
                        success: function (response) {
                            // Handle success, show SweetAlert with success message
                            // $("#cancellation_but").hide();
                            // $("#cancellationtxt").show();
                            const requests = response.requests;
                            gridjsReRender(requests);
                            swal.fire(
                                "Success",
                                "Return request Approved",
                                "success"
                            );

                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                },
                            });

                            Toast.fire({
                                icon: "success",
                                title: "Return request Approved",
                            });

                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        },
                        error: function (err) {
                            console.error(err);
                            // Handle error, show SweetAlert with error message
                            if (err.responseJSON && err.responseJSON.error) {
                                swal.fire(
                                    "Error",
                                    err.responseJSON.error,
                                    "error"
                                );
                            } else {
                                // swal.fire('Error', 'Failed to subscribe', 'error');
                            }
                        },
                    });
                }
            });
    });
});

// RETURN REJECT

$(document).ready(function () {
    // $("#cancellationtxt").hide();
    $(document).on("click", ".rejection_but", function () {
        var order = $(".rejection_but"  ).data("ogmilkorderid1");
        alert(order);
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-danger ms-2",
                cancelButton: "btn btn-success",
            },
            buttonsStyling: false,
        });
        swalWithBootstrapButtons
            .fire({
                title: "Are you sure?",
                text: "You want to reject this return ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, reject it!",
                cancelButtonText: "No, Go Back",
                reverseButtons: true,
            })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    });

                    $.ajax({
                        url: "/reject-return-request",
                        method: "post",
                        data: {
                            order_id: order,
                        },
                        success: function (response) {
                            // Handle success, show SweetAlert with success message
                            // $("#cancellation_but").hide();
                            // $("#cancellationtxt").show();
                            const requests = response.requests;
                            gridjsReRender(requests);
                            swal.fire(
                                "Success",
                                "Return request Rejected",
                                "success"
                            );

                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                },
                            });

                            Toast.fire({
                                icon: "success",
                                title: "Return request Rejected",
                            });

                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        },
                        error: function (err) {
                            console.error(err);
                            // Handle error, show SweetAlert with error message
                            if (err.responseJSON && err.responseJSON.error) {
                                swal.fire(
                                    "Error",
                                    err.responseJSON.error,
                                    "error"
                                );
                            } else {
                                // swal.fire('Error', 'Failed to subscribe', 'error');
                            }
                        },
                    });
                }
            });
    });
});
