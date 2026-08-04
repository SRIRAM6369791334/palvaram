const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Image",
        "Category",
        "Product Name",


        "Sales Quantity",

    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: highsellings.map((highselling, index) => {
        return [
            index + 1,
            gridjs.html(



                ` <img class="bannerImage_image_el gridImage" src="images/${highselling.product_image}"  alt ="categgory_image${index}"/>`
             ),
            highselling.category_name,

            gridjs.html(
                highselling.varient == 1
                ?  `<span>${highselling.productname}</span> <span>${highselling.value}</span><span>l</span>`
        :highselling.varient == 2
        ?  `<span>${highselling.productname}</span> <span>${highselling.value}</span><span>ml</span>`
        : highselling.varient == 3
        ?  `<span>${highselling.productname}</span> <span>${highselling.value}</span><span>g</span>`
        : highselling.varient == 4
        ? `<span>${highselling.productname}</span> <span>${highselling.value}</span><span>kg</span>`
        : `<span>${highselling.productname}</span> <span>${highselling.value}</span><span>No's</span>`

            ),

            highselling.salestock
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


function gridjsReRender(highsellings) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: highsellings.map((highselling, index) => {
                return [
                    index + 1,
                    gridjs.html(
                        ` <img class="bannerImage_image_el gridImage" src="images/${highselling.product_image}"  alt ="categgory_image${index}"/>`
                     ),
                    highselling.category_name,

                    gridjs.html(
                        `${highselling.productname}${highselling.value}<span></span>`
                    ),

                    highselling.salestock
                 ];
            }),
        })
        .forceRender();
}
