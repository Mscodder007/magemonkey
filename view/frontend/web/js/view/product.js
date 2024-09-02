define(['jquery', 'mage/url'], function($, url) {
    function productSearch(config) {
        var searchContainer = $('.product-search-container');
        var searchResultsContainer = $('.product-search-results');
        console.log(config);
        // Function to perform the product search
        function performProductSearch(searchTerm) {
            $.ajax({
                url: '/rest/V1/products',
                type: 'GET',
                dataType: 'json',
                data: {
                    searchCriteria: {
                        filter_groups: [{
                                filters: [{
                                        field: 'name',
                                        value: '%' + searchTerm + '%',
                                        condition_type: 'like'
                                    },
                                    {
                                        field: 'sku',
                                        value: '%' + searchTerm + '%',
                                        condition_type: 'like'
                                    },
                                    {
                                        field: 'store_id',
                                        value: config.storeId,
                                        condition_type: 'eq'
                                    }
                                ]
                            },
                            {
                                filters: [{
                                    field: 'type_id',
                                    value: 'simple',
                                    condition_type: 'eq'
                                }]
                            }
                        ],
                        current_page: 1,
                        page_size: 10,
                    },
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + config.token);
                },
                success: function(response) {
                    displaySearchResults(response.items);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        // Function to display search results in a popup
        function displaySearchResults(results) {
            searchResultsContainer.empty();

            if (results.length > 0) {
                results.forEach(function(product) {
                    var productInfo = '<tr class="search-result-item" data-image="' + url.build('media/catalog/product' + product.media_gallery_entries[0].file) +'" data-id="' + product.id + '"   data-name="' + product.name + '" data-sku="' + product.sku + '">' +
                        '<td><img class="popup-image" src="' + url.build('media/catalog/product' + product.media_gallery_entries[0].file) + '" height = "200px" width = "150px"></img></td>' +
                        '<td><strong>Name:</strong> ' + product.name + ' <strong>SKU:</strong> ' + product.sku + ' </td>' +
                        '<input type="hidden" name="id" value="' + product.id + '">' +
                        '</tr>';
                    searchResultsContainer.append(productInfo);
                });

                // Show the popup
                searchContainer.show();
            } else {
                searchResultsContainer.html('No results found.');
            }
        }

        // Handle user input and trigger search
        $('#search-input').on('keyup', function() {
            var searchTerm = $(this).val();
            if (searchTerm.length > 2) {
                performProductSearch(searchTerm);
            } else {
                //searchContainer.hide();
            }
        });

        $('body').on('click', '.delete-row', function() {
            $(this).closest('tr').remove(); // Remove the row when the delete button is clicked
        });

        // Handle click on a search result
        searchResultsContainer.on('click', '.search-result-item', function() {
            $('.search-result-item').hide();
            var productData = {
                id: $(this).data('id'),
                name: $(this).data('name'),
                sku: $(this).data('sku'),
                image:$(this).data('image')
            }

            // Create a table row for the selected product with additional input fields
            var productRow = $('<tr>');
            productRow.append('<td><img class="popup-image" src="'+productData.image+ '"></img></td>');
            productRow.append('<td>' + productData.name + '</td>');
            productRow.append('<input type="hidden" name="product_id[]" value="' + productData.id + '">');
            productRow.append('<td><input type="text" required name="qty[' + productData.id + ']" value=""></td>');
            productRow.append('<td><input type="file" name="support_media[' + productData.id + '][]"  multiple="multiple" value=""> <span class="image-preview-container"></span></td>');
            productRow.append('<td><select id="reasonsSelect" required  name="reason[' + productData.id + ']"  class="selresons"></select></td>');
            productRow.append('<td></td>');
            productRow.append('<td><input type="text" name="order_id[' + productData.id + ']" value=""></td>');
            productRow.append('<td><a href="#" class="delete-row"><span>Delete Item</span></a></td></tr>'); // Add a delete button

            var productForm = $('#product-form');
            productForm.append(productRow);

            /* reasons Otions */
            var select = productRow.find('.selresons');
            select.empty(); // Clear existing options
            var responseData = config;
            var reasonsArray = JSON.parse(responseData.reasons);
            $.each(reasonsArray, function(index, value) {
                var optionElement = $('<option>', {
                    value: value,
                    text: value
                });
                select.append(optionElement);
            });

            /* Status Otions */
            var selectStatus = productRow.find('.rmastatus');
            selectStatus.empty(); // Clear existing options
            var responseStatusData = config;
            var reasonsStatusArray = JSON.parse(responseStatusData.status);
            console.log(reasonsStatusArray);
            $.each(reasonsStatusArray, function(index, value) {
                var optionStatusElement = $('<option>', {
                    value: value,
                    text: value
                });
                selectStatus.append(optionStatusElement);
            });
        });
    }
    return productSearch;
});