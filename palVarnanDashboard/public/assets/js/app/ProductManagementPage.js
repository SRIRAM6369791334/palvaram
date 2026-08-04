
// ─── Flatpickr init ──────────────────────────────────────────────────────────
document.querySelectorAll('.flatpickr').forEach(el => flatpickr(el, { dateFormat: 'Y-m-d' }));

// ─── Tab switching ───────────────────────────────────────────────────────────
document.getElementById('mgmtTabs').addEventListener('click', e => {
    const tab = e.target.closest('.mgmt-tab');
    if (!tab) return;
    document.querySelectorAll('.mgmt-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    tab.classList.add('active');
    document.getElementById('panel-' + tab.dataset.tab).classList.add('active');
});

// ─── Resolution selector ─────────────────────────────────────────────────────
function selectResolution(context, type, el) {
    const parent = el.closest('.card-body') || el.closest('.tab-panel');
    parent.querySelectorAll('.res-opt').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    parent.querySelectorAll('.res-form').forEach(f => f.classList.remove('active'));
    const form = document.getElementById(`${context}-form-${type}`);
    if (form) form.classList.add('active');
}
window.selectResolution = selectResolution;

// ─── Product search dropdown helper ─────────────────────────────────────────
function initProductSearch(inputId, dropdownId, hiddenId) {
    const input    = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    if (!input || !dropdown) return;

    input.addEventListener('input', () => {
        const q = input.value.toLowerCase();
        let hasResult = false;
        dropdown.querySelectorAll('.product-dropdown-item').forEach(item => {
            const match = item.dataset.name.toLowerCase().includes(q);
            item.style.display = match ? '' : 'none';
            if (match) hasResult = true;
        });
        dropdown.classList.toggle('show', q.length > 0 && hasResult);
    });

    dropdown.querySelectorAll('.product-dropdown-item').forEach(item => {
        item.addEventListener('click', () => {
            input.value = item.dataset.name;
            if (hiddenId) document.getElementById(hiddenId).value = item.dataset.id;
            dropdown.classList.remove('show');
        });
    });

    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
}

initProductSearch('returnDiffProductSearch',  'returnDiffProductDropdown',  'returnDiffProductId');
initProductSearch('returnCompProductSearch',  'returnCompProductDropdown',  null);
initProductSearch('damagedDiffProductSearch', 'damagedDiffProductDropdown', null);
initProductSearch('expiredDiffProductSearch', 'expiredDiffProductDropdown', null);

// ═══════════════════════════════════════════════════════════════════
// RETURN TAB
// ═══════════════════════════════════════════════════════════════════

let allReturnOrders = window.returnOrders || [];
let filteredReturnOrders = [...allReturnOrders];
let selectedReturnOrderId = null;

function buildReturnRows(data) {
    return data.map((o, i) => [
        i + 1,
        o.order_id,
        o.billing_name ?? '—',
        o.billing_phone ?? '—',
        gridjs.html(`<span class="badge bg-${o.payment_status === 'paid' ? 'success' : 'warning'}">${o.payment_status}</span>`),
        gridjs.html(`<span class="badge bg-warning text-dark">${o.status}</span>`),
        '₹' + parseFloat(o.total_amount ?? 0).toFixed(2),
        gridjs.html(`
            <button class="btn btn-sm btn-outline-warning" onclick="openReturnResolve('${o.order_id}')">
                <i class="bx bx-revision me-1"></i>Resolve
            </button>
        `),
    ]);
}

const gridReturn = new gridjs.Grid({
    columns: ['S.No','Order ID','Customer','Phone','Payment','Status','Amount',{ name:'Action', sort:false }],
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: buildReturnRows(filteredReturnOrders),
    style: tableStyle(),
});
gridReturn.render(document.getElementById('returnOrdersTable'));

document.getElementById('btnSearchReturnOrder').addEventListener('click', () => {
    const q = document.getElementById('returnOrderSearch').value.toLowerCase();
    filteredReturnOrders = q
        ? allReturnOrders.filter(o =>
            (o.order_id ?? '').toLowerCase().includes(q) ||
            (o.billing_name ?? '').toLowerCase().includes(q)
          )
        : [...allReturnOrders];
    gridReturn.updateConfig({ data: buildReturnRows(filteredReturnOrders) }).forceRender();
});

window.openReturnResolve = function(orderId) {
    selectedReturnOrderId = orderId;
    const order = allReturnOrders.find(o => o.order_id === orderId);
    document.getElementById('resolveOrderId').textContent = orderId;
    const itemsHtml = order?.items?.length
        ? `<table class="table table-sm table-bordered">
                <thead><tr><th>Product</th><th>Variant</th><th>Qty</th><th>Price</th></tr></thead>
                <tbody>${order.items.map(it => `
                    <tr>
                        <td>${it.product_name}</td>
                        <td>${it.variant_name ?? '—'}</td>
                        <td>${it.quantity}</td>
                        <td>₹${parseFloat(it.price).toFixed(2)}</td>
                    </tr>`).join('')}
                </tbody>
           </table>`
        : `<p class="text-muted">No item details found for this order.</p>`;
    document.getElementById('returnItemsList').innerHTML = itemsHtml;
    document.getElementById('returnResolveForm').style.display = 'block';
    document.getElementById('returnResolveForm').scrollIntoView({ behavior: 'smooth' });
};

document.getElementById('btnCancelResolve').addEventListener('click', () => {
    document.getElementById('returnResolveForm').style.display = 'none';
    selectedReturnOrderId = null;
});

document.getElementById('btnSubmitReturn').addEventListener('click', () => {
    const selected = document.querySelector('#returnResolutionOpts .res-opt.selected');
    if (!selected) { alert('Please select a resolution type.'); return; }
    const resolution = selected.dataset.res;

    const payload = {
        order_id: selectedReturnOrderId,
        resolution,
        same_qty:       document.getElementById('returnSameQty')?.value,
        diff_product_id: document.getElementById('returnDiffProductId')?.value,
        diff_qty:       document.getElementById('returnDiffQty')?.value,
        wallet_amount:  document.getElementById('returnWalletAmt')?.value,
        _token: window.csrfToken,
    };

    fetch('/product-management/return/resolve', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
        body: JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert('Return resolved successfully!');
            document.getElementById('returnResolveForm').style.display = 'none';
        } else {
            alert('Error: ' + (res.message ?? 'Something went wrong'));
        }
    })
    .catch(() => alert('Network error. Please try again.'));
});

// ═══════════════════════════════════════════════════════════════════
// CANCELLED TAB
// ═══════════════════════════════════════════════════════════════════

let allCancelledOrders = window.cancelledOrders || [];

function statusColor(s) {
    const m = { cancelled:'danger', pending:'warning', processing:'info', delivered:'success', out_for_delivery:'primary' };
    return m[s] ?? 'secondary';
}

function buildCancelledRows(data) {
    return data.map((o, i) => {
        const canCancel = ['pending','processing'].includes(o.status);
        return [
            i + 1,
            o.order_id,
            o.billing_name ?? '—',
            '₹' + parseFloat(o.total_amount ?? 0).toFixed(2),
            o.payment_method ?? '—',
            gridjs.html(`<span class="badge bg-${statusColor(o.status)}">${o.status}</span>`),
            o.reason ?? '—',
            gridjs.html(canCancel
                ? `<button class="btn btn-sm btn-danger" onclick="openCancelModal('${o.order_id}','${o.total_amount}')">
                       <i class="bx bx-x me-1"></i>Cancel
                   </button>`
                : `<span class="badge bg-dark">Already Cancelled</span>`
            ),
        ];
    });
}

const gridCancelled = new gridjs.Grid({
    columns: ['S.No','Order ID','Customer','Amount','Payment',{ name:'Status', sort:false },'Reason',{ name:'Action', sort:false }],
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: buildCancelledRows(allCancelledOrders),
    style: tableStyle(),
});
gridCancelled.render(document.getElementById('cancelledOrdersTable'));

document.getElementById('cancelStatusFilter').addEventListener('change', function() {
    const v = this.value;
    const filtered = v === 'all' ? allCancelledOrders : allCancelledOrders.filter(o => o.status === v);
    gridCancelled.updateConfig({ data: buildCancelledRows(filtered) }).forceRender();
});

document.getElementById('cancelOrderSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const filtered = q
        ? allCancelledOrders.filter(o =>
            (o.order_id ?? '').toLowerCase().includes(q) ||
            (o.billing_name ?? '').toLowerCase().includes(q))
        : allCancelledOrders;
    gridCancelled.updateConfig({ data: buildCancelledRows(filtered) }).forceRender();
});

window.openCancelModal = function(orderId, amount) {
    document.getElementById('cancelOrderId').value = orderId;
    document.getElementById('cancelOrderIdDisplay').value = orderId;
    document.getElementById('cancelRefundAmount').value = parseFloat(amount ?? 0).toFixed(2);
    new bootstrap.Modal(document.getElementById('cancelOrderModal')).show();
};

document.getElementById('btnConfirmCancel').addEventListener('click', () => {
    const payload = {
        order_id:      document.getElementById('cancelOrderId').value,
        reason:        document.getElementById('cancelReason').value,
        refund_method: document.getElementById('cancelRefundMethod').value,
        refund_amount: document.getElementById('cancelRefundAmount').value,
    };
    fetch('/product-management/cancel', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
        body: JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(res => {
        bootstrap.Modal.getInstance(document.getElementById('cancelOrderModal')).hide();
        alert(res.success ? 'Order cancelled successfully!' : 'Error: ' + res.message);
    })
    .catch(() => alert('Network error.'));
});

// ═══════════════════════════════════════════════════════════════════
// DAMAGED TAB
// ═══════════════════════════════════════════════════════════════════

let allDamagedStock = window.damagedStock || [];
let selectedDamagedStockId = null;

function buildDamagedRows(data) {
    return data.map((s, i) => [
        i + 1,
        s.country_name  ?? '—',
        s.warehouse_name ?? '—',
        s.branch_name   ?? '—',
        s.productname   ?? '—',
        s.varient_name  ?? '—',
        s.availablestock ?? 0,
        s.overallstock   ?? 0,
        s.last_stockupdate_date ?? '—',
        gridjs.html(`
            <button class="btn btn-sm btn-outline-danger" onclick="openDamagedResolve(${s.id},'${s.productname}')">
                <i class="bx bx-error-circle me-1"></i>Resolve
            </button>
        `),
    ]);
}

const gridDamaged = new gridjs.Grid({
    columns: ['S.No','Country','Warehouse','Branch','Product','Variant','Available','Overall','Last Updated',{ name:'Action', sort:false }],
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: buildDamagedRows(allDamagedStock),
    style: tableStyle(),
});
gridDamaged.render(document.getElementById('damagedStockTable'));

document.getElementById('btnSearchDamagedStock').addEventListener('click', () => {
    const q = document.getElementById('damagedStockSearch').value.toLowerCase();
    const filtered = q ? allDamagedStock.filter(s =>
        (s.productname ?? '').toLowerCase().includes(q) ||
        (s.varient_name ?? '').toLowerCase().includes(q)
    ) : allDamagedStock;
    gridDamaged.updateConfig({ data: buildDamagedRows(filtered) }).forceRender();
});

window.openDamagedResolve = function(stockId, productName) {
    selectedDamagedStockId = stockId;
    document.getElementById('damagedProductName').textContent = productName;
    document.getElementById('damagedResolveForm').style.display = 'block';
    document.getElementById('damagedResolveForm').scrollIntoView({ behavior: 'smooth' });
    // default to none
    const noneOpt = document.querySelector('#panel-damaged .res-opt[data-res="none"]');
    if (noneOpt) selectResolution('damaged', 'none', noneOpt);
};

document.getElementById('btnCancelDamagedResolve').addEventListener('click', () => {
    document.getElementById('damagedResolveForm').style.display = 'none';
});

document.getElementById('btnSubmitDamaged').addEventListener('click', () => {
    const qty = document.getElementById('damagedQty').value;
    const reason = document.getElementById('damagedReason').value;
    if (!qty || !reason) { alert('Please fill Damaged Qty and Reason.'); return; }

    const selected = document.querySelector('#panel-damaged .res-opt.selected');
    const resolution = selected?.dataset.res ?? 'none';

    fetch('/product-management/damaged/log', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
        body: JSON.stringify({
            stock_id: selectedDamagedStockId,
            damaged_qty: qty,
            reason,
            damaged_date: document.getElementById('damagedDate').value,
            resolution,
        }),
    })
    .then(r => r.json())
    .then(res => {
        alert(res.success ? 'Damage logged and stock deducted!' : 'Error: ' + res.message);
        if (res.success) document.getElementById('damagedResolveForm').style.display = 'none';
    })
    .catch(() => alert('Network error.'));
});

// ═══════════════════════════════════════════════════════════════════
// EXPIRED TAB
// ═══════════════════════════════════════════════════════════════════

let allExpiredBatches = window.expiredBatches || [];
let selectedBatchId = null;

function buildExpiredRows(data) {
    return data.map((b, i) => {
        const daysAgo = Math.floor((Date.now() - new Date(b.expiry_date)) / 86400000);
        return [
            i + 1,
            b.batch_no ?? '—',
            b.productname ?? '—',
            b.manufacturing_date ?? '—',
            b.expiry_date ?? '—',
            gridjs.html(`<span class="badge bg-danger">${daysAgo}d ago</span>`),
            b.quantity ?? 0,
            b.remaining_qty ?? 0,
            gridjs.html(`
                <button class="btn btn-sm btn-outline-primary" onclick="openExpiredResolve(${b.id},'${b.batch_no}')">
                    <i class="bx bx-time me-1"></i>Resolve
                </button>
            `),
        ];
    });
}

const gridExpired = new gridjs.Grid({
    columns: ['S.No','Batch No','Product','Mfg Date','Expiry Date',{ name:'Expired', sort:false },'Total Qty','Remaining',{ name:'Action', sort:false }],
    pagination: { limit: 10 },
    sort: true,
    search: true,
    data: buildExpiredRows(allExpiredBatches),
    style: tableStyle(),
});
gridExpired.render(document.getElementById('expiredBatchesTable'));

window.openExpiredResolve = function(batchId, batchNo) {
    selectedBatchId = batchId;
    document.getElementById('expiredBatchNo').textContent = batchNo;
    document.getElementById('expiredResolveForm').style.display = 'block';
    document.getElementById('expiredResolveForm').scrollIntoView({ behavior: 'smooth' });
    const noneOpt = document.querySelector('#panel-expired .res-opt[data-res="none"]');
    if (noneOpt) selectResolution('expired', 'none', noneOpt);
};

document.getElementById('btnCancelExpiredResolve').addEventListener('click', () => {
    document.getElementById('expiredResolveForm').style.display = 'none';
});

document.getElementById('btnSubmitExpired').addEventListener('click', () => {
    const qty = document.getElementById('expiredQty').value;
    if (!qty) { alert('Please enter the qty to write off.'); return; }

    const selected = document.querySelector('#panel-expired .res-opt.selected');
    const resolution = selected?.dataset.res ?? 'none';

    fetch('/product-management/expired/writeoff', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
        body: JSON.stringify({
            batch_id: selectedBatchId,
            expired_qty: qty,
            disposal: document.getElementById('expiredDisposal').value,
            resolution,
        }),
    })
    .then(r => r.json())
    .then(res => {
        alert(res.success ? 'Batch written off and stock deducted!' : 'Error: ' + res.message);
        if (res.success) document.getElementById('expiredResolveForm').style.display = 'none';
    })
    .catch(() => alert('Network error.'));
});

// ─── Shared GridJS style helper ──────────────────────────────────────────────
function tableStyle() {
    return {
        table: { border: '1px solid #ccc' },
        th: {
            'background-color': 'rgba(0,0,0,0.06)',
            color: '#000',
            'border-bottom': '3px solid #ccc',
            'text-align': 'center',
            'border-right': '0.5px solid #ccc',
            'font-size': '12px',
        },
        td: {
            'text-align': 'center',
            'border-right': '0.5px solid #ccc',
            'border-bottom': '0.5px solid #ccc',
            'font-size': '13px',
        },
    };
}