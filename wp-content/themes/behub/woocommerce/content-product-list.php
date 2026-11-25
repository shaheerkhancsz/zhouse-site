<?php

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<li <?php wc_product_class('product-list', $product); ?>>
    <?php
    /**
     * Functions hooked in to behub_woocommerce_before_shop_loop_item action
     *
     */
    do_action('behub_woocommerce_before_shop_loop_item');


    ?>
    <div class="product-transition">
        <div class="product-image">
            <?php
            /**
             * Functions hooked in to behub_woocommerce_before_shop_loop_item_title action
             *
             * @see woocommerce_show_product_loop_sale_flash - 10 - woo
             * @see woocommerce_template_loop_product_thumbnail - 15 - woo
             *
             */
            do_action('behub_woocommerce_before_shop_loop_item_title');
            ?>
        </div>
    </div>
    <div class="product-caption">
        <?php
        behub_woocommerce_get_product_category();
        woocommerce_template_loop_product_title();
        woocommerce_template_loop_rating();
        woocommerce_template_loop_price();
        behub_woocommerce_get_product_description();
        woocommerce_template_loop_add_to_cart();
        ?>
    </div>
    <?php
    /**
     * Functions hooked in to behub_woocommerce_after_shop_loop_item action
     *
     */
    do_action('behub_woocommerce_after_shop_loop_item');
    ?>
</li>
