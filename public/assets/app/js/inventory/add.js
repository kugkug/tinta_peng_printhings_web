$(document).ready(function () {
    var itemId = $("#item-id").val();

    // If editing, load item data
    if (itemId) {
        loadItemData(itemId);
    }

    // Save button
    $('[data-trigger="save"]').on("click", function () {
        saveItem();
    });

    // Clear button
    $('[data-trigger="clear"]').on("click", function () {
        clearForm();
    });

    function loadItemData(id) {
        $.ajax({
            url: "/api/items/get",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            data: { ItemId: id },
            beforeSend: function () {
                $('[data-trigger="save"]')
                    .prop("disabled", true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Loading...');
            },
            success: function (result) {
                $('[data-trigger="save"]')
                    .prop("disabled", false)
                    .html('<i class="fa fa-save"></i> Save');

                if (result.status === "success") {
                    var item = result.data;

                    // Populate form fields
                    $('[data-key="ItemName"]').val(item.item_name);
                    $('[data-key="ItemDescription"]').val(
                        item.item_description
                    );
                    $('[data-key="ItemPrice"]').val(item.item_price);
                    $('[data-key="ItemQuantity"]').val(item.item_quantity);
                    $('[data-key="ItemPricePerPiece"]').val(
                        item.item_price_per_piece
                    );
                    $('[data-key="ItemPartsPerPiece"]').val(
                        item.item_parts_per_piece
                    );
                    $('[data-key="ItemPricePerPart"]').val(
                        item.item_price_per_part
                    );
                    $('[data-key="ItemPricePerPartOfPiece"]').val(
                        item.item_price_per_part_of_piece
                    );
                } else {
                    _show_toastr("error", result.message, "Error");
                }
            },
            error: function (e) {
                $('[data-trigger="save"]')
                    .prop("disabled", false)
                    .html('<i class="fa fa-save"></i> Save');
                console.error(e);
                _show_toastr("error", "Failed to load item data", "Error");
            },
        });
    }

    function saveItem() {
        // Collect form data
        var data = {
            ItemName: $('[data-key="ItemName"]').val(),
            ItemDescription: $('[data-key="ItemDescription"]').val(),
            ItemPrice: $('[data-key="ItemPrice"]').val(),
            ItemQuantity: $('[data-key="ItemQuantity"]').val(),
            ItemPricePerPiece: $('[data-key="ItemPricePerPiece"]').val(),
            ItemPartsPerPiece: $('[data-key="ItemPartsPerPiece"]').val(),
            ItemPricePerPart: $('[data-key="ItemPricePerPart"]').val(),
            ItemPricePerPartOfPiece: $(
                '[data-key="ItemPricePerPartOfPiece"]'
            ).val(),
        };

        // Add item ID if editing
        if (itemId) {
            data.ItemId = itemId;
        }

        // Validate required fields
        if (
            !data.ItemName ||
            !data.ItemPrice ||
            !data.ItemQuantity ||
            !data.ItemPricePerPiece ||
            !data.ItemPartsPerPiece ||
            !data.ItemPricePerPart ||
            !data.ItemPricePerPartOfPiece
        ) {
            _show_toastr(
                "warning",
                "Please fill in all required fields",
                "Validation Error"
            );
            return;
        }

        // Validate numeric fields
        if (
            isNaN(data.ItemPrice) ||
            isNaN(data.ItemQuantity) ||
            isNaN(data.ItemPricePerPiece) ||
            isNaN(data.ItemPartsPerPiece) ||
            isNaN(data.ItemPricePerPart) ||
            isNaN(data.ItemPricePerPartOfPiece)
        ) {
            _show_toastr(
                "warning",
                "Price and quantity fields must be valid numbers",
                "Validation Error"
            );
            return;
        }

        $.ajax({
            url: "/api/items/save",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            data: data,
            beforeSend: function () {
                $('[data-trigger="save"]')
                    .prop("disabled", true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            },
            success: function (result) {
                $('[data-trigger="save"]')
                    .prop("disabled", false)
                    .html('<i class="fa fa-save"></i> Save');

                if (result.status === "success") {
                    if (result.js) {
                        eval(result.js);
                    }
                } else {
                    _show_toastr("error", result.message, "Error");
                }
            },
            error: function (e) {
                $('[data-trigger="save"]')
                    .prop("disabled", false)
                    .html('<i class="fa fa-save"></i> Save');
                console.error(e);

                var errorMessage = "Failed to save item";
                if (e.responseJSON && e.responseJSON.message) {
                    errorMessage = e.responseJSON.message;
                }

                _show_toastr("error", errorMessage, "Error");
            },
        });
    }

    function clearForm() {
        if (itemId) {
            // If editing, reload the original data
            loadItemData(itemId);
        } else {
            // If adding new, clear all fields
            $('[data-key="ItemName"]').val("");
            $('[data-key="ItemDescription"]').val("");
            $('[data-key="ItemPrice"]').val("");
            $('[data-key="ItemQuantity"]').val("");
            $('[data-key="ItemPricePerPiece"]').val("");
            $('[data-key="ItemPartsPerPiece"]').val("");
            $('[data-key="ItemPricePerPart"]').val("");
            $('[data-key="ItemPricePerPartOfPiece"]').val("");
        }
    }
});
