const gridNew = new gridjs.Grid({
    columns: [
        "S.No",
        "Name",

        "Email",
        "Subject",
        "Phone",
        "Message",


    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: bulkorders.map((bulkorder, index) => {

        return [
            index + 1,

            bulkorder.name,
            bulkorder.email,
            bulkorder.subject,
            bulkorder.phone,
            bulkorder.message














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

function gridjsReRender(bulkorders) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: bulkorders.map((bulkorder, index) => {
                return [
                    bulkorder.name,
            bulkorder.email,
            bulkorder.subject,
            bulkorder.phone,
            bulkorder.message
                ];
            }),
        })
        .forceRender();
}
