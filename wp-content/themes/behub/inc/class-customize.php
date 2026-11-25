<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('BeHub_Customize')) {

    class BeHub_Customize {


        public function __construct() {
            add_action('customize_register', array($this, 'customize_register'));
        }

        /**
         * @param $wp_customize WP_Customize_Manager
         */
        public function customize_register($wp_customize) {

            /**
             * Theme options.
             */
            require_once get_theme_file_path('inc/customize-control/editor.php');
            $this->init_behub_blog($wp_customize);

            $this->init_behub_social($wp_customize);

            if (behub_is_woocommerce_activated()) {
                $this->init_woocommerce($wp_customize);
            }

            if (behub_is_elementor_activated()) {
                $this->init_behub_service($wp_customize);
                $this->init_behub_project($wp_customize);
            }

            do_action('behub_customize_register', $wp_customize);
        }


        /**
         * @param $wp_customize WP_Customize_Manager
         *
         * @return void
         */
        public function init_behub_blog($wp_customize) {

            $wp_customize->add_section('behub_blog_archive', array(
                'title' => esc_html__('Blog', 'behub'),
            ));

            // =========================================
            // Select Style
            // =========================================

            $wp_customize->add_setting('behub_options_blog_style', array(
                'type'              => 'option',
                'default'           => 'standard',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_blog_style', array(
                'section' => 'behub_blog_archive',
                'label'   => esc_html__('Blog style', 'behub'),
                'type'    => 'select',
                'choices' => array(
                    'standard' => esc_html__('Blog Standard', 'behub'),
                    'style-1'  => esc_html__('Blog Grid', 'behub'),
                    'style-2'  => esc_html__('Blog Overlay', 'behub'),
                ),
            ));

            $wp_customize->add_setting('behub_options_blog_columns', array(
                'type'              => 'option',
                'default'           => 'standard',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_blog_columns', array(
                'section' => 'behub_blog_archive',
                'label'   => esc_html__('Columns', 'behub'),
                'type'    => 'select',
                'choices' => array(
                    1 => esc_html__('1', 'behub'),
                    2  => esc_html__('2', 'behub'),
                    3  => esc_html__('3', 'behub'),
                ),
            ));
        }

        /**
         * @param $wp_customize WP_Customize_Manager
         *
         * @return void
         */
        public function init_behub_social($wp_customize) {

            $wp_customize->add_section('behub_social', array(
                'title' => esc_html__('Socials', 'behub'),
            ));
            $wp_customize->add_setting('behub_options_social_share', array(
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_social_share', array(
                'type'    => 'checkbox',
                'section' => 'behub_social',
                'label'   => esc_html__('Show Social Share', 'behub'),
            ));
            $wp_customize->add_setting('behub_options_social_share_facebook', array(
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_social_share_facebook', array(
                'type'    => 'checkbox',
                'section' => 'behub_social',
                'label'   => esc_html__('Share on Facebook', 'behub'),
            ));
            $wp_customize->add_setting('behub_options_social_share_twitter', array(
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_social_share_twitter', array(
                'type'    => 'checkbox',
                'section' => 'behub_social',
                'label'   => esc_html__('Share on Twitter', 'behub'),
            ));
            $wp_customize->add_setting('behub_options_social_share_linkedin', array(
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_social_share_linkedin', array(
                'type'    => 'checkbox',
                'section' => 'behub_social',
                'label'   => esc_html__('Share on Linkedin', 'behub'),
            ));
            $wp_customize->add_setting('behub_options_social_share_google-plus', array(
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_social_share_google-plus', array(
                'type'    => 'checkbox',
                'section' => 'behub_social',
                'label'   => esc_html__('Share on Google+', 'behub'),
            ));

            $wp_customize->add_setting('behub_options_social_share_pinterest', array(
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_social_share_pinterest', array(
                'type'    => 'checkbox',
                'section' => 'behub_social',
                'label'   => esc_html__('Share on Pinterest', 'behub'),
            ));
            $wp_customize->add_setting('behub_options_social_share_email', array(
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_social_share_email', array(
                'type'    => 'checkbox',
                'section' => 'behub_social',
                'label'   => esc_html__('Share on Email', 'behub'),
            ));
        }

        /**
         * @param $wp_customize WP_Customize_Manager
         *
         * @return void
         */

        public function init_behub_service($wp_customize) {

            $wp_customize->add_section('behub_service_archive', array(
                'title' => esc_html__('Services', 'behub'),
            ));

            $wp_customize->add_setting('behub_options_service_archive_column', array(
                'type'              => 'option',
                'default'           => '3',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_setting('behub_options_service_archive_style', array(
                'type'              => 'option',
                'default'           => 'style-1',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_service_archive_column', array(
                'section' => 'behub_service_archive',
                'label'   => esc_html__('Columns', 'behub'),
                'type'    => 'select',
                'choices' => array(
                    '1' => esc_html__('1', 'behub'),
                    '2' => esc_html__('2', 'behub'),
                    '3' => esc_html__('3', 'behub'),
                    '4' => esc_html__('4', 'behub'),
                ),
            ));

            $wp_customize->add_control('behub_options_service_archive_style', array(
                'section' => 'behub_service_archive',
                'label'   => esc_html__('Style', 'behub'),
                'type'    => 'select',
                'choices' => array(
                    'style-1' => esc_html__('Style 1', 'behub'),
                    'style-2' => esc_html__('Style 2', 'behub'),
                    'style-3' => esc_html__('Style 3', 'behub'),
                ),
            ));
        }

        public function init_behub_project($wp_customize) {

            $wp_customize->add_section('behub_project_archive', array(
                'title' => esc_html__('Project', 'behub'),
            ));

            $wp_customize->add_setting('behub_options_project_archive_column', array(
                'type'              => 'option',
                'default'           => '3',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_project_archive_column', array(
                'section' => 'behub_project_archive',
                'label'   => esc_html__('Columns', 'behub'),
                'type'    => 'select',
                'choices' => array(
                    '1' => esc_html__('1', 'behub'),
                    '2' => esc_html__('2', 'behub'),
                    '3' => esc_html__('3', 'behub'),
                    '4' => esc_html__('4', 'behub'),
                ),
            ));

        }

        /**
         * @param $wp_customize WP_Customize_Manager
         *
         * @return void
         */
        public function init_woocommerce($wp_customize) {

            $wp_customize->add_panel('woocommerce', array(
                'title' => esc_html__('Woocommerce', 'behub'),
            ));

            $wp_customize->add_section('behub_woocommerce_archive', array(
                'title'      => esc_html__('Archive', 'behub'),
                'capability' => 'edit_theme_options',
                'panel'      => 'woocommerce',
                'priority'   => 1,
            ));

            $wp_customize->add_setting('behub_options_woocommerce_archive_layout', array(
                'type'              => 'option',
                'default'           => 'default',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_woocommerce_archive_layout', array(
                'section' => 'behub_woocommerce_archive',
                'label'   => esc_html__('Layout Style', 'behub'),
                'type'    => 'select',
                'choices' => array(
                    'default'  => esc_html__('Sidebar', 'behub'),
                    //====start_premium
                    'canvas'   => esc_html__('Canvas Filter', 'behub'),
                    'dropdown' => esc_html__('Dropdown Filter', 'behub'),
                    //====end_premium
                ),
            ));

            $wp_customize->add_setting('behub_options_woocommerce_archive_sidebar', array(
                'type'              => 'option',
                'default'           => 'left',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_woocommerce_archive_sidebar', array(
                'section' => 'behub_woocommerce_archive',
                'label'   => esc_html__('Sidebar Position', 'behub'),
                'type'    => 'select',
                'choices' => array(
                    'left'  => esc_html__('Left', 'behub'),
                    'right' => esc_html__('Right', 'behub'),

                ),
            ));

            // =========================================
            // Single Product
            // =========================================

            $wp_customize->add_section('behub_woocommerce_single', array(
                'title'      => esc_html__('Single Product', 'behub'),
                'capability' => 'edit_theme_options',
                'panel'      => 'woocommerce',
            ));

            $wp_customize->add_setting('behub_options_single_product_gallery_layout', array(
                'type'              => 'option',
                'default'           => 'horizontal',
                'transport'         => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            ));
            $wp_customize->add_control('behub_options_single_product_gallery_layout', array(
                'section' => 'behub_woocommerce_single',
                'label'   => esc_html__('Style', 'behub'),
                'type'    => 'select',
                'choices' => array(
                    'horizontal' => esc_html__('Horizontal', 'behub'),
                    'vertical'   => esc_html__('Vertical', 'behub'),
                ),
            ));

            $wp_customize->add_setting('behub_options_single_product_content_meta', array(
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'behub_sanitize_editor',
            ));

            $wp_customize->add_control(new BeHub_Customize_Control_Editor($wp_customize, 'behub_options_single_product_content_meta', array(
                'section' => 'behub_woocommerce_single',
                'label'   => esc_html__('Single extra description', 'behub'),
            )));

            $wp_customize->add_setting('behub_options_single_product_archive_sidebar', array(
                'type'              => 'option',
                'default'           => 'left',
                'sanitize_callback' => 'sanitize_text_field',
            ));
            $wp_customize->add_control('behub_options_single_product_archive_sidebar', array(
                'section' => 'behub_woocommerce_single',
                'label'   => esc_html__('Sidebar Position', 'behub'),
                'type'    => 'select',
                'choices' => array(
                    'left'  => esc_html__('Left', 'behub'),
                    'right' => esc_html__('Right', 'behub'),

                ),
            ));


            // =========================================
            // Product
            // =========================================

            $wp_customize->add_section('behub_woocommerce_product', array(
                'title'      => esc_html__('Product Block', 'behub'),
                'capability' => 'edit_theme_options',
                'panel'      => 'woocommerce',
            ));

            $wp_customize->add_setting('behub_options_wocommerce_block_style', array(
                'type'              => 'option',
                'default'           => '',
                'transport'         => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            ));
            $wp_customize->add_control('behub_options_wocommerce_block_style', array(
                'section' => 'behub_woocommerce_product',
                'label'   => esc_html__('Style', 'behub'),
                'type'    => 'select',
                'choices' => array(
                    '' => esc_html__('Style 1', 'behub'),
                ),
            ));

            $wp_customize->add_setting('behub_options_woocommerce_product_hover', array(
                'type'              => 'option',
                'default'           => 'none',
                'transport'         => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            ));
            $wp_customize->add_control('behub_options_woocommerce_product_hover', array(
                'section' => 'behub_woocommerce_product',
                'label'   => esc_html__('Animation Image Hover', 'behub'),
                'type'    => 'select',
                'choices' => array(
                    'none'          => esc_html__('None', 'behub'),
                    'bottom-to-top' => esc_html__('Bottom to Top', 'behub'),
                    'top-to-bottom' => esc_html__('Top to Bottom', 'behub'),
                    'right-to-left' => esc_html__('Right to Left', 'behub'),
                    'left-to-right' => esc_html__('Left to Right', 'behub'),
                    'swap'          => esc_html__('Swap', 'behub'),
                    'fade'          => esc_html__('Fade', 'behub'),
                    'zoom-in'       => esc_html__('Zoom In', 'behub'),
                    'zoom-out'      => esc_html__('Zoom Out', 'behub'),
                ),
            ));

            $wp_customize->add_setting('behub_options_wocommerce_row_laptop', array(
                'type'              => 'option',
                'default'           => 3,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_wocommerce_row_laptop', array(
                'section' => 'woocommerce_product_catalog',
                'label'   => esc_html__('Products per row Laptop', 'behub'),
                'type'    => 'number',
            ));

            $wp_customize->add_setting('behub_options_wocommerce_row_tablet', array(
                'type'              => 'option',
                'default'           => 2,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_wocommerce_row_tablet', array(
                'section' => 'woocommerce_product_catalog',
                'label'   => esc_html__('Products per row tablet', 'behub'),
                'type'    => 'number',
            ));

            $wp_customize->add_setting('behub_options_wocommerce_row_mobile', array(
                'type'              => 'option',
                'default'           => 1,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('behub_options_wocommerce_row_mobile', array(
                'section' => 'woocommerce_product_catalog',
                'label'   => esc_html__('Products per row mobile', 'behub'),
                'type'    => 'number',
            ));
        }

    }
}
return new BeHub_Customize();
