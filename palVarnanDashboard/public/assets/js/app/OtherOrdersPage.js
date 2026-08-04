// SpecialOrdersPage.js

let allOrders = window.specialOrders || [];
let currentFilter = "all";

// ─── Helpers ────────────────────────────────────────────────────────────────

function getFilteredOrders(filter) {
    if (filter === "all") return allOrders;
    if (filter === "cancelled") return allOrders.filter(o => o.status === "cancelled");
    if (filter === "return")    return allOrders.filter(o => o.status === "return");
    if (filter === "unavailable") return allOrders.filter(
        o => o.unavailable_count !== null && o.unavailable_count > 0
    );
    return allOrders;
}

function getTypeBadge(order) {
    const badges = [];

    if (order.status === "cancelled") {
        badges.push(`<span class="badge bg-danger">Cancelled</span>`);
    }
    if (order.status === "return") {
        badges.push(`<span class="badge bg-warning text-dark">Return</span>`);
    }
    if (order.unavailable_count !== null && order.unavailable_count > 0) {
        badges.push(`<span class="badge bg-primary">Unavailable</span>`);
    }

    return badges.join(" ");
}

function getDeliveryStatusLabel(status) {
    const map = {
        0: `<span class="text-info fw-bold">Billing</span>`,
        1: `<span class="text-primary fw-bold">Packing</span>`,
        2: `<span class="text-warning fw-bold">Dispatched</span>`,
        3: `<span class="text-success fw-bold">Out For Delivery</span>`,
        4: `<span class="text-success fw-bold">Delivered</span>`,
    };
    return map[status] ?? `<span class="text-secondary">—</span>`;
}

// ─── Build Row Data ──────────────────────────────────────────────────────────

function buildRows(orders) {
    return orders.map((order, index) => {
        const customerName = order.order_address
            ? order.order_address.firstname
            : (order.billing_name || "N/A");

        const unavailableQty    = order.unavailable_count ?? "—";
        const unavailableAmount = order.unavailable_amount
            ? "₹" + parseFloat(order.unavailable_amount).toFixed(2)
            : "—";
        const reason = order.reason || "—";

        return [
            index + 1,
            order.country?.name ?? "—",
            order.order_id,
            order.date_ordered_on ?? order.created_at?.substring(0, 10),
            customerName,
            order.payment_status,
            gridjs.html(getDeliveryStatusLabel(order.delivery_status)),
            gridjs.html(getTypeBadge(order)),
            gridjs.html(`<span class="text-muted" style="font-size:13px;">${reason}</span>`),
            gridjs.html(`
                <div class="d-flex align-items-center gap-1" style="white-space:nowrap;">
                    <span>${unavailableQty}</span>
                </div>
            `),
            gridjs.html(`<span class="fw-semibold text-danger">${unavailableAmount}</span>`),
            gridjs.html(`
                <a href="viewProductdetail/${order.order_id}" target="_blank">
                    <button type="button" class="btn btn-primary btn-sm ms-1">
                        <i class="bx bx-message-square-dots me-1"></i>View
                    </button>
                </a>
            `),
        ];
    });
}

// ─── Grid Setup ─────────────────────────────────────────────────────────────

const columns = [
    "S.No",
    "Country",
    "Order ID",
    "Ordered Date",
    "Name",
    "Payment Status",
    { name: "Delivery Status", sort: false },
    { name: "Type",   sort: false },
    { name: "Reason", sort: false },
    { name: "Unavail. Qty",    sort: false },
    { name: "Unavail. Amount", sort: false },
    { name: "Action", sort: false },
];

const gridSpecial = new gridjs.Grid({
    columns,
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: buildRows(getFilteredOrders("all")),
    style: {
        table: { border: "1px solid #ccc" },
        th: {
            "background-color": "rgba(0,0,0,0.07)",
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

gridSpecial.render(document.getElementById("table-other-orders"));

// ─── Re-render helper ────────────────────────────────────────────────────────

function reRenderSpecial(filter) {
    const filtered = getFilteredOrders(filter);
    gridSpecial
        .updateConfig({ data: buildRows(filtered) })
        .forceRender();
}

// ─── Filter Tabs ─────────────────────────────────────────────────────────────

document.getElementById("filterTabs").addEventListener("click", function (e) {
    const tab = e.target.closest(".filter-tab");
    if (!tab) return;

    document.querySelectorAll(".filter-tab").forEach(t => t.classList.remove("active"));
    tab.classList.add("active");

    currentFilter = tab.dataset.filter;
    reRenderSpecial(currentFilter);
});