<?php
namespace PSN\Database;

if ( ! defined( 'ABSPATH' ) ) exit;


class ProductStockNotification_Database {

    private static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'back_to_stock_notifier';
    }


    public static function create_table() {
        global $wpdb;
        $table_name = self::get_table_name();
        $charset_collate = $wpdb->get_charset_collate();

        // SQL to create the back_to_stock_notifier table with email logs
        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id mediumint(9) NOT NULL,
        email varchar(255) NOT NULL,
        product_id mediumint(9) NOT NULL,
        email_sent tinyint(1) DEFAULT 0 NOT NULL,  -- 0 = not sent, 1 = sent
        email_sent_time datetime DEFAULT NULL,  -- The time the email was sent
        log_status varchar(255) DEFAULT 'pending',  -- Status of the notification ('pending', 'sent', 'failed')
        PRIMARY KEY (id)
    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function check_if_registered($email, $product_id) {
        global $wpdb;
        $user_id = get_current_user_id();
        $table_name = self::get_table_name();

        // Initialize the query to select all matching rows
        $query = "SELECT * FROM $table_name WHERE product_id = %d AND (email = %s";
        $query_args = [$product_id, $email];

        // If the user is logged in, also check the user_id
        if ($user_id > 0) {
            $query .= " OR user_id = %d";
            $query_args[] = $user_id;
        }

        $query .= ") AND email_sent = 0";

        // Prepare and execute the query
        //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
        $prepared_query = $wpdb->prepare($query, ...$query_args);
        //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
        $results = $wpdb->get_results($prepared_query);

        // Check if there are any matching rows
        if (!empty($results)) {
            // Process the results
            foreach ($results as $row) {
                // Check if the email and product_id match in the same row
                if ($row->email == $email && $row->product_id == $product_id) {
                    // If a match is found with email and product_id, return true
                    return true;
                }
            }
        }

        // Return false if no matching record is found
        return false;
    }

    public static function insert_entry($user_id, $email, $product_id) {
        global $wpdb;
        //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $wpdb->insert(self::get_table_name(), array(
            'user_id' => intval($user_id),
            'email' => sanitize_email($email),
            'product_id' => intval($product_id)
        ));
    }

    public static function get_all_entries() {
        //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
        global $wpdb;
        //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $wpdb->get_results("SELECT * FROM " . self::get_table_name(), ARRAY_A);
    }


    // Fetch the notification log
    public static function get_notification_log() {
        global $wpdb;
        // Fetch log entries

        //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $results = $wpdb->get_results("SELECT * FROM". self::get_table_name() . "WHERE email_sent = 1 ORDER BY email_sent_time DESC", ARRAY_A);
        return $results;
    }

}