// ================= TOAST HELPER =================

function showToast(message, type = 'success') {

    let existing = document.getElementById('po-toast');
    if (existing) existing.remove();

    let colors = {
        success: { bg: '#f0fdf4', border: '#bbf7d0', text: '#15803d', icon: 'mdi-check-circle-outline' },
        error:   { bg: '#fef2f2', border: '#fecaca', text: '#dc2626', icon: 'mdi-alert-circle-outline' },
    };

    let c = colors[type] || colors.success;

    let toast = document.createElement('div');
    toast.id = 'po-toast';
    toast.style.cssText = `
        position:fixed; top:24px; right:24px; z-index:99999;
        background:${c.bg}; border:1px solid ${c.border};
        color:${c.text}; border-radius:12px;
        padding:14px 20px; display:flex; align-items:center; gap:10px;
        font-size:14px; font-weight:500;
        box-shadow:0 4px 24px rgba(0,0,0,0.08);
        animation:slideInToast .25s ease;
        min-width:260px; max-width:380px;
    `;
    toast.innerHTML = `
        <i class="mdi ${c.icon}" style="font-size:20px;flex-shrink:0;"></i>
        <span style="flex:1;">${message}</span>
        <i class="mdi mdi-close" style="cursor:pointer;font-size:16px;opacity:0.6;"
            onclick="this.closest('#po-toast').remove()"></i>
    `;

    if (!document.getElementById('po-toast-style')) {
        let s = document.createElement('style');
        s.id = 'po-toast-style';
        s.textContent = `@keyframes slideInToast { from{opacity:0;transform:translateY(-12px)} to{opacity:1;transform:translateY(0)} }`;
        document.head.appendChild(s);
    }

    document.body.appendChild(toast);
    setTimeout(() => { if (toast.parentNode) toast.remove(); }, 4000);
}


// ================= GRID HELPER =================

function buildGrid(orders, statusFilter) {

    let filtered = statusFilter === 'all' ? orders : orders.filter(o => o.status === statusFilter);

    return new gridjs.Grid({
        columns: [
            "S.NO", "Country", "Warehouse", "PO Code", "Supplier",
            "Total Amount", "Status", "Date", "Action"
        ],
        pagination: { limit: 10 },
        sort: true,
        search: true,
        data: filtered.map((o, index) => {

            // ── Restrict status options ──
            let statusOptions = '';
            if (o.status === 'pending') {
                statusOptions = `
                    <option value="pending" selected>Pending</option>
                    <option value="ordered">Ordered</option>
                `;
            } else if (o.status === 'ordered') {
                statusOptions = `
                    <option value="ordered" selected>Ordered</option>
                    <option value="received">Received</option>
                `;
            } else {
                statusOptions = `<option value="received" selected>Received</option>`;
            }

            return [
                index + 1,
                o.country?.name ?? '-',
                o.warehouse?.warehouse_name ?? '-',
                gridjs.html(`<a href="/purchase-orders/${o.id}" class="fw-bold text-primary" style="text-decoration:none;">${o.po_code}</a>`),
                o.supplier?.name ?? '-',
                '₹ ' + Number(o.total_amount).toLocaleString(),
                gridjs.html(`
                    <select class="form-select form-select-sm po-status"
                        data-id="${o.id}" data-current="${o.status}"
                        ${o.status === 'received' ? 'disabled' : ''}>
                        ${statusOptions}
                    </select>
                `),
                new Date(o.created_at).toLocaleDateString(),
                gridjs.html(`
                    <button class="btn btn-sm btn-primary edit-po" data-id="${o.id}">
                        Edit
                    </button>
                `)
            ];
        }),
        style: {
            table: { border: "1px solid #e5e7eb" },
            th: { "background-color": "#f9fafb", "text-align": "center", "font-size": "12px" },
            td: { "text-align": "center", "vertical-align": "middle" }
        }
    });
}


// ================= RENDER ALL TAB GRIDS =================

function renderTabGrids() {
    ['all', 'pending', 'ordered', 'received'].forEach(tab => {
        let el = document.getElementById(`po-table-${tab}`);
        if (el) {
            el.innerHTML = '';
            buildGrid(window.orders, tab).render(el);
        }
    });
}

renderTabGrids();


function updateStatus(id, status, $select) {

    $.ajax({
        url: '/purchase-orders/update-status',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id:     id,
            status: status
        },
        success: function () {

            // Update local data
            let order = window.orders.find(o => o.id == id);
            if (order) order.status = status;

            let label = status.charAt(0).toUpperCase() + status.slice(1);
            showToast(`Status updated to <strong>${label}</strong> successfully.`, 'success');

            // ── Destroy old grids and re-render ──
            ['all', 'pending', 'ordered', 'received'].forEach(tab => {
                let el = document.getElementById(`po-table-${tab}`);
                if (el) el.innerHTML = '';
            });

            renderTabGrids();

            // Switch to matching tab after a tiny delay so DOM is ready
            setTimeout(() => {
                let tabEl = document.getElementById(`tab-${status}`);
                if (tabEl) new bootstrap.Tab(tabEl).show();
            }, 50);
        },
        error: function (xhr) {
            if ($select) $select.val($select.data("prev"));
            showToast(xhr.responseJSON?.message || 'Failed to update status.', 'error');
        }
    });
}



function buildBarcodeOptions(countryId) {
    if (!countryId) {
        return `<option value="">Select Country First</option>`;
    }

    let matching = window.allVariants.filter(v => {
        if (!v.country_id) return false;
        // Only non-gift products
        if (v.product?.is_gift == 1) return false;
        let ids = v.country_id.toString().split(',').map(id => id.trim());
        return ids.includes(countryId.toString());
    });

    if (matching.length === 0) {
        return `<option value="">No barcodes for this country</option>`;
    }

    let options = `<option value="">Search Barcode</option>`;
    matching.forEach(v => {
        let productName = v.product?.product_name ?? '';
        let variantName = v.varient_name ?? v.varient ?? productName;
        options += `
            <option
                value="${v.id}"
                data-product-id="${v.product_id}"
                data-product-name="${productName}"
                data-variant-name="${variantName}"
                data-value="${v.value ?? ''}"
                data-unit-id="${v.unit_id ?? ''}"
                data-unit="${v.unit?.short_name ?? ''}"
                data-category="${v.categoryid ?? ''}"
                data-subcategory="${v.subcategoryid ?? ''}"
                data-item-code="${v.sku ?? ''}"
            >${v.barcode}</option>
        `;
    });

    return options;
}


// ================= ADD ROW =================

let rowIndex = 0;

$(document).on("click", "#add_po_item", function () {

    let selectedCountry = $("#country_id").val();

    if (!selectedCountry) {
        showToast('Please select a Country first.', 'error');
        return;
    }

    let barcodeOptions = buildBarcodeOptions(selectedCountry);

    let row = `
    <tr>

        <td>
            <select name="items[${rowIndex}][variant_id]"
                class="form-select barcode_select">

                ${barcodeOptions}
            </select>

            <input type="hidden"
                name="items[${rowIndex}][product_id]"
                class="row_product_id">

            <input type="hidden"
                name="items[${rowIndex}][category_id]"
                class="row_category_id">

            <input type="hidden"
                name="items[${rowIndex}][subcategory_id]"
                class="row_subcategory_id">

            <input type="hidden"
                name="items[${rowIndex}][unit_id]"
                class="row_unit_id">
        </td>

        <td>
            <input type="text"
                class="form-control item_code">
        </td>

        <td>
            <input type="text"
                class="form-control product_name"
                readonly>
        </td>

        <td>
            <input type="text"
                class="form-control variant_name"
                readonly>
        </td>

        <td>
            <input type="text"
                class="form-control value_field"
                readonly>
        </td>

        <td>
            <input type="text"
                class="form-control unit_name"
                readonly>
        </td>

        <td>
            <input type="number"
                name="items[${rowIndex}][quantity]"
                class="form-control qty"
                min="1">
        </td>

        <td>
            <input type="number"
                name="items[${rowIndex}][price]"
                class="form-control price"
                min="0">
        </td>

        <td class="row_total">0</td>

        <td>
            <button type="button"
                class="btn btn-danger remove_row">
                X
            </button>
        </td>

    </tr>
    `;

    $("#po_items_body").append(row);


$(".barcode_select").last().select2({
    width: '100%',
    dropdownParent: $("#createPOModal")
});

    rowIndex++;
});


$(document).on("change", ".barcode_select", function () {

    let $selected = $(this).find(':selected');
    let $row      = $(this).closest('tr');

    let productId   = $selected.data('product-id');
    let productName = $selected.data('product-name');
    let variantName = $selected.data('variant-name');
    let value       = $selected.data('value');
    let unitId      = $selected.data('unit-id');
    let unitName    = $selected.data('unit');
    let category    = $selected.data('category');
    let subcategory = $selected.data('subcategory');
    let itemCode    = $selected.data('item-code');

    $row.find('.row_product_id').val(productId);
    $row.find('.row_category_id').val(category);
    $row.find('.row_subcategory_id').val(subcategory);
    $row.find('.row_unit_id').val(unitId);

    // $row.find('.item_code').val(itemCode);
    $row.find('.product_name').val(productName);
    $row.find('.variant_name').val(variantName || productName);
    $row.find('.value_field').val(value);
    $row.find('.unit_name').val(unitName);
});

// ================= REMOVE ROW =================

$(document).on("click", ".remove_row", function () {
    $(this).closest("tr").remove();
    calculateTotal();
});


// ================= CALCULATE =================

$(document).on("keyup change", ".qty, .price", function () {
    let row   = $(this).closest("tr");
    let qty   = parseInt(row.find(".qty").val())    || 0;
    let price = parseFloat(row.find(".price").val()) || 0;
    row.find(".row_total").text(qty * price);
    calculateTotal();
});

function calculateTotal() {
    let grand = 0;
    $("#po_items_body tr").each(function () {
        grand += parseFloat($(this).find(".row_total").text()) || 0;
    });
    $("#po_grand_total").text(grand);
}


// ================= STATUS CHANGE =================

$(document).on("focus", ".po-status", function () {
    $(this).data("prev", $(this).val());
});

$(document).on("change", ".po-status", function () {

    let status  = $(this).val();
    let id      = $(this).data("id");
    let $select = $(this);

    if (!$select.data("prev")) $select.data("prev", $select.data("current"));

    if (status === 'received') {

        let order = window.orders.find(o => o.id == id);
        if (!order) return;

        $("#batch_po_id").val(order.id);
        $("#batch_po_code").text(order.po_code);

        let rows = '';
        order.items.forEach(item => {
            rows += `
            <tr>
                <td>${item.product?.product_name ?? '-'}
                    ${item.variant ? `<br><small class="text-muted">${item.variant.varient_name ?? item.variant.varient}</small>` : ''}
                </td>
                <td>${item.quantity}</td>
                <td>
                    <input type="hidden" class="batch_item_id" value="${item.id}">
                    <input type="text" class="form-control batch_no" placeholder="e.g. BT-001" required>
                </td>
                <td><input type="date" class="form-control mfg_date" required></td>
                <td><input type="date" class="form-control exp_date" required></td>
                <td><input type="number" class="form-control recv_qty" value="${item.quantity}" required></td>
            </tr>`;
        });

        $("#batch_items_body").html(rows);
        $("#batchEntryModal").data("select", $select);
        $("#batchEntryModal").data("prev-status", $select.data("prev") || "pending");

        let modalEl    = document.getElementById('batchEntryModal');
        document.body.appendChild(modalEl);

        let batchModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
        batchModal.show();
        $("#batchEntryModal").data("bs-instance", batchModal);

    } else {
        $select.data("prev", status);
        updateStatus(id, status, $select);
    }
});




$(document).on("submit", "#createPOForm", function (e) {
    e.preventDefault();

    // ── Header validation ──
    let isValid = true;

    // Country
    if (!$("#country_id").val()) {
        if (!$("#country-error").length) {
            $("#country_id").after('<span id="country-error" class="text-danger" style="font-size:12px;">Country is required.</span>');
        }
        isValid = false;
    } else {
        $("#country-error").remove();
    }

    // Supplier
    if (!$("#supplier_id").val()) {
        if (!$("#supplier-error").length) {
            $("#supplier_id").after('<span id="supplier-error" class="text-danger" style="font-size:12px;">Supplier is required.</span>');
        }
        isValid = false;
    } else {
        $("#supplier-error").remove();
    }

    // Warehouse
    if (!$("#warehouse_id").val()) {
        if (!$("#warehouse-error").length) {
            $("#warehouse_id").after('<span id="warehouse-error" class="text-danger" style="font-size:12px;">Warehouse is required.</span>');
        }
        isValid = false;
    } else {
        $("#warehouse-error").remove();
    }

    // ── Items validation ──
    let rows = $("#po_items_body tr");

    if (rows.length === 0) {
        showToast("Please add at least one item.", "error");
        isValid = false;
    }

    rows.each(function () {
        let $row = $(this);

        let barcode = $row.find(".barcode_select").val();
        let qty     = $row.find(".qty").val();
        let price   = $row.find(".price").val();

        if (!barcode) {
            $row.find(".barcode_select").addClass("is-invalid");
            if (!$row.find(".barcode_select").next(".invalid-feedback").length) {
                $row.find(".barcode_select").after('<div class="invalid-feedback">Barcode is required.</div>');
            }
            isValid = false;
        } else {
            $row.find(".barcode_select").removeClass("is-invalid");
            $row.find(".barcode_select").next(".invalid-feedback").remove();
        }

        if (!qty || parseInt(qty) < 1) {
            $row.find(".qty").addClass("is-invalid");
            if (!$row.find(".qty").next(".invalid-feedback").length) {
                $row.find(".qty").after('<div class="invalid-feedback">Qty required.</div>');
            }
            isValid = false;
        } else {
            $row.find(".qty").removeClass("is-invalid");
            $row.find(".qty").next(".invalid-feedback").remove();
        }

        if (!price || parseFloat(price) < 0) {
            $row.find(".price").addClass("is-invalid");
            if (!$row.find(".price").next(".invalid-feedback").length) {
                $row.find(".price").after('<div class="invalid-feedback">Price required.</div>');
            }
            isValid = false;
        } else {
            $row.find(".price").removeClass("is-invalid");
            $row.find(".price").next(".invalid-feedback").remove();
        }
    });

    if (!isValid) return;

    $(".create_po_submit_btn").attr("disabled", true).text("Saving...");

    $.ajax({
        url: "/purchase-orders",        // change to "/gift-purchase-orders" in giftPurchaseOrder.js
        method: "POST",
        data: $(this).serialize(),
        success: function () {
            $(".create_po_submit_btn").removeAttr("disabled").text("Submit");
            $("#createPOForm")[0].reset();
            $("#supplier_id").html('<option value="">Select Country First</option>').prop('disabled', true);
            $("#po_items_body").html('');
            $("#po_grand_total").text('0');
            $("#createPOModal").modal('hide');
            Swal.fire("Success", "Purchase Order Created", "success");
            location.reload();
        },
        error: function (xhr) {
            $(".create_po_submit_btn").removeAttr("disabled").text("Submit");
            Swal.fire("Error", xhr.responseJSON?.message || "Failed", "error");
        }
    });
});


// ================= EDIT PO =================

$(document).on("click", ".edit-po", function () {

    let id    = $(this).data("id");
    let order = window.orders.find(o => o.id == id);
    if (!order) return;

    // Filter barcodes to this order's country, same as Create PO.
    let barcodeOptions = buildBarcodeOptions(order.country_id);

    $("#edit_po_id").val(order.id);
    $("#edit_country_id").val(order.country_id);

    // ── Fetch suppliers for this order's country, then pre-select the current supplier ──
    $("#edit_supplier_id").html('<option value="">Loading...</option>').prop('disabled', true);

    $.get('/get-suppliers', { country_id: order.country_id, is_gift: 0 }, function (suppliers) {

        let options = '<option value="">Select Supplier</option>';
        suppliers.forEach(s => {
            options += `<option value="${s.id}" ${s.id == order.supplier_id ? 'selected' : ''}>${s.name}</option>`;
        });
        $('#edit_supplier_id').html(options).prop('disabled', false);
    });

    $.get('/get-warehouses', { country_id: order.country_id }, function (warehouses) {
        $('#edit_warehouse_id').html('<option value="">Select</option>');
        warehouses.forEach(w => {
            $('#edit_warehouse_id').append(
                `<option value="${w.id}" ${w.id == order.warehouse_id ? 'selected' : ''}>${w.warehouse_name}</option>`
            );
        });
    });

    $("#edit_po_items").html('');
    let grand = 0;

    let itemPromises = order.items.map(item => {
        return new Promise(resolve => {
            $.get('/get-variants', { product_id: item.product_id }, resolve);
        });
    });

    Promise.all(itemPromises).then(variantResults => {

        order.items.forEach((item, idx) => {

            let total    = item.quantity * item.price;
            grand       += total;
            let variants = variantResults[idx] || [];

            let unitOptions = `<option disabled>Select Unit</option>`;
            window.units.forEach(u => {
                unitOptions += `<option value="${u.id}" ${u.id == item.unit_id ? 'selected' : ''}>${u.short_name}</option>`;
            });

            let variantOptions = `<option value="">-- No Variant --</option>`;
            variants.forEach(v => {
                variantOptions += `<option value="${v.id}" data-price="${v.offer_price}" ${v.id == item.variant_id ? 'selected' : ''}>
                    ${v.varient_name ?? v.varient}
                </option>`;
            });

            let row = `
                <tr>

                    <td>
                        <select class="form-select edit_barcode_select">
                            ${barcodeOptions}
                        </select>

                        <input type="hidden" class="edit_product_id">
                        <input type="hidden" class="edit_category_id">
                        <input type="hidden" class="edit_subcategory_id">
                        <input type="hidden" class="edit_unit_id">
                    </td>

                    <td><input type="text" class="form-control edit_item_code"></td>
                    <td><input type="text" class="form-control edit_product_name" readonly></td>
                    <td><input type="text" class="form-control edit_variant_name" readonly></td>
                    <td><input type="text" class="form-control edit_value" readonly></td>
                    <td><input type="text" class="form-control edit_unit_name" readonly></td>

                    <td>
                        <input type="number" class="form-control edit_qty">
                    </td>

                    <td>
                        <input type="number" class="form-control edit_price">
                    </td>

                    <td class="edit_row_total">0</td>

                    <td>
                        <button type="button" class="btn btn-danger remove_edit_row">X</button>
                    </td>

                </tr>`;

            $("#edit_po_items").append(row);
        });

        $("#edit_grand_total").text(grand);
        $("#editPOModal").modal('show');
    });
});


$(document).on("change", ".edit_barcode_select", function () {

    let $selected = $(this).find(':selected');
    let $row = $(this).closest('tr');

    let productId   = $selected.data('product-id');
    let productName = $selected.data('product-name');
    let variantName = $selected.data('variant-name');
    let value       = $selected.data('value');
    let unitId      = $selected.data('unit-id');
    let unitName    = $selected.data('unit');
    let category    = $selected.data('category');
    let subcategory = $selected.data('subcategory');
    let itemCode    = $selected.data('item-code');

    $row.find('.edit_product_id').val(productId);
    $row.find('.edit_category_id').val(category);
    $row.find('.edit_subcategory_id').val(subcategory);
    $row.find('.edit_unit_id').val(unitId);

    $row.find('.edit_item_code').val(itemCode);
    $row.find('.edit_product_name').val(productName);
    $row.find('.edit_variant_name').val(variantName || productName);
    $row.find('.edit_value').val(value);
    $row.find('.edit_unit_name').val(unitName);
});

// ================= EDIT: VARIANT → AUTO-FILL PRICE =================

$(document).on("change", ".edit_variant", function () {
    let price = $(this).find(':selected').data('price');
    if (price) {
        $(this).closest("tr").find(".edit_price").val(price).trigger('keyup');
    }
});


// ================= EDIT: CALCULATE =================

$(document).on("keyup change", ".edit_qty, .edit_price", function () {

    let row = $(this).closest("tr");

    let qty   = parseFloat(row.find(".edit_qty").val()) || 0;
    let price = parseFloat(row.find(".edit_price").val()) || 0;

    row.find(".edit_row_total").text(qty * price);

    let grand = 0;

    $("#edit_po_items tr").each(function () {
        grand += parseFloat($(this).find(".edit_row_total").text()) || 0;
    });

    $("#edit_grand_total").text(grand);
});




$(document).on("submit", "#editPOForm", function (e) {
    e.preventDefault();

    // ── Header validation ──
    let isValid = true;

        // Country
    if (!$("#edit_country_id").val()) {
        if (!$("#edit-country-error").length) {
            $("#edit_country_id").after('<span id="edit-country-error" class="text-danger" style="font-size:12px;">Country is required.</span>');
        }
        isValid = false;
    } else {
        $("#edit-country-error").remove();
    }

    // Supplier
    if (!$("#edit_supplier_id").val()) {
        if (!$("#edit-supplier-error").length) {
            $("#edit_supplier_id").after('<span id="edit-supplier-error" class="text-danger" style="font-size:12px;">Supplier is required.</span>');
        }
        isValid = false;
    } else {
        $("#edit-supplier-error").remove();
    }

    // Warehouse
    if (!$("#edit_warehouse_id").val()) {
        if (!$("#edit-warehouse-error").length) {
            $("#edit_warehouse_id").after('<span id="edit-warehouse-error" class="text-danger" style="font-size:12px;">Warehouse is required.</span>');
        }
        isValid = false;
    } else {
        $("#edit-warehouse-error").remove();
    }

    // ── Items validation ──
    let rows = $("#edit_po_items tr");

    if (rows.length === 0) {
        showToast("Please add at least one item.", "error");
        isValid = false;
    }

    rows.each(function () {
        let $row = $(this);

        let barcode = $row.find(".edit_barcode_select").val();
        let qty     = $row.find(".edit_qty").val();
        let price   = $row.find(".edit_price").val();

        if (!barcode) {
            $row.find(".edit_barcode_select").addClass("is-invalid");
            if (!$row.find(".edit_barcode_select").next(".invalid-feedback").length) {
                $row.find(".edit_barcode_select").after('<div class="invalid-feedback">Barcode is required.</div>');
            }
            isValid = false;
        } else {
            $row.find(".edit_barcode_select").removeClass("is-invalid");
            $row.find(".edit_barcode_select").next(".invalid-feedback").remove();
        }

        if (!qty || parseInt(qty) < 1) {
            $row.find(".edit_qty").addClass("is-invalid");
            if (!$row.find(".edit_qty").next(".invalid-feedback").length) {
                $row.find(".edit_qty").after('<div class="invalid-feedback">Qty required.</div>');
            }
            isValid = false;
        } else {
            $row.find(".edit_qty").removeClass("is-invalid");
            $row.find(".edit_qty").next(".invalid-feedback").remove();
        }

        if (!price || parseFloat(price) < 0) {
            $row.find(".edit_price").addClass("is-invalid");
            if (!$row.find(".edit_price").next(".invalid-feedback").length) {
                $row.find(".edit_price").after('<div class="invalid-feedback">Price required.</div>');
            }
            isValid = false;
        } else {
            $row.find(".edit_price").removeClass("is-invalid");
            $row.find(".edit_price").next(".invalid-feedback").remove();
        }
    });

    if (!isValid) return;

    let items = [];
    $("#edit_po_items tr").each(function () {
        items.push({
            id:             $(this).find(".item_id").val(),
            product_id:     $(this).find(".edit_product_id").val(),
            category_id:    $(this).find(".edit_category_id").val(),
            subcategory_id: $(this).find(".edit_subcategory_id").val(),
            unit_id:        $(this).find(".edit_unit_id").val(),
            variant_id:     $(this).find(".edit_barcode_select").val(),
            quantity:       $(this).find(".edit_qty").val(),
            price:          $(this).find(".edit_price").val(),
        });
    });

    $.ajax({
        url: "/purchase-orders/update",     // change to "/gift-purchase-orders/update" in giftPurchaseOrder.js
        method: "POST",
        data: {
            _token:       $('meta[name="csrf-token"]').attr('content'),
            id:           $("#edit_po_id").val(),
            supplier_id:  $("#edit_supplier_id").val(),
            country_id:   $("#edit_country_id").val(),
            warehouse_id: $("#edit_warehouse_id").val(),
            items:        items,
        },
        success: function () {
            $("#editPOModal").modal('hide');
            Swal.fire("Updated", "PO Updated Successfully", "success");
            location.reload();
        },
        error: function (xhr) {
            Swal.fire("Error", xhr.responseJSON?.message || "Failed", "error");
        }
    });
});

// ================= CLEAR VALIDATION ON CHANGE =================

$(document).on("change", "#country_id", function () { $("#country-error").remove(); });
$(document).on("change", "#supplier_id", function () { $("#supplier-error").remove(); });
$(document).on("change", "#warehouse_id", function () { $("#warehouse-error").remove(); });

$(document).on("change", "#edit_country_id", function () { $("#edit-country-error").remove(); });
$(document).on("change", "#edit_supplier_id", function () { $("#edit-supplier-error").remove(); });
$(document).on("change", "#edit_warehouse_id", function () { $("#edit-warehouse-error").remove(); });

$(document).on("change keyup", ".barcode_select, .qty, .price", function () {
    $(this).removeClass("is-invalid");
    $(this).next(".invalid-feedback").remove();
});

$(document).on("change keyup", ".edit_barcode_select, .edit_qty, .edit_price", function () {
    $(this).removeClass("is-invalid");
    $(this).next(".invalid-feedback").remove();
});


// ================= EDIT COUNTRY → SUPPLIER + WAREHOUSE =================

$('#edit_country_id').change(function () {
    let id = $(this).val();

    $('#edit_supplier_id').html('<option value="">Select Country First</option>').prop('disabled', true);
    $('#edit_warehouse_id').html('<option value="">Select</option>');

    if (!id) return;

    $.get('/get-suppliers', { country_id: id, is_gift: 0 }, function (suppliers) {

        if (suppliers.length === 0) {
            $('#edit_supplier_id').html('<option value="">No suppliers for this country</option>').prop('disabled', true);
        } else {
            let options = '<option value="">Select Supplier</option>';
            suppliers.forEach(s => {
                options += `<option value="${s.id}">${s.name}</option>`;
            });
            $('#edit_supplier_id').html(options).prop('disabled', false);
        }
    });

    $.get('/get-warehouses', { country_id: id }, function (data) {
        $('#edit_warehouse_id').html('<option value="">Select</option>');
        data.forEach(w => {
            $('#edit_warehouse_id').append(`<option value="${w.id}">${w.warehouse_name}</option>`);
        });
    });
});


// ================= CANCEL BATCH =================

$(document).on("click", "#cancelBatchBtn", function () {
    let $select = $("#batchEntryModal").data("select");
    let prev    = $("#batchEntryModal").data("prev-status");
    if ($select) $select.val(prev);

    let instance = $("#batchEntryModal").data("bs-instance");
    if (instance) instance.hide();
    else $("#batchEntryModal").modal('hide');
});


// ================= BATCH SUBMIT =================

$(document).on("submit", "#batchEntryForm", function (e) {

    e.preventDefault();

    let batches = [];
    $("#batch_items_body tr").each(function () {
        batches.push({
            item_id:            $(this).find(".batch_item_id").val(),
            batch_no:           $(this).find(".batch_no").val(),
            manufacturing_date: $(this).find(".mfg_date").val(),
            expiry_date:        $(this).find(".exp_date").val(),
            quantity:           $(this).find(".recv_qty").val()
        });
    });

    $("#submitBatchBtn").attr("disabled", true).text("Saving...");

    $.ajax({
        url: "/purchase-orders/receive-batches",
        method: "POST",
        data: {
            _token:  $('meta[name="csrf-token"]').attr('content'),
            po_id:   $("#batch_po_id").val(),
            batches: batches
        },
        success: function () {
            $("#submitBatchBtn").removeAttr("disabled").text("Confirm Received");
            let instance = $("#batchEntryModal").data("bs-instance");
            if (instance) instance.hide();
            Swal.fire("Success", "Stock received and batches saved!", "success");
            location.reload();
        },
        error: function (xhr) {
            $("#submitBatchBtn").removeAttr("disabled").text("Confirm Received");
            Swal.fire("Error", xhr.responseJSON?.message || "Failed to save batches", "error");
        }
    });
});