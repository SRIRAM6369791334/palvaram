const addValidator = new JustValidate("#addCountriesForm", {
    validateBeforeSubmitting: true,
});
const editValidator = new JustValidate("#editCountriesForm", {
    validateBeforeSubmitting: true,
});

addValidator
    .addField("#add_countryname", [
        {
            rule: "required",
            errorMessage: "*Country Name Field is required",
        },
        {
            rule: 'minLength',
            value: 3,
            errorMessage: '*Country Name should be at least 3 character long',
        },
        {
            rule: 'maxLength',
            value: 50,
            errorMessage: '*Country Name should be at Maximum 50 character long',
        },
    ])

    .addField("#add_currency", [
        {
            rule: "required",
            errorMessage: "*Currency is required",
        }
    ])
    
    .onSuccess((event) => {
        $(".add_submit_btn").attr("disabled", true).html("Submitting...");
        addCountriesFormSubmit(event);
    });

editValidator
    .addField("#edit_countryname", [
        {
            rule: "required",
            errorMessage: "*Country Name Field is required",
        },
        {
            rule: 'minLength',
            value: 3,
            errorMessage: '*Country Name should be at least 3 character long',
        },
        {
            rule: 'maxLength',
            value: 50,
            errorMessage: '*Country Name should be at Maximum 50 character long',
        },
    ])
    
    .addField("#edit_currency", [
        {
            rule: "required",
            errorMessage: "*Currency is required",
        }
    ])
    .onSuccess((event) => {
        $(".edit_submit_btn").attr("disabled", true).html("Updating...");
        editCountriesFormSubmit(event);
    });

const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Country Name",
        "Currency",
        {
            name: "Action",
            sort: false,
        },
    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: countries.map((country, index) => {
        return [
            index + 1,
            country.name,
            country.currency,
            gridjs.html(`
                <div> 
                    <button data-bs-toggle="modal"
                        data-countriesid ="${country.id}"
                        data-countriesname="${country.name}"
                        data-currency="${country.currency}"
                        data-bs-target="#editCountriesModal" 
                        class="btn btn-secondary edit_btn">Edit</button>

                    <button class="btn btn-danger delete_btn" 
                        data-countriesid=${country.id}>Delete</button> 
                </div>
            `),
        ];
    }),
    style: {
        table: { border: "1px solid #ccc" },
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

function gridjsReRender(updatedCountries) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: updatedCountries.map((country, index) => {
                return [
                    index + 1,
                    country.name,
                    country.currency,
                    gridjs.html(`
                        <div> 
                            <button data-bs-toggle="modal"
                                data-countriesid ="${country.id}"
                                data-countriesname="${country.name}"
                                data-currency="${country.currency}"
                                data-bs-target="#editCountriesModal" 
                                class="btn btn-secondary edit_btn">Edit</button>

                            <button class="btn btn-danger delete_btn" 
                                data-countriesid=${country.id}>Delete</button> 
                        </div>
                    `),
                ];
            }),
        })
        .forceRender();
}

function addCountriesFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "countries",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            const updatedCountries = response.countries;
            $("#addCountriesForm")[0].reset();
            $("#addCountriesModal").modal("hide");
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedCountries);
            Swal.fire("Added", response.message, "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".add_submit_btn").removeAttr("disabled").html("Submit");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

function editCountriesFormSubmit(e) {
    const formdata = new FormData(e.target);
    const id = $("#edit_countries_id").val();
    $.ajax({
        url: "updateCountries/" + id,
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            const updatedCountries = response.countries;
            $("#editCountriesForm")[0].reset();
            $("#editCountriesModal").modal("hide");
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedCountries);
            Swal.fire("Updated", response.message, "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edit_submit_btn").removeAttr("disabled").html("Update");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

$(function () {
    $(document).on("click", ".edit_btn", function () {
        $("#edit_countries_id").val($(this).attr("data-countriesid"));
        $("#edit_countryname").val($(this).attr("data-countriesname"));
        $("#edit_currency").val($(this).attr("data-currency"));
    });

    $(document).on("click", ".delete_btn", function () {
        const id = $(this).attr("data-countriesid");
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "destroyCountries/" + id,
                    method: "post",
                    dataType: "json",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        const updatedCountries = response.countries;
                        gridjsReRender(updatedCountries);
                        Swal.fire("Deleted!", response.message, "success");
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
                    },
                });
            }
        });
    });
});
