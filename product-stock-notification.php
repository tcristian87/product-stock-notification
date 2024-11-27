<?php

/**
 * Plugin Name: Back to Stock Notifier
 * Description: Sends email notifications when a product is back in stock.
 * Text Domain: product-stock-notification
 * Version: 1.0.0
 * Author: LERATECH
 * Author URI: https://leratech.ro/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */


if ( ! defined( 'ABSPATH' ) ) exit;

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-product-stock-notification.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-product-stock-notification-database.php';

register_activation_hook(__FILE__, ['PSN\ProductStockNotification', 'activate']);


require_once plugin_dir_path(__FILE__) . 'includes/class-product-stock-notification-admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-product-stock-notification-ajax.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-product-stock-notification-frontend.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-product-stock-notification-email.php';



// Initialize the main class
new PSN\ProductStockNotification();