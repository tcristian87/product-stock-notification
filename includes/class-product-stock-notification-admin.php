<?php
namespace PSN\Admin;
if ( ! defined( 'ABSPATH' ) ) exit;

class ProductStockNotification_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'psn_register_settings']);

    }

    public function add_admin_menu() {
        add_menu_page('Back To Stock Notifier', 'Back To Stock', 'manage_options', 'back-to-stock', [$this, 'display_back_to_stock_page']);
        add_submenu_page('back-to-stock', 'Stock Notifier', 'Stock Notifier', 'manage_options', 'stock-notifier', [$this, 'display_stock_notifier_page']);
    }

    public function display_back_to_stock_page() {
        global $wpdb;
        $results = \PSN\Database\ProductStockNotification_Database::get_all_entries();
        include plugin_dir_path(__FILE__) . '../admin/admin-view.php';
    }

    public function display_stock_notifier_page() {
        // Include the stock notifier view template
        include plugin_dir_path(__FILE__) . '../admin/stock-notifier-view.php';
    }

   public function psn_register_settings() {
        // Register settings for email subject, body, threshold, and enable checkbox
        register_setting('stock_notifier_settings_group', 'stock_notifier_email_subject');
        register_setting('stock_notifier_settings_group', 'stock_notifier_email_body');
        register_setting('stock_notifier_settings_group', 'stock_notification_threshold');
        register_setting('stock_notifier_settings_group', 'stock_notifier_enable');
    }
}

new ProductStockNotification_Admin();