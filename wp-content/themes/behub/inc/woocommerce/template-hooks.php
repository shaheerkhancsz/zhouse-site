<?php
/**
 * =================================================
 * Hook behub_page
 * =================================================
 */

/**
 * =================================================
 * Hook behub_single_post_top
 * =================================================
 */

/**
 * =================================================
 * Hook behub_single_post
 * =================================================
 */

/**
 * =================================================
 * Hook behub_single_post_bottom
 * =================================================
 */

/**
 * =================================================
 * Hook behub_loop_post
 * =================================================
 */

/**
 * =================================================
 * Hook behub_footer
 * =================================================
 */

/**
 * =================================================
 * Hook behub_after_footer
 * =================================================
 */
add_action('behub_after_footer', 'behub_sticky_single_add_to_cart', 999);

/**
 * =================================================
 * Hook wp_footer
 * =================================================
 */
add_action('wp_footer', 'behub_render_woocommerce_shop_canvas', 1);

/**
 * =================================================
 * Hook wp_head
 * =================================================
 */

/**
 * =================================================
 * Hook behub_before_header
 * =================================================
 */

/**
 * =================================================
 * Hook behub_before_content
 * =================================================
 */

/**
 * =================================================
 * Hook behub_content_top
 * =================================================
 */
add_action('behub_content_top', 'behub_shop_messages', 10);

/**
 * =================================================
 * Hook behub_post_content_before
 * =================================================
 */

/**
 * =================================================
 * Hook behub_post_content_after
 * =================================================
 */

/**
 * =================================================
 * Hook behub_sidebar
 * =================================================
 */

/**
 * =================================================
 * Hook behub_loop_after
 * =================================================
 */

/**
 * =================================================
 * Hook behub_page_after
 * =================================================
 */

/**
 * =================================================
 * Hook behub_woocommerce_before_shop_loop_item
 * =================================================
 */

/**
 * =================================================
 * Hook behub_woocommerce_before_shop_loop_item_title
 * =================================================
 */
add_action('behub_woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
add_action('behub_woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 15);

/**
 * =================================================
 * Hook behub_woocommerce_shop_loop_item_title
 * =================================================
 */
add_action('behub_woocommerce_shop_loop_item_title', 'behub_woocommerce_get_product_category', 5);
add_action('behub_woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 5);

/**
 * =================================================
 * Hook behub_woocommerce_after_shop_loop_item_title
 * =================================================
 */
add_action('behub_woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 15);
add_action('behub_woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 20);
add_action('behub_woocommerce_after_shop_loop_item_title', 'behub_woocommerce_get_product_description', 25);
add_action('behub_woocommerce_after_shop_loop_item_title', 'behub_woocommerce_product_list_add_to_cart', 30);

/**
 * =================================================
 * Hook behub_woocommerce_after_shop_loop_item
 * =================================================
 */
