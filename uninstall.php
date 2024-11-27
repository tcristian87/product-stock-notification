<?php
/**
 * Uninstall script for Product Stock Notification plugin
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; // Exit if accessed directly
}

global $wpdb;
$table_name = $wpdb->prefix . 'back_to_stock_notifier';

// Safely drop the custom table
$wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS %s", $table_name));

// Remove plugin options
delete_option('stock_notifier_email_subject');
delete_option('stock_notifier_email_body');
delete_option('stock_notification_threshold');

