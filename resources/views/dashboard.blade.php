<x-app-layout>
    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4">Welcome, {{ auth()->user()->name }}</h1>
        <div class="flex justify-end mb-4">
            <button class="bg-blue-500 text-white px-4 py-2 rounded" data-bs-toggle="modal" data-bs-target="#addProductModal">
                Add New Item
            </button>
        </div>

        <table class="table table-bordered table-hover table-striped w-100">
            <thead class="bg-dark text-white">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productTable">
                @foreach ($products as $product)
                    <tr id="product-{{ $product->id }}">
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->description }}</td>
                        <td>{{ $product->qty }}</td>
                        <td>{{ $product->price }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-btn" data-id="{{ $product->id }}">Edit</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $product->id }}">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>


        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addProductForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="qty" class="form-label">Quantity</label>
                            <input type="number" name="qty" id="qty" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" name="price" id="price" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editProductForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_qty" class="form-label">Quantity</label>
                            <input type="number" name="qty" id="edit_qty" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Price</label>
                            <input type="number" name="price" id="edit_price" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include JavaScript file -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Add new product
            $('#addProductForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('products.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        checkProductTable();
                        $('#addProductModal').modal('hide');
                        $('#addProductForm')[0].reset();
                        const product = response.product;
                        $('#productTable').prepend(`
                            <tr id="product-${product.id}">
                                <td>${product.id}</td>
                                <td>${product.name}</td>
                                <td>${product.description}</td>
                                <td>${product.qty}</td>
                                <td>${product.price}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm edit-btn" data-id="${product.id}">Edit</button>
                                    <button class="btn btn-danger btn-sm delete-btn" data-id="${product.id}">Delete</button>
                                </td>
                            </tr>
                        `);
                        Swal.fire({
                            title: 'Success!',
                            text: response.success,
                            icon: 'success',
                            confirmButtonText: 'Okay'
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error: ' + xhr.responseJSON.message,
                            icon: 'error',
                            confirmButtonText: 'Okay'
                        });
                    }
                });

            });

            // show the data in edit modal
            $(document).on('click', '.edit-btn', function () {
                const id = $(this).data('id');
                
                $.ajax({
                    url: `/products/${id}/edit`,
                    method: 'GET',
                    success: function (response) {
                        checkProductTable();
                        const product = response.product;
                        $('#edit_name').val(product.name);
                        $('#edit_description').val(product.description);
                        $('#edit_qty').val(product.qty);
                        $('#edit_price').val(product.price);

                        // Set the form action to update the specific product
                        $('#editProductForm').attr('action', `/products/${id}`);
                        $('#editProductForm').attr('method', 'POST');
                        $('#editProductForm').append('<input type="hidden" name="_method" value="PUT">');

                        // Show Edit Product Modal
                        $('#editProductModal').modal('show');
                    },
                    error: function (xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Unable to fetch product data.',
                            icon: 'error',
                            confirmButtonText: 'Okay'
                        });
                    }
                });

            });

            // Submit update form via Ajax
            $('#editProductForm').on('submit', function (e) {
                e.preventDefault();
                const id = $(this).attr('action').split('/').pop();

                $.ajax({
                    url: `/products/${id}`,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function (response) {
                        checkProductTable();
                        const product = response.product;
                        $(`#product-${product.id} td:nth-child(2)`).text(product.name);
                        $(`#product-${product.id} td:nth-child(3)`).text(product.description);
                        $(`#product-${product.id} td:nth-child(4)`).text(product.qty);
                        $(`#product-${product.id} td:nth-child(5)`).text(product.price);

                        $('#editProductModal').modal('hide');
                        Swal.fire({
                            title: 'Success!',
                            text: response.success,
                            icon: 'success',
                            confirmButtonText: 'Okay'
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error: ' + xhr.responseJSON.message,
                            icon: 'error',
                            confirmButtonText: 'Okay'
                        });
                    }
                });

          
            });

            // Delete product
            $(document).on('click', '.delete-btn', function () {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/products/${id}`,
                            method: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (response) {
                                checkProductTable();
                                $(`#product-${id}`).remove();
                                Swal.fire(
                                    'Deleted!',
                                    response.success,
                                    'success'
                                );
                            },
                            error: function (xhr) {
                                Swal.fire(
                                    'Error!',
                                    'Error: ' + xhr.responseJSON.message,
                                    'error'
                                );
                            }
                        });
                    }
                });


            });

            // check if product is available 
            checkProductTable();
        });

        function checkProductTable() {
            setTimeout(function() {
                if ($('#productTable tr').length === 0) {
                    $('#productTable').html('<tr id="no-products-message"><td colspan="6" class="text-center text-gray-500">No products available</td></tr>');
                } else {
                    $('#no-products-message').remove();  // Remove the message if products exist
                }
            }, 0);  // Ensures DOM updates are applied before checking
        }
        
    </script>
</x-app-layout>
