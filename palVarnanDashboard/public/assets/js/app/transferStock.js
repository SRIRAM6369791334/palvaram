// ================= TAB =================

function switchTransferTab(tab) {
    ['store', 'warehouse'].forEach(t => {
        document.getElementById(`tab-${t}`).classList.toggle('active', t === tab);
        document.getElementById(`pane-${t}`).classList.toggle('active', t === tab);
    });

    // Sync modal radio button with tab
    if (tab === 'warehouse') {
        $("#type_warehouse").prop('checked', true);
        $("#section_store_transfer").hide();
        $("#section_warehouse_transfer").show();
        initWTCountries();
    } else {
        $("#type_store").prop('checked', true);
        $("#section_store_transfer").show();
        $("#section_warehouse_transfer").hide();
    }
}

// ================= GRIDS =================

const storeTransfers     = window.transfers.filter(t => t.from_branch && t.to_branch);
const warehouseTransfers = window.transfers.filter(t => !t.from_branch && !t.to_branch);

// Store→Store grid
new gridjs.Grid({
    columns: ["S.NO", "Transfer ID", "From", "To", "Product", "Quantity", "Date", "Status", 
              { name: "Action", sort: false }],
    data: storeTransfers.map((t, i) => [
        i + 1,
        `TR-${String(t.id).padStart(5, '0')}`,
        `${t.from_warehouse} - ${t.from_branch}`,
        `${t.to_warehouse} - ${t.to_branch}`,
        t.productname,
        t.quantity,
        new Date(t.created_at).toLocaleDateString(),
        gridjs.html(statusBadge(t.status)),
        gridjs.html(actionButtons(t)),
    ])
}).render(document.getElementById("store-transfer-table"));

// Warehouse→Warehouse grid
new gridjs.Grid({
    columns: ["S.NO", "Transfer ID", "From Warehouse", "To Warehouse", "Product", "Quantity", 
              "Date", "Status", { name: "Action", sort: false }],
    data: warehouseTransfers.map((t, i) => [
        i + 1,
        `TR-${String(t.id).padStart(5, '0')}`,
        t.from_warehouse,
        t.to_warehouse,
        t.productname,
        t.quantity,
        new Date(t.created_at).toLocaleDateString(),
        gridjs.html(statusBadge(t.status)),
        gridjs.html(actionButtons(t)),
    ])
}).render(document.getElementById("warehouse-transfer-table"));


// ================= INIT =================

$(document).ready(function () {
    $("#transfer_country").html('<option disabled selected>Select Country</option>');
    window.allCountries.forEach(c => {
        $("#transfer_country").append(`<option value="${c.id}">${c.name}</option>`);
    });
});



// ================= SENDER FLOW =================

// Country → Warehouse
$(document).on("change", "#transfer_country", function () {

    let countryId = $(this).val();

    $("#transfer_from_warehouse").html('<option disabled selected>Select Warehouse</option>');
    $("#transfer_from_branch").html('<option disabled selected>Select Branch</option>');
    $("#transfer_product").html('<option disabled selected>Select Product</option>');

    let warehouses = window.allWarehouses.filter(w => w.country_id == countryId);

    warehouses.forEach(w => {
        $("#transfer_from_warehouse").append(`<option value="${w.id}">${w.warehouse_name}</option>`);
    });

});

// Warehouse → Branch
$(document).on("change", "#transfer_from_warehouse", function () {

    let warehouseId = $(this).val();

    $("#transfer_from_branch").html('<option disabled selected>Select Branch</option>');

    let branches = window.allBranches.filter(b => b.warehouse_id == warehouseId);

    branches.forEach(b => {
        $("#transfer_from_branch").append(`<option value="${b.id}">${b.branch_name}</option>`);
    });

});

// When sender branch changes → set receiver warehouse + load receiver branches
$(document).on("change", "#transfer_from_branch", function () {

    let branchId    = $(this).val();
    let warehouseId = $("#transfer_from_warehouse").val();

    $("#transfer_product").html('<option disabled selected>Select Product</option>');

    // Set receiver warehouse to same as sender (hidden input)
    $("#transfer_to_warehouse").val(warehouseId);

    // Load receiver branches — same warehouse, exclude sender branch
    let branches = window.allBranches.filter(b =>
        b.warehouse_id == warehouseId && b.id != branchId
    );

    $("#transfer_to_branch").html('<option disabled selected>Select Store</option>');
    branches.forEach(b => {
        $("#transfer_to_branch").append(
            `<option value="${b.id}">${b.branch_name}</option>`
        );
    });

    // Load products for sender
    let stocks = window.allStocks.filter(s =>
        s.branch_id == branchId && s.warehouse_id == warehouseId
    );

    stocks.forEach(s => {
        $("#transfer_product").append(
            `<option value="${s.product_id}"
                data-variant="${s.variant_id ?? ''}"
                data-available="${s.availablestock}">
                ${s.productname}
            </option>`
        );
    });
});

// Product selected → update available + hidden fields
$(document).on("change", "#transfer_product", function () {
    let selected  = $(this).find(':selected');
    let available = selected.data('available');
    let variantId = selected.data('variant');

    $("#transfer_current_stock").text(available);
    $("#transfer_available_stock").val(available);
    $("#transfer_variant_id").val(variantId);  // hidden input
});





$(document).on("change", "#transfer_country", function () {

    let countryId = $(this).val();
    let fromWarehouseId = $("#transfer_from_warehouse").val();

    $("#transfer_to_warehouse").html('<option disabled selected>Select Warehouse</option>');
    $("#transfer_to_branch").html('<option disabled selected>Select Branch</option>');

    let warehouses = window.allWarehouses.filter(w => 
        w.country_id == countryId && w.id != fromWarehouseId // 🔥 EXCLUDE sender warehouse
    );

    warehouses.forEach(w => {
        $("#transfer_to_warehouse").append(
            `<option value="${w.id}">${w.warehouse_name}</option>`
        );
    });

});


// $(document).on("submit", "#transferStockForm", function (e) {
//     e.preventDefault();

//     let type = $("input[name='transfer_type']:checked").val();

//     if (type === 'warehouse') {
//         submitWarehouseTransfer();
//     } else {
//         submitStoreTransfer();
//     }
// });

$(document).on("click", ".transfer_submit_btn", function () {
    let type = $("input[name='transfer_type']:checked").val();
    if (type === 'warehouse') {
        submitWarehouseTransfer();
    } else {
        submitStoreTransfer();
    }
});

function submitStoreTransfer() {
    let qty           = parseInt($("#transfer_quantity").val());
    let available     = parseInt($("#transfer_available_stock").val());
    let fromWarehouse = $("#transfer_from_warehouse").val();
    let fromBranch    = $("#transfer_from_branch").val();
    let toBranch      = $("#transfer_to_branch").val();

    if (!fromWarehouse || !fromBranch) {
        Swal.fire("Error", "Select sender details", "warning"); return;
    }
    if (!toBranch) {
        Swal.fire("Error", "Select receiver store", "warning"); return;
    }
    if (fromBranch == toBranch) {
        Swal.fire("Error", "Cannot transfer to same store", "warning"); return;
    }
    if (!qty || qty <= 0) {
        Swal.fire("Error", "Enter valid quantity", "warning"); return;
    }
    if (qty > available) {
        Swal.fire("Error", "Exceeds available stock", "warning"); return;
    }

    let formData = new FormData();
    formData.append('_token',            $('meta[name="csrf-token"]').attr('content'));
    formData.append('from_warehouse_id', fromWarehouse);
    formData.append('from_branch_id',    fromBranch);
    formData.append('to_branch_id',      toBranch);
    formData.append('product_id',        $("#transfer_product").val());
    formData.append('variant_id',        $("#transfer_variant_id").val() || '');
    formData.append('transfer_quantity', qty);

    $(".transfer_submit_btn").attr("disabled", true).text("Transferring...");

    $.ajax({
        url: "transferStock",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function () {
            $(".transfer_submit_btn").removeAttr("disabled").text("Transfer Stock");
            $("#transferStockForm")[0].reset();
            $("#transferStockModal").modal('hide');
            Swal.fire("Success", "Stock Transferred", "success");
            location.reload();
        },
        error: function (xhr) {
            $(".transfer_submit_btn").removeAttr("disabled").text("Transfer Stock");
            Swal.fire("Error", xhr.responseJSON?.message || "Failed", "error");
        }
    });
}

// ── Toggle transfer type ──
$(document).on("change", "input[name='transfer_type']", function () {
    let type = $(this).val();
    if (type === 'warehouse') {
        $("#section_store_transfer").hide();
        $("#section_warehouse_transfer").show();
        initWTCountries();
    } else {
        $("#section_store_transfer").show();
        $("#section_warehouse_transfer").hide();
    }
});

function initWTCountries() {
    $("#wt_country").html('<option disabled selected>Select Country</option>');
    window.allCountries.forEach(c => {
        $("#wt_country").append(`<option value="${c.id}">${c.name}</option>`);
    });
}

// Country → Source Warehouses
$(document).on("change", "#wt_country", function () {
    let countryId = $(this).val();

    $("#wt_from_warehouse").html('<option disabled selected>Select Warehouse</option>');
    $("#wt_to_warehouse").html('<option disabled selected>Select Warehouse</option>');
    $("#wt_product").html('<option disabled selected>Select Product</option>');
    $("#wt_current_stock").text(0);

    window.allWarehouses
        .filter(w => w.country_id == countryId)
        .forEach(w => {
            $("#wt_from_warehouse").append(
                `<option value="${w.id}">${w.warehouse_name}</option>`
            );
        });
});

// Source Warehouse → Products + load destination warehouses
$(document).on("change", "#wt_from_warehouse", function () {
    let fromWarehouseId = $(this).val();
    let countryId = $("#wt_country").val();

    $("#wt_product").html('<option disabled selected>Select Product</option>');
    $("#wt_current_stock").text(0);
    $("#wt_available_stock").val('');
    $("#wt_variant_id").val('');

    // Warehouse-level stocks only (no branch)
    let stocks = window.allWarehouseStocks.filter(s =>
        s.warehouse_id == fromWarehouseId
    );

    stocks.forEach(s => {
        $("#wt_product").append(
            `<option value="${s.product_id}"
                data-variant="${s.variant_id ?? ''}"
                data-available="${s.availablestock}">
                ${s.productname}
            </option>`
        );
    });

    // Destination: same country, exclude source warehouse
    $("#wt_to_warehouse").html('<option disabled selected>Select Warehouse</option>');
    window.allWarehouses
        .filter(w => w.country_id == countryId && w.id != fromWarehouseId)
        .forEach(w => {
            $("#wt_to_warehouse").append(
                `<option value="${w.id}">${w.warehouse_name}</option>`
            );
        });
});

// Product → show available stock
$(document).on("change", "#wt_product", function () {
    let selected  = $(this).find(':selected');
    $("#wt_current_stock").text(selected.data('available'));
    $("#wt_available_stock").val(selected.data('available'));
    $("#wt_variant_id").val(selected.data('variant'));
});

// ── Submit ──
function submitWarehouseTransfer() {
    let fromWarehouse = $("#wt_from_warehouse").val();
    let toWarehouse   = $("#wt_to_warehouse").val();
    let productId     = $("#wt_product").val();
    let variantId     = $("#wt_variant_id").val();
    let qty           = parseInt($("#wt_quantity").val());
    let available     = parseInt($("#wt_available_stock").val());

    if (!fromWarehouse) { Swal.fire("Error", "Select source warehouse", "warning"); return; }
    if (!toWarehouse)   { Swal.fire("Error", "Select destination warehouse", "warning"); return; }
    if (!productId)     { Swal.fire("Error", "Select a product", "warning"); return; }
    if (!qty || qty <= 0) { Swal.fire("Error", "Enter valid quantity", "warning"); return; }
    if (qty > available)  { Swal.fire("Error", "Exceeds available stock", "warning"); return; }

    let formData = new FormData();
    formData.append('_token',            $('meta[name="csrf-token"]').attr('content'));
    formData.append('from_warehouse_id', fromWarehouse);
    formData.append('to_warehouse_id',   toWarehouse);
    formData.append('product_id',        productId);
    formData.append('variant_id',        variantId || '');
    formData.append('transfer_quantity', qty);

    $(".transfer_submit_btn").attr("disabled", true).text("Transferring...");

    $.ajax({
        url: "warehouseTransfer",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function () {
            $(".transfer_submit_btn").removeAttr("disabled").text("Transfer Stock");
            $("#transferStockForm")[0].reset();
            $("#transferStockModal").modal('hide');
            $("#section_store_transfer").show();
            $("#section_warehouse_transfer").hide();
            Swal.fire("Success", "Warehouse stock transferred successfully", "success");
            location.reload();
        },
        error: function (xhr) {
            $(".transfer_submit_btn").removeAttr("disabled").text("Transfer Stock");
            Swal.fire("Error", xhr.responseJSON?.message || "Transfer failed", "error");
        }
    });
}


function fetchReceiverStoreStock() {
    const productId   = $("#transfer_product").val();
    const warehouseId = $("#transfer_from_warehouse").val(); // same warehouse for store→store
    const branchId    = $("#transfer_to_branch").val();
    const variantId   = $("#transfer_variant_id").val() || '';

    if (!productId || !warehouseId || !branchId) {
        $("#transfer_to_branch_stock").html(
            `<span class="stock-badge unknown"><i class="mdi mdi-store-outline"></i> Select all fields</span>`
        );
        return;
    }

    $("#transfer_to_branch_stock").html(
        `<span class="stock-badge unknown"><i class="mdi mdi-loading mdi-spin"></i> Loading...</span>`
    );

    $.ajax({
        url: "/get-store-stocks",
        type: "GET",
        data: { product_id: productId, warehouse_id: warehouseId, branch_id: branchId, variant_id: variantId },
        success: function (data) {
            const stock      = parseInt(data.available) || 0;
            const badgeClass = stock > 10 ? 'good' : stock > 0 ? 'low' : 'none';
            const icon       = stock > 10 ? 'mdi-check-circle-outline' : stock > 0 ? 'mdi-alert-outline' : 'mdi-close-circle-outline';
            $("#transfer_to_branch_stock").html(
                `<span class="stock-badge ${badgeClass}"><i class="mdi ${icon}"></i> ${stock} units in store</span>`
            );
        },
        error: function () {
            $("#transfer_to_branch_stock").html(
                `<span class="stock-badge none"><i class="mdi mdi-alert-circle-outline"></i> Failed to load</span>`
            );
        }
    });
}

function fetchReceiverWarehouseStock() {
    const productId   = $("#wt_product").val();
    const warehouseId = $("#wt_to_warehouse").val();
    const variantId   = $("#wt_variant_id").val() || null;

    if (!productId || !warehouseId) {
        $("#wt_to_warehouse_stock").html(
            `<span class="stock-badge unknown"><i class="mdi mdi-warehouse"></i> Select all fields</span>`
        );
        return;
    }

    const match = window.allWarehouseStocks.find(s =>
        s.product_id == productId &&
        s.warehouse_id == warehouseId &&
        (variantId ? s.variant_id == variantId : !s.variant_id)
    );

    const stock      = match ? parseInt(match.availablestock) || 0 : 0;
    const badgeClass = stock > 10 ? 'good' : stock > 0 ? 'low' : 'none';
    const icon       = stock > 10 ? 'mdi-check-circle-outline' : stock > 0 ? 'mdi-alert-outline' : 'mdi-close-circle-outline';

    $("#wt_to_warehouse_stock").html(
        `<span class="stock-badge ${badgeClass}"><i class="mdi ${icon}"></i> ${stock} units in warehouse</span>`
    );
}

$(document).on("change", "#transfer_to_branch", function () {
    fetchReceiverStoreStock();
});

$(document).on("change", "#wt_to_warehouse", function () {
    fetchReceiverWarehouseStock();
});

function statusBadge(status) {
    const map = {
        pending:   'background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;',
        accepted:  'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;',
        cancelled: 'background:#fef2f2;color:#dc2626;border:1px solid #fecaca;',
    };
    const style = map[status] || map.pending;
    return `<span style="padding:4px 12px;border-radius:100px;font-size:12px;font-weight:600;${style}">${status}</span>`;
}

function actionButtons(t) {
    if (t.status !== 'pending') return '-';
    return `
        <button onclick="handleTransferAction(${t.id}, 'accept')"
            style="padding:4px 12px;border-radius:6px;font-size:12px;font-weight:600;
                   background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;cursor:pointer;margin-right:4px;">
            ✓ Accept
        </button>
        <button onclick="handleTransferAction(${t.id}, 'cancel')"
            style="padding:4px 12px;border-radius:6px;font-size:12px;font-weight:600;
                   background:#fef2f2;color:#dc2626;border:1px solid #fecaca;cursor:pointer;">
            ✗ Cancel
        </button>`;
}

function handleTransferAction(id, action) {
    const label = action === 'accept' ? 'Accept' : 'Cancel';
    const icon  = action === 'accept' ? 'question' : 'warning';

    Swal.fire({
        title: `${label} this transfer?`,
        icon,
        showCancelButton: true,
        confirmButtonText: `Yes, ${label}`,
        cancelButtonText: 'No',
        reverseButtons: true,
    }).then(result => {
        if (!result.isConfirmed) return;

        $.ajax({
            url:    `/stock-transfers/${id}/${action}`,
            method: 'POST',
            data:   { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                Swal.fire('Done', res.message, 'success').then(() => location.reload());
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Action failed', 'error');
            }
        });
    });
}