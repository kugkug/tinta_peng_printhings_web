$(function () {
    const csrfToken = $('meta[name="_token"]').attr('content');
    const productId = $('#product-id').val() || null;
    const state = {
        items: [],
        itemsById: {},
        historyById: {},
    };

    init();

    function init() {
        bindGlobalEvents();
        loadAvailableItems()
            .done(function () {
                if (productId) {
                    loadProductData(productId);
                } else {
                    addMaterialRow();
                    addPackagingRow();
                }
                fetchHistorySuggestions();
            })
            .fail(function () {
                toastr.error('Unable to load inventory items. Please refresh the page.', 'Error');
            });
    }

    function bindGlobalEvents() {
        $('#add-material-btn').on('click', function () {
            addMaterialRow();
        });

        $('#add-ink-btn').on('click', function () {
            addInkRow();
        });

        $('#add-packaging-btn').on('click', function () {
            addPackagingRow();
        });

        $('#clear-all-btn').on('click', function () {
            if (confirm('Clear all materials, ink, and packaging entries?')) {
                clearAllSections();
                calculateTotalCost();
            }
        });

        $('#refresh-history-btn').on('click', fetchHistorySuggestions);

        $('#history-select').on('change', function () {
            const historyId = $(this).val();
            if (!historyId) {
                return;
            }
            const history = state.historyById[historyId];
            if (history) {
                populateFromHistory(history);
                showHistoryIndicator();
                setTemplateStatus(`Loaded configuration for <strong>${history.product_code}</strong>.`, 'success');
            }
        });

        $('#clear-history-indicator').on('click', function () {
            hideHistoryIndicator();
            setTemplateStatus('', null);
        });

        $('#load-template-btn').on('click', function () {
            loadTemplateByCode();
        });

        $('#template-product-code').on('keypress', function (event) {
            if (event.which === 13) {
                event.preventDefault();
                loadTemplateByCode();
            }
        });

        $('#generate-code-btn').on('click', function () {
            generateProductCode();
        });

        $('#check-code-btn').on('click', function () {
            checkProductCodeAvailability();
        });

        $('#product-form').on('submit', function (event) {
            event.preventDefault();
            submitForm();
        });

        $('#materials-container').on('change', '.component-item-select', function () {
            const $row = $(this).closest('.material-row');
            handleItemSelection($row, 'materials');
        });

        $('#materials-container')
            .on('input', '.material-quantity, .material-unit-price', function () {
                const $row = $(this).closest('.material-row');
                updateMaterialRowTotals($row);
            })
            .on('input', '.material-total-cost', function () {
                updateRowTimestamp($(this).closest('.material-row'));
                calculateTotalCost();
            })
            .on('click', '.remove-row-btn', function () {
                $(this).closest('.material-row').remove();
                calculateTotalCost();
            });

        $('#inks-container').on('change', '.component-item-select', function () {
            const $row = $(this).closest('.ink-row');
            handleItemSelection($row, 'inks');
        });

        $('#inks-container')
            .on('input', '.ink-cost-per-page, .ink-total-pages', function () {
                const $row = $(this).closest('.ink-row');
                updateInkRowTotals($row);
            })
            .on('input', '.ink-total-cost', function () {
                updateRowTimestamp($(this).closest('.ink-row'));
                calculateTotalCost();
            })
            .on('click', '.remove-row-btn', function () {
                $(this).closest('.ink-row').remove();
                calculateTotalCost();
            });

        $('#packaging-container').on('change', '.component-item-select', function () {
            const $row = $(this).closest('.packaging-row');
            handleItemSelection($row, 'packaging');
        });

        $('#packaging-container')
            .on('input', '.packaging-quantity, .packaging-total-cost', function () {
                const $row = $(this).closest('.packaging-row');
                updateRowTimestamp($row);
                calculateTotalCost();
            })
            .on('click', '.remove-row-btn', function () {
                $(this).closest('.packaging-row').remove();
                calculateTotalCost();
            });
    }

    function loadAvailableItems() {
        return $.ajax({
            url: '/api/items/list',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        }).done(function (result) {
            if (result.status !== 'success') {
                throw new Error(result.message || 'Failed to fetch items');
            }

            state.items = result.data || [];
            state.itemsById = {};

            state.items.forEach(function (item) {
                state.itemsById[item.id] = item;
            });
        });
    }

    function fetchHistorySuggestions() {
        $.ajax({
            url: '/api/products/history/list',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        }).done(function (result) {
            if (result.status !== 'success') {
                return;
            }

            const $select = $('#history-select');
            $select.empty().append('<option value="">Select a previous configuration...</option>');
            state.historyById = {};

            (result.data || []).forEach(function (history) {
                state.historyById[history.id] = history;
                const label = history.product_code
                    ? `${history.product_code} • ${history.product_name || 'Untitled'}`
                    : `History #${history.id}`;
                $select.append(`<option value="${history.id}">${label}</option>`);
            });
        });
    }

    function loadProductData(id) {
        $.ajax({
            url: '/api/products/get',
            type: 'POST',
            data: { ProductId: id },
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .done(function (result) {
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Failed to load product');
                }

                const product = result.data;
                $('#product-code').val(product.product_code);
                $('#product-name').val(product.product_name);
                $('#product-description').val(product.product_description);

                populateFromComponents({
                    materials: product.materials || [],
                    inks: product.inks || [],
                    packaging: product.packaging || [],
                });

                calculateTotalCost();
            })
            .fail(function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Could not load product details.', 'Error');
            });
    }

    function populateFromComponents(payload) {
        clearAllSections();

        (payload.materials || []).forEach(function (material) {
            addMaterialRow({
                item_id: material.item_id,
                quantity_used: material.quantity_used,
                unit_price: material.unit_price,
                total_cost: material.total_cost,
            });
        });

        (payload.inks || []).forEach(function (ink) {
            addInkRow({
                item_id: ink.item_id,
                pages_yield: ink.pages_yield,
                cost_per_page: ink.cost_per_page,
                total_pages_printed: ink.total_pages_printed,
                total_cost: ink.total_cost,
            });
        });

        (payload.packaging || []).forEach(function (pack) {
            addPackagingRow({
                item_id: pack.item_id,
                quantity_used: pack.quantity_used,
                total_cost: pack.total_cost,
            });
        });

        if (
            (payload.materials || []).length === 0 &&
            (payload.inks || []).length === 0 &&
            (payload.packaging || []).length === 0
        ) {
            addMaterialRow();
            addPackagingRow();
        }

        calculateTotalCost();
    }

    function populateFromHistory(history) {
        populateFromComponents({
            materials: history.materials || [],
            inks: history.inks || [],
            packaging: history.packaging || [],
        });

        if (!productId && history.product_code) {
            $('#product-code').val(history.product_code);
        }

        calculateTotalCost();
    }

    function addMaterialRow(prefill = {}) {
        const template = $('#material-row-template').html();
        const $row = $(template);
        populateItemSelect($row.find('.component-item-select'), prefill.item_id);

        if (prefill.quantity_used !== undefined) {
            $row.find('.material-quantity').val(parseFloat(prefill.quantity_used));
        }
        if (prefill.unit_price !== undefined) {
            $row.find('.material-unit-price').val(parseFloat(prefill.unit_price).toFixed(2));
        }
        if (prefill.total_cost !== undefined) {
            $row.find('.material-total-cost').val(parseFloat(prefill.total_cost).toFixed(2));
        }

        $('#materials-container').append($row);

        if (prefill.item_id) {
            handleItemSelection($row, 'materials');
        }

        updateMaterialRowTotals($row);
        applySelect2($row.find('.component-item-select'));
    }

    function addInkRow(prefill = {}) {
        const template = $('#ink-row-template').html();
        const $row = $(template);
        populateItemSelect($row.find('.component-item-select'), prefill.item_id);

        if (prefill.pages_yield !== undefined) {
            $row.find('.ink-pages-yield').val(parseFloat(prefill.pages_yield));
        }
        if (prefill.cost_per_page !== undefined) {
            $row.find('.ink-cost-per-page').val(parseFloat(prefill.cost_per_page).toFixed(2));
        }
        if (prefill.total_pages_printed !== undefined) {
            $row.find('.ink-total-pages').val(parseFloat(prefill.total_pages_printed));
        }
        if (prefill.total_cost !== undefined) {
            $row.find('.ink-total-cost').val(parseFloat(prefill.total_cost).toFixed(2));
        }

        $('#inks-container').append($row);

        if (prefill.item_id) {
            handleItemSelection($row, 'inks');
        }

        updateInkRowTotals($row);
        applySelect2($row.find('.component-item-select'));
    }

    function addPackagingRow(prefill = {}) {
        const template = $('#packaging-row-template').html();
        const $row = $(template);
        populateItemSelect($row.find('.component-item-select'), prefill.item_id);

        if (prefill.quantity_used !== undefined) {
            $row.find('.packaging-quantity').val(parseFloat(prefill.quantity_used));
        }
        if (prefill.total_cost !== undefined) {
            $row.find('.packaging-total-cost').val(parseFloat(prefill.total_cost).toFixed(2));
        }

        $('#packaging-container').append($row);

        if (prefill.item_id) {
            handleItemSelection($row, 'packaging');
        }

        updateRowTimestamp($row);
        applySelect2($row.find('.component-item-select'));
    }

    function populateItemSelect($select, selectedId) {
        $select.empty().append('<option value="">Select an item...</option>');

        state.items.forEach(function (item) {
            const label = item.sku
                ? `${item.item_name} (${item.sku})`
                : item.item_name;
            const option = $('<option></option>')
                .val(item.id)
                .text(label);
            if (selectedId && parseInt(selectedId, 10) === parseInt(item.id, 10)) {
                option.prop('selected', true);
            }
            $select.append(option);
        });
    }

    function handleItemSelection($row, componentType) {
        const $select = $row.find('.component-item-select');
        const itemId = parseInt($select.val(), 10);
        const item = state.itemsById[itemId] || null;
        $row.find('.component-sku').val(item ? item.sku || '' : '');

        switch (componentType) {
            case 'materials':
                if (item) {
                    const defaultUnit = computeDefaultUnitPrice(item);
                    const $unitInput = $row.find('.material-unit-price');
                    if (!$unitInput.val()) {
                        $unitInput.val(defaultUnit > 0 ? defaultUnit.toFixed(2) : '');
                    }
                }
                updateMaterialAvailability($row, item);
                updateMaterialRowTotals($row);
                break;
            case 'inks':
            case 'packaging':
                updateRowTimestamp($row);
                break;
        }
    }

    function updateMaterialRowTotals($row) {
        const itemId = parseInt($row.find('.component-item-select').val(), 10);
        const item = state.itemsById[itemId] || null;
        const quantity = parseFloat($row.find('.material-quantity').val()) || 0;
        const unitPrice = parseFloat($row.find('.material-unit-price').val()) || 0;
        const total = quantity * unitPrice;

        $row.find('.material-total-cost').val(total > 0 ? total.toFixed(2) : '0.00');
        updateMaterialAvailability($row, item);
        updateRowTimestamp($row);
        calculateTotalCost();
    }

    function updateInkRowTotals($row) {
        const costPerPage = parseFloat($row.find('.ink-cost-per-page').val()) || 0;
        const totalPages = parseFloat($row.find('.ink-total-pages').val()) || 0;
        const total = costPerPage * totalPages;

        $row.find('.ink-total-cost').val(total > 0 ? total.toFixed(2) : '0.00');
        updateRowTimestamp($row);
        calculateTotalCost();
    }

    function updateMaterialAvailability($row, item) {
        const $quantityInput = $row.find('.material-quantity');
        const $availabilityText = $row.find('.availability-text');

        if (!item || item.item_quantity === undefined || item.item_quantity === null) {
            $availabilityText.text('');
            $quantityInput.removeClass('is-invalid');
            return;
        }

        const requestedQty = parseFloat($quantityInput.val()) || 0;
        const available = parseFloat(item.item_quantity);

        if (requestedQty > available) {
            $availabilityText
                .removeClass('text-muted')
                .addClass('text-danger')
                .text(`⚠️ Insufficient inventory. Available: ${available}, Requested: ${requestedQty}`);
            $quantityInput.addClass('is-invalid');
        } else {
            $availabilityText
                .removeClass('text-danger')
                .addClass('text-muted')
                .text(`Available: ${available}`);
            $quantityInput.removeClass('is-invalid');
        }
    }

    function computeDefaultUnitPrice(item) {
        const candidates = [
            item.item_price_per_part,
            item.item_price_per_piece,
            item.item_price,
            item.price_without_shipping_fee,
        ];

        for (let i = 0; i < candidates.length; i += 1) {
            const value = parseFloat(candidates[i]);
            if (!isNaN(value) && value > 0) {
                return value;
            }
        }

        return 0;
    }

    function updateRowTimestamp($row) {
        const now = new Date();
        const formatted = now.toLocaleString();
        $row.find('.row-timestamp').text(formatted);
    }

    function calculateTotalCost() {
        let total = 0;

        $('.material-total-cost, .ink-total-cost, .packaging-total-cost').each(function () {
            const value = parseFloat($(this).val());
            if (!isNaN(value)) {
                total += value;
            }
        });

        $('#total-cost').text(total.toFixed(2));
    }

    function clearAllSections() {
        $('#materials-container').empty();
        $('#inks-container').empty();
        $('#packaging-container').empty();
        hideHistoryIndicator();
    }

    function loadTemplateByCode() {
        const code = $('#template-product-code').val().trim();
        if (!code) {
            toastr.warning('Enter a product code to load a history or template.', 'Missing Code');
            return;
        }

        setTemplateStatus('Loading configuration…', 'info');

        $.ajax({
            url: '/api/products/check-code',
            type: 'POST',
            data: { ProductCode: code },
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .done(function (result) {
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Failed to check product code');
                }

                const payload = result.data;

                if (payload.history) {
                    populateFromHistory(payload.history);
                    if (!productId) {
                        $('#product-code').val(code);
                    }
                    showHistoryIndicator();
                    setTemplateStatus(`History loaded for <strong>${code}</strong>.`, 'success');
                    calculateTotalCost();
                    return;
                }

                if (payload.exists && payload.product) {
                    loadProductTemplate(payload.product.id, code);
                    return;
                }

                setTemplateStatus('No matching product or history found for the provided code.', 'warning');
            })
            .fail(function (xhr) {
                setTemplateStatus('Failed to load template. Please try again.', 'danger');
                toastr.error(xhr.responseJSON?.message || 'An error occurred while loading the template.', 'Error');
            });
    }

    function loadProductTemplate(id, code) {
        $.ajax({
            url: '/api/products/get',
            type: 'POST',
            data: { ProductId: id },
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .done(function (result) {
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Failed to load template');
                }

                populateFromComponents({
                    materials: result.data.materials || [],
                    inks: result.data.inks || [],
                    packaging: result.data.packaging || [],
                });

                if (!productId && code) {
                    $('#product-code').val(code);
                }

                showHistoryIndicator();
                setTemplateStatus(`Loaded template from product <strong>${code}</strong>.`, 'success');
                calculateTotalCost();
            })
            .fail(function (xhr) {
                setTemplateStatus('Unable to load product details for the given code.', 'danger');
                toastr.error(xhr.responseJSON?.message || 'An error occurred while loading the product template.', 'Error');
            });
    }

    function generateProductCode() {
        $.ajax({
            url: '/api/products/generate-code',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .done(function (result) {
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Failed to generate code');
                }
                $('#product-code').val(result.data.product_code);
                $('#code-status').html('<span class="text-success">New code generated.</span>');
            })
            .fail(function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Could not generate a product code.', 'Error');
            });
    }

    function checkProductCodeAvailability() {
        const code = $('#product-code').val().trim();
        if (!code) {
            toastr.warning('Enter a product code before checking.', 'Missing Code');
            return;
        }

        $.ajax({
            url: '/api/products/check-code',
            type: 'POST',
            data: { ProductCode: code },
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .done(function (result) {
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Failed to check code');
                }

                const payload = result.data;
                if (payload.exists) {
                    const sameProduct = payload.product && productId && parseInt(payload.product.id, 10) === parseInt(productId, 10);
                    if (sameProduct) {
                        $('#code-status').html('<span class="text-info">This is the current product code.</span>');
                    } else {
                        $('#code-status').html('<span class="text-warning">Code already exists. Loading history for review…</span>');
                        if (payload.history) {
                            populateFromHistory(payload.history);
                            showHistoryIndicator();
                            calculateTotalCost();
                        }
                    }
                } else {
                    $('#code-status').html('<span class="text-success">Code is available.</span>');
                }
            })
            .fail(function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Could not verify product code.', 'Error');
            });
    }

    function gatherFormData() {
        const payload = {
            ProductCode: $('#product-code').val().trim(),
            ProductName: $('#product-name').val().trim(),
            ProductDescription: $('#product-description').val().trim(),
            Materials: [],
            Inks: [],
            Packaging: [],
        };

        if (productId) {
            payload.ProductId = productId;
        }

        let hasInvalidQuantity = false;

        $('#materials-container .material-row').each(function () {
            const $row = $(this);
            const itemId = parseInt($row.find('.component-item-select').val(), 10);
            if (!itemId) {
                return;
            }

            if ($row.find('.material-quantity').hasClass('is-invalid')) {
                hasInvalidQuantity = true;
            }

            payload.Materials.push({
                item_id: itemId,
                quantity_used: parseFloat($row.find('.material-quantity').val()) || 0,
                unit_price: parseFloat($row.find('.material-unit-price').val()) || 0,
                total_cost: parseFloat($row.find('.material-total-cost').val()) || 0,
            });
        });

        $('#inks-container .ink-row').each(function () {
            const $row = $(this);
            const itemId = parseInt($row.find('.component-item-select').val(), 10);
            if (!itemId) {
                return;
            }

            payload.Inks.push({
                item_id: itemId,
                pages_yield: parseFloat($row.find('.ink-pages-yield').val()) || 0,
                cost_per_page: parseFloat($row.find('.ink-cost-per-page').val()) || 0,
                total_pages_printed: parseFloat($row.find('.ink-total-pages').val()) || 0,
                total_cost: parseFloat($row.find('.ink-total-cost').val()) || 0,
            });
        });

        $('#packaging-container .packaging-row').each(function () {
            const $row = $(this);
            const itemId = parseInt($row.find('.component-item-select').val(), 10);
            if (!itemId) {
                return;
            }

            payload.Packaging.push({
                item_id: itemId,
                quantity_used: parseFloat($row.find('.packaging-quantity').val()) || 0,
                total_cost: parseFloat($row.find('.packaging-total-cost').val()) || 0,
            });
        });

        if (hasInvalidQuantity) {
            toastr.error('One or more materials exceed available inventory. Please adjust the quantities.', 'Insufficient Inventory');
            return null;
        }

        if (
            payload.Materials.length === 0 &&
            payload.Inks.length === 0 &&
            payload.Packaging.length === 0
        ) {
            toastr.warning('Add at least one material, ink, or packaging entry before saving.', 'Missing Components');
            return null;
        }

        if (!payload.ProductName) {
            toastr.warning('Product name is required.', 'Validation');
            return null;
        }

        return payload;
    }

    function submitForm() {
        const formData = gatherFormData();
        if (!formData) {
            return;
        }

        const $submitBtn = $('#submit-btn');
        $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving…');

        $.ajax({
            url: '/api/products/save',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .done(function (result) {
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Failed to save product');
                }

                toastr.success(result.message || 'Product saved successfully.', 'Success');

                setTimeout(function () {
                    window.location.href = '/products';
                }, 1200);
            })
            .fail(function (xhr) {
                const errorMsg = xhr.responseJSON?.message || 'An error occurred while saving the product.';
                toastr.error(errorMsg, 'Error');
            })
            .always(function () {
                $submitBtn.prop('disabled', false).html(productId ? '<i class="fa fa-save"></i> Update Product' : '<i class="fa fa-save"></i> Save Product');
            });
    }

    function showHistoryIndicator() {
        $('#history-indicator').show();
    }

    function hideHistoryIndicator() {
        $('#history-indicator').hide();
    }

    function setTemplateStatus(message, level) {
        const $status = $('#template-status');
        if (!message) {
            $status.empty();
            return;
        }
        const cls = level ? `alert alert-${level} mb-0` : '';
        $status.html(`<div class="${cls}">${message}</div>`);
    }

    function applySelect2($select) {
        if (typeof $.fn.select2 === 'function') {
            $select.select2({
                placeholder: 'Select an item…',
                width: '100%',
            });
        }
    }
});
