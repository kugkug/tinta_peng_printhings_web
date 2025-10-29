$(document).ready(function () {
    var itemsTable;
    var selectedItems = [];

    // Initialize DataTable
    loadItemsTable();

    function loadItemsTable() {
        // Destroy existing table if it exists
        if ($.fn.DataTable.isDataTable("#items-table")) {
            $("#items-table").DataTable().destroy();
        }

        $.ajax({
            url: "/api/items/list",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            success: function (result) {
                if (result.status === "success") {
                    // Show low stock alert if there are items with low stock
                    if (result.low_stock_count && result.low_stock_count > 0) {
                        $("#low-stock-count").text(result.low_stock_count);
                        $("#low-stock-threshold").text(result.low_stock_threshold);
                        $("#low-stock-alert").show();
                    } else {
                        $("#low-stock-alert").hide();
                    }
                    
                    itemsTable = $("#items-table").DataTable({
                        data: result.data,
                        columns: [
                            {
                                data: null,
                                orderable: false,
                                className: "select-checkbox",
                                render: function (data, type, row) {
                                    return `<input type="checkbox" class="item-checkbox" data-id="${row.id}">`;
                                },
                            },
                            {
                                data: "sku",
                                render: function (data, type, row) {
                                    return (
                                        '<span class="badge badge-info">' +
                                        data +
                                        "</span>"
                                    );
                                },
                            },
                            {
                                data: "item_name",
                                render: function (data, type, row) {
                                    var lowStockBadge = row.is_low_stock
                                        ? '<span class="badge badge-warning ml-2" title="Low Stock"><i class="fa fa-exclamation-triangle"></i> Low Stock</span>'
                                        : "";
                                    var desc = row.item_description
                                        ? '<br><small class="text-muted">' +
                                          row.item_description.substring(
                                              0,
                                              50
                                          ) +
                                          "...</small>"
                                        : "";
                                    return data + lowStockBadge + desc;
                                },
                            },
                            {
                                data: "item_price",
                                render: function (data) {
                                    return (
                                        '<span class="text-success">₱' +
                                        data +
                                        "</span>"
                                    );
                                },
                            },
                            { 
                                data: "item_quantity",
                                render: function (data, type, row) {
                                    if (row.is_low_stock) {
                                        return (
                                            '<span class="badge badge-danger" style="font-size: 1em;">' +
                                            '<i class="fa fa-exclamation-triangle"></i> ' +
                                            data +
                                            "</span>"
                                        );
                                    }
                                    return '<span class="badge badge-success" style="font-size: 1em;">' + data + "</span>";
                                },
                            },
                            {
                                data: "item_price_per_piece",
                                render: function (data) {
                                    return "₱" + data;
                                },
                            },
                            { data: "item_parts_per_piece" },
                            {
                                data: null,
                                orderable: false,
                                render: function (data, type, row) {
                                    return `
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-success" data-action="download-pdf" data-id="${row.id}" title="Download Barcode PDF">
                                                <i class="fa fa-download"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-info" data-action="view-barcode" data-id="${row.id}" title="View Barcode">
                                                <i class="fa fa-barcode"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary" data-action="edit" data-id="${row.id}" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-action="delete" data-id="${row.id}" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    `;
                                },
                            },
                        ],
                        order: [[1, "desc"]],
                        pageLength: 25,
                        responsive: true,
                        language: {
                            emptyTable:
                                "No items found. Click 'Add New' to create your first item.",
                        },
                    });

                    // Attach event handlers
                    attachEventHandlers();
                }
            },
            error: function (e) {
                console.error(e);
                _show_toastr("error", "Failed to load items", "Error");
            },
        });
    }

    function attachEventHandlers() {
        // Select all checkbox
        $("#select-all").on("change", function () {
            var isChecked = $(this).is(":checked");
            $(".item-checkbox").prop("checked", isChecked);
            updateSelectedItems();
        });

        // Individual checkbox
        $("#items-table").on("change", ".item-checkbox", function () {
            updateSelectedItems();
            updateSelectAllCheckbox();
        });

        // Clear selection button
        $("#clear-selection").on("click", function () {
            $(".item-checkbox").prop("checked", false);
            $("#select-all").prop("checked", false);
            updateSelectedItems();
        });

        // Download selected PDFs
        $("#download-selected-pdf").on("click", function () {
            if (selectedItems.length === 0) {
                _show_toastr(
                    "warning",
                    "Please select at least one item",
                    "No Selection"
                );
                return;
            }
            downloadMultiplePDF(selectedItems);
        });

        // Download single PDF
        $("#items-table").on(
            "click",
            '[data-action="download-pdf"]',
            function () {
                var itemId = $(this).data("id");
                downloadSinglePDF(itemId);
            }
        );

        // Edit button
        $("#items-table").on("click", '[data-action="edit"]', function () {
            var itemId = $(this).data("id");
            window.location.href = "/inventory/edit/" + itemId;
        });

        // Delete button
        $("#items-table").on("click", '[data-action="delete"]', function () {
            var itemId = $(this).data("id");
            deleteItem(itemId);
        });

        // View barcode button
        $("#items-table").on(
            "click",
            '[data-action="view-barcode"]',
            function () {
                var itemId = $(this).data("id");
                generateBarcode(itemId);
            }
        );
    }

    function updateSelectedItems() {
        selectedItems = [];
        $(".item-checkbox:checked").each(function () {
            selectedItems.push($(this).data("id"));
        });

        // Update UI
        $("#selected-count").text(selectedItems.length);
        if (selectedItems.length > 0) {
            $("#bulk-actions-bar").slideDown(200);
        } else {
            $("#bulk-actions-bar").slideUp(200);
        }
    }

    function updateSelectAllCheckbox() {
        var totalCheckboxes = $(".item-checkbox").length;
        var checkedCheckboxes = $(".item-checkbox:checked").length;
        $("#select-all").prop(
            "checked",
            totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0
        );
    }

    function downloadSinglePDF(itemId) {
        // Open PDF in new tab/download
        window.open("/inventory/barcode/pdf/" + itemId, "_blank");
    }

    function downloadMultiplePDF(itemIds) {
        // Create a form and submit it
        var form = $("<form>", {
            method: "POST",
            action: "/inventory/barcode/pdf/multiple",
            target: "_blank",
        });

        // Add CSRF token
        form.append(
            $("<input>", {
                type: "hidden",
                name: "_token",
                value: $('meta[name="_token"]').attr("content"),
            })
        );

        // Add item IDs
        itemIds.forEach(function (id) {
            form.append(
                $("<input>", {
                    type: "hidden",
                    name: "item_ids[]",
                    value: id,
                })
            );
        });

        // Append to body, submit, and remove
        $("body").append(form);
        form.submit();
        form.remove();

        _show_toastr(
            "success",
            "PDF download started for " + itemIds.length + " item(s)",
            "Success"
        );
    }

    function deleteItem(itemId) {
        _confirm(
            "Delete Item",
            "Are you sure you want to delete this item? This action cannot be undone.",
            "warning",
            "Yes, delete it!",
            false,
            function () {
                $.ajax({
                    url: "/api/items/delete",
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="_token"]').attr(
                            "content"
                        ),
                    },
                    data: { ItemId: itemId },
                    success: function (result) {
                        if (result.status === "success") {
                            if (result.js) {
                                eval(result.js);
                            }
                        } else {
                            _show_toastr("error", result.message, "Error");
                        }
                    },
                    error: function (e) {
                        console.error(e);
                        _show_toastr("error", "Failed to delete item", "Error");
                    },
                });
            }
        );
    }

    function generateBarcode(itemId) {
        $.ajax({
            url: "/api/items/generate-barcode",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            data: { ItemId: itemId },
            success: function (result) {
                if (result.status === "success") {
                    $("#barcode-item-name").text(result.item_name);
                    $("#barcode-display").html(result.barcode_html);
                    $("#barcode-sku").text("SKU: " + result.sku);
                    $("#barcodeModal").modal("show");
                } else {
                    _show_toastr("error", result.message, "Error");
                }
            },
            error: function (e) {
                console.error(e);
                _show_toastr("error", "Failed to generate barcode", "Error");
            },
        });
    }
});
