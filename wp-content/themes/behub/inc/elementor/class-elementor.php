<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('BeHub_Elementor')) :

    /**
     * The BeHub Elementor Integration class
     */
    class BeHub_Elementor {
        private $suffix = '';

        public function __construct() {
            $this->suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

            add_action('elementor/frontend/after_enqueue_scripts', [$this, 'register_auto_scripts_frontend']);
            add_action('elementor/init', array($this, 'add_category'));
            add_action('wp_enqueue_scripts', [$this, 'add_scripts'], 15);
            add_action('elementor/widgets/register', array($this, 'customs_widgets'));
            add_action('elementor/widgets/register', array($this, 'include_widgets'));
            add_action('elementor/frontend/after_enqueue_scripts', [$this, 'add_js']);

            // Custom Animation Scroll
            add_filter('elementor/controls/animations/additional_animations', [$this, 'add_animations_scroll']);

            // Backend
            add_action('elementor/editor/after_enqueue_styles', [$this, 'add_style_editor'], 99);

            // Add Icon Custom
            add_action('elementor/icons_manager/native', [$this, 'add_icons_native']);
            add_action('elementor/controls/controls_registered', [$this, 'add_icons']);

            // Add Breakpoints
            add_action('wp_enqueue_scripts', 'behub_elementor_breakpoints', 9999);

            if (!behub_is_elementor_pro_activated()) {
                require trailingslashit(get_template_directory()) . 'inc/elementor/custom-css.php';
                require trailingslashit(get_template_directory()) . 'inc/elementor/sticky-section.php';
                if (is_admin()) {
                    add_action('manage_elementor_library_posts_columns', [$this, 'admin_columns_headers']);
                    add_action('manage_elementor_library_posts_custom_column', [$this, 'admin_columns_content'], 10, 2);
                }
            }

            add_filter('elementor/fonts/additional_fonts', [$this, 'additional_fonts']);
            add_action('wp_enqueue_scripts', [$this, 'elementor_kit']);
        }

        public function elementor_kit() {
            $active_kit_id = Elementor\Plugin::$instance->kits_manager->get_active_id();
            Elementor\Plugin::$instance->kits_manager->frontend_before_enqueue_styles();
            $myvals = get_post_meta($active_kit_id, '_elementor_page_settings', true);
            if (!empty($myvals)) {
                $css = '';
                foreach ($myvals['system_colors'] as $key => $value) {
                    $css .= $value['color'] !== '' ? '--' . $value['_id'] . ':' . $value['color'] . ';' : '';
                }

                $var = "body{{$css}}";
                wp_add_inline_style('behub-style', $var);
            }
        }

        public function additional_fonts($fonts) {
            $fonts["BeHub"] = 'system';
            return $fonts;
        }

        public function admin_columns_headers($defaults) {
            $defaults['shortcode'] = esc_html__('Shortcode', 'behub');

            return $defaults;
        }

        public function admin_columns_content($column_name, $post_id) {
            if ('shortcode' === $column_name) {
                ob_start();
                ?>
                <input class="elementor-shortcode-input" type="text" readonly onfocus="this.select()" value="[hfe_template id='<?php echo esc_attr($post_id); ?>']"/>
                <?php
                ob_get_contents();
            }
        }

        public function add_js() {
            global $behub_version;
            wp_enqueue_script('behub-elementor-frontend', get_theme_file_uri('/assets/js/elementor-frontend.js'), [], $behub_version);
        }

        public function add_style_editor() {
            global $behub_version;
            wp_enqueue_style('behub-elementor-editor-icon', get_theme_file_uri('/assets/css/admin/elementor/icons.css'), [], $behub_version);
        }

        public function add_scripts() {
            global $behub_version;
            $suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
            wp_enqueue_style('behub-elementor', get_template_directory_uri() . '/assets/css/base/elementor.css', '', $behub_version);
            wp_style_add_data('behub-elementor', 'rtl', 'replace');

            // Add Scripts
            wp_register_script('tweenmax', get_theme_file_uri('/assets/js/vendor/TweenMax.min.js'), array('jquery'), '1.11.1');
            wp_register_script('parallaxmouse', get_theme_file_uri('/assets/js/vendor/jquery-parallax.js'), array('jquery'), $behub_version);

            if (behub_elementor_check_type('animated-bg-parallax')) {
                wp_enqueue_script('tweenmax');
                wp_enqueue_script('jquery-panr', get_theme_file_uri('/assets/js/vendor/jquery-panr' . $suffix . '.js'), array('jquery'), '0.0.1');
            }
        }


        public function register_auto_scripts_frontend() {
            global $behub_version;
            wp_register_script('behub-elementor-amenities', get_theme_file_uri('/assets/js/elementor/amenities.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-brand', get_theme_file_uri('/assets/js/elementor/brand.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-button-popup', get_theme_file_uri('/assets/js/elementor/button-popup.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-image-gallery', get_theme_file_uri('/assets/js/elementor/image-gallery.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-location', get_theme_file_uri('/assets/js/elementor/location.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-location-gallery', get_theme_file_uri('/assets/js/elementor/location-gallery.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-location-map', get_theme_file_uri('/assets/js/elementor/location-map.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-location-packages', get_theme_file_uri('/assets/js/elementor/location-packages.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-posts-grid', get_theme_file_uri('/assets/js/elementor/posts-grid.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-price-table', get_theme_file_uri('/assets/js/elementor/price-table.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-tabs', get_theme_file_uri('/assets/js/elementor/tabs.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-team-box', get_theme_file_uri('/assets/js/elementor/team-box.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-testimonial', get_theme_file_uri('/assets/js/elementor/testimonial.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-video', get_theme_file_uri('/assets/js/elementor/video.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-workspace', get_theme_file_uri('/assets/js/elementor/workspace.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-workspace-gallery', get_theme_file_uri('/assets/js/elementor/workspace-gallery.js'), array('jquery','elementor-frontend'), $behub_version, true);
            wp_register_script('behub-elementor-workspace-tab', get_theme_file_uri('/assets/js/elementor/workspace-tab.js'), array('jquery','elementor-frontend'), $behub_version, true);
           
        }

        public function add_category() {
            Elementor\Plugin::instance()->elements_manager->add_category(
                'behub-addons',
                array(
                    'title' => esc_html__('BeHub Addons', 'behub'),
                    'icon'  => 'fa fa-plug',
                ),
                1);
        }

        public function add_animations_scroll($animations) {
            $animations['BeHub Animation'] = [
                'opal-move-up'    => 'Move Up',
                'opal-move-down'  => 'Move Down',
                'opal-move-left'  => 'Move Left',
                'opal-move-right' => 'Move Right',
                'opal-flip'       => 'Flip',
                'opal-helix'      => 'Helix',
                'opal-scale-up'   => 'Scale',
                'opal-am-popup'   => 'Popup',
            ];

            return $animations;
        }

        public function animations_scroll_style_src() {
            global $behub_version;
            $animations = [
                'opal-move-up',
                'opal-move-down',
                'opal-move-left',
                'opal-move-right',
                'opal-flip',
                'opal-helix',
                'opal-scale-up',
                'opal-am-popup',
            ];
            foreach ($animations as $animation) {
                wp_deregister_style('e-animation-' . $animation);
                wp_register_style('e-animation-' . $animation, get_theme_file_uri('/assets/css/base/animations/'.$animation.'.css'), [], $behub_version);
            }
        }

        public function customs_widgets() {
            $files = glob(get_theme_file_path('/inc/elementor/custom-widgets/*.php'));
            foreach ($files as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }

        /**
         * @param $widgets_manager Elementor\Widgets_Manager
         */
        public function include_widgets($widgets_manager) {
            require 'base-carousel-widget.php';
            $files = glob(get_theme_file_path('/inc/elementor/widgets/*.php'));
            foreach ($files as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }

        public function add_icons( $manager ) {
            $new_icons = json_decode( '{"behub-icon-access":"access","behub-icon-account":"account","behub-icon-angle-down":"angle-down","behub-icon-angle-left":"angle-left","behub-icon-angle-right":"angle-right","behub-icon-angle-up":"angle-up","behub-icon-arrow-down":"arrow-down","behub-icon-arrow-left":"arrow-left","behub-icon-arrow-right":"arrow-right","behub-icon-arrow-up":"arrow-up","behub-icon-bed":"bed","behub-icon-call":"call","behub-icon-calling":"calling","behub-icon-car-parking":"car-parking","behub-icon-cart":"cart","behub-icon-charge":"charge","behub-icon-check-1":"check-1","behub-icon-check-circle":"check-circle","behub-icon-check-fill":"check-fill","behub-icon-chevron-circle-down":"chevron-circle-down","behub-icon-chevron-circle-left":"chevron-circle-left","behub-icon-chevron-circle-right":"chevron-circle-right","behub-icon-chevron-circle-up":"chevron-circle-up","behub-icon-chil-out":"chil-out","behub-icon-clean":"clean","behub-icon-cleaning-services":"cleaning-services","behub-icon-community-event":"community-event","behub-icon-community":"community","behub-icon-compare":"compare","behub-icon-craft-coffee":"craft-coffee","behub-icon-daily-coffee":"daily-coffee","behub-icon-date-form":"date-form","behub-icon-dedicated-locker":"dedicated-locker","behub-icon-dinner":"dinner","behub-icon-dot-1":"dot-1","behub-icon-event-1":"event-1","behub-icon-event":"event","behub-icon-eye":"eye","behub-icon-facebook-f":"facebook-f","behub-icon-fast-wifi":"fast-wifi","behub-icon-fitness":"fitness","behub-icon-google-plus-g":"google-plus-g","behub-icon-healthy-snacks":"healthy-snacks","behub-icon-heart-1":"heart-1","behub-icon-high-speed":"high-speed","behub-icon-hour-working":"hour-working","behub-icon-left-arrow":"left-arrow","behub-icon-linkedin-in":"linkedin-in","behub-icon-locations":"locations","behub-icon-mail-1":"mail-1","behub-icon-manager":"manager","behub-icon-map-marker":"map-marker","behub-icon-massage":"massage","behub-icon-meeting":"meeting","behub-icon-message-notif":"message-notif","behub-icon-message-square":"message-square","behub-icon-network-location":"network-location","behub-icon-office":"office","behub-icon-patio":"patio","behub-icon-play-circle-solid":"play-circle-solid","behub-icon-private":"private","behub-icon-quote-1":"quote-1","behub-icon-right-arrow":"right-arrow","behub-icon-room-booking":"room-booking","behub-icon-seats":"seats","behub-icon-services-1":"services-1","behub-icon-shopping-bag":"shopping-bag","behub-icon-sms":"sms","behub-icon-space":"space","behub-icon-speed-wifi":"speed-wifi","behub-icon-tea-coffee":"tea-coffee","behub-icon-team-size":"team-size","behub-icon-technology-security":"technology-security","behub-icon-toy":"toy","behub-icon-trusted":"trusted","behub-icon-validated-spaces-":"validated-spaces-","behub-icon-virtual-mail":"virtual-mail","behub-icon-virtual":"virtual","behub-icon-wellness":"wellness","behub-icon-360":"360","behub-icon-bars":"bars","behub-icon-caret-down":"caret-down","behub-icon-caret-left":"caret-left","behub-icon-caret-right":"caret-right","behub-icon-caret-up":"caret-up","behub-icon-cart-empty":"cart-empty","behub-icon-check-square":"check-square","behub-icon-circle":"circle","behub-icon-cloud-download-alt":"cloud-download-alt","behub-icon-comment":"comment","behub-icon-comments":"comments","behub-icon-contact":"contact","behub-icon-credit-card":"credit-card","behub-icon-dot-circle":"dot-circle","behub-icon-edit":"edit","behub-icon-envelope":"envelope","behub-icon-expand-alt":"expand-alt","behub-icon-external-link-alt":"external-link-alt","behub-icon-file-alt":"file-alt","behub-icon-file-archive":"file-archive","behub-icon-filter":"filter","behub-icon-folder-open":"folder-open","behub-icon-folder":"folder","behub-icon-frown":"frown","behub-icon-gift":"gift","behub-icon-grid":"grid","behub-icon-grip-horizontal":"grip-horizontal","behub-icon-heart-fill":"heart-fill","behub-icon-heart":"heart","behub-icon-history":"history","behub-icon-home":"home","behub-icon-info-circle":"info-circle","behub-icon-instagram":"instagram","behub-icon-level-up-alt":"level-up-alt","behub-icon-list":"list","behub-icon-map-marker-check":"map-marker-check","behub-icon-meh":"meh","behub-icon-minus-circle":"minus-circle","behub-icon-minus":"minus","behub-icon-mobile-android-alt":"mobile-android-alt","behub-icon-money-bill":"money-bill","behub-icon-pencil-alt":"pencil-alt","behub-icon-play-circle":"play-circle","behub-icon-play":"play","behub-icon-plus-circle":"plus-circle","behub-icon-plus":"plus","behub-icon-quote":"quote","behub-icon-random":"random","behub-icon-reply-all":"reply-all","behub-icon-reply":"reply","behub-icon-search-plus":"search-plus","behub-icon-search":"search","behub-icon-shield-check":"shield-check","behub-icon-shopping-basket":"shopping-basket","behub-icon-shopping-cart":"shopping-cart","behub-icon-sign-out-alt":"sign-out-alt","behub-icon-smile":"smile","behub-icon-spinner":"spinner","behub-icon-square":"square","behub-icon-star":"star","behub-icon-store":"store","behub-icon-sync":"sync","behub-icon-tachometer-alt":"tachometer-alt","behub-icon-th-large":"th-large","behub-icon-th-list":"th-list","behub-icon-thumbtack":"thumbtack","behub-icon-ticket":"ticket","behub-icon-times-circle":"times-circle","behub-icon-times":"times","behub-icon-trophy-alt":"trophy-alt","behub-icon-truck":"truck","behub-icon-user-headset":"user-headset","behub-icon-user-shield":"user-shield","behub-icon-user":"user","behub-icon-video":"video","behub-icon-adobe":"adobe","behub-icon-amazon":"amazon","behub-icon-android":"android","behub-icon-angular":"angular","behub-icon-apper":"apper","behub-icon-apple":"apple","behub-icon-atlassian":"atlassian","behub-icon-behance":"behance","behub-icon-bitbucket":"bitbucket","behub-icon-bitcoin":"bitcoin","behub-icon-bity":"bity","behub-icon-bluetooth":"bluetooth","behub-icon-btc":"btc","behub-icon-centos":"centos","behub-icon-chrome":"chrome","behub-icon-codepen":"codepen","behub-icon-cpanel":"cpanel","behub-icon-discord":"discord","behub-icon-dochub":"dochub","behub-icon-docker":"docker","behub-icon-dribbble":"dribbble","behub-icon-dropbox":"dropbox","behub-icon-drupal":"drupal","behub-icon-ebay":"ebay","behub-icon-facebook":"facebook","behub-icon-figma":"figma","behub-icon-firefox":"firefox","behub-icon-google-plus":"google-plus","behub-icon-google":"google","behub-icon-grunt":"grunt","behub-icon-gulp":"gulp","behub-icon-html5":"html5","behub-icon-joomla":"joomla","behub-icon-link-brand":"link-brand","behub-icon-linkedin":"linkedin","behub-icon-mailchimp":"mailchimp","behub-icon-opencart":"opencart","behub-icon-paypal":"paypal","behub-icon-pinterest-p":"pinterest-p","behub-icon-reddit":"reddit","behub-icon-skype":"skype","behub-icon-slack":"slack","behub-icon-snapchat":"snapchat","behub-icon-spotify":"spotify","behub-icon-trello":"trello","behub-icon-twitter":"twitter","behub-icon-vimeo":"vimeo","behub-icon-whatsapp":"whatsapp","behub-icon-wordpress":"wordpress","behub-icon-yoast":"yoast","behub-icon-youtube":"youtube"}', true );
			$icons     = $manager->get_control( 'icon' )->get_settings( 'options' );
			$new_icons = array_merge(
				$new_icons,
				$icons
			);
			// Then we set a new list of icons as the options of the icon control
			$manager->get_control( 'icon' )->set_settings( 'options', $new_icons ); 
        }

        public function add_icons_native($tabs) {
            global $behub_version;
            $tabs['opal-custom'] = [
                'name'          => 'behub-icon',
                'label'         => esc_html__('BeHub Icon', 'behub'),
                'prefix'        => 'behub-icon-',
                'displayPrefix' => 'behub-icon-',
                'labelIcon'     => 'fab fa-font-awesome-alt',
                'ver'           => $behub_version,
                'fetchJson'     => get_theme_file_uri('/inc/elementor/icons.json'),
                'native'        => true,
            ];

            return $tabs;
        }
    }

endif;

return new BeHub_Elementor();
