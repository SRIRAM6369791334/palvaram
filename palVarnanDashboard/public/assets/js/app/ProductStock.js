/* ═══════════════════════════════════════════════════════════════
   ProductStock.js — Premium Stock Dashboard
   ═══════════════════════════════════════════════════════════════ */

const PER_PAGE = 10;
let whPage = 1;
let stPage = 1;

/* ── Data ── */
const whData = window.stocks      || [];
const stData = window.storeStocks || [];

/* ─────────────────────────────────────────
   Helpers
───────────────────────────────────────── */
function productLabel(r) {
    let name = r.productname || '';
    if (r.varient_name && r.varient_name !== 'Standard') {
        name += ` (${r.varient_name})`;
    }
    if (r.value && r.value !== 'null' && r.value !== '') {
        name += ` ${r.value}${r.unit_short_name || ''}`;
    }
    return name;
}

function stockNum(val) {
    const n = parseInt(val) || 0;
    if (n <= 3)  return `<span class="sk-num danger">${n.toLocaleString()}</span>`;
    if (n <= 20) return `<span class="sk-num low">${n.toLocaleString()}</span>`;
    return `<span class="sk-num ok">${n.toLocaleString()}</span>`;
}

function badge(text, color) {
    return `<span class="sk-badge ${color}">${text || '—'}</span>`;
}

function sno(n) {
    return `<span class="sk-sno">${n}</span>`;
}

/* ─────────────────────────────────────────
   Tab switcher
───────────────────────────────────────── */
function switchTab(tab) {
    ['warehouse', 'store'].forEach(t => {
        document.getElementById(`tab-${t === 'warehouse' ? 'wh' : 'st'}`).classList.toggle('active', t === tab);
        document.getElementById(`pane-${t}`).classList.toggle('active', t === tab);
    });
}

/* ─────────────────────────────────────────
   Pagination renderer
───────────────────────────────────────── */
function renderPages(containerId, totalPages, current, onClickFn) {
    const el = document.getElementById(containerId);
    let html = '';

    // Prev
    html += `<button class="sk-page-btn" onclick="${onClickFn}(${current - 1})" ${current <= 1 ? 'disabled' : ''}>
        <i class="mdi mdi-chevron-left"></i>
    </button>`;

    // Pages
    for (let p = 1; p <= totalPages; p++) {
        if (
            p === 1 || p === totalPages ||
            (p >= current - 1 && p <= current + 1)
        ) {
            html += `<button class="sk-page-btn${p === current ? ' active' : ''}" onclick="${onClickFn}(${p})">${p}</button>`;
        } else if (p === current - 2 || p === current + 2) {
            html += `<button class="sk-page-btn" disabled style="border:none;background:none;color:#ccc">…</button>`;
        }
    }

    // Next
    html += `<button class="sk-page-btn" onclick="${onClickFn}(${current + 1})" ${current >= totalPages ? 'disabled' : ''}>
        <i class="mdi mdi-chevron-right"></i>
    </button>`;

    el.innerHTML = html;
}

/* ─────────────────────────────────────────
   Warehouse Stock Table
───────────────────────────────────────── */
function goWhPage(p) {
    whPage = p;
    renderWarehouse();
}

function renderWarehouse() {
    const q = (document.getElementById('wh-search').value || '').toLowerCase();

    const filtered = whData.filter(r =>
        productLabel(r).toLowerCase().includes(q) ||
        (r.category_name   || '').toLowerCase().includes(q) ||
        (r.country_name    || '').toLowerCase().includes(q) ||
        (r.warehouse_name  || '').toLowerCase().includes(q)
    );

    const total      = filtered.length;
    const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
    if (whPage > totalPages) whPage = 1;

    const start = (whPage - 1) * PER_PAGE;
    const slice = filtered.slice(start, start + PER_PAGE);

    const tbody = document.getElementById('wh-tbody');

    if (!slice.length) {
        tbody.innerHTML = `
            <tr><td colspan="8">
                <div class="sk-empty">
                    <i class="mdi mdi-package-variant-closed"></i>
                    <p>No records match your search</p>
                </div>
            </td></tr>`;
    } else {
        tbody.innerHTML = slice.map((r, i) => `
            <tr>
                <td>${sno(start + i + 1)}</td>
                <td>${r.country_name || '—'}</td>
                <td>${r.warehouse_name || '—'}</td>
                <td>${badge(r.category_name, 'blue')}</td>
                <td><span class="sk-product">${productLabel(r)}</span></td>
                <td class="right">${stockNum(r.availablestock)}</td>
                <td class="right"><span class="sk-num" style="color:#999">${parseInt(r.salestock) || 0}</span></td>
                
            </tr>`).join('');
    }

    const from = total ? start + 1 : 0;
    const to   = Math.min(start + PER_PAGE, total);
    document.getElementById('wh-info').textContent =
        `Showing ${from}–${to} of ${total} items`;

    renderPages('wh-pages', totalPages, whPage, 'goWhPage');

    /* Update stat cards */
    const totalAvail = whData.reduce((a, r) => a + (parseInt(r.availablestock) || 0), 0);
    const lowCount   = whData.filter(r => (parseInt(r.availablestock) || 0) <= 3).length;
    document.getElementById('stat-warehouse').textContent = totalAvail.toLocaleString();
    document.getElementById('stat-low').textContent       = lowCount;
}

/* ─────────────────────────────────────────
   Store Stock Table
───────────────────────────────────────── */
function goStPage(p) {
    stPage = p;
    renderStore();
}

function renderStore() {
    const q = (document.getElementById('st-search').value || '').toLowerCase();

    const filtered = stData.filter(r =>
        productLabel(r).toLowerCase().includes(q) ||
        (r.branch_name    || '').toLowerCase().includes(q) ||
        (r.warehouse_name || '').toLowerCase().includes(q) ||
        (r.category_name  || '').toLowerCase().includes(q) ||
        (r.country_name   || '').toLowerCase().includes(q)
    );

    const total      = filtered.length;
    const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
    if (stPage > totalPages) stPage = 1;

    const start = (stPage - 1) * PER_PAGE;
    const slice = filtered.slice(start, start + PER_PAGE);

    const tbody = document.getElementById('st-tbody');

    if (!slice.length) {
        tbody.innerHTML = `
            <tr><td colspan="7">
                <div class="sk-empty">
                    <i class="mdi mdi-store-off-outline"></i>
                    <p>No store stock found</p>
                </div>
            </td></tr>`;
    } else {
        tbody.innerHTML = slice.map((r, i) => `
            <tr>
                <td>${sno(start + i + 1)}</td>
                <td>${r.country_name || '—'}</td>
                <td>${r.warehouse_name || '—'}</td>
                <td>${badge(r.branch_name, 'amber')}</td>
                <td>${badge(r.category_name, 'blue')}</td>
                <td><span class="sk-product">${productLabel(r)}</span></td>
                <td class="right">${stockNum(r.availablestock)}</td>
            </tr>`).join('');
    }

    const from = total ? start + 1 : 0;
    const to   = Math.min(start + PER_PAGE, total);
    document.getElementById('st-info').textContent =
        `Showing ${from}–${to} of ${total} items`;

    renderPages('st-pages', totalPages, stPage, 'goStPage');

    /* Update stat card */
    const totalStore = stData.reduce((a, r) => a + (parseInt(r.availablestock) || 0), 0);
    document.getElementById('stat-store').textContent = totalStore.toLocaleString();
}

/* ─────────────────────────────────────────
   Re-render after AJAX updates
───────────────────────────────────────── */
function refreshWarehouse(updatedStocks) {
    whData.length = 0;
    updatedStocks.forEach(s => whData.push(s));
    renderWarehouse();
}

function refreshStore(updatedStore) {
    stData.length = 0;
    updatedStore.forEach(s => stData.push(s));
    renderStore();
}

/* ─────────────────────────────────────────
   Modal events
───────────────────────────────────────── */
$(document).on('click', '.edit_btn', function () {
    $('#productid').val($(this).attr('data-productid'));
});

$(document).on('click', '.edit_btn1', function () {
    $('#productid1').val($(this).attr('data-productid1'));
    $('#availa_quantity1').val($(this).attr('data-availa'));
});

/* ─────────────────────────────────────────
   Validation
───────────────────────────────────────── */
const addValidator = new JustValidate('#addStockForm', { validateBeforeSubmitting: true });
addValidator
    .addField('#stock_quantity', [
        { rule: 'required',       errorMessage: 'Stock quantity is required' },
        { rule: 'maxLength',      value: 5,        errorMessage: 'Max 5 digits allowed' },
        { rule: 'customRegexp',   value: /^[0-9]+$/, errorMessage: 'Numbers only' },
    ])
    .onSuccess(e => {
        $('.edit_submit_btn').attr('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i>Saving…');
        addStockFormSubmit(e);
    });

const addValidator1 = new JustValidate('#addStockForm1', { validateBeforeSubmitting: true });
addValidator1
    .addField('#stock_quantity1', [
        { rule: 'required',     errorMessage: 'Stock quantity is required' },
        { rule: 'maxLength',    value: 5,        errorMessage: 'Max 5 digits allowed' },
        { rule: 'customRegexp', value: /^[0-9]+$/, errorMessage: 'Numbers only' },
        {
            validator: value => parseInt(value) <= parseInt($('#availa_quantity1').val()),
            errorMessage: 'Cannot exceed available stock',
        },
    ])
    .onSuccess(e => {
        $('.edit1_submit_btn').attr('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i>Saving…');
        addStockForm1Submit(e);
    });

/* ─────────────────────────────────────────
   AJAX — Add Stock
───────────────────────────────────────── */
function addStockFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: window.updateStackUrl,
        method: 'POST',
        dataType: 'json',
        data: formdata,
        processData: false,
        contentType: false,
        success(response) {
            $('#addStockForm')[0].reset();
            $('#editProductModal').modal('hide');
            document.body.style.overflowY = 'scroll';
            refreshWarehouse(response.stocks);
            $('.edit_submit_btn').removeAttr('disabled').html('<i class="mdi mdi-plus me-1"></i> Add Quantity');
            Swal.fire({ icon: 'success', title: 'Stock Added', text: 'Records updated successfully.', timer: 2000, showConfirmButton: false });
        },
        error(xhr) {
            $('.edit_submit_btn').removeAttr('disabled').html('<i class="mdi mdi-plus me-1"></i> Add Quantity');
            Swal.fire('Error', xhr.responseJSON?.error || 'Something went wrong.', 'error');
        },
    });
}

/* ─────────────────────────────────────────
   AJAX — Reduce Stock
───────────────────────────────────────── */
function addStockForm1Submit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: 'reduceStock',
        method: 'POST',
        dataType: 'json',
        data: formdata,
        processData: false,
        contentType: false,
        success(response) {
            $('#addStockForm1')[0].reset();
            $('#updateProductModal').modal('hide');
            document.body.style.overflowY = 'scroll';
            refreshWarehouse(response.stocks);
            $('.edit1_submit_btn').removeAttr('disabled').html('<i class="mdi mdi-minus me-1"></i> Reduce Quantity');
            Swal.fire({ icon: 'success', title: 'Stock Reduced', text: 'Records updated successfully.', timer: 2000, showConfirmButton: false });
        },
        error(xhr) {
            $('.edit1_submit_btn').removeAttr('disabled').html('<i class="mdi mdi-minus me-1"></i> Reduce Quantity');
            Swal.fire('Error', xhr.responseJSON?.error || 'Something went wrong.', 'error');
        },
    });
}

/* ─────────────────────────────────────────
   Create Stock Form (if used)
───────────────────────────────────────── */
$(document).on('submit', '#createStockForm', function (e) {
    e.preventDefault();
    const formdata = new FormData(this);
    $.ajax({
        url: 'storeStock',
        method: 'POST',
        data: formdata,
        processData: false,
        contentType: false,
        success(res) {
            if (res.status === 'exists') {
                Swal.fire({
                    title: 'Already Exists',
                    text: 'Stock already exists. Do you want to add more?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Add',
                }).then(result => {
                    if (result.isConfirmed) {
                        const stockQty = $("#createStockForm [name='stock_quantity']").val();
                        $.ajax({
                            url: window.updateStackUrl,
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                _token: $("input[name='_token']").first().val(),
                                productid: res.stock_id,
                                stock_quantity: stockQty,
                            },
                            success(response) {
                                $('#createStockModal').modal('hide');
                                document.body.style.overflowY = 'scroll';
                                refreshWarehouse(response.stocks);
                                Swal.fire({ icon: 'success', title: 'Updated', text: 'Stock updated successfully.', timer: 2000, showConfirmButton: false });
                            },
                            error(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.error || 'Update failed.', 'error');
                            },
                        });
                    }
                });
                return;
            }
            Swal.fire({ icon: 'success', title: 'Created', text: 'Stock created successfully.', timer: 2000, showConfirmButton: false });
            location.reload();
        },
    });
});

/* ─────────────────────────────────────────
   Dropdown cascade (create modal)
───────────────────────────────────────── */
$(document).ready(function () {
    if (window.allCountries) {
        $('#country_id').append('<option disabled selected>Select Country</option>');
        window.allCountries.forEach(c => $('#country_id').append(`<option value="${c.id}">${c.name}</option>`));
    }
    if (window.allCategories) {
        $('#category_id').append('<option disabled selected>Select Category</option>');
        window.allCategories.forEach(c => $('#category_id').append(`<option value="${c.id}">${c.category_name}</option>`));
    }
    if (window.allSubcategories) {
        $('#subcategory_id').append('<option disabled selected>Select Subcategory</option>');
        window.allSubcategories.forEach(s => $('#subcategory_id').append(`<option value="${s.id}">${s.subcategory_name}</option>`));
    }
    if (window.allProducts) {
        $('#product_id').append('<option disabled selected>Select Product</option>');
        window.allProducts.forEach(p => $('#product_id').append(`<option value="${p.id}">${p.product_name}</option>`));
    }

    /* Initial render */
    renderWarehouse();
    renderStore();
});

$(document).on('change', '#country_id', function () {
    const countryId = $(this).val();
    $('#warehouse_id').html('<option disabled selected>Select Warehouse</option>');
    $('#branch_id').html('<option disabled selected>Select Branch</option>');
    $('#category_id').html('<option disabled selected>Select Category</option>');
    $('#subcategory_id').html('<option disabled selected>Select Subcategory</option>');
    $('#product_id').html('<option disabled selected>Select Product</option>');
    $('#variant_id').html('<option disabled selected>Select Variant</option>');
    window.allWarehouses.filter(w => w.country_id == countryId)
        .forEach(w => $('#warehouse_id').append(`<option value="${w.id}">${w.warehouse_name}</option>`));
    window.allCategories.filter(c => c.country_id == countryId)
        .forEach(c => $('#category_id').append(`<option value="${c.id}">${c.category_name}</option>`));
});

$(document).on('change', '#warehouse_id', function () {
    const warehouseId = $(this).val();
    $('#branch_id').html('<option disabled selected>Select Branch</option>');
    window.allBranches.filter(b => b.warehouse_id == warehouseId)
        .forEach(b => $('#branch_id').append(`<option value="${b.id}">${b.branch_name}</option>`));
});

$(document).on('change', '#category_id', function () {
    const categoryId = $(this).val();
    $('#subcategory_id').html('<option disabled selected>Select Subcategory</option>');
    window.allSubcategories.filter(s => parseInt(s.category_name) === parseInt(categoryId))
        .forEach(s => $('#subcategory_id').append(`<option value="${s.id}">${s.subcategory_name}</option>`));
});

$(document).on('change', '#subcategory_id', function () {
    const subcategoryId = $(this).val();
    $('#product_id').html('<option disabled selected>Select Product</option>');
    window.allProducts.filter(p => p.subcategory_id == subcategoryId)
        .forEach(p => $('#product_id').append(`<option value="${p.id}">${p.product_name}</option>`));
});

$(document).on('change', '#product_id', function () {
    const productId = $(this).val();
    $('#variant_id').html('<option disabled selected>Select Variant</option>');
    if (!window.allVariants || !window.allVariants.length) return;
    const variants = window.allVariants.filter(v => v.product_id == productId);
    if (!variants.length) {
        $('#variant_id').append('<option disabled>No variants found</option>');
        return;
    }
    variants.forEach(v => {
        const label = v.varient_name && v.varient_name !== 'Standard'
            ? `${v.varient_name} (${v.value})` : 'Standard';
        $('#variant_id').append(`<option value="${v.id}">${label}</option>`);
    });
});

/* ─────────────────────────────────────────
   Transfer modal (if applicable)
───────────────────────────────────────── */
$(document).on('click', '.transfer_btn', function () {
    const countryId = $(this).attr('data-countryid');
    $('#transfer_current_country').text($(this).attr('data-country') || '—');
    $('#transfer_current_warehouse').text($(this).attr('data-warehouse') || '—');
    $('#transfer_current_branch').text($(this).attr('data-branch') || '—');
    $('#transfer_current_stock').text($(this).attr('data-availablestock') || '0');
    $('#transfer_from_stock_id').val($(this).attr('data-stockid'));
    $('#transfer_available_stock').val($(this).attr('data-availablestock'));
    $('#transfer_to_warehouse').html('<option value="" disabled selected>Select Warehouse</option>');
    $('#transfer_to_branch').html('<option value="" disabled selected>Select Branch</option>');
    if (countryId && window.allWarehouses) {
        window.allWarehouses.filter(w => w.country_id == countryId)
            .forEach(w => $('#transfer_to_warehouse').append(`<option value="${w.id}">${w.warehouse_name}</option>`));
    }
});

$(document).on('change', '#transfer_to_warehouse', function () {
    const warehouseId = $(this).val();
    $('#transfer_to_branch').html('<option value="" disabled selected>Select Branch</option>');
    if (warehouseId && window.allBranches) {
        window.allBranches.filter(b => b.warehouse_id == warehouseId)
            .forEach(b => $('#transfer_to_branch').append(`<option value="${b.id}">${b.branch_name}</option>`));
    }
});

$(document).on('submit', '#transferStockForm', function (e) {
    e.preventDefault();
    const quantity  = parseInt($('#transfer_quantity').val());
    const available = parseInt($('#transfer_available_stock').val());
    const toWarehouse = $('#transfer_to_warehouse').val();
    const toBranch    = $('#transfer_to_branch').val();

    if (!toWarehouse || !toBranch) return Swal.fire('Error', 'Please select warehouse and branch.', 'warning');
    if (!quantity || quantity <= 0) return Swal.fire('Error', 'Please enter a valid quantity.', 'warning');
    if (quantity > available)       return Swal.fire('Error', 'Transfer quantity cannot exceed available stock.', 'warning');

    $('.transfer_submit_btn').attr('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i>Transferring…');

    $.ajax({
        url: 'transferStock',
        method: 'POST',
        dataType: 'json',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success(response) {
            $('.transfer_submit_btn').removeAttr('disabled').text('Transfer Stock');
            $('#transferStockForm')[0].reset();
            $('#transferStockModal').modal('hide');
            document.body.style.overflowY = 'scroll';
            if (response.stocks)      refreshWarehouse(response.stocks);
            if (response.storeStocks) refreshStore(response.storeStocks);
            Swal.fire({ icon: 'success', title: 'Transferred', text: 'Stock transferred successfully.', timer: 2000, showConfirmButton: false });
        },
        error(xhr) {
            $('.transfer_submit_btn').removeAttr('disabled').text('Transfer Stock');
            Swal.fire('Error', xhr.responseJSON?.message || 'Transfer failed.', 'error');
        },
    });
});