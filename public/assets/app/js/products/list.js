$(document).ready(function () {
    var productsTable;

    // Initialize DataTable
    loadProductsTable();

    function loadProductsTable() {
        // Destroy existing table if it exists
        if ($.fn.DataTable.isDataTable("#products-table")) {
            $("#products-table").DataTable().destroy();
        }

        $.ajax({
            url: "/api/products/list",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            success: function (result) {
                if (result.status === "success") {
                    productsTable = $("#products-table").DataTable({
                        data: result.data,
                        columns: [
                            {
                                data: "product_code",
                                render: function (data, type, row) {
                                    return (
                                        '<span class="badge badge-primary">' +
                                        data +
                                        "</span>"
                                    );
                                },
                            },
                            {
                                data: "product_name",
                                render: function (data, type, row) {
                                    return "<strong>" + data + "</strong>";
                                },
                            },
                            {
                                data: "product_description",
                                render: function (data, type, row) {
                                    if (!data)
                                        return '<span class="text-muted">No description</span>';
                                    var desc =
                                        data.length > 50
                                            ? data.substring(0, 50) + "..."
                                            : data;
                                    return (
                                        '<small class="text-muted">' +
                                        desc +
                                        "</small>"
                                    );
                                },
                            },
                            {
                                data: "total_cost",
                                render: function (data) {
                                    return (
                                        '<span class="text-success">₱' +
                                        data +
                                        "</span>"
                                    );
                                },
                            },
                            {
                                data: "items_count",
                                render: function (data) {
                                    return (
                                        '<span class="badge badge-info">' +
                                        data +
                                        " items</span>"
                                    );
                                },
                            },
                            {
                                data: "created_at",
                                render: function (data) {
                                    return "<small>" + data + "</small>";
                                },
                            },
                            {
                                data: null,
                                orderable: false,
                                render: function (data, type, row) {
                                    return `
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" data-action="view" data-id="${row.id}" title="View">
                                                <i class="fa fa-eye"></i>
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
                        order: [[5, "desc"]],
                        pageLength: 25,
                        responsive: true,
                    });
                } else {
                    toastr.error(
                        result.message || "Failed to load products",
                        "Error"
                    );
                }
            },
            error: function (xhr, status, error) {
                toastr.error("An error occurred: " + error, "Error");
            },
        });
    }

    // Handle table row actions
    $("#products-table").on("click", "[data-action]", function () {
        const action = $(this).data("action");
        const productId = $(this).data("id");

        switch (action) {
            case "view":
                viewProduct(productId);
                break;
            case "edit":
                window.location.href = "/products/edit/" + productId;
                break;
            case "delete":
                deleteProduct(productId);
                break;
        }
    });

    // View product details
    function viewProduct(productId) {
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

                    // Populate modal
                    $("#view-product-code").text(product.product_code);
                    $("#view-product-name").text(product.product_name);
                    $("#view-product-description").text(
                        product.product_description || "No description"
                    );

                    // Build items table
                    let itemsHtml = "";
                    let total = 0;

                    product.items.forEach(function (item) {
                        itemsHtml += `
                            <tr>
                                <td>${item.sku}</td>
                                <td>${item.item_name}</td>
                                <td>${item.quantity}</td>
                                <td>₱${parseFloat(item.unit_cost).toFixed(
                                    2
                                )}</td>
                                <td>₱${parseFloat(item.subtotal).toFixed(
                                    2
                                )}</td>
                            </tr>
                        `;
                        total += parseFloat(item.subtotal);
                    });

                    $("#view-product-items").html(itemsHtml);
                    $("#view-product-total").text("₱" + total.toFixed(2));

                    // Show modal
                    $("#viewProductModal").modal("show");
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

    // Delete product
    function deleteProduct(productId) {
        if (!confirm("Are you sure you want to delete this product?")) {
            return;
        }

        $.ajax({
            url: "/api/products/delete",
            type: "POST",
            data: { ProductId: productId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
            },
            success: function (result) {
                if (result.status === "success") {
                    toastr.success(
                        result.message || "Product deleted successfully",
                        "Success"
                    );
                    loadProductsTable();
                } else {
                    toastr.error(
                        result.message || "Failed to delete product",
                        "Error"
                    );
                }
            },
            error: function (xhr, status, error) {
                toastr.error("An error occurred: " + error, "Error");
            },
        });
    }
});
