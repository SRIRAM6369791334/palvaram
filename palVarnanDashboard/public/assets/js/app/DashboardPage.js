

const gridNewProduct = new gridjs.Grid({
    columns: ["S.No", "Order ID", "Ordered Date", "Customer", "Area", "Status"],
    sort: !0,
    data: productOrders.map((productOrder, index) => {
        return [
            index + 1,
            gridjs.html(
                `<a href="productOrders/${productOrder.order_id}">${productOrder.order_id} </a>`
            ),
            productOrder.date_ordered_on,
            productOrder.customer.name,
            productOrder.order_address.address_line_one,
            gridjs.html(
                productOrder.delivery_person_id
                    ? `<div class="is_delivery_assigned">Assigned</div>
            `
                    : `<div class="is_delivery_not_assigned">Pending</div>
            `
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

gridNewProduct.render(document.getElementById("table-gridjs-product"));
