// ===============================
// STATUS BADGE (uses delivery_status)
// ===============================
const getStatusBadge = (status) => {
    switch (status) {
        case 0:
            return `<span class="badge bg-warning text-dark">Pending</span>`;
        case 1:
            return `<span class="badge bg-primary">Processing</span>`;
        case 2:
            return `<span class="badge bg-info">Shipped</span>`;
        case 3:
            return `<span class="badge bg-secondary">Out for Delivery</span>`;
        case 4:
            return `<span class="badge bg-success">Delivered</span>`;
        default:
            return `<span class="badge bg-dark">Unknown</span>`;
    }
};

// ===============================
// PAYMENT BADGE
// ===============================
const getPaymentBadge = (status) => {
    return status === "paid"
        ? `<span class="badge bg-success">Paid</span>`
        : `<span class="badge bg-danger">COD</span>`;
};

// ===============================
// GRID
// ===============================
const gridAll = new gridjs.Grid({
    columns: [
        "S.No",
        "Country",
        "Order ID",
        "Date",
        "Customer",
        "Products",          // ✅ Quantity
        "Product Amount",    // ✅ subtotal
        "Total Amount",      // ✅ total
        "Payment",
        "Status",
        "Action"
    ],

    pagination: { limit: 10 },
    search: true,
    sort: true,

    data: productOrders.map((order, index) => [

        index + 1,

        order.country?.name ?? "-",

        order.order_id ?? "-",

        order.date_ordered_on ?? "-",

        order.order_address
            ? order.order_address.firstname
            : (order.billing_name || "N/A"),

        // ✅ TOTAL QUANTITY (CORRECT)
        order.total_quantity ?? 0,

        // ✅ PRODUCT AMOUNT
        `₹ ${parseFloat(order.subtotal || 0).toFixed(2)}`,

        // ✅ TOTAL AMOUNT
        `₹ ${parseFloat(order.total_amount || 0).toFixed(2)}`,

        // PAYMENT
        gridjs.html(getPaymentBadge(order.payment_status)),

        // STATUS
        gridjs.html(getStatusBadge(order.delivery_status)),

        // ACTION
        gridjs.html(`
            

            <a href="viewProductdetail/${order.order_id}" target="_blank">
                <button class="btn btn-sm btn-success ms-2">
                    View
                </button>
            </a>
        `)
    ])
});

gridAll.render(document.getElementById("table-gridjs"));

// ===============================
// MODAL DATA SET
// ===============================
$(document).on("click", ".edit_btn", function () {
    $("#order_id_input").val($(this).data("orderid"));
    $("#customer_name_input").val($(this).data("customername"));
});


// ===============================
// STATUS UPDATE (AJAX)
// ===============================
function updateStatus(event) {
    event.preventDefault();

    let formData = new FormData(event.target);

    $.ajax({
        type: "POST",
        url: "updatestatus",
        data: formData,
        processData: false,
        contentType: false,

        success: function () {
            $("#assignToModal").modal("hide");

            Swal.fire("Success", "Status updated successfully", "success")
                .then(() => location.reload());
        },

        error: function () {
            Swal.fire("Error", "Something went wrong", "error");
        }
    });
}