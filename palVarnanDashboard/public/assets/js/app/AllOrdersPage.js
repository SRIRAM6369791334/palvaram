// ===============================
// STATUS BADGE (supports delivery_status and order.status string)
// ===============================
const getStatusBadge = (deliveryStatus, orderStatus) => {
    const s = String(orderStatus || '').toLowerCase().trim();
    if (s === 'cancelled' || deliveryStatus === 5 || deliveryStatus === '5') {
        return `<span class="badge bg-danger">Cancelled</span>`;
    }
    if (s === 'delivered' || deliveryStatus === 4 || deliveryStatus === '4') {
        return `<span class="badge bg-success">Delivered</span>`;
    }
    if (s === 'out for delivery' || deliveryStatus === 3 || deliveryStatus === '3') {
        return `<span class="badge bg-secondary">Out for Delivery</span>`;
    }
    if (s === 'shipped' || deliveryStatus === 2 || deliveryStatus === '2') {
        return `<span class="badge bg-info">Shipped</span>`;
    }
    if (s === 'processing' || deliveryStatus === 1 || deliveryStatus === '1') {
        return `<span class="badge bg-primary">Processing</span>`;
    }
    if (s === 'pending' || s === 'order placed' || deliveryStatus === 0 || deliveryStatus === '0') {
        return `<span class="badge bg-warning text-dark">Pending</span>`;
    }
    if (s) {
        return `<span class="badge bg-secondary">${orderStatus}</span>`;
    }
    return `<span class="badge bg-dark">Unknown</span>`;
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
        gridjs.html(getStatusBadge(order.delivery_status, order.status)),

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