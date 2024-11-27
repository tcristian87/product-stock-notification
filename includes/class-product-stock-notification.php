<?php
namespace PSN;
if ( ! defined( 'ABSPATH' ) ) exit;

class ProductStockNotification
{
    public function __construct() {
//        register_activation_hook(plugin_dir_url(__FILE__ . '../product-stock-notification.php'), [$this, 'activate']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public static function activate() {
        \PSN\Database\ProductStockNotification_Database::create_table();
    }

    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('stock-custom-script', plugin_dir_url(__FILE__) . '../assets/js/custom-script.js', array('jquery'), '1.0.0', false);
        wp_localize_script('stock-custom-script', 'psn_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('back_to_stock_nonce')
        ));
        // Check if we are on a single product page
        if (is_product()) {
            global $post;
            $product = wc_get_product($post->ID); // Get the product object

            // Localize the script with product data and nonce
            if ($product) {
                wp_localize_script('stock-custom-script', 'woocommerce_params', array(
                    'product_id' => $product->get_id(),
                    'nonce' => wp_create_nonce('back_to_stock_nonce'),
                ));
            }
        }
        wp_enqueue_style('back-to-stock-notification-style', plugin_dir_url(__FILE__) . '../assets/css/style.css', array(), '1.0.0');
    }
}