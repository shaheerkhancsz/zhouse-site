<?php
$theme         = wp_get_theme('behub');
$behub_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if (!isset($content_width)) {
    $content_width = 980; /* pixels */
}
require get_theme_file_path('inc/class-tgm-plugin-activation.php');
$behub = (object)array(
    'version' => $behub_version,
    /**
     * Initialize all the things.
     */
    'main'    => require 'inc/class-main.php',
);

require get_theme_file_path('inc/functions.php');
require get_theme_file_path('inc/template-hooks.php');
require get_theme_file_path('inc/template-functions.php');

require_once get_theme_file_path('inc/merlin/vendor/autoload.php');
require_once get_theme_file_path('inc/merlin/class-merlin.php');
require_once get_theme_file_path('inc/merlin-config.php');

require_once get_theme_file_path('inc/class-customize.php');

if (behub_is_woocommerce_activated()) {
    require get_theme_file_path('inc/woocommerce/class-woocommerce-shordcode.php');
    $behub->woocommerce = require get_theme_file_path('inc/woocommerce/class-woocommerce.php');

    require get_theme_file_path('inc/woocommerce/class-woocommerce-adjacent-products.php');

    require get_theme_file_path('inc/woocommerce/woocommerce-functions.php');
    require get_theme_file_path('inc/woocommerce/woocommerce-template-functions.php');
    require get_theme_file_path('inc/woocommerce/woocommerce-template-hooks.php');
    require get_theme_file_path('inc/woocommerce/template-hooks.php');
    require get_theme_file_path('inc/woocommerce/class-woocommerce-settings.php');
    require get_theme_file_path('inc/woocommerce/class-woocommerce-brand.php');
    require get_theme_file_path('inc/woocommerce/class-woocommerce-extra.php');
    require get_theme_file_path('inc/merlin/includes/class-wc-widget-product-brands.php');
}

if(behub_is_cmb2_activated()) {
    require get_theme_file_path('inc/cmb_field_map/cmb-field-map.php');
}

if (behub_is_elementor_activated()) {
    require get_theme_file_path('inc/elementor/functions-elementor.php');
    $behub->elementor = require get_theme_file_path('inc/elementor/class-elementor.php');
    $behub->megamenu  = require get_theme_file_path('inc/megamenu/megamenu.php');
    $behub->parallax  = require get_theme_file_path('inc/elementor/section-parallax.php');
    $behub->background  = require get_theme_file_path('inc/elementor/background-section.php');
    if (behub_is_cmb2_activated()) {
        require get_theme_file_path('inc/merlin/includes/workspace.php');
        require get_theme_file_path('inc/merlin/includes/locations.php');
        require get_theme_file_path('inc/merlin/includes/virtualtour.php');
    }
    if (defined('ELEMENTOR_PRO_VERSION')) {
        require get_theme_file_path('inc/elementor/class-elementor-pro.php');
    }

    if (function_exists('hfe_init')) {
        require get_theme_file_path('inc/header-footer-elementor/class-hfe.php');
        require get_theme_file_path('inc/merlin/includes/breadcrumb.php');
    }

}

if (!is_user_logged_in()) {
    require get_theme_file_path('inc/modules/class-login.php');
}
//gust form code here
add_filter('wpforms_process_filter', function($fields, $entry, $form_data) {
    $target_form_id = 10554; // ✅ Replace with your actual form ID
    if ($form_data['id'] != $target_form_id) return $fields;

    $checkin_time = '';
    $checkout_time = '';
    $billing_field_id = null;

    foreach ($fields as $field_id => $field) {
        $label = trim($field['name']); // Trim whitespace just in case

        if (stripos($label, 'Check-in Time') !== false) {
            $checkin_time = strtotime($field['value']);
        }

        if (stripos($label, 'Check-out Time') !== false) {
            $checkout_time = strtotime($field['value']);
        }

        if ($label === 'Guest Billing Status') { // ✅ Exact match preferred
            $billing_field_id = $field_id;
        }
    }

    if ($checkin_time && $checkout_time && $billing_field_id !== null) {
        $duration = ($checkout_time - $checkin_time) / 3600; // in hours

        if ($duration > 3) {
            $fields[$billing_field_id]['value'] = 'Billable';
        } else {
            $fields[$billing_field_id]['value'] = 'Free';
        }
    }

    return $fields; // ✅ Must return the modified fields
}, 10, 3);
