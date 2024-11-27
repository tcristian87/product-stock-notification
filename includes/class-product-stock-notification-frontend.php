<?php
namespace PSN\Frontend;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ProductStockNotification_Frontend
{
    public function __construct()
    {
        // Hook to replace the "Out of Stock" message and add the form
        add_filter('woocommerce_get_availability', [$this, 'custom_availability_class'], 10, 2);

    }

    function custom_availability_class($availability, $product)
    {
        if (!$product->is_in_stock()) {
            $availability['class'] = 'custom-out-of-stock';
        }
        return $availability;
    }

}
// Initialize the class
new ProductStockNotification_Frontend();

