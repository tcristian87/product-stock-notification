<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<div class="wrap">
    <h1><?php esc_html_e('Stock Notifier Settings', 'product-stock-notification'); ?></h1>

    <form method="post" action="options.php">
        <?php
        settings_fields('stock_notifier_settings_group'); // WordPress settings API group
        do_settings_sections('stock_notifier_settings_group');
        ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="stock_notifier_email_subject"><?php esc_html_e('Email Subject', 'product-stock-notification'); ?></label>
                </th>
                <td>
                    <textarea name="stock_notifier_email_subject" id="stock_notifier_email_subject" class="regular-text" rows="1"><?php
                        echo esc_textarea(get_option('stock_notifier_email_subject', __('Your product {product_name} is back in stock!', 'product-stock-notification')));
                        ?>
                    </textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="stock_notifier_email_body"><?php esc_html_e('Email Body', 'product-stock-notification'); ?></label>
                </th>
                <td>
            <textarea name="stock_notifier_email_body" id="stock_notifier_email_body" class="large-text" rows="5"><?php
                echo esc_textarea(get_option('stock_notifier_email_body', __('Good news! The product you were waiting for is back in stock: {product_name}', 'product-stock-notification')));
                ?>
            </textarea>
                    <p class="description"><?php esc_html_e('Use {product_name} and {product_link} as placeholders.', 'product-stock-notification'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="stock_notification_threshold"><?php esc_html_e('Stock Notification Threshold', 'product-stock-notification'); ?></label>
                </th>
                <td>
                    <input type="number" name="stock_notification_threshold" id="stock_notification_threshold"
                           value="<?php echo esc_attr(get_option('stock_notification_threshold', 1)); ?>"
                           class="small-text">
                    <p class="description"><?php esc_html_e('Send a notification when the product stock is above this number.', 'product-stock-notification'); ?></p>
                </td>
            </tr>
        </table>


        <?php submit_button(); ?>
    </form>

    <h2><?php esc_html_e('Notification Log', 'product-stock-notification'); ?></h2>

    <table class="widefat">
        <thead>
        <tr>
            <th><?php esc_html_e('Date', 'product-stock-notification'); ?></th>
            <th><?php esc_html_e('User', 'product-stock-notification'); ?></th>
            <th><?php esc_html_e('Product', 'product-stock-notification'); ?></th>
            <th><?php esc_html_e('Email Sent', 'product-stock-notification'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $log_entries = PSN\Database\ProductStockNotification_Database::get_notification_log(); // Assume you have a log
        if (!empty($log_entries)) :
            foreach ($log_entries as $entry) :
                $product = wc_get_product($entry['product_id']);
                $product_name = $product ? $product->get_name() : __('Unknown', 'product-stock-notification');
                ?>
                <tr>
                    <td><?php echo esc_html($entry['email_sent_time']); ?></td>
                    <td><?php echo esc_html($entry['email']); ?></td>
                    <td><?php echo esc_html($product_name); ?></td>
                    <td><?php echo esc_html($entry['email_sent'] ? __('Yes', 'product-stock-notification') : __('No', 'product-stock-notification')); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="4"><?php esc_html_e('No notifications sent yet.', 'product-stock-notification'); ?></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

</div>
