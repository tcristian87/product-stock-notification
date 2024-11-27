<?php
namespace PSN\Ajax;
if ( ! defined( 'ABSPATH' ) ) exit;

class ProductStockNotification_Ajax {
    public function __construct() {
        add_action('wp_ajax_back_to_stock_notify', [$this, 'back_to_stock_notify_callback'], 5);
        add_action('wp_ajax_nopriv_back_to_stock_notify', [$this, 'back_to_stock_notify_callback'], 5 );
    }

    public function back_to_stock_notify_callback() {
        // Check nonce for security
        check_ajax_referer('back_to_stock_nonce', 'nonce');

        // Validate and sanitize the input
        if (isset($_POST['email']) && isset($_POST['product_id'])) {
            $email = sanitize_email(wp_unslash($_POST['email']));
            $product_id = intval($_POST['product_id']);

            // Ensure the email is valid and product ID is a valid number
            if (!is_email($email) || $product_id <= 0) {
                wp_send_json_error(__('Invalid email or product ID provided.', 'product-stock-notification'));
                wp_die(); // Exit after sending error
            }

            // Check if the user or email is already registered
            $already_registered = \PSN\Database\ProductStockNotification_Database::check_if_registered($email, $product_id);
            if ($already_registered) {
                wp_send_json_error(__('You are already registered for notifications when this product is back in stock.', 'product-stock-notification'));
            } else {
                // Insert into the database
                \PSN\Database\ProductStockNotification_Database::insert_entry(get_current_user_id(), $email, $product_id);
                wp_send_json_success(__('You\'ll receive an email as soon as this product is back in stock. Stay tuned!', 'product-stock-notification'));
            }
        } else {
            wp_send_json_error(__('Missing data. Please provide an email and product ID.', 'product-stock-notification'));
        }

        wp_die(); // Always terminate the request
    }

}

new ProductStockNotification_Ajax();
