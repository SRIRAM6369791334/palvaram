let allStock = window.stockData || [];
let currentType = "all";
let activeFilters = { country: "", warehouse: "", branch: "", category: "" };

// ─── Hierarchy select cascade ────────────────────────────────────────────────

const selCountry   = document.getElementById("filterCountry");
const selWarehouse = document.getElementById("filterWarehouse");
const selBranch    = document.getElementById("filterBranch");
const selCategory  = document.getElementById("filterCategory");

function cascadeWarehouse() {
    const cid = selCountry.value;
    Array.from(selWarehouse.options).forEach(o => {
        o.hidden = cid ? (o.dataset.country !== cid && o.value !== "") : false;
    });
    if (selWarehouse.options[selWarehouse.selectedIndex]?.hidden) {
        selWarehouse.value = "";
    }
    cascadeBranch();
}

function cascadeBranch() {
    const wid = selWarehouse.value;
    Array.from(selBranch.options).forEach(o => {
        o.hidden = wid ? (o.dataset.warehouse !== wid && o.value !== "") : false;
    });
    if (selBranch.options[selBranch.selectedIndex]?.hidden) {
        selBranch.value = "";
    }
}

selCountry.addEventListener("change", () => {
    activeFilters.country = selCountry.value;
    activeFilters.warehouse = "";
    activeFilters.branch = "";
    selWarehouse.value = "";
    selBranch.value = "";
    cascadeWarehouse();
    reRenderStock();
});

selWarehouse.addEventListener("change", () => {
    activeFilters.warehouse = selWarehouse.value;
    activeFilters.branch = "";
    selBranch.value = "";
    cascadeBranch();
    reRenderStock();
});

selBranch.addEventListener("change", () => {
    activeFilters.branch = selBranch.value;
    reRenderStock();
});

selCategory.addEventListener("change", () => {
    activeFilters.category = selCategory.value;
    reRenderStock();
});

document.getElementById("btnResetFilters").addEventListener("click", () => {
    selCountry.value = "";
    selWarehouse.value = "";
    selBranch.value = "";
    selCategory.value = "";
    activeFilters = { country: "", warehouse: "", branch: "", category: "" };
    cascadeWarehouse();
    reRenderStock();
});


const TODAY = new Date();
TODAY.setHours(0, 0, 0, 0);

function daysSince(dateStr) {
    if (!dateStr) return 0;
    const d = new Date(dateStr);
    return Math.floor((TODAY - d) / 86400000);
}

function daysUntil(dateStr) {
    if (!dateStr) return null;
    const d = new Date(dateStr);
    d.setHours(0, 0, 0, 0);
    return Math.floor((d - TODAY) / 86400000);
}

function getSoonestBatch(batches) {
    if (!batches || !batches.length) return null;
    let soonestBatch = null, soonestDays = null;
    batches.forEach(b => {
        const d = daysUntil(b.expiry_date);
        if (d !== null && (soonestDays === null || d < soonestDays)) {
            soonestDays = d;
            soonestBatch = b;
        }
    });
    return soonestBatch ? { batch: soonestBatch, days: soonestDays } : null;
}

function escapeHtml(str) {
    return String(str ?? "").replace(/[&<>"']/g, m => ({
        "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
    }[m]));
}

function getShelfLifeStatus(batch) {
    const daysLeft = daysUntil(batch.expiry_date);
    if (daysLeft === null) return null;

    if (!batch.manufacturing_date) {
      
        return {
            daysLeft,
            remainingPct: null,
            isNearExpiry: daysLeft >= 0 && daysLeft <= 30,
        };
    }

    const mfg = new Date(batch.manufacturing_date); mfg.setHours(0, 0, 0, 0);
    const exp = new Date(batch.expiry_date);        exp.setHours(0, 0, 0, 0);
    const totalLifeDays = Math.round((exp - mfg) / 86400000);

    if (totalLifeDays <= 0) {
        return { daysLeft, remainingPct: null, isNearExpiry: daysLeft >= 0 && daysLeft <= 30 };
    }

    const remainingPct = (daysLeft / totalLifeDays) * 100;
    return {
        daysLeft,
        remainingPct,
        isNearExpiry: daysLeft >= 0 && remainingPct <= 25,
    };
}

function getStockType(row) {
    const types = [];
    if ((row.availablestock ?? 0) > 0) types.push("available");
    if ((row.salestock ?? 0) > 0)      types.push("reserved");
    if ((row.damaged_qty ?? 0) > 0)    types.push("damaged");

    // Expired: has a batch where expiry_date < today
    if (row.batches && row.batches.some(b => daysUntil(b.expiry_date) !== null && daysUntil(b.expiry_date) < 0)) {
        types.push("expired");
    }

    // Aging: stock not moved in > 30 days
    const age = daysSince(row.last_stockupdate_date);
    if (age > 30 && age <= 90) types.push("aging");

    // Dead: no movement for > 90 days OR zero available
    if (age > 90 || (row.overallstock ?? 0) === 0) types.push("dead");

    return types.length ? types : ["available"];
}

function getFilteredStock() {
    return allStock.filter(row => {
        // Hierarchy filters
        if (activeFilters.country   && String(row.country_id)   !== activeFilters.country)   return false;
        if (activeFilters.warehouse && String(row.warehouse_id)  !== activeFilters.warehouse)  return false;
        if (activeFilters.branch    && String(row.branch_id)     !== activeFilters.branch)     return false;
        if (activeFilters.category  && String(row.category_id)   !== activeFilters.category)   return false;

        // Stock type tab
        if (currentType !== "all") {
            const types = getStockType(row);
            return types.includes(currentType);
        }
        return true;
    });
}


function agingBadge(dateStr) {
    const d = daysSince(dateStr);
    if (d <= 7)       return `<span class="aging-badge aging-fresh">Fresh (${d}d)</span>`;
    if (d <= 30)      return `<span class="aging-badge aging-warning">${d}d old</span>`;
    if (d <= 90)      return `<span class="aging-badge aging-old">${d}d old</span>`;
    return             `<span class="aging-badge aging-dead">${d}d (Dead)</span>`;
}

function stockBar(available, overall) {
    if (!overall) return `<span class="text-muted">—</span>`;
    const pct = Math.min(100, Math.round((available / overall) * 100));
    let color = "#198754";
    if (pct < 50) color = "#fd7e14";
    if (pct < 20) color = "#dc3545";
    return `
        <div style="display:inline-flex;align-items:center;gap:6px;">
           
            <span style="font-size:11px;color:#666;">${pct}%</span>
        </div>`;
}

function expiryBadge(batches) {
    const nearest = getSoonestBatch(batches);
    if (!nearest) return `<span class="text-muted">—</span>`;

    const { days } = nearest;
    if (days < 0)       return `<span class="badge bg-danger">Expired ${Math.abs(days)}d ago</span>`;
    if (days <= 7)      return `<span class="badge bg-warning text-dark">Expires in ${days}d</span>`;
    return                  `<span class="badge bg-success">Expires in ${days}d</span>`;
}

// ─── Offers column ──────────────────────────────────────────────────────────

function offerColumn(row) {
    const nearest = getSoonestBatch(row.batches);
    if (!nearest) return `<span class="text-muted">—</span>`;

    const status = getShelfLifeStatus(nearest.batch);
    if (!status || !status.isNearExpiry) return `<span class="text-muted">—</span>`;

    const existing  = nearest.batch.offer_price;
    const btnLabel  = existing ? `Edit Offer (₹${existing})` : `Set Offer`;
    const btnClass  = existing ? "btn-outline-success" : "btn-outline-warning";

    const pctNote = status.remainingPct !== null
        ? `<div class="small text-muted mt-1">${Math.max(0, Math.round(status.remainingPct))}% shelf life left</div>`
        : "";

    return `
        <div>
            <button type="button" class="btn btn-sm ${btnClass} btn-open-offer-modal"
                data-batch-id="${nearest.batch.id}"
                data-product="${escapeHtml(row.productname)}"
                data-variant="${escapeHtml(row.varient_name)}"
                data-batchno="${escapeHtml(nearest.batch.batch_no)}"
                data-expiry="${nearest.batch.expiry_date ?? ''}"
                data-purchase="${row.purchase_price ?? ''}"
                data-mrp="${row.mrp_price ?? ''}"
                data-variant-offer="${row.variant_offer_price ?? ''}"
                data-offer="${existing ?? ''}">
                <i class="bx bx-tag me-1"></i>${btnLabel}
            </button>
            ${pctNote}
        </div>`;
}

function typeBadges(row) {
    return getStockType(row).map(t => {
        const map = {
            available: `<span class="badge bg-success">Available</span>`,
            reserved:  `<span class="badge bg-primary">Reserved</span>`,
            damaged:   `<span class="badge bg-danger">Damaged</span>`,
            expired:   `<span class="badge bg-warning text-dark">Expired</span>`,
            aging:     `<span class="badge" style="background:#6f42c1;">Aging</span>`,
            dead:      `<span class="badge bg-dark">Dead</span>`,
        };
        return map[t] || "";
    }).join(" ");
}

// ─── Build rows ───────────────────────────────────────────────────────────────

function buildRows(data) {
    return data.map((row, i) => [
        i + 1,
        row.country_name    ?? "—",
        row.warehouse_name  ?? "—",
        row.branch_name     ?? "—",
        row.productname     ?? "—",
        row.varient_name    ?? "—",
        gridjs.html(`<span class="fw-semibold text-success">${row.availablestock ?? 0}</span>`),
        gridjs.html(`<span class="fw-semibold text-primary">${row.salestock ?? 0}</span>`),
        gridjs.html(`<span class="fw-semibold text-danger">${row.damaged_qty ?? 0}</span>`),
        gridjs.html(stockBar(row.availablestock, row.overallstock)),
        gridjs.html(expiryBadge(row.batches)),
        gridjs.html(agingBadge(row.last_stockupdate_date)),
        gridjs.html(typeBadges(row)),
        gridjs.html(offerColumn(row)),
        gridjs.html(`
            <a href="stock/detail/${row.id}" target="_blank">
                <button type="button" class="btn btn-primary btn-sm">
                    <i class="bx bx-show me-1"></i>View
                </button>
            </a>
        `),
    ]);
}

// ─── GridJS setup ─────────────────────────────────────────────────────────────

const columns = [
    { name: "S.No",      width: "55px"  },
    { name: "Country"                   },
    { name: "Warehouse"                 },
    { name: "Store "            },
    { name: "Product"                   },
    { name: "Variant"                   },
    { name: "Available",  sort: true    },
    { name: "Reserved",   sort: true    },
    { name: "Damaged",    sort: true    },
    { name: "Health %",   sort: false   },
    { name: "Expiry",     sort: false   },
    { name: "Stock Age",  sort: false   },
    { name: "Status",     sort: false   },
    { name: "Offers",     sort: false   },
    { name: "Action",     sort: false   },
];

const gridStock = new gridjs.Grid({
    columns,
    pagination: { limit: 15 },
    sort: true,
    search: true,
    data: buildRows(getFilteredStock()),
    style: {
        table: { border: "1px solid #ccc" },
        th: {
            "background-color": "rgba(0,0,0,0.06)",
            color: "#000",
            "border-bottom": "3px solid #ccc",
            "text-align": "center",
            "border-right": "0.5px solid #ccc",
            "font-size": "12px",
        },
        td: {
            "text-align": "center",
            "border-right": "0.5px solid #ccc",
            "border-bottom": "0.5px solid #ccc",
            "font-size": "13px",
        },
    },
});

gridStock.render(document.getElementById("table-stock"));

// ─── Re-render ────────────────────────────────────────────────────────────────

function reRenderStock() {
    gridStock
        .updateConfig({ data: buildRows(getFilteredStock()) })
        .forceRender();
}

// ─── Filter tab clicks ────────────────────────────────────────────────────────

document.getElementById("stockFilterTabs").addEventListener("click", e => {
    const tab = e.target.closest(".filter-tab");
    if (!tab) return;
    document.querySelectorAll(".filter-tab").forEach(t => t.classList.remove("active"));
    tab.classList.add("active");
    currentType = tab.dataset.type;
    reRenderStock();
});

// ─── Offer modal ────────────────────────────────────────────────────────────

const offerModalEl   = document.getElementById("setOfferModal");
const offerModal      = new bootstrap.Modal(offerModalEl);
const offerModalError = document.getElementById("offerModalError");

function updateDiscountBadge() {
    const variantOffer = parseFloat(document.getElementById("offerModalVariantOffer").dataset.raw || "0");
    const newOffer      = parseFloat(document.getElementById("offerModalPriceInput").value);
    const badge          = document.getElementById("offerModalDiscountBadge");

    if (!variantOffer || isNaN(newOffer) || newOffer <= 0) {
        badge.textContent = "—";
        badge.className = "fw-semibold text-muted";
        return;
    }

    const pct = Math.round(((variantOffer - newOffer) / variantOffer) * 100);
    if (pct > 0) {
        badge.textContent = `${pct}% lower than selling offer`;
        badge.className = "fw-semibold text-success";
    } else if (pct < 0) {
        badge.textContent = `${Math.abs(pct)}% higher than selling offer`;
        badge.className = "fw-semibold text-danger";
    } else {
        badge.textContent = "Same as selling offer";
        badge.className = "fw-semibold text-muted";
    }
}

function formatDate(dateStr) {
    if (!dateStr) return "—";
    const d = new Date(dateStr);
    if (isNaN(d)) return "—";
    const dd = String(d.getDate()).padStart(2, "0");
    const mm = String(d.getMonth() + 1).padStart(2, "0");
    const yyyy = d.getFullYear();
    return `${dd}/${mm}/${yyyy}`;
}

document.getElementById("table-stock").addEventListener("click", function (e) {
    const btn = e.target.closest(".btn-open-offer-modal");
    if (!btn) return;

    document.getElementById("offerModalProduct").textContent = btn.dataset.product || "—";
    document.getElementById("offerModalVariant").textContent = btn.dataset.variant || "—";
    document.getElementById("offerModalBatch").textContent   = btn.dataset.batchno || "—";
    document.getElementById("offerModalExpiry").textContent  = formatDate(btn.dataset.expiry);

    const purchase     = btn.dataset.purchase;
    const mrp          = btn.dataset.mrp;
    const variantOffer = btn.dataset.variantOffer;

    document.getElementById("offerModalPurchasePrice").textContent =
        purchase ? `₹${parseFloat(purchase).toFixed(2)}` : "—";

    const mrpEl = document.getElementById("offerModalMrpPrice");
    mrpEl.textContent = mrp ? `₹${parseFloat(mrp).toFixed(2)}` : "—";
    mrpEl.dataset.raw = mrp || "0";

    // ← REPLACE THE OLD SINGLE-LINE VERSION WITH THIS:
    const variantOfferEl = document.getElementById("offerModalVariantOffer");
    variantOfferEl.textContent = variantOffer ? `₹${parseFloat(variantOffer).toFixed(2)}` : "—";
    variantOfferEl.dataset.raw = variantOffer || "0";

    document.getElementById("offerModalBatchId").value    = btn.dataset.batchId;
    document.getElementById("offerModalPriceInput").value = btn.dataset.offer || "";

    offerModalError.classList.add("d-none");
    offerModalError.textContent = "";
    updateDiscountBadge();

    offerModal.show();
});

document.getElementById("offerModalPriceInput").addEventListener("input", updateDiscountBadge);

document.getElementById("btnSaveOfferModal").addEventListener("click", function () {
    const batchId = document.getElementById("offerModalBatchId").value;
    const price   = parseFloat(document.getElementById("offerModalPriceInput").value);

    if (isNaN(price) || price < 0) {
        offerModalError.textContent = "Please enter a valid offer price.";
        offerModalError.classList.remove("d-none");
        return;
    }

    const saveBtn = this;
    saveBtn.disabled = true;

    fetch(`/stock/batches/${batchId}/offer-price`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ offer_price: price }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            allStock.forEach(row => {
                (row.batches || []).forEach(b => {
                    if (String(b.id) === String(batchId)) b.offer_price = data.offer_price;
                });
            });
            reRenderStock();
            offerModal.hide();
        } else {
            offerModalError.textContent = data.message || "Failed to save offer price.";
            offerModalError.classList.remove("d-none");
        }
    })
    .catch(() => {
        offerModalError.textContent = "Failed to save offer price.";
        offerModalError.classList.remove("d-none");
    })
    .finally(() => { saveBtn.disabled = false; });
});

// Initial cascade setup
cascadeWarehouse();