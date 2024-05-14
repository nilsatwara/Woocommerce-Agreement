jQuery(document).ready(function ($) {
    // Define your trigger, for example, a button click
    $('.pdf_regenerate').on('click', function () {
        // Get the order ID from somewhere (e.g., an input field)

        var orderID = $(this).attr("data-order");
        var productID = $(this).attr("data-product");

        // Create the AJAX request
        $.ajax({
            type: 'POST',
            url: regenerate_pdf.ajaxurl,
            data: {
                action: 'regenerate_pdf_admin',
                order_id: orderID,
                product_id: productID,
            },
            success: function (response) {
                // Handle the response data
                if (response.data.success == true) {
                    alert(response.data.message)
                } else {
                    alert(response.error.message)
                }
            }, error: function (xhr, status, error) {
                console.log('Error updating canvas value: ' + error);
            }
        });
    });
});
