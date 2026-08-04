const gridNew = new gridjs.Grid({
    columns: [
        "S.No",
        "Name",

        "Amount",


    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: topcustomers.map((topcustomer, index) => {

        return [
            index + 1,

            topcustomer.name,
            topcustomer.total_amount














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

function gridjsReRender(topcustomers) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: topcustomers.map((topcustomer, index) => {
                return [
                    topcustomer.name,
            topcustomer.total_amount



                ];
            }),
        })
        .forceRender();
}
