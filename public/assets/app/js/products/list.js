$(document).ready(function () {
    var productsTable;

    // Initialize DataTable
    loadProductsTable();

    function loadProductsTable() {
        if ($.fn.DataTable.isDataTable('#products-table')) {
            $('#products-table').DataTable().destroy();
        }

        $.ajax({
            url: '/api/products/list',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            },
            success: function (result) {
                if (result.status === 'success') {
                    productsTable = $('#products-table').DataTable({
                        data: result.data,
                        columns: [
                            {
                                data: 'product_code',
                                render: function (data) {
                                    return '<span class="badge badge-primary">' + data + '</span>';
                                },
                            },
                            {
                                data: 'product_name',
                                render: function (data) {
                                    return '<strong>' + data + '</strong>';
                                },
                            },
                            {
                                data: 'product_description',
                                render: function (data) {
                                    if (!data) {
                                        return '<span class="text-muted">No description</span>';
                                    }
                                    var desc = data.length > 60 ? data.substring(0, 60) + '…' : data;
                                    return '<small class="text-muted">' + desc + '</small>';
                                },
                            },
                            {
                                data: 'materials_count',
                                className: 'text-center',
                                render: function (data) {
                                    return '<span class="badge badge-light text-primary">' + data + '</span>';
                                },
                            },
                            {
                                data: 'ink_count',
                                className: 'text-center',
                                render: function (data) {
                                    return '<span class="badge badge-light text-info">' + data + '</span>';
                                },
                            },
                            {
                                data: 'packaging_count',
                                className: 'text-center',
                                render: function (data) {
                                    return '<span class="badge badge-light text-warning">' + data + '</span>';
                                },
                            },
                            {
                                data: 'total_cost',
                                className: 'text-right',
                                render: function (data) {
                                    return '<span class="text-success">₱' + data + '</span>';
                                },
                            },
                            {
                                data: 'created_at',
                                render: function (data) {
                                    return '<small>' + data + '</small>';
                                },
                            },
                            {
                                data: null,
                                orderable: false,
                                className: 'text-center',
                                render: function (data, type, row) {
                                    return (
                                        '<div class="btn-group" role="group">' +
                                        '<button type="button" class="btn btn-sm btn-info" data-action="view" data-id="' + row.id + '" title="View"><i class="fa fa-eye"></i></button>' +
                                        '<button type="button" class="btn btn-sm btn-primary" data-action="edit" data-id="' + row.id + '" title="Edit"><i class="fa fa-edit"></i></button>' +
                                        '<button type="button" class="btn btn-sm btn-danger" data-action="delete" data-id="' + row.id + '" title="Delete"><i class="fa fa-trash"></i></button>' +
                                        '</div>'
                                    );
                                },
                            },
                        ],
                        order: [[7, 'desc']],
                        pageLength: 25,
                        responsive: true,
                    });
                } else {
                    toastr.error(result.message || 'Failed to load products', 'Error');
                }
            },
            error: function (xhr, status, error) {
                toastr.error('An error occurred: ' + error, 'Error');
            },
        });
    }

    $('#products-table').on('click', '[data-action]', function () {
        const action = $(this).data('action');
        const productId = $(this).data('id');

        switch (action) {
            case 'view':
                viewProduct(productId);
                break;
            case 'edit':
                window.location.href = '/products/edit/' + productId;
                break;
            case 'delete':
                deleteProduct(productId);
                break;
        }
    });

    function viewProduct(productId) {
        $.ajax({
            url: '/api/products/get',
            type: 'POST',
            data: { ProductId: productId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            },
            success: function (result) {
                if (result.status !== 'success') {
                    toastr.error(result.message || 'Failed to load product details', 'Error');
                    return;
                }

                const product = result.data;
                $('#view-product-code').text(product.product_code || '');
                $('#view-product-name').text(product.product_name || '');
                $('#view-product-description').text(product.product_description || 'No description');
                const totalCost = parseFloat(product.total_cost || 0).toFixed(2);
                $('#view-product-total').text(totalCost);

                populateSection('#view-materials-body', product.materials, function (entry) {
                    return (
                        '<tr>' +
                        '<td>' + (entry.sku || '—') + '</td>' +
                        '<td>' + (entry.item_name || '—') + '</td>' +
                        '<td class="text-right">' + formatNumber(entry.quantity_used) + '</td>' +
                        '<td class="text-right">₱' + formatCurrency(entry.unit_price) + '</td>' +
                        '<td class="text-right">₱' + formatCurrency(entry.total_cost) + '</td>' +
                        '</tr>'
                    );
                }, 5);

                populateSection('#view-inks-body', product.inks, function (entry) {
                    return (
                        '<tr>' +
                        '<td>' + (entry.sku || '—') + '</td>' +
                        '<td>' + (entry.item_name || '—') + '</td>' +
                        '<td class="text-right">' + formatNumber(entry.pages_yield) + '</td>' +
                        '<td class="text-right">₱' + formatCurrency(entry.cost_per_page) + '</td>' +
                        '<td class="text-right">' + formatNumber(entry.total_pages_printed) + '</td>' +
                        '<td class="text-right">₱' + formatCurrency(entry.total_cost) + '</td>' +
                        '</tr>'
                    );
                }, 6);

                populateSection('#view-packaging-body', product.packaging, function (entry) {
                    return (
                        '<tr>' +
                        '<td>' + (entry.sku || '—') + '</td>' +
                        '<td>' + (entry.item_name || '—') + '</td>' +
                        '<td class="text-right">' + formatNumber(entry.quantity_used) + '</td>' +
                        '<td class="text-right">₱' + formatCurrency(entry.total_cost) + '</td>' +
                        '</tr>'
                    );
                }, 4);

                $('#viewProductModal').modal('show');
            },
            error: function (xhr, status, error) {
                toastr.error('An error occurred: ' + error, 'Error');
            },
        });
    }

    function populateSection(selector, entries, rowBuilder, colspan) {
        const $body = $(selector);
        $body.empty();

        if (!entries || entries.length === 0) {
            $body.append(
                '<tr><td class="text-center text-muted" colspan="' + colspan + '">No records found.</td></tr>'
            );
            return;
        }

        entries.forEach(function (entry) {
            $body.append(rowBuilder(entry));
        });
    }

    function formatCurrency(value) {
        const number = parseFloat(value || 0);
        return isNaN(number) ? '0.00' : number.toFixed(2);
    }

    function formatNumber(value) {
        const number = parseFloat(value || 0);
        if (isNaN(number)) {
            return '0';
        }
        return Number.isInteger(number) ? number.toString() : number.toFixed(2);
    }

    function deleteProduct(productId) {
        if (!confirm('Are you sure you want to delete this product?')) {
            return;
        }

        $.ajax({
            url: '/api/products/delete',
            type: 'POST',
            data: { ProductId: productId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            },
            success: function (result) {
                if (result.status === 'success') {
                    toastr.success(result.message || 'Product deleted successfully', 'Success');
                    loadProductsTable();
                } else {
                    toastr.error(result.message || 'Failed to delete product', 'Error');
                }
            },
            error: function (xhr, status, error) {
                toastr.error('An error occurred: ' + error, 'Error');
            },
        });
    }
});
