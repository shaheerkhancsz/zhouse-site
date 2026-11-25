<?php
/**
 * =================================================
 * Hook behub_page
 * =================================================
 */
add_action('behub_page', 'behub_page_header', 10);
add_action('behub_page', 'behub_page_content', 20);

/**
 * =================================================
 * Hook behub_single_post_top
 * =================================================
 */
add_action('behub_single_post_top', 'behub_post_header', 5);
add_action('behub_single_post_top', 'behub_post_thumbnail', 10);

/**
 * =================================================
 * Hook behub_single_post
 * =================================================
 */
add_action('behub_single_post', 'behub_post_content', 30);

/**
 * =================================================
 * Hook behub_single_post_bottom
 * =================================================
 */
add_action('behub_single_post_bottom', 'behub_post_taxonomy', 5);
add_action('behub_single_post_bottom', 'behub_post_nav', 10);
add_action('behub_single_post_bottom', 'behub_display_comments', 20);

/**
 * =================================================
 * Hook behub_loop_post
 * =================================================
 */
add_action('behub_loop_post', 'behub_post_header', 15);
add_action('behub_loop_post', 'behub_post_content', 30);

/**
 * =================================================
 * Hook behub_footer
 * =================================================
 */
add_action('behub_footer', 'behub_footer_default', 20);

/**
 * =================================================
 * Hook behub_after_footer
 * =================================================
 */

/**
 * =================================================
 * Hook wp_footer
 * =================================================
 */
add_action('wp_footer', 'behub_template_account_dropdown', 1);
add_action('wp_footer', 'behub_mobile_nav', 1);

/**
 * =================================================
 * Hook wp_head
 * =================================================
 */
add_action('wp_head', 'behub_pingback_header', 1);

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
add_action('behub_sidebar', 'behub_get_sidebar', 10);

/**
 * =================================================
 * Hook behub_loop_after
 * =================================================
 */
add_action('behub_loop_after', 'behub_paging_nav', 10);

/**
 * =================================================
 * Hook behub_page_after
 * =================================================
 */
add_action('behub_page_after', 'behub_display_comments', 10);

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

/**
 * =================================================
 * Hook behub_woocommerce_after_shop_loop_item
 * =================================================
 */
