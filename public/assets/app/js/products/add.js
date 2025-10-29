$(document).ready(function () {
    let availableItems = [];
    let itemRowCounter = 0;

    // Load available items
    loadAvailableItems();

    // Get product ID if editing
    const productId = $("#product-id").val();
    if (productId) {
        loadProductData(productId);
    }

    // Load available items from inventory
    function loadAvailableItems() {
        $.ajax({
            url: "/api/items/list",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            success: function (result) {
                if (result.status === "success") {
                    availableItems = result.data;
                }
            },
            error: function (xhr, status, error) {
                toastr.error("Failed to load items: " + error, "Error");
            },
        });
    }

    // Load product data for editing
    function loadProductData(productId) {
        $.ajax({
            url: "/api/products/get",
            type: "POST",
            data: { ProductId: productId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            success: function (result) {
                if (result.status === "success") {
                    const product = result.data;

                    // Populate form fields
                    $("#product-code").val(product.product_code);
                    $("#product-name").val(product.product_name);
                    $("#product-description").val(product.product_description);

                    // Add items
                    product.items.forEach(function (item) {
                        addItemRow(item);
                    });

                    calculateTotalCost();
                } else {
                    toastr.error(
                        result.message || "Failed to load product",
                        "Error"
                    );
                }
            },
            error: function (xhr, status, error) {
                toastr.error("An error occurred: " + error, "Error");
            },
        });
    }

    // Generate product code
    $("#generate-code-btn").click(function () {
        $.ajax({
            url: "/api/products/generate-code",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            success: function (result) {
                if (result.status === "success") {
                    $("#product-code").val(result.data.product_code);
                    $("#code-status").html(
                        '<span class="text-success">New code generated</span>'
                    );
                } else {
                    toastr.error(
                        result.message || "Failed to generate code",
                        "Error"
                    );
                }
            },
            error: function (xhr, status, error) {
                toastr.error("An error occurred: " + error, "Error");
            },
        });
    });

    // Check if product code exists
    $("#check-code-btn").click(function () {
        const code = $("#product-code").val().trim();

        if (!code) {
            toastr.warning("Please enter a product code", "Warning");
            return;
        }

        $.ajax({
            url: "/api/products/check-code",
            type: "POST",
            data: { ProductCode: code },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            success: function (result) {
                if (result.status === "success") {
                    if (result.data.exists) {
                        const existingProductId = $("#product-id").val();
                        if (
                            existingProductId &&
                            result.data.product.id == existingProductId
                        ) {
                            $("#code-status").html(
                                '<span class="text-info">Current product code</span>'
                            );
                        } else {
                            $("#code-status").html(
                                '<span class="text-warning">Code already exists: ' +
                                    result.data.product.product_name +
                                    "</span>"
                            );

                            if (
                                confirm(
                                    "This product code already exists. Do you want to use the same code for a similar product?"
                                )
                            ) {
                                // Keep the code
                                $("#code-status").html(
                                    '<span class="text-info">Using existing code</span>'
                                );
                            }
                        }
                    } else {
                        $("#code-status").html(
                            '<span class="text-success">Code is available</span>'
                        );
                    }
                } else {
                    toastr.error(
                        result.message || "Failed to check code",
                        "Error"
                    );
                }
            },
            error: function (xhr, status, error) {
                toastr.error("An error occurred: " + error, "Error");
            },
        });
    });

    // Add item row
    $("#add-item-btn").click(function () {
        addItemRow();
    });

    // Load from existing product template
    $("#load-template-btn").click(function () {
        const templateCode = $("#template-product-code").val().trim();
        const multiplier = parseFloat($("#quantity-multiplier").val()) || 1;

        if (!templateCode) {
            toastr.warning("Please enter a product code to load", "Warning");
            return;
        }

        // Check if product exists and get its details
        $.ajax({
            url: "/api/products/check-code",
            type: "POST",
            data: { ProductCode: templateCode },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            success: function (result) {
                if (result.status === "success") {
                    if (result.data.exists) {
                        // Product exists, now load its full details
                        loadProductTemplate(result.data.product.id, multiplier);
                    } else {
                        $("#template-status").html(
                            '<div class="alert alert-warning alert-sm mb-0">' +
                                '<i class="fa fa-exclamation-triangle"></i> Product code not found. Please check and try again.' +
                                "</div>"
                        );
                    }
                } else {
                    toastr.error(
                        result.message || "Failed to check product code",
                        "Error"
                    );
                }
            },
            error: function (xhr, status, error) {
                toastr.error("An error occurred: " + error, "Error");
            },
        });
    });

    // Load product template details and populate items
    function loadProductTemplate(productId, multiplier) {
        $.ajax({
            url: "/api/products/get",
            type: "POST",
            data: { ProductId: productId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            success: function (result) {
                if (result.status === "success") {
                    const product = result.data;

                    // Clear existing items first
                    $("#items-container").empty();

                    // Show success message
                    $("#template-status").html(
                        '<div class="alert alert-success alert-sm mb-0">' +
                            '<i class="fa fa-check"></i> Loaded <strong>' +
                            product.product_name +
                            "</strong> with " +
                            product.items.length +
                            " item(s)" +
                            (multiplier > 1
                                ? " (quantities multiplied by " +
                                  multiplier +
                                  ")"
                                : "") +
                            "</div>"
                    );

                    // Add each item with multiplied quantity
                    product.items.forEach(function (item) {
                        const adjustedItem = {
                            id: item.id,
                            sku: item.sku,
                            quantity: item.quantity * multiplier,
                            unit_cost: item.unit_cost,
                            subtotal:
                                item.quantity * multiplier * item.unit_cost,
                        };
                        addItemRow(adjustedItem);
                    });

                    calculateTotalCost();

                    toastr.success(
                        "Items loaded successfully from product template",
                        "Success"
                    );
                } else {
                    toastr.error(
                        result.message || "Failed to load product details",
                        "Error"
                    );
                }
            },
            error: function (xhr, status, error) {
                toastr.error("An error occurred: " + error, "Error");
            },
        });
    }

    // Clear all items
    $("#clear-items-btn").click(function () {
        if (
            $(".item-row").length === 0 ||
            confirm(
                "Are you sure you want to clear all items from the Bill of Materials?"
            )
        ) {
            $("#items-container").empty();
            $("#total-cost").text("0.00");
            $("#template-status").html("");
            toastr.info("All items cleared", "Info");
        }
    });

    function addItemRow(itemData = null) {
        const template = document.getElementById("item-row-template");
        const clone = template.content.cloneNode(true);
        const row = $(clone).find(".item-row");

        // Set unique ID
        row.attr("data-row-id", itemRowCounter++);

        // Populate item select
        const itemSelect = row.find(".item-select");
        availableItems.forEach(function (item) {
            const option = $("<option></option>")
                .val(item.id)
                .text(item.item_name + " (" + item.sku + ")")
                .data("item", item);

            if (itemData && itemData.id == item.id) {
                option.prop("selected", true);
            }

            itemSelect.append(option);
        });

        // If editing, populate fields
        if (itemData) {
            row.find(".item-sku").val(itemData.sku);
            row.find(".item-quantity").val(itemData.quantity);
            row.find(".item-unit-cost").val(itemData.unit_cost);
            row.find(".item-subtotal").text(
                parseFloat(itemData.subtotal).toFixed(2)
            );
        }

        // Add to container
        $("#items-container").append(row);

        // Initialize select2 if available
        if (typeof $.fn.select2 !== "undefined") {
            itemSelect.select2({
                placeholder: "Select an item...",
                width: "100%",
            });
        }

        // Bind events
        bindItemRowEvents(row);
    }

    function bindItemRowEvents(row) {
        // When item is selected, populate SKU and unit cost
        row.find(".item-select").on("change", function () {
            const selectedOption = $(this).find("option:selected");
            const item = selectedOption.data("item");

            if (item) {
                row.find(".item-sku").val(item.sku || "N/A");
                row.find(".item-unit-cost").val(
                    parseFloat(item.item_price_per_part).toFixed(2)
                );

                // Update available quantity display
                const availableQty = parseFloat(item.item_quantity);
                const quantityInput = row.find(".item-quantity");
                quantityInput.attr("max", availableQty);

                // Add or update availability text
                let availabilityText = row.find(".availability-text");
                if (availabilityText.length === 0) {
                    row.find(".item-subtotal")
                        .parent()
                        .append(
                            '<br><small class="availability-text text-info"></small>'
                        );
                    availabilityText = row.find(".availability-text");
                }
                availabilityText.text(
                    `Available in inventory: ${availableQty}`
                );

                calculateRowSubtotal(row);
            }
        });

        // When quantity changes, recalculate and check availability
        row.find(".item-quantity").on("input change", function () {
            const selectedOption = row.find(".item-select option:selected");
            const item = selectedOption.data("item");

            if (item) {
                const requestedQty = parseFloat($(this).val()) || 0;
                const availableQty = parseFloat(item.item_quantity);
                const availabilityText = row.find(".availability-text");

                if (requestedQty > availableQty) {
                    availabilityText
                        .removeClass("text-info")
                        .addClass("text-danger")
                        .text(
                            `⚠️ Insufficient inventory! Available: ${availableQty}, Requested: ${requestedQty}`
                        );
                    $(this).addClass("is-invalid");
                } else {
                    availabilityText
                        .removeClass("text-danger")
                        .addClass("text-info")
                        .text(`Available in inventory: ${availableQty}`);
                    $(this).removeClass("is-invalid");
                }
            }

            calculateRowSubtotal(row);
        });

        // Remove item row
        row.find(".remove-item-btn").click(function () {
            row.remove();
            calculateTotalCost();
        });
    }

    function calculateRowSubtotal(row) {
        const quantity = parseFloat(row.find(".item-quantity").val()) || 0;
        const unitCost = parseFloat(row.find(".item-unit-cost").val()) || 0;
        const subtotal = quantity * unitCost;

        row.find(".item-subtotal").text(subtotal.toFixed(2));
        calculateTotalCost();
    }

    function calculateTotalCost() {
        let total = 0;

        $(".item-row").each(function () {
            const subtotal =
                parseFloat($(this).find(".item-subtotal").text()) || 0;
            total += subtotal;
        });

        $("#total-cost").text(total.toFixed(2));
    }

    // Form submission
    $("#product-form").submit(function (e) {
        e.preventDefault();

        // Validate that at least one item is added
        if ($(".item-row").length === 0) {
            toastr.warning("Please add at least one item", "Warning");
            return;
        }

        // Check for insufficient inventory warnings
        let hasInsufficientInventory = false;
        $(".item-row").each(function () {
            const row = $(this);
            const quantityInput = row.find(".item-quantity");

            if (quantityInput.hasClass("is-invalid")) {
                hasInsufficientInventory = true;
            }
        });

        if (hasInsufficientInventory) {
            toastr.error(
                "Cannot save product: One or more items have insufficient inventory. Please adjust quantities.",
                "Insufficient Inventory"
            );
            return;
        }

        // Collect form data
        const formData = {
            ProductId: $("#product-id").val() || null,
            ProductCode: $("#product-code").val().trim(),
            ProductName: $("#product-name").val().trim(),
            ProductDescription: $("#product-description").val().trim(),
            Items: [],
        };

        // Collect items data
        $(".item-row").each(function () {
            const row = $(this);
            const itemId = row.find(".item-select").val();
            const quantity = parseFloat(row.find(".item-quantity").val());
            const unitCost = parseFloat(row.find(".item-unit-cost").val());

            if (itemId && quantity > 0) {
                formData.Items.push({
                    item_id: itemId,
                    quantity: quantity,
                    unit_cost: unitCost,
                });
            }
        });

        if (formData.Items.length === 0) {
            toastr.warning("Please add at least one valid item", "Warning");
            return;
        }

        // Disable submit button
        const submitBtn = $("#submit-btn");
        submitBtn
            .prop("disabled", true)
            .html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        // Submit form
        $.ajax({
            url: "/api/products/save",
            type: "POST",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            success: function (result) {
                if (result.status === "success") {
                    toastr.success(
                        result.message || "Product saved successfully",
                        "Success"
                    );

                    // Redirect to products list after a short delay
                    setTimeout(function () {
                        window.location.href = "/products";
                    }, 1500);
                } else {
                    toastr.error(
                        result.message || "Failed to save product",
                        "Error"
                    );
                    submitBtn
                        .prop("disabled", false)
                        .html('<i class="fa fa-save"></i> Save Product');
                }
            },
            error: function (xhr, status, error) {
                let errorMsg = "An error occurred: " + error;

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                toastr.error(errorMsg, "Error");
                submitBtn
                    .prop("disabled", false)
                    .html('<i class="fa fa-save"></i> Save Product');
            },
        });
    });
});
