$(function () {
    flatpickr(".date_picker_from_date", {
        maxDate: "today",
        altInput: true,
        altFormat: "j F, Y",
        dateFormat: "Y-m-d",
    });

    $(".date_picker_from_date").on("change", function () {
        const selectedDate = this.value;

        flatpickr(".date_picker_to_date", {
            minDate: selectedDate,
            maxDate: "today",
            altInput: true,
            altFormat: "j F, Y",
            dateFormat: "Y-m-d",
        });
        $(".date_picker_container").show();
    });
});

const addValidator = new JustValidate("#income_reports_form", {
    validateBeforeSubmitting: true,
});

addValidator
    .addField(".date_picker_from_date", [
        {
            rule: "required",
            errorMessage: "*From Date field is required",
        },
    ])
    .addField(".date_picker_to_date", [
        {
            rule: "required",
            errorMessage: "*To Date field is required",
        },
    ])

    .onSuccess((event) => {
        $(".report_btn").attr("disabled", "true");
        $(".report_btn").html("Loading....");
        reportsFormSubmit(event);
    });

$(".product_grid_container").hide();
$(".milk_grid_container").hide();
function reportsFormSubmit(event) {
    const formData = new FormData(event.target);
    $(".milk_grid_container").show();

    // const productType = $("#add_category_select").val();

    $.ajax({
        url: "getIncomeReports",
        method: "POST",
        dataType: "json",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".report_btn").removeAttr("disabled");
            $(".report_btn").html("Get Report");
            $(".income_reports_container").html(response.view);
            initDataTable();
            $(".loader").hide();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".report_btn").removeAttr("disabled");
            $(".report_btn").html("Get Report");
            Swal.fire(textStatus.toUpperCase(), textStatus, "warning");
        },
    });
}

function initDataTable() {
    $.fn.dataTable.Api.register("sum()", function () {
        return this.flatten().reduce(function (a, b) {
            console.log(a, b);
            if (typeof a === "string") {
                a = a.replace(/[^\d.-]/g, "") * 1;
            }
            if (typeof b === "string") {
                b = b.replace(/[^\d.-]/g, "") * 1;
            }
            return a + b;
        }, 0);
    });

    $("#milkOrderReportTable").DataTable({
        dom:
            "<'row milk_table_top_row'<'col-sm-6'f><'col-sm-6 custom_export_btns'B>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            {
                extend: "excelHtml5",
                className: "",
            },
            {
                extend: "csvHtml5",
                className: "",
            },
            {
                extend: "pdfHtml5",
                className: "",
            },
        ],
        lengthChange: false,
        aaSorting: [],
        orderCellsTop: true,
        fixedHeader: true,
        footerCallback: function (row, data, start, end, display) {
            console.log("dadafdfafsd");
            var api = this.api();
            var total = api.column(5, { page: "current" }).data().sum();

            $(api.column(5).footer()).html(total);
        },
    });
}
