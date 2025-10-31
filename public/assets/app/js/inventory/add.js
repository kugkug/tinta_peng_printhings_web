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
                    $('[data-key="Brand"]').val(item.brand);
                    $('[data-key="ItemName"]').val(item.item_name);
                    $('[data-key="VariantOne"]').val(item.variant_one);
                    $('[data-key="VariantTwo"]').val(item.variant_two);
                    $('[data-key="Size"]').val(item.size);
                    $('[data-key="Microns"]').val(item.microns);
                    $('[data-key="Gsm"]').val(item.gsm);
                    $('[data-key="SheetsPerPack"]').val(item.sheets_per_pack);
                    $('[data-key="PriceWithoutShippingFee"]').val(
                        item.price_without_shipping_fee
                    );
                    $('[data-key="EstimatedShippingFee"]').val(
                        item.estimated_shipping_fee
                    );
                    $('[data-key="DatePurchased"]').val(item.date_purchased);
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
            Brand: $('[data-key="Brand"]').val(),
            ItemName: $('[data-key="ItemName"]').val(),
            VariantOne: $('[data-key="VariantOne"]').val(),
            VariantTwo: $('[data-key="VariantTwo"]').val(),
            Size: $('[data-key="Size"]').val(),
            Microns: $('[data-key="Microns"]').val(),
            Gsm: $('[data-key="Gsm"]').val(),
            SheetsPerPack: $('[data-key="SheetsPerPack"]').val(),
            PriceWithoutShippingFee: $(
                '[data-key="PriceWithoutShippingFee"]'
            ).val(),
            EstimatedShippingFee: $(
                '[data-key="EstimatedShippingFee"]'
            ).val(),
            DatePurchased: $('[data-key="DatePurchased"]').val(),
        };

        // Add item ID if editing
        if (itemId) {
            data.ItemId = itemId;
        }

        // Validate required fields
        if (
            !data.Brand ||
            !data.ItemName ||
            !data.PriceWithoutShippingFee
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
            (data.SheetsPerPack && isNaN(data.SheetsPerPack)) ||
            isNaN(data.PriceWithoutShippingFee) ||
            (data.EstimatedShippingFee && isNaN(data.EstimatedShippingFee))
        ) {
            _show_toastr(
                "warning",
                "Numeric fields must contain valid numbers",
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
            $('[data-key="Brand"]').val("");
            $('[data-key="ItemName"]').val("");
            $('[data-key="VariantOne"]').val("");
            $('[data-key="VariantTwo"]').val("");
            $('[data-key="Size"]').val("");
            $('[data-key="Microns"]').val("");
            $('[data-key="Gsm"]').val("");
            $('[data-key="SheetsPerPack"]').val("");
            $('[data-key="PriceWithoutShippingFee"]').val("");
            $('[data-key="EstimatedShippingFee"]').val("");
            $('[data-key="DatePurchased"]').val("");
        }
    }
});
