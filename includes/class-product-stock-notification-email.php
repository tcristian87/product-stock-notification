<?php


namespace PSN\Email;


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ProductStockNotification_Email {

    public function __construct() {
        // Hooks check only if the option is enabled in settings

            // Hook into WooCommerce to send emails when the product stock status is set
            add_action('woocommerce_product_set_stock_status', [$this, 'send_back_in_stock_notifications'], 10, 3);
            add_action('woocommerce_variation_set_stock_status', [$this, 'send_back_in_stock_notifications'], 10, 3);

    }

    /**
     * Function for `woocommerce_product_set_stock_status` action-hook.
     *
     * @param int        $product_id   The ID of the product.
     * @param string     $stock_status The new stock status of the product.
     *
     */

    public function send_back_in_stock_notifications($product_id, $stock_status) {
        $product = wc_get_product($product_id);
        if ($stock_status == 'instock') {
            $this->notify_users($product);
        }
    }


    private function notify_users($product) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'back_to_stock_notifier';
        $product_id = $product->get_id();

        // Query to get all the users who registered for this product
        //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $results = $wpdb->get_results(
        //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $wpdb->prepare("SELECT email FROM $table_name WHERE product_id = %d AND email_sent = 0", $product_id),
            ARRAY_A
        );

        // If no users have registered for this product, exit
        if (empty($results)) {
            return;
        }

        // Send notification emails to each user
        foreach ($results as $row) {
            $this->send_email($row['email'], $product);

            // Update the database to mark that the email was sent
            $wpdb->update(
                $table_name,
                array(
                    'email_sent' => 1,
                    'email_sent_time' => current_time('mysql'),
                    'log_status' => 'sent'
                ),
                array('email' => $row['email'], 'product_id' => $product_id)
            );
        }
    }

    /**
     * Sends the back-in-stock email to the given email address.
     *
     * @param string $email The email address to send the notification to.
     * @param WC_Product $product The WooCommerce product object.
     */

    private function send_email($email, $product) {
        // Get the email subject and body from the admin settings, with default values
        $subject_template = get_option('stock_notifier_email_subject');
        $body_template = get_option('stock_notifier_email_body');

        // Replace the placeholders with actual product details
        $subject = str_replace(
            ['{product_name}'],
            [$product->get_name()],
            $subject_template
        );
        $body = str_replace(
            ['{product_name}', '{product_link}'],
            [
                $product->get_name(),
                '<a href="' . esc_url($product->get_permalink()) . '">click here</a>'
            ],
            $body_template
        );


        // Email headers
        $headers = array('Content-Type: text/html; charset=UTF-8');

        // Send the email
        wp_mail($email, $subject, nl2br($body), $headers);
    }


}

new ProductStockNotification_Email();