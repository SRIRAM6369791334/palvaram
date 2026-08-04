// ─── GIFT VARIANT PAGE ────────────────────────────────────────────────────────

// ─── Helpers ──────────────────────────────────────────────────────────────────

function resolveCountryName(countryId) {
    if (!countryId) return '-';
    const ids   = countryId.toString().split(',').map(id => id.trim());
    const names = ids.map(id => {
        const found = (window.countries || []).find(c => c.id == id);
        return found ? found.name : id;
    }).filter(Boolean);
    return names.join(', ') || '-';
}

function resolveName(list, id, field) {
    if (!id) return '-';
    const found = (list || []).find(item => item.id == id);
    return found ? found[field] : '-';
}

// ─── Cascading dropdown loader (AJAX) ────────────────────────────────────────

/**
 * Fires the filtered-products endpoint with current filter state,
 * then updates category / subcategory / brand / product dropdowns.
 *
 * @param {string} prefix   'add' | 'edit'
 * @param {string} changed  which dropdown changed: 'country'|'category'|'subcategory'|'brand'
 */
function loadFilteredData(prefix, changed) {
    const countryVals = $(`#${prefix}_country_select`).val() || [];

    if (!countryVals.length) {
        // Clear everything downstream
        resetDropdown(`${prefix}_category_select`,    'All Categories');
        resetDropdown(`${prefix}_subcategory_select`, 'All Sub Categories');
        resetDropdown(`${prefix}_brand_select`,       'All Brands');
        resetDropdown(`${prefix}_product_select`,     'Select Country first', true);
        return;
    }

    const payload = {
        country_ids:    countryVals,
        category_id:    changed === 'country' ? '' : $(`#${prefix}_category_select`).val(),
        subcategory_id: (changed === 'country' || changed === 'category') ? '' : $(`#${prefix}_subcategory_select`).val(),
        brand_id:       (changed === 'country') ? '' : $(`#${prefix}_brand_select`).val(),
        _token:         $('meta[name="csrf-token"]').attr('content'),
    };

    $.ajax({
        url:      window.giftVariantUrls.filteredProducts,
        method:   'GET',
        data:     payload,
        dataType: 'json',
        success: function (res) {
            // ── Categories ──
            if (changed === 'country') {
                populateSelect(
                    `${prefix}_category_select`,
                    res.categories,
                    'id', 'category_name',
                    'All Categories'
                );
            }

            // ── Sub categories ──
            if (changed === 'country' || changed === 'category') {
                populateSelect(
                    `${prefix}_subcategory_select`,
                    res.subcategories,
                    'id', 'subcategory_name',
                    'All Sub Categories'
                );
            }

            // ── Brands ──
            if (changed === 'country') {
                populateSelect(
                    `${prefix}_brand_select`,
                    res.brands,
                    'id', 'name',
                    'All Brands'
                );
            }

            // ── Products (always refresh) ──
            populateSelect(
                `${prefix}_product_select`,
                res.products,
                'id', 'product_name',
                'Select Product',
                true  // required (disabled placeholder)
            );
        },
        error: function () {
            Swal.fire('Error', 'Failed to load filtered data.', 'error');
        },
    });
}

function populateSelect(selectId, items, valField, labelField, placeholder, required = false) {
    const $sel = $(`#${selectId}`);
    $sel.empty();
    $sel.append(`<option value="" ${required ? 'disabled' : ''} selected>${placeholder}</option>`);
    (items || []).forEach(item => {
        $sel.append(`<option value="${item[valField]}">${item[labelField]}</option>`);
    });
}

function resetDropdown(selectId, placeholder, required = false) {
    const $sel = $(`#${selectId}`);
    $sel.empty();
    $sel.append(`<option value="" ${required ? 'disabled' : ''} selected>${placeholder}</option>`);
}

// ─── Validators ───────────────────────────────────────────────────────────────

const addValidator = new JustValidate('#addVariantForm', { validateBeforeSubmitting: true });
const editValidator = new JustValidate('#editVariantForm', { validateBeforeSubmitting: true });

// ─── On Page Load ─────────────────────────────────────────────────────────────

$(document).ready(function () {

    // ── Init Select2 on country selects ──
    if ($.fn.select2) {
        $('#add_country_select').select2({
            placeholder: 'Select Country',
            allowClear: true,
            multiple: true,
            dropdownParent: $('#addVariantModal'),
        });
        $('#edit_country_select').select2({
            placeholder: 'Select Country',
            allowClear: true,
            multiple: true,
            dropdownParent: $('#editVariantModal'),
        });
    }

    // Auto-select for single-country admins
    if (window.allowedCountryIds && window.allowedCountryIds.length === 1) {
        const cid = String(window.allowedCountryIds[0]);
        if ($.fn.select2) {
            $('#add_country_select').val([cid]).trigger('change');
        } else {
            $('#add_country_select').val(cid).trigger('change');
        }
    }

    // ── ADD: country change ──
    $(document).on('change', '#add_country_select', function () {
        loadFilteredData('add', 'country');
    });
    $(document).on('change', '#add_category_select', function () {
        loadFilteredData('add', 'category');
    });
    $(document).on('change', '#add_subcategory_select', function () {
        loadFilteredData('add', 'subcategory');
    });
    $(document).on('change', '#add_brand_select', function () {
        loadFilteredData('add', 'brand');
    });

    // ── EDIT: cascading changes ──
    $(document).on('change', '#edit_country_select', function () {
        loadFilteredData('edit', 'country');
    });
    $(document).on('change', '#edit_category_select', function () {
        loadFilteredData('edit', 'category');
    });
    $(document).on('change', '#edit_subcategory_select', function () {
        loadFilteredData('edit', 'subcategory');
    });
    $(document).on('change', '#edit_brand_select', function () {
        loadFilteredData('edit', 'brand');
    });

    // ── Flash sale checkbox toggles ──
    $(document).on('change', '.flash-sale-chk', function () {
        $(this).closest('.mb-3').find('.flash-date-input').toggle($(this).is(':checked'));
    });

    // ── ADD variant image preview ──
    const addPreviewBackup = $('#add_varient_preview').html();
    $('#add_varient_image').on('change', function () {
        previewImage(this, '#add_varient_preview');
    });

    // ── EDIT variant image preview ──
    $('#edit_varient_image').on('change', function () {
        const file = this.files[0];
        if (!file || !file.type.match('image.*')) return;
        const reader = new FileReader();
        reader.onload = e => {
            $('.edit_varient_preview_container').html(
                `<img src="${e.target.result}"
                    style="width:80px;height:80px;object-fit:cover;border-radius:5px;">`
            );
        };
        reader.readAsDataURL(file);
    });

    // ── Reset add modal on close ──
    $('#addVariantModal').on('hidden.bs.modal', function () {
        $('#addVariantForm')[0].reset();
        $('#add_varient_preview').html(addPreviewBackup);
        resetDropdown('add_category_select',    'All Categories');
        resetDropdown('add_subcategory_select', 'All Sub Categories');
        resetDropdown('add_brand_select',       'All Brands');
        resetDropdown('add_product_select',     'Select Country first', true);
        if ($.fn.select2 && window.allowedCountryIds && window.allowedCountryIds.length > 1) {
            $('#add_country_select').val(null).trigger('change');
        }
    });

    // ── Edit button click ──
    $(document).on('click', '.edit_btn', function () {
        const $btn = $(this);

        $('#edit_variant_id').val($btn.attr('data-variantid'));
        $('#edit_sku').val($btn.attr('data-sku'));
        $('#edit_barcode').val($btn.attr('data-barcode'));
        $('#edit_varient_name').val($btn.attr('data-varientname'));
        $('#edit_mrp_price').val($btn.attr('data-mrpprice'));
        $('#edit_offer_price').val($btn.attr('data-offerprice'));
        $('#edit_low_stock').val($btn.attr('data-lowstock'));
        $('#edit_size_select').val($btn.attr('data-sizeid') || '');
        $('#edit_color_select').val($btn.attr('data-colorid') || '');

        const gst = $btn.attr('data-gst');
        if (gst !== '') $('#edit_product_gst').val(gst);

        $('#edit_hot_deals').prop('checked',   $btn.attr('data-hotdeals')   == 1);
        $('#edit_popular_prod').prop('checked', $btn.attr('data-popularprod') == 1);

        const flashSale = $btn.attr('data-flashsale') == 1;
        $('#edit_flash_sale_chk').prop('checked', flashSale);
        $('#edit_flash_sale_date').toggle(flashSale);
        if ($btn.attr('data-flashsaledate')) {
            $('#edit_flash_sale_date').val(
                $btn.attr('data-flashsaledate').replace(' ', 'T').substring(0, 16)
            );
        }

        // Variant image preview
        const varientImg = $btn.attr('data-varientimage');
        $('.edit_varient_preview_image').attr('src', varientImg ? `/images/${varientImg}` : '');

        // Country select → triggers cascade → we restore product selection after load
        const countryIds     = $btn.attr('data-countryid')
            ? $btn.attr('data-countryid').toString().split(',').map(s => s.trim())
            : [];
        const productId      = $btn.attr('data-productid-val');
        const categoryId     = $btn.attr('data-categoryid');
        const subcategoryId  = $btn.attr('data-subcategoryid');
        const brandId        = $btn.attr('data-brandid');

        if ($.fn.select2) {
            $('#edit_country_select').val(countryIds).trigger('change.select2');
        } else {
            $('#edit_country_select').val(countryIds).trigger('change');
        }

        // Load filtered data, then restore selections
        const payload = {
            country_ids:    countryIds,
            category_id:    '',
            subcategory_id: '',
            brand_id:       '',
            _token:         $('meta[name="csrf-token"]').attr('content'),
        };
        $.ajax({
            url:      window.giftVariantUrls.filteredProducts,
            method:   'GET',
            data:     payload,
            dataType: 'json',
            success: function (res) {
                populateSelect('edit_category_select',    res.categories,    'id', 'category_name',    'All Categories');
                populateSelect('edit_subcategory_select', res.subcategories, 'id', 'subcategory_name', 'All Sub Categories');
                populateSelect('edit_brand_select',       res.brands,        'id', 'name',             'All Brands');
                populateSelect('edit_product_select',     res.products,      'id', 'product_name',     'Select Product', true);

                // Restore values
                if (categoryId)    $('#edit_category_select').val(categoryId);
                if (subcategoryId) $('#edit_subcategory_select').val(subcategoryId);
                if (brandId)       $('#edit_brand_select').val(brandId);
                if (productId)     $('#edit_product_select').val(productId);

                openEditModal();
            },
        });
    });

    // ── Close edit modal ──
    $(document).on('click', '#editVariantModal .btn-close, #editVariantModal [data-bs-dismiss="modal"]', function () {
        closeEditModal();
    });
    $(document).on('click', '#editVariantModal', function (e) {
        if ($(e.target).attr('id') === 'editVariantModal') closeEditModal();
    });

    // ── Delete button ──
    $(document).on('click', '.delete_btn', function () {
        const id = $(this).attr('data-variantid');
        Swal.fire({
            title: 'Are you sure?',
            text:  "You won't be able to revert this!",
            icon:  'warning',
            showCancelButton:  true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText:  'No, cancel!',
            reverseButtons:    true,
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url:      giftVariantUrls.destroy + '/' + id,
                    method:   'POST',
                    dataType: 'json',
                    data:     { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        gridjsReRender(response.variants);
                        Swal.fire('Deleted!', 'Variant deleted successfully.', 'success');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire(textStatus.toUpperCase(), errorThrown, 'warning');
                    },
                });
            }
        });
    });
});

// ─── Validators setup ─────────────────────────────────────────────────────────

addValidator
    .addField('#add_country_select', [{ rule: 'required', errorMessage: '*Country is required' }])
    .addField('#add_product_select', [{ rule: 'required', errorMessage: '*Product is required' }])
    .addField('#add_mrp_price', [
        { rule: 'required', errorMessage: '*MRP Price is required' },
        { rule: 'number',   errorMessage: '*Must be a number' },
    ])
    .addField('#add_offer_price', [
        { rule: 'required', errorMessage: '*Selling Price is required' },
        { rule: 'number',   errorMessage: '*Must be a number' },
    ])
    .addField('#add_low_stock', [
        { rule: 'required', errorMessage: '*Low Stock is required' },
        { rule: 'number',   errorMessage: '*Must be a number' },
    ])
    .onSuccess(e => {
        $('.add_submit_btn').attr('disabled', true).html('Uploading...');
        addVariantFormSubmit(e);
    });

editValidator
    .addField('#edit_product_select', [{ rule: 'required', errorMessage: '*Product is required' }])
    .addField('#edit_mrp_price', [
        { rule: 'required', errorMessage: '*MRP Price is required' },
        { rule: 'number',   errorMessage: '*Must be a number' },
    ])
    .addField('#edit_offer_price', [
        { rule: 'required', errorMessage: '*Selling Price is required' },
        { rule: 'number',   errorMessage: '*Must be a number' },
    ])
    .addField('#edit_low_stock', [
        { rule: 'required', errorMessage: '*Low Stock is required' },
        { rule: 'number',   errorMessage: '*Must be a number' },
    ])
    .onSuccess(e => {
        $('.edit_submit_btn').attr('disabled', true).html('Updating...');
        editVariantFormSubmit(e);
    });

// ─── Grid.js Row Builder ──────────────────────────────────────────────────────

function buildRow(v, index) {
    const prod = v.product || {};
    return [
        index + 1,
        resolveCountryName(v.country_id),
        prod.cate_name   || '-',
        prod.subcate_name || 'N/A',
        prod.product_name || '-',
        v.varient_name   || '-',
        resolveName(window.sizes,  v.size_id,  'size'),
        resolveName(window.colors, v.color_id, 'color'),
        v.sku            || '-',
        v.mrp_price,
        v.offer_price,
        v.low_stock,
        gridjs.html(
            v.varient_img
                ? `<img src="/images/${v.varient_img}"
                        style="width:50px;height:50px;object-fit:cover;border-radius:5px;"
                        onerror="this.style.display='none'">`
                : '-'
        ),
        gridjs.html(`
            <div class="d-flex gap-2 justify-content-center">
                <button
                    class="btn btn-secondary btn-sm edit_btn"
                    data-variantid="${v.id}"
                    data-productid-val="${v.product_id}"
                    data-countryid="${v.country_id || ''}"
                    data-categoryid="${prod.category_id || ''}"
                    data-subcategoryid="${prod.subcategory_id || ''}"
                    data-brandid="${v.brand_id || ''}"
                    data-varientname="${(v.varient_name || '').replace(/"/g, '&quot;')}"
                    data-sku="${v.sku || ''}"
                    data-barcode="${v.barcode || ''}"
                    data-mrpprice="${v.mrp_price}"
                    data-offerprice="${v.offer_price}"
                    data-lowstock="${v.low_stock || 5}"
                    data-varientimage="${v.varient_img || ''}"
                    data-gst="${v.product_gst ?? ''}"
                    data-hotdeals="${v.hot_deals || 0}"
                    data-popularprod="${v.Popular_products || 0}"
                    data-flashsale="${v.flash_sale || 0}"
                    data-flashsaledate="${v.flash_sale_date || ''}"
                    data-sizeid="${v.size_id || ''}"
                    data-colorid="${v.color_id || ''}"
                    data-itemcode="${v.item_code || ''}"
                >Edit</button>
                <button class="btn btn-danger btn-sm delete_btn"
                    data-variantid="${v.id}">Delete</button>
            </div>
        `),
    ];
}

// ─── Grid.js Init ─────────────────────────────────────────────────────────────

const gridNew = new gridjs.Grid({
    columns: [
        'S.NO', 'Country', 'Category', 'Sub Category', 'Product',
        'Variant Name', 'Size', 'Color', 'SKU',
        'MRP', 'Selling Price', 'Low Stock', 'Image',
        { name: 'Action', sort: false },
    ],
    pagination: { limit: 10 },
    sort:   true,
    search: true,
    data:   (window.variants || []).map((v, i) => buildRow(v, i)),
    style: {
        table: { border: '1px solid #ccc' },
        th: {
            'background-color': 'rgba(0,0,0,0.1)', color: '#000',
            'border-bottom': '3px solid #ccc', 'text-align': 'center',
            'border-right': '0.5px solid #ccc',
        },
        td: {
            'text-align': 'center',
            'border-right': '0.5px solid #ccc',
            'border-bottom': '0.5px solid #ccc',
        },
    },
});
gridNew.render(document.getElementById('table-gridjs'));

function gridjsReRender(variants) {
    gridNew.updateConfig({
        data: (variants || []).map((v, i) => buildRow(v, i)),
    }).forceRender();
}

// ─── AJAX: Add Variant ────────────────────────────────────────────────────────

function addVariantFormSubmit(e) {
    const formdata = new FormData(e.target);

    if ($.fn.select2) {
        const vals = $('#add_country_select').val();
        formdata.delete('country_id');
        formdata.delete('country_id[]');
        (vals || []).forEach(v => formdata.append('country_id[]', v));
    }

    $.ajax({
        url:         giftVariantUrls.store,
        method:      'POST',
        dataType:    'json',
        data:        formdata,
        processData: false,
        contentType: false,
        success: function (res) {
            $('.add_submit_btn').removeAttr('disabled').html('Submit');
            $('#addVariantModal').modal('hide');
            gridjsReRender(res.variants);
            Swal.fire('Added', 'Gift Variant Added Successfully.', 'success');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('.add_submit_btn').removeAttr('disabled').html('Submit');
            let msg = errorThrown;
            if (jqXHR.responseJSON?.errors) {
                msg = Object.values(jqXHR.responseJSON.errors).flat().join('<br>');
            } else if (jqXHR.responseJSON?.message) {
                msg = jqXHR.responseJSON.message;
            }
            Swal.fire({ icon: 'error', title: textStatus.toUpperCase(), html: msg });
        },
    });
}

// ─── AJAX: Edit Variant ───────────────────────────────────────────────────────

function editVariantFormSubmit(e) {
    const formdata = new FormData(e.target);

    if ($.fn.select2) {
        const vals = $('#edit_country_select').val();
        formdata.delete('country_id');
        formdata.delete('country_id[]');
        (vals || []).forEach(v => formdata.append('country_id[]', v));
    }

    $.ajax({
        url:         giftVariantUrls.update + '/' + $('#edit_variant_id').val(),
        method:      'POST',
        dataType:    'json',
        data:        formdata,
        processData: false,
        contentType: false,
        success: function (res) {
            $('.edit_submit_btn').removeAttr('disabled').html('Update');
            closeEditModal();
            gridjsReRender(res.variants);
            Swal.fire('Updated', 'Gift Variant Updated Successfully.', 'success');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('.edit_submit_btn').removeAttr('disabled').html('Update');
            let msg = errorThrown;
            if (jqXHR.responseJSON?.errors) {
                msg = Object.values(jqXHR.responseJSON.errors).flat().join('<br>');
            } else if (jqXHR.responseJSON?.message) {
                msg = jqXHR.responseJSON.message;
            }
            Swal.fire({ icon: 'error', title: textStatus.toUpperCase(), html: msg });
        },
    });
}

// ─── Modal helpers ────────────────────────────────────────────────────────────

function openEditModal() {
    const modal = document.getElementById('editVariantModal');
    if (!document.getElementById('editVariantBackdrop')) {
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop show';
        backdrop.id        = 'editVariantBackdrop';
        document.body.appendChild(backdrop);
    }
    modal.removeAttribute('aria-hidden');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('role', 'dialog');
    modal.classList.add('show');
    modal.style.setProperty('display', 'block', 'important');
    document.body.classList.add('modal-open');
    document.body.style.overflow = 'hidden';
    modal.scrollTop = 0;
}

function closeEditModal() {
    const $modal = $('#editVariantModal');
    $modal.removeClass('show')
        .removeAttr('aria-modal').removeAttr('role')
        .attr('aria-hidden', 'true')
        .css({ display: '', 'z-index': '' });
    $('#editVariantBackdrop').remove();
    $('body').removeClass('modal-open').css('overflow', '');
    $('#editVariantForm')[0].reset();
    $('.edit_varient_preview_container').html(
        '<i class="mdi mdi-cloud-upload text-muted" style="font-size:24px;"></i>'
    );
}

// ─── Image preview utility ────────────────────────────────────────────────────

function previewImage(input, previewSelector) {
    const file = input.files[0];
    if (!file || !file.type.match('image.*')) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = $('<img>').attr('src', e.target.result)
            .css({ width: '80px', height: '80px', 'object-fit': 'cover', 'border-radius': '5px' });
        $(previewSelector).empty().append(img);
    };
    reader.readAsDataURL(file);
}