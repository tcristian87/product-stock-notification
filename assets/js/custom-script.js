jQuery(document).ready(function ($) {


    $(".cart").on('change', function(){
            if(!$('.back-to-stock-notification-form').length && $('.custom-out-of-stock').length){
                let product_id = $('.variation_id').val();
                 PSN_form_append(product_id);

            }
        });

        if ($('.custom-out-of-stock').length) {
            let product_id = woocommerce_params.product_id;
            PSN_form_append(product_id);
        }


    function PSN_form_append(product_id){
                // Define the form HTML
                var backToStockForm = `
                <div class="back-to-stock-notification-form">
                    <form id="back-to-stock-form" method="post">
                        <p>Notify me when this product is back in stock</p>
                        <input type="email" name="back_to_stock_email" id="back_to_stock_email" placeholder="Enter your email address" required>
                        <input type="hidden" name="back_to_stock_product_id" value="${product_id}">
                        <input type="hidden" name="psn_nonce_check" value="${woocommerce_params.nonce}">
                        <button type="button" class="button psn_back_to_stock_submit" value="Notify Me">Notify Me</button>
                        <span id="back_to_stock_response"></span>
                    </form>
                </div>`;
            ($('.psn_back_to_stock_submit').length) ? $('.custom-out-of-stock').append(backToStockForm) : $('.custom-out-of-stock').append(backToStockForm);
        }

    $(document).on('click', '.psn_back_to_stock_submit', function (e) {

        e.preventDefault();

        var email = $('#back_to_stock_email').val();
        var product_id = $('input[name="back_to_stock_product_id"]').val();
        var nonce = $('input[name="psn_nonce_check"]').val();

        $.ajax({
            url: psn_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'back_to_stock_notify',
                email: email,
                product_id: product_id,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {

                    $("#back-to-stock-form button").prop("disabled", true);
                    $("#back-to-stock-form button").prop("hidden", true);
                    $('#back_to_stock_response').text(response.data);
                    setTimeout(function(){
                        location.reload();
                    }, 3000);
                } else {
                    $("#back-to-stock-form button").prop("hidden", true);
                    $('#back_to_stock_response').text(response.data).css('color','red');
                }
            },
            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                alert(err.Message);
                $('#back_to_stock_response').text( 'An error occurred. Please try again.', 'product-stock-notification');
            }
        });
    });

  });