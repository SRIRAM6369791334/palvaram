/**
 * LocationPage.js
 * Handles the 3-step drill-down: Country → Warehouse → Branch
 * Matches the project's existing JS pattern (no Alpine, no framework).
 */

(function () {

    /* ── State ─────────────────────────────────────────── */
    let selectedCountry   = null;
    let selectedWarehouse = null;

    /* ── DOM refs ───────────────────────────────────────── */
    const step1          = document.getElementById('loc-step-1');
    const step2          = document.getElementById('loc-step-2');
    const step3          = document.getElementById('loc-step-3');

    const countryGrid    = document.getElementById('country-grid');
    const warehouseGrid  = document.getElementById('warehouse-grid');
    const branchGrid     = document.getElementById('branch-grid');

    const warehouseLabel = document.getElementById('warehouse-label');
    const branchLabel    = document.getElementById('branch-label');

    const trail          = document.getElementById('loc-trail');

    /* ── Helpers ────────────────────────────────────────── */
    function showStep(n) {
        step1.style.display = n === 1 ? 'block' : 'none';
        step2.style.display = n === 2 ? 'block' : 'none';
        step3.style.display = n === 3 ? 'block' : 'none';
    }

    function setLoading(grid) {
        grid.innerHTML = '<div class="loc-loading"><div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Loading...</div>';
    }

    function setEmpty(grid, msg) {
        grid.innerHTML = `<div class="loc-empty">${msg}</div>`;
    }

    function updateTrail() {
        let html = `<span class="crumb ${selectedCountry ? '' : 'active'}" id="trail-country" style="cursor:${selectedCountry ? 'pointer' : 'default'}" onclick="LocationPage.goTo(1)">Countries</span>`;

        if (selectedCountry) {
            html += `<span class="sep">›</span>
                     <span class="crumb ${selectedWarehouse ? '' : 'active'}" style="cursor:${selectedWarehouse ? 'pointer' : 'default'}" onclick="LocationPage.goTo(2)">
                         Warehouses <span style="color:#adb5bd">(${selectedCountry.name})</span>
                     </span>`;
        }

        if (selectedWarehouse) {
            html += `<span class="sep">›</span>
                     <span class="crumb active">
                         Branches <span style="color:#adb5bd">(${selectedWarehouse.warehouse_name})</span>
                     </span>`;
        }

        trail.innerHTML = html;
    }

    /* ── Card builders ──────────────────────────────────── */
    function countryCard(c) {
        const div = document.createElement('div');
        div.className = 'loc-card';
        div.innerHTML = `
            <div class="card-icon country-icon">🌍</div>
            <div class="card-name">${c.name}</div>
            <div class="card-sub">${c.iso_code ?? '—'}</div>
            <span class="card-badge badge-country">${c.currency ?? 'N/A'}</span>
        `;
        div.addEventListener('click', () => onCountrySelect(c));
        return div;
    }

    function warehouseCard(w) {
        const div = document.createElement('div');
        div.className = 'loc-card';
        div.innerHTML = `
            <div class="card-icon warehouse-icon">🏭</div>
            <div class="card-name">${w.warehouse_name}</div>
            <div class="card-sub">${w.url ?? '—'}</div>
            <span class="card-badge badge-warehouse">Warehouse</span>
        `;
        div.addEventListener('click', () => onWarehouseSelect(w));
        return div;
    }

    function branchCard(b) {
        const div = document.createElement('div');
        div.className = 'loc-card selected';
        div.innerHTML = `
            <div class="card-icon branch-icon">📍</div>
            <div class="card-name">${b.branch_name}</div>
            <div class="card-sub">${b.url ?? '—'}</div>
            <span class="card-badge badge-branch">Branch</span>
        `;
        return div;
    }

    /* ── Fetch & render ─────────────────────────────────── */
    function loadCountries() {
        setLoading(countryGrid);
        fetch(window.locationRoutes.countries)
            .then(r => r.json())
            .then(data => {
                countryGrid.innerHTML = '';
                if (!data.length) { setEmpty(countryGrid, 'No countries found.'); return; }
                data.forEach(c => countryGrid.appendChild(countryCard(c)));
            })
            .catch(() => setEmpty(countryGrid, 'Failed to load countries.'));
    }

    function onCountrySelect(country) {
        selectedCountry = country;
        selectedWarehouse = null;

        warehouseLabel.textContent = `Warehouses in ${country.name}`;
        updateTrail();
        showStep(2);
        setLoading(warehouseGrid);

        fetch(`${window.locationRoutes.warehouses}/${country.id}`)
            .then(r => r.json())
            .then(data => {
                warehouseGrid.innerHTML = '';
                if (!data.length) { setEmpty(warehouseGrid, 'No warehouses found for this country.'); return; }
                data.forEach(w => warehouseGrid.appendChild(warehouseCard(w)));
            })
            .catch(() => setEmpty(warehouseGrid, 'Failed to load warehouses.'));
    }

    function onWarehouseSelect(warehouse) {
        selectedWarehouse = warehouse;

        branchLabel.textContent = `Branches of ${warehouse.warehouse_name}`;
        updateTrail();
        showStep(3);
        setLoading(branchGrid);

        fetch(`${window.locationRoutes.branches}/${warehouse.id}`)
            .then(r => r.json())
            .then(data => {
                branchGrid.innerHTML = '';
                if (!data.length) { setEmpty(branchGrid, 'No branches found for this warehouse.'); return; }
                data.forEach(b => branchGrid.appendChild(branchCard(b)));
            })
            .catch(() => setEmpty(branchGrid, 'Failed to load branches.'));
    }

    /* ── Back navigation ────────────────────────────────── */
    document.getElementById('back-to-countries').addEventListener('click', () => {
        selectedCountry   = null;
        selectedWarehouse = null;
        updateTrail();
        showStep(1);
    });

    document.getElementById('back-to-warehouses').addEventListener('click', () => {
        selectedWarehouse = null;
        updateTrail();
        showStep(2);
    });

    /* ── Public API (used by trail clicks) ──────────────── */
    window.LocationPage = {
        goTo: function (step) {
            if (step === 1) {
                selectedCountry   = null;
                selectedWarehouse = null;
                updateTrail();
                showStep(1);
            } else if (step === 2 && selectedCountry) {
                selectedWarehouse = null;
                updateTrail();
                showStep(2);
            }
        }
    };

    /* ── Init ───────────────────────────────────────────── */
    showStep(1);
    updateTrail();
    loadCountries();

})();