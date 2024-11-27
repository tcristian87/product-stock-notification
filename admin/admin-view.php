<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<div class="wrap">
    <h2><?php esc_html_e('Back to Stock Notifications', 'product-stock-notification'); ?></h2>

    <?php if (!empty($results)) : ?>
        <table class="widefat">
            <thead>
            <tr>
                <th><?php esc_html_e('ID', 'product-stock-notification'); ?></th>
                <th><?php esc_html_e('User ID', 'product-stock-notification'); ?></th>
                <th><?php esc_html_e('Email', 'product-stock-notification'); ?></th>
                <th><?php esc_html_e('Product ID', 'product-stock-notification'); ?></th>
                <th><?php esc_html_e('Product Name', 'product-stock-notification'); ?></th>
                <th><?php esc_html_e('Email sent', 'product-stock-notification'); ?></th>

            </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $row) : ?>
                <?php
                // Retrieve product information based on product_id
                $product = wc_get_product($row['product_id']);
                $product_name = $product ? $product->get_name() : __('Unknown Product', 'product-stock-notification');
                $email_status = $row['log_status'];

                ?>
                <tr>
                    <td><?php echo esc_html($row['id']); ?></td>
                    <td><?php echo esc_html($row['user_id']); ?></td>
                    <td><?php echo esc_html($row['email']); ?></td>
                    <td><?php echo esc_html($row['product_id']); ?></td>
                    <td><?php echo esc_html($product_name); ?></td>
                    <td><?php echo esc_html(strtoupper($email_status)); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php esc_html_e('No Back to Stock notifications found.', 'product-stock-notification'); ?></p>
    <?php endif; ?>
</div>
